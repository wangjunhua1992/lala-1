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

function store_orderbys() {
	return array(
		'distance' => array(
			'title' => '离我最近',
			'key' => 'distance',
			'val' => 'asc',
			'css' => 'icon-b distance',
			'icon' => 'location'
		),
		'sailed' => array(
			'title' => '销量最高',
			'key' => 'sailed',
			'val' => 'desc',
			'css' => 'icon-b sailed-num',
			'icon' => 'hot_light'
		),
		'score' => array(
			'title' => '评分最高',
			'key' => 'score',
			'val' => 'desc',
			'css' => 'icon-b score',
			'icon' => 'favor1'
		),
		'send_price' => array(
			'title' => '起送价最低',
			'key' => 'send_price',
			'val' => 'asc',
			'css' => 'icon-b send-price',
			'icon' => 'moneybag'
		),
		'delivery_time' => array(
			'title' => '送单速度最快',
			'key' => 'delivery_time',
			'val' => 'asc',
			'css' => 'icon-b delivery-time',
			'icon' => 'waimai'
		),
	);
}

function store_discounts() {
	$data = array(
		'mallNewMember' => array(
			'title' => '首单立减',
			'key' => 'mallNewMember',
			'val' => 1,
			'css' => 'icon-b mallNewMember',
			'label' => 'label-danger'
		),
		'newMember' => array(
			'title' => '新用户立减',
			'key' => 'newMember',
			'val' => 1,
			'css' => 'icon-b newMember',
			'label' => 'label-danger'
		),
		'discount' => array(
			'title' => '立减优惠',
			'key' => 'discount',
			'val' => 1,
			'css' => 'icon-b discount',
			'label' => 'label-danger'
		),
		'cashGrant' => array(
			'title' => '下单返现',
			'key' => 'cashGrant',
			'val' => 1,
			'css' => 'icon-b cashGrant',
			'label' => 'label-success'
		),
		'grant' => array(
			'title' => '下单满赠',
			'key' => 'grant',
			'val' => 1,
			'css' => 'icon-b grant',
			'label' => 'label-success'
		),
		'deliveryFeeDiscount' => array(
			'title' => '满减配送费',
			'key' => 'deliveryFeeDiscount',
			'val' => 1,
			'css' => 'icon-b deliveryFeeDiscount',
			'label' => 'label-deliveryFeeDiscount'
		),
		'delivery_price' => array(
			'title' => '免配送费',
			'key' => 'delivery_price',
			'val' => 0,
			'css' => 'icon-b mian',
			'label' => 'label-warning'
		),
		'bargain' => array(
			'title' => '特价优惠',
			'key' => 'bargain',
			'val' => 1,
			'css' => 'icon-b bargain',
			'label' => 'label-primary'
		),
		'couponCollect' => array(
			'title' => '进店领券',
			'key' => 'couponCollect',
			'val' => 1,
			'css' => 'icon-b couponCollect',
			'label' => 'label-success'
		),
		'couponGrant' => array(
			'title' => '下单返券',
			'key' => 'couponGrant',
			'val' => 1,
			'css' => 'icon-b couponGrant',
			'label' => 'label-success'
		),
		'selfDelivery' => array(
			'title' => '自提优惠',
			'key' => 'selfDelivery',
			'val' => 1,
			'css' => 'icon-b selfDelivery',
			'label' => 'label-warning'
		),
		'invoice_status' => array(
			'title' => '支持开发票',
			'key' => 'invoice_status',
			'val' => 1,
			'css' => 'icon-b invoice',
		),
		'svipRedpacket' => array(
			'title' => '会员领红包',
			'key' => 'svipRedpacket',
			'val' => 1,
			'css' => 'icon-b label-danger',
		),
	);
	if(check_plugin_perm('zhunshibao')) {
		$data['zhunshibao'] = array(
			'title' => '准时宝',
			'key' => 'zhunshibao',
			'val' => 1,
			'css' => 'icon-b label-danger',
		);
	}
	return $data;
}

function store_all_activity() {
	return array(
		'mallNewMember' => array(
			'title' => '平台新用户立减',
			'key' => 'mallNewMember',
			'label' => 'label-danger'
		),
		'newMember' => array(
			'title' => '门店新用户立减',
			'key' => 'newMember',
			'label' => 'label-danger'
		),
		'discount' => array(
			'title' => '满减优惠',
			'key' => 'discount',
			'label' => 'label-danger'
		),
		'cashGrant' => array(
			'title' => '下单返现',
			'key' => 'cashGrant',
			'label' => 'label-success'
		),
		'grant' => array(
			'title' => '下单满赠',
			'key' => 'grant',
			'label' => 'label-success'
		),
		'bargain' => array(
			'title' => '特价优惠',
			'key' => 'bargain',
			'label' => 'label-primary'
		),
		'couponCollect' => array(
			'title' => '进店领券',
			'key' => 'couponCollect',
			'label' => 'label-success'
		),
		'couponGrant' => array(
			'title' => '下单返券',
			'key' => 'couponGrant',
			'label' => 'label-success'
		),
		'selfDelivery' => array(
			'title' => '自提打折',
			'key' => 'selfDelivery',
			'label' => 'label-warning'
		),
		'deliveryFeeDiscount' => array(
			'title' => '满减配送费',
			'key' => 'deliveryFeeDiscount',
			'label' => 'label-warning'
		),
		'selfPickup' => array(
			'title' => '自提满减优惠',
			'key' => 'selfPickup',
			'label' => 'label-success'
		),
		'svipRedpacket' => array(
			'title' => '超级会员红包',
			'key' => 'svipRedpacket',
			'label' => 'label-success'
		),
		'zhunshibao' => array(
			'title' => '准时宝',
			'key' => 'zhunshibao',
			'label' => 'label-danger'
		)
	);
}

//get_store
function store_fetch($id, $field = array()) {
	global $_W;
	if(empty($id)) {
		return false;
	}
	$field_str = '*';
	if(!empty($field)) {
		$field[] = 'status';
		if(in_array('cid', $field) && !in_array('cate_parentid1', $field)) {
			$field = array_merge($field, array('cate_parentid1', 'cate_childid1', 'cate_parentid2', 'cate_childid2'));
		}
		$field = array_unique($field);
		$field_str = implode(',', $field);
	}
	$data = pdo_fetch("SELECT {$field_str} FROM " . tablename('tiny_wmall_store') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if(empty($data)) {
		return error(-1, '门店不存在或已删除');
	}
	if($data['status'] == 4) {
		return error(-1, '门店已删除');
	}
	if(empty($data['delivery_mode'])) {
		$data['delivery_mode'] = 2;
	}
	$data['origin_logo'] = $data['logo'];
	$data['logo'] = tomedia($data['logo']);
	$data['delivery_title'] = ($data['delivery_mode'] == 2 && $data['delivery_type'] != 2) ? $_W['we7_wmall']['config']['mall']['delivery_title'] : '';
	$cid = array_filter(explode('|', $data['cid']));
	$data['category_arr'] = array_values($cid);
	$cid = implode(',', $cid);
	if(!empty($data['cid']) && !empty($cid)) {
		$category = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store_category') . " where uniacid = :uniacid and id in ({$cid})", array(':uniacid' => $_W['uniacid']));
		$data['category'] = array();
		if(!empty($category)) {
			$category_cn1 = $category_cn2 = '';
			foreach($category as $val) {
				if($val['id'] == $data['cate_parentid1']) {
					$category_cn1 .= $val['title'];
				}
				if($val['id'] == $data['cate_childid1']) {
					$category_cn1 .= "-{$val['title']}";
				}
				if($val['id'] == $data['cate_parentid2']) {
					$category_cn2 .= $val['title'];
				}
				if($val['id'] == $data['cate_childid2']) {
					$category_cn2 .=  "-{$val['title']}";
				}
				$data['category'][] = $val['title'];
			}
			$data['category'] = implode('、', $data['category']);
			$data['category_cn1'] = $category_cn1;
			$data['category_cn2'] = $category_cn2;
		}
	}
	$se_fileds = array('thumbs', 'delivery_areas', 'delivery_areas1', 'delivery_extra', 'sns', 'payment', 'business_hours', 'remind_reply', 'qualification', 'comment_reply', 'wechat_qrcode', 'custom_url', 'serve_fee', 'order_note', 'delivery_times', 'data', 'haodian_data');
	foreach($se_fileds as $se_filed) {
		if(isset($data[$se_filed])) {
			if(!in_array($se_filed, array('thumbs', 'delivery_areas', 'qualification'))) {
				$data[$se_filed] = iunserializer($data[$se_filed]);
			} else {
				$data[$se_filed] = iunserializer($data[$se_filed]);
				if($se_filed == 'thumbs') {
					foreach($data[$se_filed] as &$thumb) {
						$thumb['image'] = tomedia($thumb['image']);
					}
				} elseif($se_filed == 'qualification') {
					foreach($data[$se_filed] as &$thumb) {
						$thumb['thumb'] = tomedia($thumb['thumb']);
					}
				}
			}
		}
	}
	$data['address_type'] = 0;
	if(check_plugin_perm('area') && $_W['we7_wmall']['config']['mall']['address_type'] == 1) {
		$data['address_type'] = 1;
	}
	if($data['auto_handel_order'] == 2 && isset($data['auto_print_order'])) {
		$data['auto_print_order'] = 0;
	}
	if(!empty($data['delivery_areas1'])) {
		//检测商户设置的配送区域是否有效
		foreach($data['delivery_areas1'] as $key => $value) {
			mload()->model('plugin');
			pload()->model('area');
			$status = area_check_area_status($key);
			if(empty($status)) {
				unset($data['delivery_areas1'][$key]);
			}
		}
		$data['delivery_areas1_ids'] = array_keys($data['delivery_areas1']);
	}
	$data['is_in_business_hours'] = intval($data['is_in_business']);
	if(isset($data['business_hours'])) {
		if($data['is_in_business'] == 1) {
			if($data['rest_can_order'] == 1) {
				$data['is_in_business_hours'] = true;
			} else {
				$data['is_in_business_hours'] = store_is_in_business_hours($data['business_hours']);
			}
		}
		if($data['is_in_business_hours'] && !store_is_in_business_hours($data['business_hours'])) {
			$data['is_rest_reserve'] = 1;
			$rest_order_info = store_rest_start_delivery_time($data);
			if($rest_order_info['nextday'] == 1) {
				$data['rest_reserve_cn'] = "现在预订， 最早明天{$rest_order_info['delivery_time']}开始配送";
			} else {
				$data['rest_reserve_cn'] = "现在预订， 最早{$rest_order_info['delivery_time']}开始配送";
			}
		}
		$hour = array();
		foreach($data['business_hours'] as $li) {
			if(!is_array($li)) continue;
			$hour[] = "{$li['s']}~{$li['e']}";
		}
		$data['business_hours_cn'] = implode(',', $hour);
	}
	if(isset($data['score'])) {
		$data['score_cn'] = round($data['score'] / 5, 2) * 100;
		$data['score'] = floatval($data['score']);
	}
	if(isset($data['delivery_fee_mode'])) {
		if($data['delivery_fee_mode'] == 1) {
			$data['order_address_limit'] = 1; //不检测距离
			if(!$data['not_in_serve_radius'] && $data['serve_radius'] > 0) {
				$data['order_address_limit'] = 2; //检测门店到收货地址的距离,超过配送范围不让下单
			}
		} elseif($data['delivery_fee_mode'] == 2) {
			$data['delivery_price_extra'] = iunserializer($data['delivery_price']);
			$data['delivery_price'] = $data['delivery_price_extra']['start_fee'];
			if(!$data['not_in_serve_radius'] && $data['serve_radius'] > 0) {
				$data['order_address_limit'] = 2; //检测门店到收货地址的距离,超过配送范围不让下单
			} else {
				$data['order_address_limit'] = 3; //检测门店到收货地址的距离
			}
		} elseif($data['delivery_fee_mode'] == 3) {
			$data['order_address_limit'] = 4;
			$price = store_order_condition($data);
			$data['delivery_price'] = $price['delivery_price'];
			$data['send_price'] = $price['send_price'];
		}
	}
	if(isset($data['haodian_score'])) {
		$data['haodian_score'] = floatval($data['haodian_score']);
	}
	if(isset($data['haodian_cid']) && $data['haodian_cid'] > 0) {
		$data['haodian_cid_cn'] = pdo_fetchcolumn("select title from " . tablename('tiny_wmall_haodian_category') . " where uniacid = :uniacid and agentid = :agentid and id = :id", array('uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':id' => $data['haodian_cid']));
		$data['haodian_category_cn'] = $data['haodian_cid_cn'];
	}
	if(isset($data['haodian_child_id']) && $data['haodian_child_id'] > 0) {
		$data['haodian_child_id_cn'] = pdo_fetchcolumn("select title from " . tablename('tiny_wmall_haodian_category') . " where uniacid = :uniacid and agentid = :agentid and id = :id", array('uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':id' => $data['haodian_child_id']));
		$data['haodian_category_cn'] .= "-{$data['haodian_child_id_cn']}";
	}
	if(isset($data['data'])) {
		$data['data'] = iunserializer($data['data']);
		if(!empty($data['data']['shopSign'])) {
			$data['data']['shopSign'] = tomedia($data['data']['shopSign']);
		}
		$data['service_titles'] = array('takeout' => '点外卖', 'tangshi' => '扫码点餐', 'assign' => '排号','reserve' => '预定', 'paybill' => '当面付');
		if(!empty($data['data']['service_titles'])) {
			$data['service_titles'] = array_merge($data['service_titles'], $data['data']['service_titles']);
		}
		$data['pindan_status'] = 1;
		if(!empty($data['data']['pindan']) && isset($data['data']['pindan']['pindan_status'])) {
			$data['pindan_status'] = $data['data']['pindan']['pindan_status'];
		}
		if(empty($data['data']['cn'])) {
			$data['data']['cn'] = array(
				'box_price' => '餐盒费',
				'pack_fee' => '包装费'
			);
		}
		$data['cn'] = $data['data']['cn'];
		if(!empty($data['data']['zhunshibao']) && $data['data']['zhunshibao']['status'] == 1) {
			if($data['data']['zhunshibao']['fee_type'] == 1) {
				$rule_cn = '';
				if(!empty($data['data']['zhunshibao']['rule'])) {
					foreach($data['data']['zhunshibao']['rule'] as $val) {
						$rule_cn .= "延误{$val['time']}分钟,赔{$val['fee']}{$_W['Lang']['dollarSignCn']},";
					}
				}
				if(!empty($rule_cn)) {
					$rule_cn = rtrim($rule_cn, ',');
					$rule_cn = "骑手送达{$rule_cn}";
				}
				$data['data']['zhunshibao']['rule_cn'] = $rule_cn;
			}
			$data['zhunshibao_agreement'] = get_config_text("zhunshibao:agreement");
		}
		if(empty($data['data']['order_form'])) {
			$data['data']['order_form'] = array(
				'person_num' => '1'
			);
		}
	}
	if(isset($data['haodian_data'])) {
		$data['haodian_tags'] = array();
		if(!empty($data['haodian_data']['tags'])) {
			$data['haodian_tags'] = $data['haodian_data']['tags'];
		}
	}
	if(isset($data['menu'])) {
		$data['menu'] = json_decode(base64_decode($data['menu']), true);
		if(!empty($data['menu']['data'])) {
			foreach($data['menu']['data']['data'] as &$val) {
				if(empty($val['img'])) {
					continue;
				}
				$val['img'] = tomedia($val['img']);
			}
		}
	}
	if($data['kabao_status'] == 1) {
		if(!check_plugin_perm('kabao') || get_plugin_config('kabao.status') != 1) {
			$data['kabao_status'] = 0;
		}
	}
	return $data;
}

//store_fetch_activity
function store_fetch_activity($sid, $type = array()) {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);

	if(!empty($type)) {
		$type = implode("','", $type);
		$type = "'{$type}'";
		$condition .= " and type in ({$type})";
	}
	$condition .= ' order by displayorder desc';
	$data = pdo_fetchall("SELECT title,type,data FROM " . tablename('tiny_wmall_store_activity') . $condition, $params, 'type');
	$activity = array(
		'num' => 0,
		'items' => array(),
		'labels' => array()
	);
	if(!empty($data)) {
		$activity['num'] = count($data);
		$activity['items'] = $data;
		foreach($data as $da) {
			if($da['type'] == 'discount') {
				$discount = iunserializer($da['data']);
				foreach ($discount as $dis) {
					$activity['labels'][] = array(
						'title' => "{$dis['condition']}减{$dis['back']}",
						'class' => 'tag tag-danger'
					);
				}
			} elseif ($da['type'] == 'grant') {
				$activity['labels'][] = array(
					'title' => "满赠",
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'mallNewMember') {
				$mallNewMember = iunserializer($da['data']);
				$activity['labels'][] = array(
					'title' => "首单减{$mallNewMember['back']}",
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'newMember') {
				$newMember = iunserializer($da['data']);
				$activity['labels'][] = array(
					'title' => "新客减{$newMember['back']}",
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'couponCollect') {
				$activity['labels'][] = array(
					'title' => "有机会领券",
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'couponGrant') {
				$couponGrant = iunserializer($da['data']);
				$activity['labels'][] = array(
					'title' => "返{$couponGrant['discount']}{$_W['Lang']['dollarSignCn']}券",
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'bargain') {
				$activity['labels'][] = array(
					'title' => $da['title'],
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'deliveryFeeDiscount') {
				$activity['labels'][] = array(
					'title' => '可减配送费',
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'selfPickup' || $da['type'] == 'selfDelivery') {
				$activity['labels'][] = array(
					'title' => '自提优惠',
					'class' => 'tag tag-danger'
				);
			} elseif ($da['type'] == 'cashGrant') {
				$activity['labels'][] = array(
					'title' => '返余额',
					'class' => 'tag tag-danger'
				);
			} elseif($da['type'] == 'svipRedpacket') {
				$da['data'] = iunserializer($da['data']);
				$activity['labels'][] = array(
					'title' => "{$da['data']['discount']}{$_W['Lang']['dollarSignCn']}无门槛红包",
					'class' => 'tag tag-svip'
				);
			}
		}
		$activity['labels_num'] = count($activity['labels']);
	}
	return $activity;
}


function store_delivery_times($sid, $force_update = false) {
	global $_W;
	$cache_key = "we7wmall_store_delivery_times|{$sid}|{$_W['uniacid']}";
	if(!$force_update && 0) {
		$data = cache_read($cache_key);
		if(!empty($data) && $data['updatetime'] > TIMESTAMP) {
			return $data;
		}
	}
	$store = store_fetch($sid, array('id', 'delivery_reserve_days', 'delivery_within_days', 'delivery_time', 'delivery_times', 'delivery_fee_mode', 'delivery_price'));
	//配送时间
	$days = array();
	$totaytime = strtotime(date('Y-m-d'));
	//配送时间段
	$times = $store['delivery_times'];
	$last_time = $totaytime + 79200; //晚上10点
	if(!empty($times)) {
		$last_time = array_pop($times);
		$last_time = explode(':', $last_time['end']);
		$last_time = mktime($last_time[0], $last_time[1]);
	}
	$predict_timestamp = TIMESTAMP + 60 * $store['delivery_time'];
	if($predict_timestamp > $last_time) {
		$totaytime = $totaytime + 86400;
		$nextday = date('m-d', $totaytime);
	}
	if($store['delivery_reserve_days'] > 0) { //需提前几天点外卖
		$days[] = date('m-d', $totaytime + $store['delivery_reserve_days'] * 86400);
	} elseif($store['delivery_within_days'] > 0) {//可提前几天点外卖
		for($i = 0; $i <= $store['delivery_within_days']; $i++) {
			$days[] = date('m-d', $totaytime + $i * 86400);
		}
	} else {
		$days[] = date('m-d', $totaytime);
	}

	$mktimes = array(
		'month' => date('m', $totaytime),
		'day' => date('d', $totaytime),
	);
	//配送时间段
	$times = $store['delivery_times'];
	$timestamp = array();
	if(!empty($times)) {
		foreach($times as $key => &$row) {
			if(empty($row['status'])) {
				unset($times[$key]);
				continue;
			}
			if($store['delivery_fee_mode'] == 1) {
				$row['delivery_price'] = $store['delivery_price'] + $row['fee'];
				$row['delivery_price_cn'] = "{$row['delivery_price']}{$_W['Lang']['dollarSignCn']}配送费";
			} else {
				$row['delivery_price'] = $store['delivery_price'] + $row['fee'];
				$row['delivery_price_cn'] = "配送费{$row['delivery_price']}{$_W['Lang']['dollarSignCn']}起";
			}
			$end = explode(':', $row['end']); //开始时间
			$row['timestamp'] = mktime($end[0], $end[1], 0, $mktimes['month'], $mktimes['day']);
			$timestamp[$key] = $row['timestamp'];
		}
	} else {
		$start = mktime(8, 0, 0, $mktimes['month'], $mktimes['day']);
		$end = mktime(22, 0, 0, $mktimes['month'], $mktimes['day']);
		for($i = $start; $i < $end;) {
			if($store['delivery_fee_mode'] == 1) {
				$store['delivery_price_cn'] = "{$store['delivery_price']}{$_W['Lang']['dollarSignCn']}配送费";
			} else {
				$store['delivery_price_cn'] = "配送费{$store['delivery_price']}{$_W['Lang']['dollarSignCn']}起";
			}
			$times[] = array(
				'start' => date('H:i', $i),
				'end' => date('H:i', $i + 1800),
				'timestamp' => $i + 1800,
				'fee' => 0,
				'delivery_price' => $store['delivery_price'],
				'delivery_price_cn' => $store['delivery_price_cn'],
			);
			$timestamp[] = $i + 1800;
			$i += 1800;
		}
	}
	$data = array(
		'nextday' => $nextday,
		'days' => $days,
		'times' => $times,
		'timestamp' => $timestamp,
		'updatetime' => strtotime(date('Y-m-d')) + 86400,
		'reserve' => ($store['delivery_reserve_days'] > 0 ? 1 : 0),
	);
	//cache_write($cache_key, $data);
	return $data;
}

function store_rest_start_delivery_time($store) {
	$delivery_time = '';
	foreach($store['business_hours'] as $hours) {
		$starthour = strtotime($hours['s']);
		if($starthour > TIMESTAMP) {
			$delivery_time = $hours['s'];
			break;
		}
	}
	if(empty($delivery_time)) {
		$delivery_time = $store['business_hours'][0]['s'];
		$delivery_time_cn = "预定中 明天{$delivery_time}开始配送";
		$nextday = 1;
	} else {
		$delivery_time_cn = "预定中 {$delivery_time}开始配送";
	}
	$data = array(
		'delivery_time' => $delivery_time,
		'delivery_time_cn' => $delivery_time_cn,
		'nextday' => $nextday
	);
	return $data;
}
