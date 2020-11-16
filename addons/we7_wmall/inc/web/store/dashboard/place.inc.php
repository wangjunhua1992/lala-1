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
$_W['page']['title'] = '后台下单-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
$store = store_check();
$sid = $store['id'];
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$store = store_fetch($sid, array('delivery_fee_mode', 'delivery_price', 'delivery_free_price', 'pack_price'));
	$categorys = store_fetchall_goods_category($sid);
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	foreach($goods as &$good) {
		$good['totalNum'] = 0;
		if($good['is_options']) {
			$good['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
		}
		$good['thumb'] = tomedia($good['thumb']);
	}
}

if($ta == 'post') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$post = $_GPC['__input'];
	$goods = array();
	foreach($post['cart'] as $good) {
		$goods[] = $good['id'];
	}
	if(!empty($goods)) {
		$goods = implode(',', array_values($goods));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ({$goods})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	}
	$cart = array();
	$goods = array();
	foreach($post['cart'] as $data) {
		$v = $data['id'];
		if(!$goods_info[$v]['is_options']) {
			$goods[$v][0] = array(
				'title' => $goods_info[$v]['title'],
				'num' => $data['num'],
				'discount_num' => 0,
				'bargain_id' => 0,
				'price' => $goods_info[$v]['price'],
				'total_price' => $goods_info[$v]['price'] * $data['num'],
				'total_discount_price' => $goods_info[$v]['price'] * $data['num'],
				'caigou_total_discount_price' => $goods_info[$v]['caigou_price'] * $data['num'],
			);
			$num_data[$v] = $data['num'];
			$num += $data['num'];
			$price += $goods_info[$v]['price'] * $data['num'];
			$caigou_price += $goods_info[$v]['caigou_price'] * $data['num'];
		} else {
			foreach($data['good']['options'] as $val) {
				$option_id = intval($val['id']);
				$option_num = intval($val['num']);
				if($option_id > 0 && $option_num > 0) {
					$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
					if(empty($option) || !empty($goods[$v][$option_id])) {
						continue;
					}
					$goods[$v][$option_id] = array(
						'title' => $goods_info[$v]['title'] . "({$option['name']})",
						'num' => $option_num,
						'discount_num' => 0,
						'bargain_id' => 0,
						'price' => $option['price'],
						'total_price' => $option['price'] * $option_num,
						'total_discount_price' => $option['price'] * $option_num,
						'caigou_total_discount_price' => $option['caigou_price'] * $option_num,
					);
					$num += $option_num;
					$price += $option['price'] * $option_num;
					$caigou_price += $option['caigou_price'] * $option_num;
				}
			}
		}
	}

	$cart = array(
		'price' => $price,
		'caigou_price' => $caigou_price,
		'num' => $num,
		'data' => $goods,
	);

	$store = store_fetch($sid, array('delivery_price', 'delivery_free_price', 'pack_price'));
	//配送费
	$delivery_price = $store['delivery_price'];
	if($store['delivery_free_price'] > 0 && $price >= $store['delivery_free_price']) {
		$delivery_price = 0;
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => 0,
		'ordersn' => date('Ymd') . random(6, true),
		'code' => random(4, true),
		'groupid' => 0,
		'order_type' => 1,
		'openid' => '',
		'mobile' => $post['user']['mobile'],
		'username' => $post['user']['username'],
		'sex' => '',
		'address' => $post['user']['address'],
		'location_x' => '',
		'location_y' => '',
		'delivery_day' => date('Y-m-d'),
		'delivery_time' => '尽快送出',
		'delivery_fee' => $delivery_price,
		'pack_fee' => $store['pack_price'],
		'pay_type' => 'delivery',
		'num' => $cart['num'],
		'price' => $cart['price'],
		'caigou_price' => $cart['caigou_price'],
		'total_fee' => $cart['price'] + $delivery_price + $store['pack_price'],
		'discount_fee' => 0,
		'store_discount_fee' => 0,
		'plateform_discount_fee' => 0,
		'final_fee' => $cart['price'] + $delivery_price + $store['pack_price'] - 0,
		'status' => 2,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => iserializer(array()),
		'note' => $post['user']['note'],
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	pdo_insert('tiny_wmall_order', $order);
	$id = pdo_insertid();
	order_update_bill($id);
	order_insert_status_log($id, 'place_order');
	order_update_goods_info($id, $sid, $cart);
	order_print($id);
	imessage(error(0, $id), '', 'ajax');
}

$GLOBALS['frames'] = array();
include itemplate('store/dashboard/place');