<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');

function yunzhong_member($uid, $mid = 0) {
	global $_W;
	$url = $_W['siteroot'] . "addons/yun_shop/api.php?i={$_W['uniacid']}&type=1&route=member.member.memberFromHXQModule";
	$post_data = array(
		'uid' => $uid,
		'mid' => $mid
	);
	$res = ihttp_post($url, $post_data);
	if(is_error($res)) {
		return error(-1, $res['message']);
	}
	$data_obj = @json_encode($res['content'],true);
	if($data_obj['status'] != 1) {
		return error(-1, $data_obj['result']);
	}
	return error(0, $data_obj['result']);
}

function yunzhong_add_order($order) {
	global $_W;
	$post_data = array(
		'uniacid' => $order['uniacid'],
		'goods_total' => $order['num'],
		'openid' => $order['openid'],
		'uid' => $order['uid'],
		//'mid' => $mid,
		'mid' => $order['spread1'],
		'order_sn' => $order['ordersn'],
		'price' => $order['final_fee'],
		'goods_price' => $order['total_fee'],
		'status' => 0,//订单状态：0未支,1支付，2待发货，3已发货，4已签收，5已取消，6待退款，7已退款
		'realname' => $order['username'],
		'mobile' => $order['mobile'],
		'address' => $order['address'],//省市县
		//'province'    => $adress_fee['province'],
		//'city'        => $adress_fee['city'],
		//'county'      => $adress_fee['county'],
		'detailed_address' => $order['address'],
	);

	$url = $_W['siteroot'] . "addons/yun_shop/api.php?i={$_W['uniacid']}&route=plugin.we7_wmall.admin.orders.postOrders";
	$res = ihttp_post($url, $post_data);
	if(is_error($res)) {
		return error(-1, $res['message']);
	}
	$data_obj = @json_encode($res['content'],true);
	if($data_obj != 1) {
		return error(-1, '同步到芸众商城失败');
	}
	return true;
}

function yunzhong_end_order($order) {
	global $_W;
	$post_data = array(
		'status' => 3,
		'order_sn' => $order['ordersn'],
	);
	$url = $_W['siteroot'] . "addons/yun_shop/api.php?i={$_W['uniacid']}&route=plugin.we7_wmall.admin.orders.completeOrder";
	$res = ihttp_post($url, $post_data);
	if(is_error($res)) {
		return error(-1, $res['message']);
	}
	$data_obj = @json_encode($res['content'],true);
	if($data_obj != 1) {
		return error(-1, '订单完成同步芸众商城失败');
	}
	return true;
}

function yunzhong_get_order_status() {
	$status = array(
		''
	);
}
