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

if($ta == 'index') {
	$channels = array(
		'eleme' => '饿了么',
		'meituan' => '美团',
		'wxapp' => '小程序',
		'wap' => 'H5',
		'h5app' => '顾客APP',
		'plateformCreate' => '后台创建',
	);
	$condition = ' WHERE uniacid = :uniacid and order_type <= 2 and status = 5 and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$sid = intval($_GPC['sid']);
	$store = array();
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('id', 'title'));
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
	$stat = array();
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) as total_success_order from ' . tablename('tiny_wmall_order') . $condition , $params);
	foreach ($channels as $key => $value) {
		$params[':order_channel'] = $key;
		//每种订单总数
		$total_order = "total_{$key}_order";
		$stat[$total_order] = pdo_fetchcolumn("select count(*) as {$total_order} from " .tablename('tiny_wmall_order') . "{$condition} and order_channel = :order_channel", $params);
		//每种订单占比
		$per_total_order = "per_total_{$key}_order";
		if($stat['total_success_order']) {
			$stat[$per_total_order] = round($stat[$total_order] / $stat['total_success_order'], 4) * 100;
		} else {
			$stat[$per_total_order] = 0;
		}
	}
	$result = array(
		'stat' => $stat,
		'store' => $store
	);
	$detail = intval($_GPC['detail']);
	if(!$detail) {
		imessage(error(0, $result), '', 'ajax');
	}

	unset($params[':order_channel']);
	$records_all=array();
	$records_all['total_success_order'] = pdo_fetchall('select stat_day,count(*) as total_success_order from ' . tablename('tiny_wmall_order') . " {$condition} group by stat_day", $params, 'stat_day');
	foreach ($channels as $key => $value ) {
		$name = "order_{$key}";
		$params[':order_channel'] = $key;
		$records_all[$name] = pdo_fetchall("select stat_day,count(*) as {$name} from " . tablename('tiny_wmall_order') . $condition . " and order_channel = :order_channel group by stat_day", $params, 'stat_day');
	}

	$basic = array(
		'stat_day' => 0,
		'total_success_order' => 0,
		'order_eleme' => 0,
		'order_meituan' => 0,
		'order_wxapp' => 0,
		'order_wap' => 0,
		'order_h5app' => 0,
		'order_plateformCreate' => 0,
	);
	for($i = $endtime; $i >= $starttime;) {
		$basic['stat_day'] = $i;
		foreach ($channels as $key => $value ) {
			$name = "order_{$key}";
			$records_all[$name][$i] = empty($records_all[$name][$i]) ? array() : $records_all[$name][$i];
			$records_all['total_success_order'][$i] = empty($records_all['total_success_order'][$i]) ? array() : $records_all['total_success_order'][$i];
		}
		$data= array_merge($basic, $records_all['total_success_order'][$i], $records_all['order_eleme'][$i], $records_all['order_meituan'][$i], $records_all['order_wxapp'][$i], $records_all['order_wap'][$i], $records_all['order_h5app'][$i],$records_all['order_plateformCreate'][$i]);
		if(!empty($data['total_success_order'])) {
			foreach ($channels as $key => $value) {
				$pre_order = "pre_order_{$key}";
				$name = "order_{$key}";
				$data[$pre_order] = round($data[$name] / $data['total_success_order'], 4) * 100;
			}
		}else{
			foreach ($channels as $key => $value) {
				$pre_order = "pre_order_{$key}";
				$data[$pre_order] = 0;
			}
		}
		$records[] = $data;
		$i = date('Ymd', strtotime($i) - 86400);
	}
	$result['detail'] = $records;
	imessage(error(0, $result), '', 'ajax');
}



