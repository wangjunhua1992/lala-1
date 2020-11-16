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
icheckauth();

if($ta == 'index') {
	$order_sn = trim($_GPC['order_sn']);
	$type = trim($_GPC['order_type']);
	if(empty($order_sn) || empty($type)) {
		imessage(error(-1, '参数错误'), '', 'ajax');
	}
	$tables_router = array(
		'takeout' => array(
			'table' => 'tiny_wmall_order',
			'cancel_status' => 6,
			'order_sn' => 'ordersn'
		),
		'deliveryCard' => array(
			'table' => 'tiny_wmall_delivery_cards_order',
			'order_sn' => 'ordersn'
		),
		'errander' => array(
			'table' => 'tiny_wmall_errander_order',
			'cancel_status' => 4,
			'order_sn' => 'order_sn'
		),
		'recharge' => array(
			'table' => 'tiny_wmall_member_recharge',
			'order_sn' => 'order_sn'
		),
		'freelunch' => array(
			'table' => 'tiny_wmall_freelunch_partaker',
			'order_sn' => 'order_sn'
		),
		'peerpay' => array(
			'table' => 'tiny_wmall_order_peerpay_payinfo',
			'order_sn' => 'order_sn'
		),
		'paybill' => array(
			'table' => 'tiny_wmall_paybill_order',
			'order_sn' => 'order_sn'
		),
		'creditshop' => array(
			'table' => 'tiny_wmall_creditshop_order_new',
			'order_sn' => 'order_sn'
		),
		'seckill' => array(
			'table' => 'tiny_wmall_seckill_order',
			'order_sn' => 'order_sn'
		),
		'vip' => array(
			'table' => 'tiny_wmall_vip_order',
			'order_sn' => 'order_sn'
		),
		'recharge_vip' => array(
			'table' => 'tiny_wmall_member_recharge',
			'order_sn' => 'order_sn'
		),
		'mealRedpacket_plus' => array(
			'table' => 'tiny_wmall_superredpacket_meal_order',
			'order_sn' => 'order_sn'
		),
		'mealRedpacket' => array(
			'table' => 'tiny_wmall_superredpacket_meal_order',
			'order_sn' => 'order_sn'
		),
		'gohome' => array(
			'table' => 'tiny_wmall_gohome_order',
			'order_sn' => 'ordersn'
		),
		'tongcheng' => array(
			'table' => 'tiny_wmall_tongcheng_order',
			'order_sn' => 'ordersn'
		),
		'haodian' => array(
			'table' => 'tiny_wmall_haodian_order',
			'order_sn' => 'ordersn'
		),
		'svip' => array(
			'table' => 'tiny_wmall_svip_meal_order',
			'order_sn' => 'ordersn'
		),
	);
	$router = $tables_router[$type];
	$order = pdo_get($router['table'], array('uniacid' => $_W['uniacid'], $router['order_sn'] => $order_sn), array('id', 'is_pay'));
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['is_pay'] != 1) {
		imessage(error(-1000, '订单未支付，请重新支付'), '', 'ajax');
	}
	imessage(error(0, $order), '', 'ajax');
}
