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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '新入驻店铺订单统计';
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = strtotime("-{$days} days", $todaytime);
	$endtime = $todaytime + 86399;
	$stores = pdo_fetchall('select id, title, addtime from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and status = :status and addtime >= :starttime and addtime < :endtime', array(':uniacid' => $_W['uniacid'], ':status' => 1, ':starttime' => $starttime, ':endtime' => $endtime), 'id');

	$condition = ' WHERE uniacid = :uniacid and order_type <= 2 and addtime >= :addtime';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':addtime' => min(array_column($stores, 'addtime')),
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
		$params[':addtime'] = $stores[$sid]['addtime'];
	}

	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'final_fee';
	$plateform = pdo_fetch('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
	$plateform['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
	$records_success = pdo_fetchall('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, sid FROM ' . tablename('tiny_wmall_order') . $condition . " and status = 5 and is_pay = 1 group by sid order by {$orderby} desc", $params, 'sid');
	$records_cancle = pdo_fetchall('select count(*) as total_cancel_order, sid from ' . tablename('tiny_wmall_order') . $condition .  " and status = 6 group by sid order by total_cancel_order desc", $params, 'sid');

	$records = array();
	if($sid > 0) {
		$records[] = array(
			'total_success_order' => intval($records_success[$sid]['total_success_order']),
			'store_final_fee' => floatval($records_success[$sid]['store_final_fee']),
			'final_fee' => floatval($records_success[$sid]['final_fee']),
			'total_cancel_order' => intval($records_cancle[$sid]['total_cancel_order']),
			'pre_final_fee' => round($records_success[$sid]['final_fee'] / $plateform['final_fee'], 4) * 100,
			'pre_success_order' => round($records_success[$sid]['total_success_order'] / $plateform['total_success_order'], 4) * 100,

			'pre_store_final_fee' => round($records_success[$sid]['store_final_fee'] / $plateform['store_final_fee'], 4) * 100,
			'pre_cancel_order' => round($records_cancle[$sid]['total_cancel_order'] / $plateform['total_cancel_order'], 4) * 100,
			'store_name' => $stores[$sid]['title'],
		);
	} else {
		foreach($stores as $store_id => $store) {
			$records[] = array(
				'total_success_order' => intval($records_success[$store_id]['total_success_order']),
				'store_final_fee' => floatval($records_success[$store_id]['store_final_fee']),
				'final_fee' => floatval($records_success[$store_id]['final_fee']),
				'total_cancel_order' => intval($records_cancle[$store_id]['total_cancel_order']),
				'pre_final_fee' => round($records_success[$store_id]['final_fee'] / $plateform['final_fee'], 4) * 100,
				'pre_success_order' => round($records_success[$store_id]['total_success_order'] / $plateform['total_success_order'], 4) * 100,

				'pre_store_final_fee' => round($records_success[$store_id]['store_final_fee'] / $plateform['store_final_fee'], 4) * 100,
				'pre_cancel_order' => round($records_cancle[$store_id]['total_cancel_order'] / $plateform['total_cancel_order'], 4) * 100,
				'store_name' => $store['title'],
			);
		}
	}

	$sort = array_column($records, $orderby);
	array_multisort($sort,SORT_DESC, $records);

	if($_W['isajax']){
		$stat = array();
		$stat['store_num'] = count($stores);
		$stat['final_fee'] = floatval($plateform['final_fee']);
		$stat['total_success_order'] = intval($plateform['total_success_order']);
		$stat['store_final_fee'] = floatval($plateform['store_final_fee']);
		$stat['total_cancel_order'] = intval($plateform['total_cancel_order']);
		if($orderby == 'total_success_order'){
			$title = '有效订单量';
		} elseif($orderby == 'final_fee') {
			$title = '营业额';
		} elseif($orderby == 'store_final_fee') {
			$title = '总收入';
		}
		$stat['title'] = $title;
		$i = 0;
		foreach ($records as $value) {
			if($i == 10){
				break;
			}
			$stat['sid'][] = $value['store_name'];
			if($orderby == 'total_success_order'){
				$stat['value'][] = $value['total_success_order'];
			} elseif($orderby == 'final_fee') {
				$stat['value'][] = $value['final_fee'];
			} elseif($orderby == 'store_final_fee') {
				$stat['value'][] = $value['store_final_fee'];
			}
			$i++;
		}
		message(error(0, $stat), '', 'ajax');
	}
}
include itemplate('statcenter/newStore');