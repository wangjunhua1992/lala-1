<?php
/**
 * 啦啦外卖 - 做好用的外卖系统!
 * =========================================================
 * Copy right 2015-2038 太原多讯网络科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.duoxunwl.com/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 啦啦外卖团队
 * @客服QQ : 2622178042
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
$config_takeout['delivery_timeout_limit'] = intval($config_takeout['delivery_timeout_limit']);
if(empty($config_takeout['delivery_timeout_limit'])) {
	$config_takeout['delivery_timeout_limit'] = 45;
}
$config_takeout['delivery_before_limit'] = intval($config_takeout['delivery_before_limit']);
if(empty($config_takeout['delivery_before_limit'])) {
	$config_takeout['delivery_before_limit'] = 30;
}
$config_takegoods_timeout = $config_takeout['delivery_status_7']['timeout_limit'] ? $config_takeout['delivery_status_7']['timeout_limit'] * 60 : 15 * 60 ;  
$config_service_timeout = $config_takeout['delivery_status_4']['timeout_limit'] ? $config_takeout['delivery_status_4']['timeout_limit'] * 60 : 15 * 60 ; 

if($ta == 'index') {
	$condition = " as a left join " . tablename('tiny_wmall_order_comment') . " as b on a.id = b.oid where a.uniacid = :uniacid and a.status = 5 and a.delivery_type = 2 and a.deliveryer_id != 0 and a.order_type <= 2";
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$deliveryers = deliveryer_fetchall(0, array('work_status' => -1, 'agentid' => -1));
	$deliveryer = array();
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= " and a.deliveryer_id = :deliveryer_id";
		$params[':deliveryer_id'] = $deliveryer_id;
		$deliveryer = array(
			'title' => $deliveryers[$deliveryer_id]['title']
		);
	}
	$days = isset($_GPC['stat_day']) ? -1 : 1;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and a.stat_day >= :start_day and a.stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$starttime = $endtime = date('Ymd');
		$condition .= ' and stat_day = :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$condition_normal = "{$condition} and (a.endtime - a.clerk_notify_collect_time < {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_timeout = "{$condition} and (a.endtime - a.clerk_notify_collect_time > {$config_takeout['delivery_timeout_limit']} * 60)";
	$stat = array();
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params);
	$stat['total_normal_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition_normal, $params);
	$stat['total_timeout_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . " {$condition_timeout}", $params);
	$stat['avg_normal_delivery_time'] = floatval(pdo_fetchcolumn('select round(avg(endtime - clerk_notify_collect_time) / 60, 2) from ' . tablename('tiny_wmall_order') . " {$condition_normal}", $params));
	if(!$stat['total_success_order']) {
		$stat['percent_normal'] = 0;
		$stat['percent_timeout'] = 0;
	} else {
		$stat['percent_normal'] = round(($stat['total_success_order'] - $stat['total_timeout_order']) / $stat['total_success_order'], 2) * 100;
		$stat['percent_timeout'] = round($stat['total_timeout_order'] / $stat['total_success_order'], 2) * 100;
	}

	$records_temp = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_success_order
	 FROM ' . tablename('tiny_wmall_order') . " {$condition} group by stat_day", $params, 'stat_day');

	$records_normal = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_normal_order,
		round(avg(endtime - clerk_notify_collect_time) / 60, 2) as avg_normal_delivery_time,
		round(avg(delivery_assign_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_notify_time,
		round(avg(delivery_takegoods_time - delivery_assign_time) / 60, 2) as avg_delivery_takegoods_time,
		round(avg(delivery_success_time - delivery_instore_time) / 60, 2) as avg_delivery_success_time
	 FROM ' . tablename('tiny_wmall_order') . " {$condition_normal} group by stat_day", $params, 'stat_day');

	$records_timeout = pdo_fetchall('SELECT
		stat_day,
		count(*) as total_timeout_order
	 FROM ' . tablename('tiny_wmall_order') . " {$condition_timeout} group by stat_day", $params, 'stat_day');

	$records = array();
	$basic = array(
		'stat_day' => 0,
		'total_success_order' => 0,
		'total_normal_order' => 0,
		'total_timeout_order' => 0,
		'avg_normal_delivery_time' => 0,
		'percent_timeout' => 0,
	);
	for($i = $endtime; $i >= $starttime;) {
		$basic['stat_day'] = $i;
		$records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
		$records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
		$records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
		$data= array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i]);
		if(!empty($data['total_success_order'])) {
			$data['percent_timeout'] = round($data['total_timeout_order'] / $data['total_success_order'], 2) * 100;
		}
		$records[] = $data;
		$i = date('Ymd', strtotime($i) - 86400);
	}
	$result = array(
		'stat' => $stat,
		'detail' => $records,
		'deliveryer' => $deliveryer
	);
	message(error(0, $result), '', 'ajax');
}

elseif($ta == 'day') {
	$condition = " as a left join " . tablename('tiny_wmall_order_comment') . " as b on a.id = b.oid where a.uniacid = :uniacid and a.status = 5 and a.delivery_type = 2 and a.deliveryer_id != 0 and a.order_type <= 2";
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$days = isset($_GPC['stat_day']) ? -1 : 1;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$starttime = $endtime = date('Ymd');
		$condition .= ' and stat_day = :stat_day';
		$params[':stat_day'] = $starttime;
	}

	$condition_normal = "{$condition} and (a.endtime - a.clerk_notify_collect_time < {$config_takeout['delivery_timeout_limit']} * 60)";
	$condition_timeout = "{$condition} and (a.endtime - a.clerk_notify_collect_time > {$config_takeout['delivery_timeout_limit']} * 60)";
	$stat = array();
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params);
	$stat['total_timeout_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . " {$condition_timeout}", $params);
	$stat['total_normal_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . " {$condition_normal}", $params);
	$stat['avg_normal_delivery_time'] = floatval(pdo_fetchcolumn('select round(avg(endtime - clerk_notify_collect_time) / 60, 2) from ' . tablename('tiny_wmall_order') . " {$condition_normal}", $params));

	if(!$stat['total_success_order']) {
		$stat['percent_normal'] = 0;
		$stat['percent_timeout'] = 0;
	} else {
		$stat['percent_normal'] = round(($stat['total_success_order'] - $stat['total_timeout_order']) / $stat['total_success_order'], 2) * 100;
		$stat['percent_timeout'] = round($stat['total_timeout_order'] / $stat['total_success_order'], 2) * 100;
	}

	$records_temp = pdo_fetchall('SELECT
		stat_day,
		a.deliveryer_id,
		count(*) as total_success_order
	 FROM ' . tablename('tiny_wmall_order') . " {$condition} group by a.deliveryer_id", $params, 'deliveryer_id');

	$records_normal = pdo_fetchall('SELECT
		stat_day,
		a.deliveryer_id,
		count(*) as total_normal_order,
		round(avg(endtime - clerk_notify_collect_time) / 60, 2) as avg_normal_delivery_time,
		round(avg(delivery_assign_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_notify_time,
		round(avg(delivery_takegoods_time - delivery_assign_time) / 60, 2) as avg_delivery_takegoods_time,
		round(avg(delivery_success_time - delivery_instore_time) / 60, 2) as avg_delivery_success_time
	 FROM ' . tablename('tiny_wmall_order') . " {$condition_normal} group by a.deliveryer_id", $params, 'deliveryer_id');

	$records_timeout = pdo_fetchall('SELECT
		stat_day,
		a.deliveryer_id,
		count(*) as total_timeout_order
	 FROM ' . tablename('tiny_wmall_order') . " {$condition_timeout} group by a.deliveryer_id", $params, 'deliveryer_id');

	$condition_paytype_delivery = "{$condition} and a.pay_type = :pay_type";
	$params[':pay_type'] = 'delivery';
	$records_paytype_delivery = pdo_fetchall('SELECT
		stat_day,
		a.deliveryer_id,
		sum(final_fee) as total_paytype_delivery
	 FROM ' . tablename('tiny_wmall_order') . " {$condition_paytype_delivery} group by a.deliveryer_id", $params, 'deliveryer_id');

	$records = array();
	$basic = array(
		'id' => 0,
		'total_success_order' => 0,
		'total_timeout_order' => 0,
		'total_normal_order' => 0,
		'avg_normal_delivery_time' => 0,
		'percent_timeout' => 0,
		'total_paytype_delivery' => 0
	);
	$records = array();
	$deliveryers = deliveryer_fetchall(0, array('work_status' => -1, 'agentid' => $agentid > 0 ? $agentid : -1));
	foreach($deliveryers as $deliveryer) {
		$i = $deliveryer['id'];
		$records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
		$records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
		$records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
		$records_paytype_delivery[$i] = empty($records_paytype_delivery[$i]) ? array() : $records_paytype_delivery[$i];
		$data= array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i], $records_paytype_delivery[$i]);
		if(!empty($data['total_success_order'])) {
			$data['percent_timeout'] = round($data['total_timeout_order'] / $data['total_success_order'], 2) * 100;
		}

		$data['title'] = $deliveryer['title'];
		$records[] = $data;
	}
	$records = array_sort($records, 'total_success_order', SORT_DESC);
	unset($deliveryers);
	$result = array(
		'stat' => $stat,
		'detail' => $records
	);
	message(error(0, $result), '', 'ajax');
}
