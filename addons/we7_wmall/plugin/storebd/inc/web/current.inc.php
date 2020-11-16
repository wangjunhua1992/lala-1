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

if($op == 'list') {
	$_W['page']['title'] = '推广员账户';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$bd_id = intval($_GPC['bd_id']);
	if($bd_id > 0) {
		$condition .= ' and bd_id = :bd_id';
		$params[':bd_id'] = $bd_id;
	}
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
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
	$psize = 50;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_storebd_current_log') . $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_storebd_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$store_spreads = storebd_user_fetchall();
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'), 'id');
	$trade_types = storebd_trade_types();
	$pager = pagination($total, $pindex, $psize);
}
include itemplate('current');