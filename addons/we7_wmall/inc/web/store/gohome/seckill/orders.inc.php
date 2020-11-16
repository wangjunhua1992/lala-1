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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if ($ta == 'list') {
	$_W['page']['title'] = '订单列表';
	$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.is_pay = 1 ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;

	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND a.addtime > :start AND a.addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);

			$condition .= ' and a.addtime >= :start';
			$params[':start'] = $starttime;
		}
	}
	$goods_name = $_GPC['goods_name'];
	if(!empty($goods_name)) {
		$condition .= " AND (b.title LIKE '%{$goods_name}%')";
	}
	$keywords = $_GPC['keywords'];
	if(!empty($keywords)) {
		$condition .= " AND (a.username LIKE '%{$keywords}%' OR a.mobile LIKE '%{$keywords}%')";
	}
	$pay_types = order_pay_types();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_seckill_order') . ' as a left join ' . tablename('tiny_wmall_seckill_goods') . ' as b on a.goods_id = b.id ' . $condition, $params);
	$orders = pdo_fetchall('select a.*,b.title as goods_title from ' . tablename('tiny_wmall_seckill_order') . ' as a left join ' . tablename('tiny_wmall_seckill_goods') . ' as b on a.goods_id = b.id ' . $condition . ' order by a.addtime desc limit ' . ($pindex - 1) * $psize.','.$psize, $params);
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	$pager = pagination($total, $pindex, $psize);
	if (!empty($orders)) {
		foreach ($orders as &$val) {
			$val['pay_type'] = $pay_types[$val['pay_type']];
		}
	}
}

if ($ta == 'status') {
	$id = intval($_GPC['id']);
	mload()->model('plugin');
	pload()->model('seckill');
	$order = seckill_order_get($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] != 1) {
		imessage(error(-1, '该订单已核销或已取消'), '', 'ajax');
	}

	$type = trim($_GPC['type']);
	if($type == 'status') {
		seckill_order_update($order, 'status');
		imessage(error(0, '核销成功'), ireferer(), 'ajax');
	} elseif($type == 'cancel') {
		$res = seckill_order_update($order, 'cancel');
		imessage(error(0, '取消成功'), ireferer(), 'ajax');
	}
}

include itemplate('store/seckill/orders');