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


function errander_types() {
	$data = array(
		'buy' => array(
			'css' => 'label label-success',
			'text' => '随意购',
			'bg' => 'bg-danger'
		),
		'delivery' => array(
			'css' => 'label label-warning',
			'text' => '快速送',
			'bg' => 'bg-success'
		),
		'pickup' => array(
			'css' => 'label label-danger',
			'text' => '快速取',
			'bg' => 'bg-primary'
		),
		'multiaddress' => array(
			'css' => 'label label-primary',
			'text' => '多地址购物',
			'bg' => 'bg-primary'
		),
	);
	return $data;
}

function errander_delivery_times($idOrCategory) {
	global $_W;
	if(is_array($idOrCategory)) {
		$category = $idOrCategory;
	} else {
		$category = errander_category_fetch($idOrCategory);
	}
	$days = array();
	$totaytime = strtotime(date('Y-m-d'));
	if($category['delivery_within_days'] > 0) {
		for($i = 0; $i <= $category['delivery_within_days']; $i++) {
			$days[] = date('m-d', $totaytime + $i * 86400);
		}
	} else {
		$days[] = date('m-d');
	}

	$times = $category['delivery_times'];
	$timestamp = array();
	if(!empty($times)) {
		foreach($times as $key => &$row) {
			if(empty($row['status'])) {
				unset($times[$key]);
				continue;
			}
			$row['delivery_price'] = $category['start_fee'] + $row['fee'];
			$row['delivery_price_cn'] = "配送费{$row['delivery_price']}{$_W['Lang']['dollarSignCn']}起";
			$end = explode(':', $row['end']);
			$row['timestamp'] = mktime($end[0], $end[1]);
			$timestamp[$key] = $row['timestamp'];
		}
	} else {
		$start = mktime(8, 0);
		$end = mktime(22, 0);
		for($i = $start; $i < $end;) {
			$category['delivery_price_cn'] = "配送费{$category['start_fee']}{$_W['Lang']['dollarSignCn']}起";
			$times[] = array(
				'start' => date('H:i', $i),
				'end' => date('H:i', $i + 1800),
				'timestamp' => $i + 1800,
				'fee' => 0,
				'delivery_price' => $category['start_fee'],
				'delivery_price_cn' => $category['delivery_price_cn'],
			);
			$timestamp[] = $i + 1800;
			$i += 1800;
		}
	}
	$data = array(
		'days' => $days,
		'times' => $times,
		'timestamp' => $timestamp,
		'updatetime' => strtotime(date('Y-m-d')) + 86400,
	);
	return $data;
}

/*
 * $extra = array('start_address', 'end_address', 'goods_weight', 'predict_index', 'delivery_tips')
 * */
function errander_order_delivery_fee($idOrCategory, $extra) {
	global $_W;
	if(is_array($idOrCategory)) {
		$category = $idOrCategory;
	} else {
		$category = errander_category_fetch($idOrCategory);
	}
	if(empty($category)) {
		return error(-1, "跑腿类型不存在");
	}
	if(empty($category['status'])) {
		return error(-1, "该跑腿类型已关闭");
	}
	$tip = floatval($extra['delivery_tips']);
	$start_address = $extra['start_address'];
	$end_address = $extra['end_address'];
	$goods_weight = floatval($extra['goods_weight']);
	$predict_index = intval($extra['predict_index']);
	$start_address_num = intval($extra['start_address_num']);

	if($tip < $category['tip_min'] || ($category['tip_max'] > 0 && $tip > $category['tip_max'])) {
		$tip = $category['tip_min'];
	}
	$delivery_time = errander_delivery_times($category);
	$delivery_fee_predict_time = $delivery_time['times'][$predict_index]['fee'];

	$fees = array();
	if($category['type'] == 'multiaddress') {
		if(!$start_address_num) {
			return error(-1, '购买地址不能为空');
		}
		$multiaddress = $category['multiaddress'];
		if($start_address_num > $multiaddress['max']) {
			return error(-1, "购买地址最多只能设置{$multiaddress['max']}个");
		}
		$delivery_fee = $delivery_fee_predict_time;
		$message = array();
		for($i = 0; $i < $start_address_num; $i++) {
			$delivery_fee += $multiaddress['fee'][$i];
			$num = $i + 1;
			$message[] = "第{$num}个购买地址收取{$multiaddress['fee'][$i]}{$_W['Lang']['dollarSignCn']}配送费";
		}
		if($delivery_fee_predict_time > 0) {
			$message[] = "特殊时间额外配送费{$delivery_time['times'][$predict_index]['fee']}{$_W['Lang']['dollarSignCn']}";
		}
		$message = implode("<br>", $message);
	} else {
		$delivery_fee = $category['start_fee'] + $delivery_fee_predict_time;
		$fees['basic'] = array(
			'title' => '起步价',
			'note' => "含{$category['start_km']}公里",
			'fee' => "{$category['start_fee']}",
			'fee_cn' => "{$_W['Lang']['dollarSign']}{$category['start_fee']}",
		);

		$distance = 0;
		if(!empty($start_address['location_y']) && !empty($start_address['location_x']) && !empty($end_address['location_y']) && !empty($end_address['location_x'])) {
			$origins = array($start_address['location_y'], $start_address['location_x']);
			$destination = array($end_address['location_y'], $end_address['location_x']);
			$distance_calculate_type = $category['distance_calculate_type'];
			$distance = calculate_distance($origins, $destination, $distance_calculate_type);
			if(is_error($distance)) {
				return error(-1, $distance['message']);
			}
		}
		if(($distance > $category['start_km']) && ($category['pre_km'] > 0)) {
			$distance_over = round($distance - $category['start_km'], 2);
			$distance_over_fee = round($category['pre_km_fee'] * ceil($distance_over / $category['pre_km']), 2);
			$delivery_fee += $distance_over_fee;

			$fees[] = array(
				'title' => '里程费',
				'note' => "{$distance_over}公里",
				'fee' => "{$distance_over_fee}",
				'fee_cn' => "{$_W['Lang']['dollarSign']}{$distance_over_fee}",
			);
		}
		$message = "{$category['start_km']}千米内";
		if($category['weight_fee_status'] == 1) {
			$message .= "，{$category['weight_fee']['start_weight']}千克内";
		}
		$message .= "收取{$category['start_fee']}{$_W['Lang']['dollarSignCn']}<br>";
		if($category['pre_km'] > 0) {
			$message .= "{$category['start_km']}千米以上，每增加{$category['pre_km']}千米多收取{$category['pre_km_fee']}{$_W['Lang']['dollarSignCn']}<br>";
		}
		if($category['weight_fee_status'] == 1) {
			$fees['basic']['note'] .= ",{$category['weight_fee']['start_weight']}公斤";
			if($goods_weight > $category['weight_fee']['start_weight']) {
				$weight_fee = array_compare($goods_weight, $category['weight_fee']['weight']);
				$index = array_search($weight_fee, $category['weight_fee']['weight']);
				$goods_weight_over = round($goods_weight - $category['weight_fee']['start_weight'], 2);
				$goods_weight_over_fee = round($goods_weight_over * $weight_fee, 2);
				$delivery_fee += $goods_weight_over_fee;
				$message .= "{$index}千克以上，每千克多收取{$weight_fee}{$_W['Lang']['dollarSignCn']}<br>";
				$fees[] = array(
					'title' => '重量费',
					'note' => "{$goods_weight_over}公斤",
					'fee' => "{$goods_weight_over_fee}",
					'fee_cn' => "{$_W['Lang']['dollarSign']}{$goods_weight_over_fee}",
				);
			}
		}
		if($delivery_fee_predict_time > 0) {
			$message .= "特殊时间额外配送费{$delivery_fee_predict_time}{$_W['Lang']['dollarSignCn']}";
			$fees[] = array(
				'title' => '特殊时间额外配送费',
				'note' => '',
				'fee' => "{$delivery_fee_predict_time}",
				'fee_cn' => "{$_W['Lang']['dollarSign']}{$delivery_fee_predict_time}",
			);
		}
	}
	if($tip > 0) {
		$fees[] = array(
			'title' => '小费',
			'note' => '',
			'fee' => "{$tip}",
			'fee_cn' => "{$_W['Lang']['dollarSign']}{$tip}",
		);
	}

	$activityed = array();
	$discount_fee = 0;
	if($_W['member']['groupid'] > 0) {
		$groupid = $_W['member']['groupid'];
		if($category['group_discount']['type'] == 1) {
			$group_discount = $category['group_discount']['data'][$groupid];
			if($delivery_fee >= $group_discount['condition']) {
				$discount_fee = $group_discount['back'];
			}
		} elseif($category['group_discount']['type'] == 2) {
			$group_discount = $category['group_discount']['data'][$groupid];
			if($delivery_fee >= $group_discount['condition']) {
				$discount_fee = round($delivery_fee * ((10 - $group_discount['back']) / 10), 2);
			}
		}
		if($discount_fee > 0) {
			$activityed[] = array(
				'title' => "{$_W['member']['groupname']}",
				'note' => ($category['group_discount']['type'] == 1 ? "返{$discount_fee}{$_W['Lang']['dollarSignCn']}" : "打{$group_discount['back']}折"),
				'fee' => "{$discount_fee}",
				'fee_cn' => "-{$_W['Lang']['dollarSign']}{$discount_fee}",
			);
		}
	}

	$total_fee = $delivery_fee + $tip;
	$data = array(
		'goods_weight' => $goods_weight,
		'delivery_fee' => $delivery_fee,
		'delivery_extra_fee' => $delivery_fee_predict_time,
		'tip' => $tip,
		'total_fee' => $total_fee,
		'discount_fee' => $discount_fee,
		'final_fee' => $total_fee - $discount_fee,
		'distance' => $distance,
		'activityed' => $activityed,
		'fees' => array_values($fees),
		'message' => $message,
	);
	return $data;
}

function get_errander_rule_fee($diypageorId) {
	global $_W;
	$diypage = $diypageorId;
	if(!is_array($diypage)) {
		$diypage = get_errander_diypage($diypage);
		$diypage = $diypage['diypage'];
	}
	$fees = array();
	$rule_fee = $diypage['data']['fees'];
	$rule_fee_type = $rule_fee['fee_type'];
	$rule_fee_distance = $rule_fee['fee_data'];
	$distance_fee = $rule_fee_distance['fee'];
	$fees['basic'] = array(
		'title' => '基础配送费',
		'name' => 'basic',
		'items' => array(
			array(
				'note' => '每单',
				'fee' => $distance_fee,
				'fee_cn' => "{$distance_fee}{$_W['Lang']['dollarSignCn']}",
			),
		),
	);

	if($rule_fee_type != 'fee') {
		$rule_fee_wrap = $rule_fee_distance[$rule_fee_type];
		if(!empty($rule_fee_wrap['data'])) {
			foreach($rule_fee_wrap['data'] as $row) {
				if(TIMESTAMP > strtotime($row['start_hour']) && TIMESTAMP < strtotime($row['end_hour'])) {
					$rule_fee_item = $row;
					break;
				}
			}
		}
		if($rule_fee_type == 'distance') {
			$fees['basic'] = array(
				'title' => '基础配送费',
				'name' => 'basic',
				'items' => array(
					array(
						'note' => "{$rule_fee_item['start_km']}公里内",
						'fee' => $rule_fee_item['start_fee'],
						'fee_cn' => "{$rule_fee_item['start_fee']}{$_W['Lang']['dollarSignCn']}",
					),
				),
			);
			if(empty($rule_fee_item['over_km']) || $rule_fee_item['over_km'] <= $rule_fee_item['start_km']) {
				$fees['distance'] = array(
					'title' => '距离附加费',
					'name' => 'distance',
					'items' => array(
						array(
							'note' => "超过{$rule_fee_item['start_km']}公里",
							'fee' => $rule_fee_item['pre_km_fee'],
							'fee_cn' => ($rule_fee_item['pre_km'] > 1) ? "每{$rule_fee_item['pre_km']}公里+{$rule_fee_item['pre_km_fee']}{$_W['Lang']['dollarSignCn']}" : "每公里+{$rule_fee_item['pre_km_fee']}{$_W['Lang']['dollarSignCn']}",
						),
					)
				);
			} else {
				$fees['distance'] = array(
					'title' => '距离附加费',
					'name' => 'distance',
					'items' => array(
						array(
							'note' => "{$rule_fee_item['start_km']}-{$rule_fee_item['over_km']}公里",
							'fee' => $rule_fee_item['pre_km_fee'],
							'fee_cn' => ($rule_fee_item['pre_km'] > 1) ? "每{$rule_fee_item['pre_km']}公里+{$rule_fee_item['pre_km_fee']}{$_W['Lang']['dollarSignCn']}" : "每公里+{$rule_fee_item['pre_km_fee']}{$_W['Lang']['dollarSignCn']}",
						),
						array(
							'note' => "超过{$rule_fee_item['over_km']}公里",
							'fee' => $rule_fee_item['pre_km_fee'],
							'fee_cn' => $rule_fee_item['over_pre_km'] > 1 ? "每{$rule_fee_item['over_pre_km']}公里+{$rule_fee_item['over_pre_km_fee']}{$_W['Lang']['dollarSignCn']}" : "每公里+{$rule_fee_item['over_pre_km_fee']}{$_W['Lang']['dollarSignCn']}",
						),
					),
				);
			}
		} elseif($rule_fee_type == 'section') {
			$rule_fee_item = $rule_fee_item['rules']['data'];
			$fees['distance']['title'] = '距离附加费';
			$fees['distance']['name'] = 'section';
			foreach($rule_fee_item as $row) {
				$fees['distance']['items'][] = array(
					'note' => "{$row['start_km']}-{$row['end_km']}公里",
					'fee' => $row['fee'],
					'fee_cn' => "{$row['fee']}{$_W['Lang']['dollarSignCn']}",
				);
			}
			$fees['basic'] = array(
				'title' => '基础配送费',
				'name' => 'basic',
				'items' => array(
					array(
						'note' => $fees['distance']['items'][0]['note'],
						'fee' => $fees['distance']['items'][0]['fee'],
						'fee_cn' => $fees['distance']['items'][0]['fee_cn'],
					)
				),
			);
			unset($fees['distance']['items'][0]);
		}
	}

	$rule_fee_weight = $rule_fee['weight_data'];
	if($rule_fee['weight_status'] == 1) {
		if(!empty($rule_fee_weight['data'])) {
			$fees['weight']['title'] = '重量附加费';
			$fees['weight']['name'] = 'weight';
			foreach($rule_fee_weight['data'] as $row) {
				$fees['weight']['items'][] = array(
					'note' => "超过{$row['over_kgs']}公斤",
					'fee' => $row['pre_kg_fees'],
					'fee_cn' => "每公斤+{$row['pre_kg_fees']}{$_W['Lang']['dollarSignCn']}",
				);
			}
		}
	}

	if($rule_fee['extra_fee_time_status'] == 1) {
		$fees['special_time']['title'] = '特殊时段附加费';
		$fees['special_time']['name'] = 'special_time';
		foreach($rule_fee['extra_fee_time_data']['data'] as $val) {
			$fees['special_time']['items'][] = array(
				'note' => "{$val['start_hour']}-{$val['end_hour']}",
				'fee' => $val['fee'],
				'fee_cn' => "+{$val['fee']}{$_W['Lang']['dollarSignCn']}",
			);
		}
	}
	$fees['extra_fee']['title'] = '选择附加费';
	$fees['extra_fee']['name'] = 'extra_fee';
	foreach($rule_fee['extra_fee'] as $item) {
		if($item['status'] == 1) {
			foreach($item['data'] as $val) {
				$fees['extra_fee']['items'][] = array(
					'note' => "{$item['title']}-{$val['fee_name']}",
					'fee' => $val['fee'],
					'fee_cn' => "+{$val['fee']}{$_W['Lang']['dollarSignCn']}",
				);
			}
		}
	}
	if(empty($fees['extra_fee']['items'])) {
		unset($fees['extra_fee']);
	}
	return $fees;
}

function errander_order_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '所有',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-default',
			'text' => '待接单',
			'color' => '',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '正在进行中',
			'color' => 'color-info'
		),
		'3' => array(
			'css' => 'label label-success',
			'text' => '已完成',
			'color' => 'color-success'
		),
		'4' => array(
			'css' => 'label label-danger',
			'text' => '已取消',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function errander_order_delivery_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-default',
			'text' => '待接单',
			'color' => '',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '待取货',
			'color' => 'color-info'
		),
		'3' => array(
			'css' => 'label label-warning',
			'text' => '配送中',
			'color' => 'color-warning'
		),
		'4' => array(
			'css' => 'label label-success',
			'text' => '已完成',
			'color' => 'color-success'
		)
	);
	return $data;
}

function errander_order_insert_status_log($id, $type, $note = '', $extra = array()) {
	global $_W;
	if(empty($type)) {
		return false;
	}
	$config = $_W['_plugin']['config'];
	mload()->model('store');
	$order = errander_order_fetch($id);
	$notes = array(
		'place_order' => array(
			'status' => 1,
			'title' => '订单提交成功',
			'note' => "单号:{$order['order_sn']}",
			'ext' => array(
				array(
					'key' => 'pay_time_limit',
					'title' => '待支付',
					'note' => "请在订单提交后{$config['pay_time_limit']}分钟内完成支付",
				)
			)
		),
		'pay' => array(
			'status' => 2,
			'title' => '订单已支付',
			'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s', $order['paytime']),
			'ext' => array(
				array(
					'key' => 'handle_time_limit',
					'title' => '待接单',
					'note' => "超出{$config['handle_time_limit']}分钟未接单，平台将自动取消订单",
				)
			)
		),
		'delivery_assign' => array(
			'status' => 3,
			'title' => '已接单',
			'note' => ''
		),
		'delivery_instore' => array(
			'status' => 4,
			'title' => '已取货',
			'note' => '',
		),
		'end' => array(
			'status' => 5,
			'title' => '订单已完成',
			'note' => '任何意见和吐槽,都欢迎联系我们'
		),
		'cancel' => array(
			'status' => 6,
			'title' => '订单已取消',
			'note' => ''
		),
		'delivery_transfer' => array(
			'status' => 7,
			'title' => '配送员申请转单',
			'note' => ''
		),
		'direct_transfer' => array(
			'status' => 8,
			'title' => '配送员发起定向转单申请',
			'note' => ''
		),
		'direct_transfer_agree' => array(
			'status' => 9,
			'title' => '配送员同意接受转单',
			'note' => ''
		),
		'direct_transfer_refuse' => array(
			'status' => 10,
			'title' => '配送员拒绝接受转单',
			'note' => ''
		),
		're_notify_deliveryer_collect' => array(
			'status' => 1,
			'title' => '重新待抢',
			'note' => "订单已被重新设置为待接单，超出{$config['handle_time_limit']}分钟未接单，平台将自动取消订单"
		),
	);
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$role = !empty($extra['role']) ? $extra['role'] : $_W['role'];
	$role_cn = !empty($extra['role_cn']) ? $extra['role_cn'] : $_W['role_cn'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'status' => $notes[$type]['status'],
		'role' => $role,
		'role_cn' => $role_cn,
		'type' => $type,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_errander_order_status_log', $data);
	if(!empty($notes[$type]['ext'])) {
		foreach($notes[$type]['ext'] as $val) {
			if($val['key'] == 'pay_time_limit' && !$config['pay_time_limit']) {
				unset($val['note']);
			}
			if($val['key'] == 'handle_time_limit' && !$config['handle_time_limit']) {
				unset($val['note']);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $id,
				'title' => $val['title'],
				'note' => $val['note'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_errander_order_status_log', $data);
		}
	}
	return true;
}