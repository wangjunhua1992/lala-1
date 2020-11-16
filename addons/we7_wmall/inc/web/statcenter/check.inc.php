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
	$_W['page']['title'] = '商户账户核对';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	$condition = ' WHERE uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$sid = intval($_GPC['sid']);
	$balance = intval($_GPC['balance']);
	if($sid > 0 && !$balance) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$accounts = pdo_fetchall('SELECT sid,amount FROM ' . tablename('tiny_wmall_store_account') . $condition, $params, 'sid');

	//$days = $_GPC['days'];
	if(!empty($_GPC['stat_day'])) {
		$starttime = strtotime($_GPC['stat_day']['start']);
		$endtime = strtotime($_GPC['stat_day']['end']);
		$endtime = $endtime + 86399;
		$condition .= ' and addtime >= :starttime and addtime <= :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	} else {
		$starttime = strtotime(date('Y-m-d', TIMESTAMP));
		$endtime = $starttime + 86399;
		$condition .= ' and addtime >= :starttime and addtime <= :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}


	$records_takeout = pdo_fetchall('SELECT sid,
		round(sum(store_final_fee), 2) as takeout_final_fee
	FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1 group by sid', $params, 'sid');

	$records_paybill = pdo_fetchall('SELECT sid,
		round(sum(store_final_fee), 2) as paybill_final_fee
	FROM ' . tablename('tiny_wmall_paybill_order') . $condition . ' and status = 1 and is_pay = 1 group by sid', $params, 'sid');

	$records_getcash = pdo_fetchall('SELECT sid,
		round(sum(get_fee), 2) as getcash_final_fee
	FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' group by sid', $params, 'sid');

	$records_change = pdo_fetchall('SELECT sid,
		round(sum(fee), 2) as change_final_fee
	FROM ' . tablename('tiny_wmall_store_current_log') . $condition . ' and trade_type = 3 group by sid', $params, 'sid');

	if($sid > 0) {
		$data = array(
			$sid => array(
				'id' => $stores[$sid]['id'],
				'title' => $stores[$sid]['title'],
				'takeout_final_fee' => $records_takeout[$sid]['takeout_final_fee'],
				'paybill_final_fee' => $records_paybill[$sid]['paybill_final_fee'],
				'getcash_final_fee' => $records_getcash[$sid]['getcash_final_fee'],
				'change_final_fee' => $records_change[$sid]['change_final_fee'],
				'amount' => $accounts[$sid]['amount'],
			)
		);
		$data[$sid]['balance'] = $data[$sid]['takeout_final_fee'] + $data[$sid]['paybill_final_fee'] + $data[$sid]['change_final_fee'] - $data[$sid]['getcash_final_fee'] - $data[$sid]['amount'];
	} else {
		foreach($stores as &$store) {
			$store['takeout_final_fee'] = $records_takeout[$store['id']]['takeout_final_fee'];
			$store['paybill_final_fee'] = $records_paybill[$store['id']]['paybill_final_fee'];
			$store['getcash_final_fee'] = $records_getcash[$store['id']]['getcash_final_fee'];
			$store['change_final_fee'] = $records_change[$store['id']]['change_final_fee'];
			$store['amount'] = $accounts[$store['id']]['amount'];
			$store['balance'] = $store['takeout_final_fee'] + $store['paybill_final_fee'] + $store['change_final_fee'] - $store['getcash_final_fee'] - $store['amount'];
		}
		$data = $stores;
	}
	if($_W['ispost'] && $balance == 1) {
		mload()->model('store');
		$fee = round($data[$sid]['balance'], 2);
		$status = store_update_account($sid, $fee, 5, '', '平台多退少补，平衡商家账户');
		if(is_error($status)) {
			imessage(error(-1, $status['message']), '', 'ajax');
		}
		imessage(error(0, '平衡账户成功'), '', 'ajax');
	}
}
include itemplate('statcenter/check');



