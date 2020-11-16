<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function is_favorite_store($sid, $uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$is_ok = pdo_get('tiny_wmall_store_favorite', array('sid' => $sid, 'uid' => $uid));
	if(!empty($is_ok)) {
		return true;
	}
	return false;
}

function store_set_data($sid, $key, $value) {
	global $_W;
	$data = store_get_data($sid);
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$data[$keys[0]] = $value;
	} elseif($counts == 2) {
		if(!is_array($data[$keys[0]])) {
			$data[$keys[0]] = array();
		}
		$data[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		if(!is_array($data[$keys[0]])) {
			$data[$keys[0]] = array();
		} elseif(!is_array($data[$keys[0]][$keys[1]])) {
			$data[$keys[0]][$keys[1]] = array();
		}
		$data[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_store', array('data' => iserializer($data)), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	return true;
}

function store_get_data($sid, $key = '') {
	global $_W;
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('data'));
	$data = iunserializer($store['data']);
	if(!is_array($data)) {
		$data = array();
	}
	if(empty($key)) {
		return $data;
	}
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		return $data[$key];
	} elseif($counts == 2) {
		return $data[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $data[$keys[0]][$keys[1]][$keys[1]];
	}
	return true;
}

function clerk_manage($id) {
	global $_W;
	$perm = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $id, 'role' => 'manager'), array(), 'sid');
	if(empty($perm)) {
		return array();
	}
	return array_keys($perm);
}

function store_can_order($storeOrSid) {
	global $_W;
	if(!is_array($storeOrSid)) {
		$sid = intval($storeOrSid);
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('status'));
	} else {
		$store = $storeOrSid;
	}
	if($store['status'] == 1 || ($store['status'] == 0 && $_W['we7_wmall']['config']['takeout']['order']['hide_store_can_order'] == 1)) {
		return true;
	}
	return error(-2, '门店状态异常，暂时无法下单');
}

function store_manager($sid) {
	global $_W;
	$perm = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'role' => 'manager'));
	$clerk = array();
	if(!empty($perm)) {
		$clerk =  pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $perm['clerk_id']));
	}
	return $clerk;
}

function store_fetchall($field = array()) {
	global $_W;
	$field_str = '*';
	if(!empty($field)) {
		$field_str = implode(',', $field);
	}
	$data = pdo_fetchall("SELECT {$field_str} FROM " . tablename('tiny_wmall_store') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');
	if(!empty($data)) {
		$se_fileds = array('thumbs', 'sns', 'mobile_verify', 'payment', 'business_hours', 'thumbs', 'remind_reply', 'comment_reply', 'wechat_qrcode', 'custom_url');
		$foreach_fileds = array_merge($se_fileds, array('score'));
		$intersect = array_intersect($field, $foreach_fileds);
		if(!empty($intersect)) {
			foreach($data as &$row) {
				foreach($se_fileds as $se_filed) {
					if(isset($row[$se_filed])) {
						if($se_filed != 'thumbs') {
							$row[$se_filed] = (array)iunserializer($row[$se_filed]);
						} else {
							$row[$se_filed] = iunserializer($row[$se_filed]);
						}
					}
				}
				if(isset($row['business_hours'])) {
					$row['is_in_business_hours'] = intval($row['is_in_business']);
					if($row['is_in_business'] == 1) {
						if($row['rest_can_order'] == 1) {
							$row['is_in_business_hours'] = true;
						} else {
							$row['is_in_business_hours'] = store_is_in_business_hours($row['business_hours']);
						}
					}
					$hour = array();
					foreach($row['business_hours'] as $li) {
						$hour[] = "{$li['s']}~{$li['e']}";
					}
					$row['business_hours_cn'] = implode(',', $hour);
				}
				if(isset($row['score'])) {
					$row['score_cn'] = round($row['score'] / 5, 2) * 100;
				}
			}
		}
	}
	return $data;
}

//store_fetchall_category
function store_fetchall_category($type = 'all',  $filter = array()) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid and agentid = :agentid and status = 1';
	$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_category') . $condition . ' order by displayorder desc', $params, 'id');
	if(!empty($data)) {
		if($filter['store_num'] == 1) {
			$stores = pdo_fetchall('select cate_parentid1, cate_childid1, cate_parentid2, cate_childid2 from ' . tablename('tiny_wmall_store') . $condition, $params);
		}
		foreach($data as &$da) {
			$store_num = 0;
			$da['thumb'] = tomedia($da['thumb']);
			$da['is_sys'] = 0;
			if(empty($da['link']) && empty($da['wxapp_link'])) {
				$da['is_sys'] = 1;
				$da['link'] = imurl('wmall/home/search', array('cid' => $da['id'], 'order' => $_GPC['order'], 'dis' => $_GPC['dis']));
			}
			if(empty($da['wxapp_link'])){
				$da['wxapp_link'] = "pages/home/category?cid={$da['id']}";
			}
			if($filter['is_sys'] == 1 && empty($da['is_sys'])) {
				unset($data[$da['id']]);
				continue;
			}
			if($filter['store_num'] == 1) {
				if(!empty($stores)) {
					foreach($stores as $val) {
						if(in_array($da['id'], $val)) {
							$store_num++;
						}
					}
				}
				$da['store_num'] = $store_num;
			}
			if($type == 'parent_child') {
				if(!empty($da['parentid'])) {
					$config_mall = $_W['we7_wmall']['config']['mall'];
					if($config_mall['store_use_child_category'] == 1) {
						$data[$da['parentid']]['child'][] = $da;
					}
					unset($data[$da['id']]);
				}
			} elseif($type == 'parent&child') {
				$da['name'] = $da['title'];
				if(empty($da['parentid'])) {
					$parent[$da['id']] = $da;
				} else {
					$child[$da['parentid']][$da['id']] = $da;
				}
			}
		}
		if($type == 'parent&child') {
			unset($data);
			$data = array(
				'parent' => $parent,
				'child' => $child
			);
		}
	}
	return $data;
}
//store_fetch_category
function store_fetch_category() {
	global $_W, $_GPC;
	$cid = intval($_GPC['cid']);
	$category = pdo_get('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $cid, 'status' => 1));
	if(!empty($category)) {
		$category['thumb'] = tomedia($category['thumb']);
		if(!empty($category['nav']) && $category['nav_status'] == 1){
			$category['nav'] = iunserializer($category['nav']);
			foreach ($category['nav'] as &$value) {
				$value['thumb'] = tomedia($value['thumb']);
			}
		}
		if(!empty($category['slide']) && $category['slide_status'] == 1){
			$category['slide'] = iunserializer($category['slide']);
			array_sort($category['slide'], 'displayorder', SORT_DESC);
			foreach ($category['slide'] as &$v) {
				$v['thumb'] = tomedia($v['thumb']);
			}
		}
		$config_mall = $_W['we7_wmall']['config']['mall'];
		if($category['parentid'] == 0 && $config_mall['store_use_child_category'] == 1) {
			$category['child'] = pdo_fetchall('select id, parentid, thumb, title from ' . tablename('tiny_wmall_store_category') . ' where uniacid = :uniacid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':parentid' => $category['id']));
			if(!empty($category['child'])) {
				foreach ($category['child'] as &$v) {
					$v['thumb'] = tomedia($v['thumb']);
				}
			} else {
				unset($category['child']);
			}
		}
	}
	return $category;
}

//is_in_business_hours
function store_is_in_business_hours($business_hours) {
	if(!is_array($business_hours)) {
		return true;
	}
	$business_hours_flag = false;
	foreach($business_hours as $li) {
		if(!is_array($li) || empty($li['s']) || empty($li['e'])) {
			continue;
		}
		$starttime = strtotime($li['s']);
		$endtime = strtotime($li['e']);
		$cross_night = 0;
		if($starttime >= $endtime) {
			$cross_night = 1;
		}
		$now = TIMESTAMP;
		if((!$cross_night && $now >= $starttime && $now <= $endtime) || ($cross_night && ($now >= $starttime || $now <= $endtime))) {
			$business_hours_flag = true;
			break;
		}
	}
	return $business_hours_flag;
}

function store_business_hours_init($sid = 0) {
	global $_W;
	if($sid > 0) {
		$store = store_fetch($sid, array('business_hours', 'is_in_business', 'rest_can_order'));
		$is_rest = 1;
		if($store['is_in_business']) {
			if($store['rest_can_order'] == 1) {//兼容非营业时间可下单
				$is_rest = 0;
			} elseif(store_is_in_business_hours($store['business_hours'])) {
				$is_rest = 0;
			}
		}
		pdo_update('tiny_wmall_store', array('is_rest' => $is_rest), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		mlog(2012, $sid);
	} else {
		$stores = pdo_fetchall('select id,business_hours,is_in_business,rest_can_order from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
		if(!empty($stores)) {
			foreach($stores as $row) {
				$row['business_hours'] = iunserializer($row['business_hours']);
				$is_rest = 1;
				if($row['is_in_business']) {
					if($row['rest_can_order'] == 1) {
						$is_rest = 0;
					} elseif(store_is_in_business_hours($row['business_hours'])) {
						$is_rest = 0;
					}
				}
				pdo_update('tiny_wmall_store', array('is_rest' => $is_rest), array('uniacid' => $_W['uniacid'], 'id' => $row['id']));
				mlog(2012, $row['id']);
			}
		}
	}
	return true;
}

//get_goods_category
function store_fetchall_goods_category($store_id, $status = '-1', $ignore_bargain = true, $type = 'parent', $category_type = 'all') {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $store_id);
	if($type == 'parent') {
		$condition .= ' and parentid = 0';
	}
	if($status >= 0) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods_category') . $condition . ' order by displayorder desc, id asc', $params, 'id');
	if($type == 'parent' && $category_type == 'available') {
		foreach($categorys as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
			if(!empty($val['is_showtime'])) {
				$now_week = date('N', TIMESTAMP);
				$start_time = intval(strtotime($val['start_time']));
				$end_time = intval(strtotime($val['end_time']));
				$week = explode(',', $val['week']);
				if ($start_time >= $end_time) {
					$end_time = $end_time + 86400;
				}
				if((!empty($val['week']) && !in_array($now_week, $week)) || (!empty($start_time) && (TIMESTAMP < $start_time || TIMESTAMP > $end_time))) {
					unset($categorys[$val['id']]);
				}
			}
		}
	}
	if($type == 'all') {
		foreach($categorys as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
			if(!empty($val['parentid'])) {
				$categorys[$val['parentid']]['child'][] = $val;
				unset($categorys[$val['id']]);
			} else {
				if($category_type == 'available') {
					if(!empty($val['is_showtime'])) {
						$now_week = date('N', TIMESTAMP);
						$start_time = intval(strtotime($val['start_time']));
						$end_time = intval(strtotime($val['end_time']));
						$week = explode(',', $val['week']);
						if ($start_time >= $end_time) {
							$end_time = $end_time + 86400;
						}
						if((!empty($val['week']) && !in_array($now_week, $week)) || (!empty($start_time) && (TIMESTAMP < $start_time || TIMESTAMP > $end_time))) {
							unset($categorys[$val['id']]);
						}
					}
				}
			}
		}
	} elseif($type == 'other') {
		foreach ($categorys as &$value) {
			$value['name'] = $value['title'];
			if(empty($value['parentid'])){
				if($category_type == 'available') {
					if(!empty($value['is_showtime'])) {
						$now_week = date('N', TIMESTAMP);
						$start_time = intval(strtotime($value['start_time']));
						$end_time = intval(strtotime($value['end_time']));
						$week = explode(',', $value['week']);
						if ($start_time >= $end_time) {
							$end_time = $end_time + 86400;
						}
						if((!empty($value['week']) && !in_array($now_week, $week)) || (!empty($start_time) && (TIMESTAMP < $start_time || TIMESTAMP > $end_time))) {
							unset($categorys[$value['id']]);
						}
					}
				}
				$parent[$value['id']] = $value;
			} else {
				$child[$value['parentid']][$value['id']] = $value;
			}
		}
		unset($categorys);
		$categorys['parent'] = $parent;
		$categorys['child'] = $child;
	}
	if(!$ignore_bargain) {
		$condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
		$params = array(
			':uniacid' => $_W['uniacid'],
			':sid' => $store_id,
			':status' => 1
		);
		$bargains = pdo_fetchall('select id, title, content, thumb from ' . tablename('tiny_wmall_activity_bargain') . $condition, $params, 'id');
		if(!empty($bargains)) {
			foreach($bargains as &$bargain) {
				array_unshift($categorys, array('id' => "bargain_{$bargain['id']}", 'title' => $bargain['title'], 'bargain_id' => $bargain['id'], 'decoration' => $bargain['content'], 'thumb' => tomedia($bargain['thumb'])));
			}
		}
	}
	foreach($categorys as &$row) {
		$row['total'] = 0;
		if(!isset($row['child']) && defined('IN_VUE')) {
			$row['child'] = array();
		}
	}
	return $categorys;
}

function get_goods_child_category($sid, $parentid) {
	global $_W, $_GPC;
	if(empty($parentid)) {
		$parentid = intval($_GPC['parentid']);
	}
	$child_category = pdo_fetchall('select id,title from' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid and parentid = :parentid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':parentid' => $parentid));
	return $child_category;
}

function store_fetch_goods($id, $field = array('basic', 'options')) {
	global $_W;
	$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($goods)) {
		return error(-1, '商品不存在或已删除');
	}
	$goods['data'] = iunserializer($goods['data']);
	$goods['thumb_'] = tomedia($goods['thumb']);
	if(in_array('options', $field) && $goods['is_options']) {
		$goods['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $id), array(), 'id');
		$goods['options_haskey'] = $goods['options'];
		$goods['options'] = array_values($goods['options']);
	}
	return $goods;
}

/*计算门店的评价*/
function store_comment_stat($sid, $update = true) {
	global $_W;
	$stat = array();
	$stat['goods_quality'] = round(pdo_fetchcolumn('select avg(goods_quality) from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)), 1);
	$stat['delivery_service'] = round(pdo_fetchcolumn('select avg(delivery_service) from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)), 1);
	$stat['score'] = round(($stat['goods_quality'] + $stat['delivery_service']) / 2, 1);
	if($update) {
		pdo_update('tiny_wmall_store', array('score' => $stat['score']), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	}
	$stat['goods_quality_star'] = score_format($stat['goods_quality']);
	$stat['delivery_service_star'] = score_format($stat['delivery_service']);
	return $stat;
}

function store_status() {
	$data = array(
		'0' => array(
			'css' => 'label label-default',
			'text' => '隐藏中',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-success',
			'text' => '显示中',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '审核中',
		),
		'3' => array(
			'css' => 'label label-danger',
			'text' => '审核未通过',
		),
		'4' => array(
			'css' => 'label label-danger',
			'text' => '回收站',
		),
	);
	return $data;
}

function store_account($sid, $fileds = array()) {
	global $_W;
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'],'sid' => $sid), $fileds);
	if(!empty($account)) {
		$se_fileds = array('bank', 'wechat', 'alipay', 'fee_goods', 'fee_takeout', 'fee_selfDelivery', 'fee_instore', 'fee_paybill', 'fee_eleme', 'fee_meituan', 'fee_gohome');
		foreach($se_fileds as $se_filed) {
			if(isset($account[$se_filed])) {
				$account[$se_filed] = (array)iunserializer($account[$se_filed]);
			}
		}
	}
	return $account;
}

function store_update_account($sid, $fee, $trade_type, $extra, $remark = '') {
	global $_W;
	//$trade_type 1: 订单入账, 2: 申请提现, 3: 账户清零 5：平台平衡商家账户 6: 限时抢购订单入账 7.保证金变动变更账户 8.生活圈订单入账。
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(empty($account)) {
		return error(-1, '账户不存在');
	}
	if(($trade_type == 1 || $trade_type == 8) && !empty($extra)) {
		$is_exist = pdo_get('tiny_wmall_store_current_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'trade_type' => $trade_type, 'extra' => $extra), array('id'));
		if(!empty($is_exist)) {
			return error(-1, '订单已经入账');
		}
	}
	$hash = md5("{$_W['uniacid']}-{$sid}-{$trade_type}-{$extra}");
	if($trade_type == 3 || $trade_type == 7) {
		//其他变动
		$hash = md5("{$_W['uniacid']}-{$sid}-{$trade_type}-{$fee}" . TIMESTAMP);
	}
	$now_amount = $account['amount'] + $fee;
	$log = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $account['agentid'],
		'sid' => $sid,
		'trade_type' => $trade_type,
		'extra' => $extra,
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'hash' => $hash,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_store_current_log', $log);
	$id = pdo_insertid();
	if(!empty($id)) {
		$status = pdo_update('tiny_wmall_store_account', array('amount' => $now_amount), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		$account_new = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		$text = "商户id:{$sid},变动前金额:{$account['amount']},变动金额:{$fee},变动后金额:{$now_amount},实际变动后金额:{$account_new['amount']}";
		if(empty($status)) {
			slog('storeaccount', '商户账户变动失败', array(), $text);
		}
	}
	if(in_array($trade_type, array(3, 5, 7)) && empty($extra)) {
		//撤销提现账户变动也记录一次，线下支付扣除商户账户不记录
		mlog(2005, $id, "{$remark}, {$text}");
	}
	return true;
}

function store_update_yucunjin($sid, $fee, $trade_type, $extra, $remark = '') {
	global $_W;
	//$trade_type 1订单扣除 3其他变动
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(empty($account)) {
		return error(-1, '账户不存在');
	}
	if($trade_type == 1 && !empty($extra)) {
		$is_exist = pdo_get('tiny_wmall_store_yucunjin_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'trade_type' => $trade_type, 'extra' => $extra), array('id'));
		if(!empty($is_exist)) {
			return error(-1, '预存金已扣除');
		}
	}
	$hash = md5("{$_W['uniacid']}-{$sid}-{$trade_type}-{$extra}");
	if($trade_type == 3) {
		//其他变动
		$hash = md5("{$_W['uniacid']}-{$sid}-{$trade_type}-{$fee}" . TIMESTAMP);
	}
	$now_yucunjin = $account['yucunjin'] + $fee;
	$log = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $account['agentid'],
		'sid' => $sid,
		'trade_type' => $trade_type,
		'extra' => $extra,
		'fee' => $fee,
		'yucunjin' => $now_yucunjin,
		'addtime' => TIMESTAMP,
		'hash' => $hash,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_store_yucunjin_log', $log);
	$id = pdo_insertid();
	if(!empty($id)) {
		$status = pdo_update('tiny_wmall_store_account', array('yucunjin' => $now_yucunjin), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		if($status === false) {
			$account_new = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
			slog('storeyucunjin', '商户预存金变动失败', array(), "商户id:{$sid},变动前金额:{$account['预存金']},变动金额:{$fee},变动后金额:{$now_yucunjin},实际变动后金额:{$account_new['yucunjin']}");
		}
	}
	return true;
}

function store_yucunjin_type() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '订单扣除',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '其他变动',
		),
	);
	return $data;
}

function store_getcash_status() {
	$data = array(
		'1' => array(
			'css' => 'label label-success',
			'text' => '提现成功',
		),
		'2' => array(
			'css' => 'label label-danger',
			'text' => '申请中',
		),
		'3' => array(
			'css' => 'label label-default',
			'text' => '提现失败',
		),
	);
	return $data;
}


function store_delivery_modes() {
	$data = array(
		'1' => array(
			'css' => 'label label-danger',
			'text' => '店内配送员',
			'color' => ''
		),
		'2' => array(
			'css' => 'label label-success',
			'text' => '平台配送员',
		),
	);
	return $data;
}

function store_fetchall_by_condition($type = 'hot', $option = array()) {
	global $_W;
	if(empty($option['limit'])) {
		$option['limit'] = 6;
	}
	if(empty($option['extra_type'])) {
		$option['extra_type'] = 'all';
	}
	$condition = ' where uniacid = :uniacid and agentid = :agentid and status = 1 and is_waimai = 1';
	$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
	if(isset($option['is_rest'])) {
		$condition .= ' and is_rest = :is_rest';
		$params[':is_rest'] = intval($option['is_rest']);
	}
	if($type == 'hot') {
		$stores = pdo_fetchall('select id,title,forward_mode,forward_url from ' . tablename('tiny_wmall_store') . $condition . ' order by click desc, displayorder desc limit 4', $params);
	} elseif($type == 'recommend') {
		$condition .= ' and is_recommend = 1 and position = 1';
		$stores = pdo_fetchall('select id,title,logo,content,business_hours,delivery_fee_mode,delivery_price,delivery_areas,send_price,delivery_time,forward_mode,forward_url,score,location_y,location_x,sailed,is_rest from ' . tablename('tiny_wmall_store') . $condition . " order by is_rest asc, displayorder desc limit {$option['limit']}", $params);
	}
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['logo'] = tomedia($row['logo']);
			$row['scores_original'] = $row['score'];
			$row['scores'] = score_format($row['score']);
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url'], $_W['channel']);
			if($option['extra_type'] == 'all') {
				$row['activity'] = store_fetch_activity($row['id']);
				$row['activity']['items'] = array_values($row['activity']['items']);
				if($row['delivery_fee_mode'] == 2) {
					$row['delivery_price'] = iunserializer($row['delivery_price']);
					$row['delivery_price'] = $row['delivery_price']['start_fee'];
				} elseif($row['delivery_fee_mode'] == 3) {
					$row['delivery_areas'] = iunserializer($row['delivery_areas']);
					if(!is_array($row['delivery_areas'])) {
						$row['delivery_areas'] = array();
					}
					$price = store_order_condition($row);
					$row['delivery_price'] = $price['delivery_price'];
					$row['send_price'] = $price['send_price'];
				}
			}
		}
	}
	return $stores;
}

function store_forward_url($sid, $forward_mode, $forward_url = '', $channel = '') {
	global $_W;
	if(empty($channel)) {
		$channel = $_W['channel'];
	}
	if($channel == 'wechat') {
		if($forward_mode == 0) {
			$url = imurl('wmall/store/goods', array('sid' => $sid));
		} elseif($forward_mode == 1) {
			$url = imurl('wmall/store/index', array('sid' => $sid));
		} elseif($forward_mode == 3) {
			$url = imurl('wmall/store/assign', array('sid' => $sid));
		} elseif($forward_mode == 4) {
			$url = imurl('wmall/store/reserve', array('sid' => $sid));
		} elseif($forward_mode == 6) {
			$url = imurl('wmall/store/paybill', array('sid' => $sid));
		} elseif($forward_mode == 5) {
			$url = $forward_url;
		}
	} else {
		$url = "/pages/store/goods?sid={$sid}";
		if($forward_mode == 0) {
			$url = "/pages/store/goods?sid={$sid}";
		} elseif($forward_mode == 1) {
			if(check_plugin_perm('wxapp')) {
				$url = "/pages/store/home?sid={$sid}";
			} else {
				$url = "/pages/store/index?sid={$sid}";
			}
		} elseif($forward_mode == 4) {
			$url = "/tangshi/pages/reserve/index?sid={$sid}";
		} elseif($forward_mode == 6) {
			$url = "/pages/store/paybill?sid={$sid}";
		}
	}
	return $url;
}

function store_order_serial_sn($store_id){
	global $_W;
	$serial_sn = pdo_fetchcolumn('select serial_sn from' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and order_plateform = :order_plateform and addtime > :addtime order by serial_sn desc', array(':uniacid' => $_W['uniacid'], ':sid' => $store_id, ':order_plateform' => 'we7_wmall', ':addtime' => strtotime(date('Y-m-d'))));
	$serial_sn = intval($serial_sn) + 1;
	return $serial_sn;
}

//checkstore
function store_check() {
	global $_W, $_GPC;
	if(!defined('IN_MOBILE')) {
		if(!empty($_GPC['_sid'])) {
			$sid = intval($_GPC['_sid']);
			isetcookie('__sid', $sid, 86400);
		} else {
			$sid = intval($_GPC['__sid']);
		}
	} else {
		$sid = intval($_GPC['sid']);
	}
	if(!defined('IN_MOBILE')) {
		if($_W['role'] != 'manager' && empty($_W['isfounder'])) {
			if($_W['we7_wmall']['store']['id'] != $sid) {
				message('您没有该门店的管理权限', '', 'error');
			}
		}
	}
	$store = pdo_fetch('SELECT id, title, status, pc_notice_status, delivery_mode FROM ' . tablename('tiny_wmall_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
	if(empty($store)) {
		if(!defined('IN_MOBILE')) {
			message('门店信息不存在或已删除', '', 'error');
		}
		exit();
	}
	$store['manager'] = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'role' => 'manager'));
	$store['account'] = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $store['id']));
	$_W['we7_wmall']['store'] = $store;
	return $store;
}

function store_serve_fee_items() {
	return array(
		'yes' => array(
			'price' => '商品费用',
			'box_price' => '餐盒费',
			'pack_fee' => '包装费',
			'delivery_fee' => '配送费',
			'serve_fee' => '店内服务费'
		),
		'no' => array(
			'store_discount_fee' => '商户活动补贴'
		)
	);
}

function is_in_store_radius($sid, $lnglat, $area_id = 0) {
	global $_W;
	if(is_array($sid)) {
		$store = $sid;
	}
	if(empty($store)) {
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode', 'delivery_price', 'delivery_areas', 'delivery_areas1', 'serve_radius', 'not_in_serve_radius'));
		if(empty($store)) {
			return false;
		}
	}
	$flag = false;
	if($store['address_type'] == 1) {
		if(!empty($lnglat) && !empty($lnglat['area_id']) && in_array($lnglat['area_id'], $store['delivery_areas1_ids'])) {
			$flag = true;
		}
	} else {
		if($store['delivery_fee_mode'] == 1 || $store['delivery_fee_mode'] == 2) {
			if(!$store['not_in_serve_radius'] && $store['serve_radius'] > 0) {
				if(empty($lnglat[0]) || empty($lnglat[1])) {
					return false;
				}
				$dist = distanceBetween($lnglat[0], $lnglat[1], $store['location_y'], $store['location_x']);
				if($dist <= ($store['serve_radius'] * 1000)) {
					$flag = true;
				}
			} else {
				$flag = true;
			}
		} elseif($store['delivery_fee_mode'] == 3) {
			if(empty($lnglat[0]) || empty($lnglat[1])) {
				return false;
			}
			if(empty($store['delivery_areas'])) {
				return false;
			}
			if(!empty($area_id)) {
				$store['delivery_areas'] = array($store['delivery_areas'][$area_id]);
			}
			foreach($store['delivery_areas'] as $area) {
				$flag = isPointInPolygon($area['path'], array($lnglat[0], $lnglat[1]));
				if($flag) {
					break;
				}
			}
		}
	}
	return $flag;
}

function is_in_store_area($storeOrId, $addressOrId, $area_id = 0) {
	global $_W, $_GPC;
	if(is_array($storeOrId)) {
		$store = $storeOrId;
	} else {
		$store = store_fetch($storeOrId, array('location_y', 'location_x', 'delivery_fee_mode','delivery_price', 'delivery_areas', 'delivery_areas1', 'serve_radius', 'not_in_serve_radius'));
	}
	if(empty($store)) {
		return false;
	}
	if(is_array($addressOrId)) {
		$address = $addressOrId;
	} else {
		$address = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 1', array(':uniacid' => $_W['uniacid'], ':id' => $addressOrId));
	}
	if(empty($address)) {
		return false;
	}
	$flag = false;
	if($store['address_type'] == 1) {
		$flag = is_in_store_radius($store, array('area_id' => $address['area_id']));
	} else {
		$flag = is_in_store_radius($store, array($address['location_y'], $address['location_x']), $area_id);
	}
	return $flag;
}

function store_order_condition($sid, $lnglat = array()) {
	global $_GPC;
	if(is_array($sid)) {
		$store = $sid;
	}
	if(empty($store)) {
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode', 'delivery_price', 'delivery_areas', 'delivery_areas1', 'delivery_price', 'delivery_free_price', 'send_price'));
		if(empty($store)) {
			return error(-1, '门店不存在');
		}
	}
	$price = array(
		'send_price' => $store['send_price'],
		'delivery_price' => $store['delivery_price'],
		'delivery_free_price' => $store['delivery_free_price'],
	);
	if(empty($store['address_type']) && $store['delivery_fee_mode'] == 3) {
		if(empty($lnglat)) {
			if($_GPC['address_id'] > 0) {
				$address = member_fetch_address($_GPC['address_id']);
				$lnglat = array($address['location_y'], $address['location_x']);
			} else {
				$lnglat = array($_GPC['__lng'], $_GPC['__lat']);
			}
		}
		$delivery_price_arr = array();
		$send_price_arr = array();
		$delivery_free_price_arr = array();
		foreach($store['delivery_areas'] as $key => $area) {
			$in = isPointInPolygon($area['path'], $lnglat);
			if($in) {
				if($_GPC['op'] == 'goods') {
					isetcookie('_guess_area', $key, 300);
				}
				$price['delivery_price'] = $area['delivery_price'];
				$price['send_price'] = $area['send_price'];
				$price['delivery_free_price'] = $area['delivery_free_price'];
				break;
			}
			$delivery_price_arr[] = $area['delivery_price'];
			$send_price_arr[] = $area['send_price'];
			$delivery_free_price_arr[] = $area['delivery_free_price'];
		}
		if(!$in) {
			$price['delivery_price'] = min($delivery_price_arr);
			$price['send_price'] = min($send_price_arr);
			$price['delivery_free_price'] = min($delivery_free_price_arr);
		}
	}
	return $price;
}

function store_notice_stat($clerk_id = 0, $agentid = 0) {
	global $_W;
	if(empty($clerk_id)) {
		$clerk_id = $_W['clerk']['id'];
	}
	$new_id = pdo_fetchcolumn('SELECT notice_id FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid and type = :type ORDER BY notice_id DESC LIMIT 1', array(':uid' => $clerk_id, ':type' => 'store'));
	$new_id = intval($new_id);
	$notices = pdo_fetchall('SELECT id FROM ' . tablename('tiny_wmall_notice') . ' WHERE uniacid = :uniacid and agentid = :agentid and status = 1 AND type = :type AND id > :id', array(':uniacid' => $_W['uniacid'], ':agentid' => $agentid, ':type' => 'store',':id' => $new_id));
	if(!empty($notices)) {
		foreach($notices as &$notice) {
			$insert = array(
				'type' => 'store',
				'uid' => $clerk_id,
				'notice_id' => $notice['id'],
				'is_new' => 1,
			);
			pdo_insert('tiny_wmall_notice_read_log', $insert);
		}
	}
	$total = intval(pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid AND is_new = 1', array(':uid' => $clerk_id)));
	return $total;
}

function store_stat_init($name, $sid = 0, $day = 30, $type = 'order') {
	global $_W;
	$limittime = TIMESTAMP - 86400 * $day;
	$routers = array(
		'sailed' => 'count(*) as sailed',
		'delivery_time' => 'avg(delivery_success_time - paytime) as delivery_time, data',
	);
	if($name == 'sailed' && $type == 'goods') {
		$routers['sailed'] = 'sum(num) as sailed';
	}
	if(empty($sid)) {
		$condition = ' where uniacid = :uniacid';
		$params = array(
			':uniacid' => $_W['uniacid']
		);
		if($_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid';
			$params[':agentid'] = $_W['agentid'];
		}

		$orders = pdo_fetchall("select sid, {$routers[$name]} from" . tablename('tiny_wmall_order') . "{$condition} and status = 5 and addtime > {$limittime} and delivery_success_time > 0 group by sid", $params,'sid');
		$sids = array();
		if(!empty($orders)) {
			$sids = array_keys($orders);
		}
		$stores = pdo_fetchall('select id, sailed, delivery_time, data from ' . tablename('tiny_wmall_store') . $condition, $params);
		foreach($stores as &$da) {
			if($name == 'delivery_time') {
				$da['data'] = iunserializer($da['data']);
				if(!empty($da['data']) && $da['data']['delivery_time_type'] == 1) {
					continue;
				}
			}
			$update = array();
			if(in_array($da['id'], $sids)) {
				$value = intval($orders[$da['id']][$name]);
				if($name == 'delivery_time') {
					$value = floor($value / 60);
					$value = min($value, 255);
				}
				$update[$name] = $value;
			} else {
				$update[$name] = 0;
			}
			pdo_update('tiny_wmall_store', $update, array('id' => $da['id']));
		}
	} else {
		$store = pdo_fetch("select id,sailed,delivery_time,data from". tablename('tiny_wmall_store') . " where uniacid = :uniacid and id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $sid));
		if(empty($store)) {
			return error(-1,'商店不存在');
		}
		if($name == 'delivery_time') {
			$store['data'] = iunserializer($store['data']);
			if(!empty($store['data']) && $store['data']['delivery_time_type'] == 1) {
				return error(-1, '门店预计送达时间计算方式为门店手动设置');
			}
		}
		$orders = pdo_fetch("select sid, {$routers[$name]} from" .tablename('tiny_wmall_order') . "where uniacid = :uniacid and sid = :sid and status = 5 and addtime > {$limittime} ", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
		$update = array();
		if(!empty($orders)) {
			$value = intval($orders[$name]);
			if($name == 'delivery_time') {
				$value = floor($value / 60);
				$value = min($value, 255);
			}
			$update[$name] = $value;
		} else{
			$update[$name] = 0;
		}
		pdo_update('tiny_wmall_store', $update, array('id' => $sid));
	}
	return true;
}

function store_policy_status() {
	$data = array(
		'1' => array(
			'text' => '已创建未执行',
			'css' => 'label label-danger',
		),
		'2' => array(
			'text' => '执行中',
			'css' => 'label label-success',
		),
		'3' => array(
			'text' => '执行结束',
			'css' => 'label label-default',
		),
	);
	return $data;
}

function store_settle_notice($sid, $type = 'clerk', $note= '') {
	global $_W;
	load()->func('communication');
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = store_manager($sid);
	$store_status = array(
		1 => '审核通过',
		2 => '审核中',
		3 => '审核未通过',
	);
	$acc = WeAccount::create($_W['acid']);
	if($type == 'clerk') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店申请人信息不完善');
		}
		//通知申请人
		$tips = "【{$store['title']}】申请入驻【{$_W['we7_wmall']['config']['mall']['title']}】进度通知";
		$remark = array(
			"审核时间: " . date('Y-m-d H: i', time()),
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store['manager']['mobile'],
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date('Y-m-d H: i', $store['addtime']),
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		if($store['status'] == 1) {
			mlog(2003, $store['sid'], $note);
		} elseif($store['status'] == 3) {
			mlog(2004, $store['sid'], $note);
		}
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', "平台商户入驻进度微信通知申请人-门店:{$store['title']}", $send, $status['message']);
		}
	} elseif($type == 'manager') {
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(empty($maneger['openid'])) {
			return error(-1, '平台管理员信息不存在');
		}
		$tips = "尊敬的【{$maneger['nickname']}】，有新的商家提交了入驻请求。请登录电脑进行审核";
		$remark = array(
			"商家地址: {$store['address']}",
			"申请人手机号: {$store['manager']['mobile']}",
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['manager']['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date('Y-m-d H:i', time()),
				'color' => '#ff510',
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['settle_apply_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', '平台商户入驻微信通知平台管理员', $send, $status['message']);
		}
	}
	return $status;
}

function store_getcash_notice($sid, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = store_manager($store['id']);
	if($type != 'borrow_openid') {
		$log = pdo_get('tiny_wmall_store_getcash_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $getcash_log_id));
		if(empty($log)) {
			return error(-1, '提现记录不存在');
		}
	}
	$log['account'] = iunserializer($log['account']);
	$channel_cn = '微信';
	if($log['channel'] == 'alipay') {
		$channel_cn = '支付宝';
	} elseif($log['channel'] == 'bank') {
		$channel_cn = '银行卡';
	}
	load()->func('communication');
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		mlog(2009, $getcash_log_id);
		if(!empty($store['manager']) && !empty($store['manager']['openid'])) {
			//通知申请人
			$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: " . $channel_cn,
				"真实姓名: " . $log['account']['realname'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', "商户提现申请微信通知申请人-门店：{$store['title']}-{$store['manager']['nickname']}", $send, $status['message']);
			}
		}
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,【{$store['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: " . $channel_cn,
				"真实姓名: " . $log['account']['realname'],
				"提现总金额: " . $log['get_fee'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send);
			if(is_error($status)) {
				slog('wxtplNotice', '商户申请提现微信通知平台管理员', $send, $status['message']);
			}
		}
	} elseif($type == 'success') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: " . $channel_cn,
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_success_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', "商户申请提现成功微信通知申请人-门店：{$store['title']}-{$store['manager']['nickname']}", $send, $status['message']);
		}
	} elseif($type == 'fail') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】, 【{$store['title']}】账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: " . $channel_cn,
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', "商户申请提现失败微信通知申请人-门店：{$store['title']}-{$store['manager']['nickname']}", $send, $status['message']);
		}
	} elseif($type == 'borrow_openid') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】, 您正在进行门店【{$store['title']}】的提现申请。平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
		$remark = array(
			"申请门店: " . $store['title'],
			"账户类型: " . $channel_cn,
			'请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员'
		);
		$params = array(
			'first' => $tips,
			'money' => $getcash_log_id,
			'timet' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$url = imurl("wmall/auth/oauth", array('params' => base64_encode(json_encode($payment_wechat[$payment_wechat['type']]))), true);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_apply_tpl'], $send, $url);
		if(is_error($status)) {
			slog('wxtplNotice', "微信端商户申请提现授权微信通知申请人-门店：{$store['title']}-{$store['manager']['nickname']}", $send, $status['message']);
		}
	} elseif($type == 'cancel') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$addtime = date('Y-m-d H:i', $log['addtime']);
		$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】在{$addtime}的申请提现已被平台管理员撤销";
		$remark = array(
			"订单　号: " . $log['trade_no'],
			"申请门店: " . $store['title'],
			"撤销时间: " . date('Y-m-d H:i', $log['endtime']),
			'撤销原因: ' . $note,
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', TIMESTAMP),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall']['config']['notice']['wechat']['getcash_fail_tpl'], $send);
		if(is_error($status)) {
			slog('wxtplNotice', "商户申请提现被平台管理员撤销微信通知申请人-门店：{$store['title']}-{$store['manager']['nickname']}", $send, $status['message']);
		}
	}
	return $status;
}

function store_category_label() {
	global $_W;
	$data = pdo_fetchall('select id, title, alias,  color, is_system, displayorder from' . tablename('tiny_wmall_category') . ' where uniacid = :uniacid and type = :type order by is_system desc, displayorder desc', array(':uniacid' => $_W['uniacid'], ':type' => 'TY_store_label'), 'id');
	return $data;
}


function store_get_menu($sid, $type = 'home') {
	global $_W;
	$config_menu = store_get_data($sid, 'diymenu');
	$menu_id = intval($config_menu[$type]);
	$temp = pdo_get('tiny_wmall_store_menu', array('uniacid' => $_W['uniacid'], 'id' => $menu_id, 'version' => 2));
	if(!empty($temp)) {
		$menu = json_decode(base64_decode($temp['data']), true);
		foreach($menu['data'] as &$val) {
			if(!empty($val['img'])) {
				$val['img'] = tomedia($val['img']);
			}
		}
		return $menu;
	} else {
		$menu = array (
			'name' => 'default',
			'params' => array (
				'navstyle' => '0',
			),
			'css' => array (
				'iconColor' => '#163636',
				'iconColorActive' => '#ff2d4b',
				'textColor' => '#929292',
				'textColorActive' => '#ff2d4b',
			),
			'data' => array (
				'M0123456789101' => array (
					'link' => 'pages/shop/index?sid=' . $sid ,
					'icon' => 'icon-home',
					'text' => '首页',
				),
				'M0123456789104' => array (
					'link' => 'pages/order/index',
					'icon' => 'icon-order',
					'text' => '订单',
				),
				'M0123456789105' => array (
					'link' => 'pages/member/mine',
					'icon' => 'icon-mine',
					'text' => '我的',
				),
			),
		);
	}
	return $menu;
}

function store_get_diypages($sid, $type = 0) {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);
	if(in_array($type, array(6, 7))) {
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	} else {
		$condition .= ' and type != :type';
		$params[':type'] = 'home';
	}
	$pages = pdo_fetchall("select * from " . tablename('tiny_wmall_store_page') . $condition, $params);
	return $pages;

}







