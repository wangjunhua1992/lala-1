<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

//绑定转换程序
$binds = pdo_fetchall('select a.storebd_id, b.sid, b.bd_id, b.addtime from ' . tablename('tiny_wmall_store') . ' as a left join ' . tablename('tiny_wmall_storebd_store') . ' as b on a.id = b.sid where a.uniacid = :uniacid and a.status = 1 and a.storebd_id = 0 and b.sid > 0', array(':uniacid' => $_W['uniacid']));
if(!empty($binds)) {
	foreach($binds as $bind) {
		if($bind['sid'] > 0 && $bind['bd_id'] > 0) {
			pdo_update('tiny_wmall_store', array('storebd_id' => $bind['bd_id']), array('uniacid' => $_W['uniacid'], 'id' => $bind['sid']));
			pdo_query('update ' . tablename('tiny_wmall_order') . " set storebd_id = :storebd_id where uniacid = :uniacid and sid = :sid and  status = 5 and addtime > :addtime", array(':uniacid' => $_W['uniacid'], ':sid' => $bind['sid'], ':storebd_id' => $bind['bd_id'], ':addtime' => $bind['addtime']));
		}
	}
}

if($op == 'index') {
	$_W['page']['title'] = '业务员推广店铺订单统计';
	//店铺业务员
	$storebds = pdo_fetchall('select a.id as storebd_id, b.realname from ' . tablename('tiny_wmall_storebd_user') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and a.status = 1', array(':uniacid' => $_W['uniacid']), 'storebd_id');

	//业务员推广的商户
	$condition_store = ' where uniacid =:uniacid and status = 1 ';
	$params_store = array(
		':uniacid' => $_W['uniacid']
	);
	$storebd_id = intval($_GPC['storebd_id']);
	if($storebd_id > 0) {
		$condition_store .= ' and storebd_id = :storebd_id ';
		$params_store[':storebd_id'] = $storebd_id;
	} else {
		$condition_store .= ' and storebd_id > 0 ';
	}
	$stores = pdo_fetchall('select id, title, storebd_id from ' . tablename('tiny_wmall_store') . $condition_store, $params_store, 'id');

	$condition = ' where uniacid = :uniacid and status = 5 and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid ';
		$params[':sid'] = $sid;
	}

	if($storebd_id > 0) {
		$condition .= ' and storebd_id = :storebd_id ';
		$params[':storebd_id'] = $storebd_id;
	} else {
		$condition .= ' and storebd_id > 0 ';
	}

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
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

	$plateform = pdo_fetch('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee FROM ' . tablename('tiny_wmall_order') . $condition, $params);

	$records_success = pdo_fetchall('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, sid, storebd_id FROM ' . tablename('tiny_wmall_order') . $condition . " group by sid ", $params, 'sid');

	$records = array();
	if($sid > 0) {
		$records[] = array(
			'total_success_order' => intval($records_success[$sid]['total_success_order']),
			'store_final_fee' => floatval($records_success[$sid]['store_final_fee']),
			'final_fee' => floatval($records_success[$sid]['final_fee']),
			'pre_final_fee' => round($records_success[$sid]['final_fee'] / $plateform['final_fee'], 4) * 100,
			'pre_success_order' => round($records_success[$sid]['total_success_order'] / $plateform['total_success_order'], 4) * 100,

			'pre_store_final_fee' => round($records_success[$sid]['store_final_fee'] / $plateform['store_final_fee'], 4) * 100,
			'store_name' => $stores[$sid]['title'],
			'storebd_realname' => $storebds[$stores[$sid]['storebd_id']]['realname'],
		);
	} else {
		foreach($stores as $store_id => $store) {
			$records[] = array(
				'total_success_order' => intval($records_success[$store_id]['total_success_order']),
				'store_final_fee' => floatval($records_success[$store_id]['store_final_fee']),
				'final_fee' => floatval($records_success[$store_id]['final_fee']),
				'pre_final_fee' => round($records_success[$store_id]['final_fee'] / $plateform['final_fee'], 4) * 100,
				'pre_success_order' => round($records_success[$store_id]['total_success_order'] / $plateform['total_success_order'], 4) * 100,
				'pre_store_final_fee' => round($records_success[$store_id]['store_final_fee'] / $plateform['store_final_fee'], 4) * 100,
				'store_name' => $store['title'],
				'storebd_realname' =>$storebds[$store['storebd_id']]['realname'],
			);
		}
	}

	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'final_fee';
	$sort = array_column($records, $orderby);
	array_multisort($sort,SORT_DESC, $records);

	if($_W['isajax']){
		$stat = array();
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
include itemplate('statcenter');