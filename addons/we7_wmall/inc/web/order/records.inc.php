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
mload()->model('deliveryer');
$op = trim($_GPC['op'])? trim($_GPC['op']): 'stat';
$deliveryer_alls = deliveryer_all();

if($op == 'stat') {
	$_W['page']['title'] = '接单统计';

	$condition = ' where uniacid = :uniacid and status = 1';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' and id = :id';
		$params[':id'] = $deliveryer_id;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_deliveryer') . $condition . ' order by id desc', $params);

	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition = ' where uniacid = :uniacid and addtime >= :starttime and addtime <= :endtime and deliveryer_id > 0 and delivery_type = 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	if($deliveryer_id > 0) {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$finish = pdo_fetchall('select count(*) as total,deliveryer_id from ' . tablename('tiny_wmall_order') . $condition . ' and delivery_status = 5 group by deliveryer_id', $params, 'deliveryer_id');
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	$timeout = pdo_fetchall('select count(*) as total,deliveryer_id from ' . tablename('tiny_wmall_order') . $condition . " and status = 5 and order_type <= 2 and (cast(endtime as signed) - cast(clerk_notify_collect_time as signed) > {$config_takeout['delivery_timeout_limit']} * 60) group by deliveryer_id", $params, 'deliveryer_id');
	$wait_pickup = pdo_fetchall('select count(*) as total,deliveryer_id from ' . tablename('tiny_wmall_order') . $condition . ' and delivery_status = 7 group by deliveryer_id', $params, 'deliveryer_id');
	$deliverying = pdo_fetchall('select count(*) as total,deliveryer_id from ' . tablename('tiny_wmall_order') . $condition . ' and delivery_status = 4 group by deliveryer_id', $params, 'deliveryer_id');
}

if($op == 'list') {
	$_W['page']['title'] = '接单记录';
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition = ' where uniacid = :uniacid and addtime >= :starttime and addtime <= :endtime and deliveryer_id > 0 and delivery_type = 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($orders)) {
		foreach($orders as &$val) {
			$val['time_interval'] = order_time_analyse($val['id']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$order_delivery_status = order_delivery_status();
}

include itemplate('order/records');