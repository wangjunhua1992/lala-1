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
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$stat_day = json_decode(htmlspecialchars_decode($_GPC['stat_day']), true);
		$starttime = str_replace('-', '', trim($stat_day['start']));
		$endtime = str_replace('-', '', trim($stat_day['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$stat = array();
	$stat['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
	$stat['store_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params));
	$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
	$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
	$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params));
	$stat['avg_pre_order'] = floatval(round($stat['total_success_order'] > 0 ? ($stat['total_fee'] / $stat['total_success_order']) : 0, 2));

	$result = array(
		'stat' => $stat
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'detail') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$stat_day = json_decode(htmlspecialchars_decode($_GPC['stat_day']), true);
		$starttime = str_replace('-', '', trim($stat_day['start']));
		$endtime = str_replace('-', '', trim($stat_day['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$records_temp = pdo_fetchall('SELECT stat_day, count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall('SELECT stat_day, count(*) as total_cancel_order, round(sum(store_final_fee), 2) as store_final_fee
	 FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 6 group by stat_day', $params, 'stat_day');
	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		if(empty($records_temp[$i])) {
			$records[] = array(
				'stat_day' => $i,
				'total_success_order' => 0,
				'final_fee' => 0,
				'store_final_fee' => 0,
				'plateform_discount_fee' => 0,
				'store_discount_fee' => 0,
				'plateform_serve_fee' => 0,
				'plateform_delivery_fee' => 0,
				'total_cancel_order' => intval($cancel_records[$i]['total_cancel_order']),
			);
		} else {
			$records_temp[$i]['total_cancel_order'] = intval($cancel_records[$i]['total_cancel_order']);
			$records[] = $records_temp[$i];
		}
		$i = date('Ymd', strtotime($i) - 86400);
	}
	$result = array(
		'records' => $records,
	);
	imessage(error(0, $result), '', 'ajax');
}



