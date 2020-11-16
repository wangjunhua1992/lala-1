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

$pay_types = array(
	'wechat' => array(
		'css' => 'color-success',
		'text' => '微信支付'
	),
	'alipay' => array(
		'css' => 'color-info',
		'text' => '支付宝支付'
	),
	'credit' => array(
		'css' => 'color-danger',
		'text' => '余额支付'
	),
);

if($ta == 'list') {
	$condition = ' where a.uniacid = :uniacid and sid = :sid and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	if(trim($_GPC['pay_type']) != 'all') {
		$pay_type = trim($_GPC['pay_type']);
		$condition .= ' and a.pay_type = :pay_type';
		$params[':pay_type'] = $pay_type;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and b.nickname like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$orders = pdo_fetchall('select a.*,b.nickname,b.avatar from ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by addtime desc limit ' . ($page - 1) * $psize . ',' . $psize, $params);

	if(!empty($orders)) {
		foreach($orders as &$order) {
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['pay_type_cn'] = $order['pay_type'] ? $pay_types[$order['pay_type']]['text'] : '未支付';
			$order['pay_type_css'] = $order['pay_type'] ? $pay_types[$order['pay_type']]['css'] : '';
		}
	}
	$result = array(
		'orders' => $orders
	);
	imessage(error(0, $result), '', 'ajax');
}


elseif($ta == 'detail') {
	$id = intval($_GPC['id']);
	$condition = ' where a.uniacid = :uniacid and a.id = :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':id' => $id,
	);
	$order = pdo_fetch('select a.*, b.nickname, b.avatar from ' . tablename('tiny_wmall_paybill_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	if(!empty($order)) {
		$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
		$order['pay_type_cn'] = $order['pay_type'] ? $pay_types[$order['pay_type']]['text'] : '未支付';
	}
	$result = array(
		'order' => $order
	);
	imessage(error(0, $result), '', 'ajax');
}
