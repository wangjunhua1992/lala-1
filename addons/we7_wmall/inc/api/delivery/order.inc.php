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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];
if(empty($deliveryer['is_takeout'])) {
	message(ierror(-1, '您没有平台外卖单的配送权限，请联系管理员授权'), '', 'ajax');
}

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 3;
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'load';
	$id = intval($_GPC['id']);
	if($type == 'load') {
		if($id > 0) {
			$condition .= " and id < :id";
			$params[':id'] = $id;
		}
	} else {
		$condition .= " and id > :id";
		$params[':id'] = $id;
	}

	$can_collect_order = 1;
	if($config_takeout['order']['dispatch_mode'] != 1 && !$config_takeout['order']['can_collect_order']) {
		$can_collect_order = 0;
	}
	if($status == 3) {
		$condition .= " and delivery_status = :status and delivery_type = 2 and {$can_collect_order}";
		$params[':status'] = $status;
		if($config_takeout['order']['deliverynoassign_sort_type'] == 'desc'){
			$condition .= ' order by id desc';
		} else {
			$condition .= ' order by id asc';
		}
		if($config_takeout['order']['max_dispatching'] > 0){
			$condition .= " limit {$config_takeout['order']['max_dispatching']}";
		}
	} else {
		if($status == 7) {
			$condition .= ' and (delivery_status = 7 or delivery_status = 8)';
		} else {
			$condition .= '  and delivery_status = :status';
			$params[':status'] = $status;
		}
		$condition .= ' and deliveryer_id = :deliveryer_id and delivery_type = 2';
		$params[':deliveryer_id'] = $deliveryer['id'];
		$condition .= ' order by id desc limit 15';
	}
	$min_id = intval(pdo_fetchcolumn('SELECT min(id) as min_id FROM ' . tablename('tiny_wmall_order') . $condition , $params));
	$orders = pdo_fetchall('SELECT id,serial_sn, ordersn,order_type,order_plateform,distance,addtime, status, username, mobile, address, location_x, location_y, delivery_status, delivery_type, delivery_fee,plateform_deliveryer_fee,delivery_time,sid, num, final_fee, note FROM ' . tablename('tiny_wmall_order') . $condition, $params, 'id');
	$min = $max = 0;
	if(!empty($orders)) {
		$stores_id = array();
		foreach($orders as &$da) {
			$stores_id[] = $da['sid'];
		}
		$stores_str = implode(',', array_unique($stores_id));
		$stores = pdo_fetchall('select id, title, address, location_x, location_y, telephone from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');

		foreach($orders as &$da) {
			//当订单状态为待抢状态时,计算配送费
			if($da['status'] == 3){
				$da['plateform_deliveryer_fee'] = order_calculate_deliveryer_fee($da, $deliveryer);
				if(!$config_takeout['order']['show_acceptaddress_when_firstdelivery'] && !$deliveryer['order_takeout_num']) {
					$da['address'] = '接单后可见收货地址';
				}
			}
			$da['deliveryer_fee'] = $da['plateform_deliveryer_fee'];
			$da['addtime_cn'] = date('m-d H:i', $da['addtime']);
			$da['store'] = array(
				'title' => $stores[$da['sid']]['title'],
				'telephone' => $stores[$da['sid']]['telephone'],
				'address' => $stores[$da['sid']]['address'],
				'location_x' => $stores[$da['sid']]['location_x'],
				'location_y' => $stores[$da['sid']]['location_y']
			);
			$da['store2user_distance'] = $da['store2deliveryer_distance'] = '未知';
			if(!empty($da['location_x']) && !empty($da['location_y'])) {
				if(!empty($da['store']['location_x']) && !empty($da['store']['location_y'])) {
					$da['store2user_distance'] = distanceBetween($da['location_y'], $da['location_x'], $da['store']['location_y'], $da['store']['location_x']);
					$da['store2user_distance'] = round($da['store2user_distance'] / 1000, 2);
				}
				if(!empty($deliveryer['location_x']) && !empty($deliveryer['location_y'])) {
					$da['store2deliveryer_distance'] = distanceBetween($da['store']['location_y'], $da['store']['location_x'], $deliveryer['location_y'], $deliveryer['location_x']);
					$da['store2deliveryer_distance'] = round($da['store2deliveryer_distance'] / 1000, 2);
				}
			}
			$stores_id[] = $da['sid'];
		}
		$more = 1;
		$min = min(array_keys($orders));
		$max = max(array_keys($orders));
		if($min <= $min_id) {
			$more = 0;
		}
	}
	$orders = array_values($orders);
	$data = array(
		'list' => $orders,
		'max_id' => $max,
		'min_id' => $min,
		'more' => $more
	);
	$delivery_status = order_delivery_status();
	$respon = array('resultCode' => 0, 'resultMessage' => '调用成功', 'data' => $data);
	message($respon, '', 'ajax');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$order['deliveryer_fee'] = $order['plateform_deliveryer_fee'];
	$order['deliveryer_transfer_status'] = 0;
	if($deliveryer['perm_transfer']['status_takeout'] == 1 && in_array($order['delivery_status'],array(4,7,8))) {
		$order['deliveryer_transfer_status'] = "1";
	}
	$order['deliveryer_transfer_reason'] = $config_takeout['order']['deliveryer_transfer_reason'];
	$order['addtime_cn'] = date('Y-m-d H:i', $order['addtime']);
	$order['paytime_cn'] = date('Y-m-d H:i', $order['paytime']);
	$order['deliveryingtime_cn'] = date('Y-m-d H:i', $order['delivery_assign_time']);
	$order['deliveryinstoretime_cn'] = date('Y-m-d H:i', $order['delivery_instore_time']);
	$order['deliverysuccesstime_cn'] = date('Y-m-d H:i', $order['delivery_success_time']);
	$order['delivery_assign_time_cn'] = array(
		'day' => $order['delivery_assign_time'] > 0 ? date('m-d', $order['delivery_assign_time']) : '未知',
		'time' => $order['delivery_assign_time'] > 0 ? date('H:i', $order['delivery_assign_time']) : '未知',
	);
	$order['delivery_instore_time_cn'] = array(
		'day' => $order['delivery_instore_time'] > 0 ? date('m-d', $order['delivery_instore_time']) : '未知',
		'time' => $order['delivery_instore_time'] > 0 ? date('H:i', $order['delivery_instore_time']) : '未知',
	);
	$order['delivery_success_time_cn'] = array(
		'day' => $order['delivery_success_time'] > 0 ? date('m-d', $order['delivery_success_time']) : '未知',
		'time' => $order['delivery_success_time'] > 0 ? date('H:i', $order['delivery_success_time']) : '未知',
	);
	$order['delivery_takegoods_time_cn'] = array(
		'day' => $order['delivery_takegoods_time'] > 0 ? date('m-d', $order['delivery_takegoods_time']) : '未知',
		'time' => $order['delivery_takegoods_time'] > 0 ? date('H:i', $order['delivery_takegoods_time']) : '未知',
	);
	$store = store_fetch($order['sid'], array('id', 'title', 'address', 'telephone', 'logo', 'location_x', 'location_y'));
	$order['store'] = array(
		'title' => $store['title'],
		'address' => $store['address'],
		'telephone' => $store['telephone'],
		'location_x' => $store['location_x'],
		'location_y' => $store['location_y'],
	);

	$deliveryer = deliveryer_fetch($deliveryer['id']);
	$order['deliveryer'] = array(
		'title' => $deliveryer['title'],
		'mobile' => $deliveryer['mobile'],
		'age' => $deliveryer['age'],
		'sex' => $deliveryer['sex'],
		'location_x' => $deliveryer['location_x'],
		'location_y' => $deliveryer['location_y'],
	);

	$order['store2user_distance'] = $order['store2deliveryer_distance'] = '未知';
	if(!empty($order['location_x']) && !empty($order['location_y'])) {
		if(!empty($order['store']['location_x']) && !empty($order['store']['location_y'])) {
			$order['store2user_distance'] = distanceBetween($order['location_y'], $order['location_x'], $order['store']['location_y'], $order['store']['location_x']);
			$order['store2user_distance'] = strval(round($order['store2user_distance'] / 1000, 2));
		}
		if(!empty($order['deliveryer']['location_x']) && !empty($order['deliveryer']['location_y'])) {
			$order['store2deliveryer_distance'] = distanceBetween($order['store']['location_y'], $order['store']['location_x'], $order['deliveryer']['location_y'], $order['deliveryer']['location_x']);
			$order['store2deliveryer_distance'] = strval(round($order['store2deliveryer_distance'] / 1000, 2));
		}
	}

	$goods = order_fetch_goods($order['id']);
	$order['goods'] = $goods;

	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$order['activityed'] = $activityed;

	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	message(ierror(0, '', $order), '', 'ajax');
}

//抢单
if($op == 'collect') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_assign', array('deliveryer_id' => $deliveryer['id'], 'delivery_handle_type' => 'app'));
	if(is_error($result)) {
		message(ierror(-1, $result['message']), '', 'ajax');
	}
	message(ierror(0, '抢单成功'), '', 'ajax');
}

//APP接口里写的是instore，实际是确认取货(这个没有客户微信通知)
if($op == 'instore') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_takegoods', array('deliveryer_id' => $deliveryer['id'], 'delivery_handle_type' => 'app'));
	if(is_error($result)) {
		message(ierror(-1, $result['message']), '', 'ajax');
	}
	message(ierror(0, '确认到店成功'), '', 'ajax');
}

if($op == 'success') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_success', array('deliveryer_id' => $deliveryer['id'], 'delivery_success_location_x' => $deliveryer['location_x'], 'delivery_success_location_y' => $deliveryer['location_y']));
	if(is_error($result)) {
		message(ierror(-1, $result['message']), '', 'ajax');
	}
	message(ierror(0, '确认送达成功'), '', 'ajax');
}

if($op == 'transfer_reason') {
	if(empty($config_takeout['order']['deliveryer_transfer_reason'])) {
		$config_takeout['order']['deliveryer_transfer_reason'] = array(
			'其它'
		);
	}
	message(ierror(0, $config_takeout['order']['deliveryer_transfer_reason']), '', 'ajax');
}

if($op == 'transfer') {
	$id = intval($_GPC['id']);
	$reason = urldecode($_GPC['reason']);
	$result = order_deliveryer_update_status($id, 'delivery_transfer', array('deliveryer_id' => $deliveryer['id'], 'reason' => $reason));
	if(is_error($result)) {
		message(ierror(-1, $result['message']), '', 'ajax');
	}
	message(ierror(0, '转单成功'), '', 'ajax');
}



