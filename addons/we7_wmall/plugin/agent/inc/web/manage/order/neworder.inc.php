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
mload()->model('goods');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$_input = $_GPC['__input'];
mload()->model('agent');
$_W['member']['uid'] = 111111111;

if($op == 'index'){
	$_W['page']['title'] = '代客下单';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid']), array('id', 'title'), 'id');
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$store = store_fetch($sid);
		if(empty($store))  {
			imessage('门店不存在或已删除', '', 'info');
		}
		$store['extra_fee'] = 0;
		if($_W['ispost']) {
			$result = array(
				'store' => $store,
				'category' => array_values(store_fetchall_goods_category($sid, 1, true)),
				'cart' => cart_data_init($sid),
			);
			$cid = $result['category'][0]['id'];
			$result['goods'] = goods_filter($sid, array('cid' => $cid));
			$result['cid'] = $cid;
			imessage(error(0, $result), '', 'ajax');
		}
	}
	include itemplate('order/neworder');
}
elseif($op == 'goods') {
	$sid = intval($_GPC['sid']);
	$goods = goods_filter($sid, array('cid' => $_input['cid'], 'psize' => $_input['psize']));
	$result = array(
		'goods' => $goods
	);
	imessage(error(0, $result), '', 'ajax');
}
elseif($op == 'truncate') {
	$sid = intval($_GPC['sid']);
	$data = pdo_delete('tiny_wmall_order_cart', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	imessage(error(0, ''), '', 'ajax');
}
elseif($op == 'cart') {
	$sid = intval($_input['sid']);
	$goods_id = intval($_input['goods_id']);
	$option_id = trim($_input['option_id']);
	$sign = trim($_input['sign']);
	$cart = cart_data_init($sid, $goods_id, $option_id, $sign);
	imessage($cart, '', 'ajax');
}
elseif($op == 'create') {
	if(!check_plugin_exist('wxapp')) {
		imessage(error(-1, '注意：代客下单目前仅对购买过"小程序"插件的客户开放。'), '', 'ajax');
	}
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid);
	if(empty($store)) {
		imessage(error(-1, '店铺不存在或已删除'), '', 'ajax');
	}
	$cart = order_check_member_cart($sid);
	if(is_error($cart)) {
		imessage($cart, '', 'ajax');
	}
	$condition = array(
		'order_type' => 1,
	);
	$extra = $_input['extra'];
	if(empty($extra)) {
		imessage(error(-1, '参数错误'), '', 'ajax');
	}
	//平台附加费
	$extra_fee_note = array();
	$extra_fee = 0;
	if(!empty($store['data']['extra_fee'])) {
		foreach($store['data']['extra_fee'] as $item) {
			$item_fee = floatval($item['fee']);
			if($item['status'] == 1 && $item_fee > 0) {
				$extra_fee += $item_fee;
				$extra_fee_note[] = $item;
			}
		}
	}
	$total_fee = $cart['price'] + $cart['box_price'] + $store['pack_price'] + $extra['delivery_fee'] + $extra_fee;
	if($extra['order_type'] == 1) {
		$city = $_W['we7_wmall']['config']['takeout']['range']['city'];
		$location = geocode_geo($extra['address'], $city);
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $store['agentid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'mall_first_order' => $_W['member']['is_mall_newmember'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
		'code' => random(4, true),
		'order_type' => $extra['order_type'],
		'openid' => $_W['openid'],
		'mobile' => $extra['order_type'] ==1 ? $extra['mobile'] : '',
		'username' => $extra['order_type'] ==1 ? $extra['username'] : '',
		'sex' => '',
		'address' => $extra['order_type'] ==1 ? $extra['address'] : '',
		'location_x' => $location['location'][1],
		'location_y' => $location['location'][0],
		'delivery_day' => date('Y-m-d'),
		'delivery_time' => '尽快送达',
		'delivery_fee' => $extra['order_type'] ==1 ? $extra['delivery_fee'] : 0.00,
		'pack_fee' => $store['pack_price'],
		'pay_type' => 'delivery',
		'is_pay' => 1,
		'num' => $cart['num'],
		'distance' => $address['distance'],
		'box_price' => $cart['box_price'],
		'price' => $cart['price'],
		'caigou_price' => $cart['caigou_price'],
		'extra_fee' => $extra_fee,
		'total_fee' => $total_fee,
		'discount_fee' => 0,
		'store_discount_fee' => 0,
		'plateform_discount_fee' => 0,
		'agent_discount_fee' => 0,
		'final_fee' => $total_fee,
		'vip_free_delivery_fee' => 0,
		'delivery_type' => $store['delivery_mode'],
		'status' => 1,
		'is_comment' => 0,
		'invoice' => '',
		'order_channel' => 'plateformCreate',
		'addtime' => TIMESTAMP,
		'paytime' => TIMESTAMP,
		'data' => iserializer(array(
			'extra_fee' => $extra_fee_note,
			'cart' => iunserializer($cart['original_data']),
			'commission' => array(
				'spread1_rate' => "0%",
				'spread1' => 0,
				'spread2_rate' => "0%",
				'spread2' => 0,
			)
		)),
		'note' => trim($extra['note']),
		'storebd_id' => intval($store['storebd_id'])
		'spreadbalance' => 1,
	);
	$order['data'] = ($order['data']);
	pdo_insert('tiny_wmall_order', $order);
	$order_id = pdo_insertid();
	order_update_bill($order_id, array('activity' => array()));
	order_insert_discount($order_id, $sid, array());
	order_insert_status_log($order_id, 'place_order');
	order_update_goods_info($order_id, $sid);
	order_del_member_cart($sid);
	order_print($order_id);
	order_clerk_notice($order_id, 'place_order');
	imessage(error(0, $order), '', 'ajax');
}
