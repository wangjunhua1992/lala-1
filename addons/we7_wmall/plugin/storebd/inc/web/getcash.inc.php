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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if ($op == 'list') {
	$_W['page']['title'] = '推广员提现记录';

	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];

	$bd_id = intval($_GPC['bd_id']);
	if($bd_id > 0) {
		$condition .= ' AND bd_id = :bd_id';
		$params[':bd_id'] = $bd_id;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND addtime > :start AND addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$condition .= ' and addtime >= :start';
			$params[':start'] = $starttime;
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_storebd_getcash_log') .  $condition, $params);
	$storebd = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_storebd_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$bd_user = storebd_user_fetchall();
}

elseif ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_storebd_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
/*	if($_W['is_agent']) {
		$log = pdo_get('tiny_wmall_storebd_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if($log['agentid'] > 0) {
			mload()->model('agent');
			agent_update_account($log['agentid'], $log['take_fee'], 3, '', '店铺推广员提现收取手续费','');
		}
	}*/
	imessage(error(0, '设置提现状态成功'), '', 'ajax');
}

elseif ($op == 'cancel') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_storebd_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), ireferer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), ireferer(), 'ajax');
	}
	$bd_user = storebd_user_fetch($log['bd_id']);
	if($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		$remark .= '撤销提现返还账户';
		storebd_user_credit_update(array('bd_id' => $log['bd_id'], 'fee' => $log['get_fee'],  'trade_type' => 3, 'remark' => $remark, 'extra' => $log['id']));
		pdo_update('tiny_wmall_storebd_getcash_log', array('status' => 3, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
		sys_notice_storebd_user_getcash($log['bd_id'], $id, 'cancel', $remark);
		imessage(error(0, '提现撤销成功'), ireferer(), 'ajax');
	}
	include itemplate('getcashOp');
	die();
}

elseif ($op == 'transfers') {
	$id = intval($_GPC['id']);
	$transfers = storebd_user_getcash_transfers($id);
	imessage($transfers, '' , 'ajax');
}
include itemplate('getcash');