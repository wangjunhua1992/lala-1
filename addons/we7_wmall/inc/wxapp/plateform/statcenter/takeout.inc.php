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
	$condition = ' where uniacid = :uniacid and status = 5 and is_pay = 1 and order_type < 3 and stat_day = :stat_day';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':stat_day' => date('Ymd'),
	);
	if($_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $_W['agentid'];
	}
	$stat = pdo_fetch('select count(*) as total_num, round(sum(final_fee), 2) as total_price from ' . tablename('tiny_wmall_order') . $condition, $params);
	$result = array(
		'stat' => $stat
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'takeout') {
	$condition = ' WHERE uniacid = :uniacid and order_type <= 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$sid = intval($_GPC['sid']);
	$store = array();
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('id', 'title'));
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
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
	$detail = intval($_GPC['detail']);

	$stat = array();
	$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
	$stat['total_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
	$stat['total_refund_fee'] = floatval(pdo_fetchcolumn('select round(sum(refund_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and refund_status > 0', $params));
	if(!$detail) {
		$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
		$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params));
		$stat['avg_pre_order'] = floatval($stat['total_success_order'] > 0 ? ($stat['total_fee'] / $stat['total_success_order']) : 0);
		$stat['avg_pre_order'] = round($stat['avg_pre_order'], 2);
		$stat['total_serve_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_serve_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_delivery_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_delivery_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_deliveryer_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_deliveryer_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['plateform_discount_fee'] = floatval(pdo_fetchcolumn('select round(sum(plateform_discount_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_extra_fee'] = floatval(pdo_fetchcolumn('select round(sum(extra_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		if($_W['is_agent']) {
			$stat['agent_discount_fee'] = floatval(pdo_fetchcolumn('select round(sum(agent_discount_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		}
	}
	$result = array(
		'stat' => $stat,
		'store' => $store
	);
	if(!$detail) {
		imessage(error(0, $result), '', 'ajax');
	}
	$records_temp = pdo_fetchall('SELECT stat_day,
		count(*) as total_success_order,
		round(sum(total_fee), 2) as total_fee,
		round(sum(final_fee), 2) as final_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall('SELECT stat_day, round(sum(refund_fee), 2) as refund_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and refund_status > 0 group by stat_day', $params, 'stat_day');
	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		if(empty($records_temp[$i])) {
			$records[] = array(
				'stat_day' => $i,
				'total_success_order' => 0,
				'total_fee' => 0,
				'final_fee' => 0,
				'refund_fee' => 0,
			);
		} else {
			$records_temp[$i]['refund_fee'] = $cancel_records[$i]['refund_fee'];
			$records[] = $records_temp[$i];
		}
		$i = date('Ymd', strtotime($i) - 86400);
	}
	$result['detail'] = $records;
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'search') {
	$params = array(
		'time' => array(
			'name' => 'stat_day',
			'start' => '开始时间',
			'end' => '截止时间',
		),
		'extra' => array(
			'store' => '1',
		),
	);
	if($_W['is_agent'] == 1 || $_W['agentid'] > 0) {
		$params['extra']['agent'] = 1;
	}
	$searchtype = trim($_GPC['searchtype']);
	if($searchtype == 'takeoutOrder') {
		$params['extra']['orderby'] = array(
			'key' => '1',
			'values' => array(
				'final_fee' => '营业额',
				'total_success_order' => '有效订单量',
				'store_final_fee' => '总收入',
			)
		);
	} elseif($searchtype == 'hotGoods') {
		$params['extra']['orderby'] = array(
			'key' => '1',
			'values' => array(
				'total_goods_price' => '销售额',
				'total_goods_num' => '销售量',
			)
		);
	} elseif($searchtype == 'deliveryIndex' || $searchtype == 'deliveryDay') {
		unset($params['extra']['store']);
		if($searchtype == 'deliveryIndex') {
			$params['extra']['deliveryer_id'] = '1';
		}
	}
	$filter = get_filter_params($params);
	$result = array(
		'filter' => $filter,
	);
	imessage(error(0, $result), '', 'ajax');
}



