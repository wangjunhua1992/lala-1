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
mload()->model('order');
$_W['page']['title'] = '买单';

if($ta == 'index') {
	$condition = ' WHERE a.uniacid = :uniacid and sid = :sid and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	if(trim($_GPC['pay_type']) == 'all') {
		$pay_type = 'all';
	} else {
		$pay_type = trim($_GPC['pay_type']);
		$condition .= ' and a.pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	
	$min = 0;
	$orders = pdo_fetchall('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' ORDER BY addtime desc limit 15', $params, 'addtime');
	if(!empty($orders)) {
		$min = min(array_keys($orders));
	}
	include itemplate('paycenter/paybill');
}


if($ta == 'detail') {
	$id = intval($_GPC['id']);
	$condition = ' WHERE a.uniacid = :uniacid and a.id = :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':id' => $id,
	);
	$order = pdo_fetch('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	include itemplate('paycenter/paybill');
}

if($ta = 'more') {
	$addtime = intval($_GPC['min']);
	$condition = ' WHERE a.uniacid = :uniacid and a.addtime < :addtime and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':addtime' => $addtime,
	);
	if(trim($_GPC['pay_type']) == 'all') {
		$pay_type = 'all';
	} else {
		$pay_type = trim($_GPC['pay_type']);
		$condition .= ' and a.pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	$orders = pdo_fetchall('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' ORDER BY addtime desc limit 15', $params, 'addtime');
	if(!empty($orders)) {
		foreach ($orders as &$value) {
			$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('errno' => 0, 'message' => $orders, 'min' => $min);
	imessage($respon, '', 'ajax');
}
