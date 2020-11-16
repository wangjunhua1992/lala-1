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
pload()->model('gohome');

function kanjia_cron() {
	global $_W;
	$key = "we7_wmall:{$_W['uniacid']}:kanjia:lock:120";
	if(check_cache_status($key, 120)) {
		return true;
	}
	gohome_goods_sync('kanjia');
	set_cache($key, array());
	return true;
}

function kanjia_get_categorys() {
	global $_W;
	$categorys = pdo_fetchall("select * from " . tablename('tiny_wmall_kanjia_category') . " where uniacid = :uniacid and agentid = :agentid order by displayorder desc", array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	if(!empty($categorys)) {
		foreach($categorys as &$cate) {
			$cate['thumb'] = tomedia($cate['thumb']);
			if(empty($cate['link'])) {
				$cate['link'] = "/gohome/pages/kanjia/category?cateid={$cate['id']}";
			}
		}
	}
	return $categorys;
}

function kanjia_get_cate($id) {
	global $_W;
	$category = pdo_get('tiny_wmall_kanjia_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(!empty($category)) {
		$category['thumb'] = tomedia($category['thumb']);
	}
	return $category;
}

function kanjia_status() {
	return array('下架中',  '进行中', '未开始', '已结束');
}

function kanjia_usestatus() {
	return array('到店消费', '快递上门', '同时支持快递与核销');
}

function kanjia_get_activity($id, $type = '') {
	global $_W;
	$where = array('uniacid' => $_W['uniacid'], 'id' => $id);
	$activity = pdo_get('tiny_wmall_kanjia', $where);
	if(!empty($activity)) {
		$update = array();
		if($activity['starttime'] > TIMESTAMP && $activity['status'] != 2) {
			$update = array('status' => 2);
		} elseif($activity['endtime'] <= TIMESTAMP && $activity['status'] != 3) {
			$update = array('status' => 3);
		}
		if(!empty($update)) {
			pdo_update('tiny_wmall_kanjia', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
			$activity['status'] = $update['status'];
		}
		$activity['thumb'] = tomedia($activity['thumb']);
		$status = kanjia_status();
		$activity['status_cn'] = $status[$activity['status']];
		$usestatus = kanjia_usestatus();
		$activity['usestatus_cn'] = $usestatus[$activity['usestatus']];
		$activity['thumbs'] = iunserializer($activity['thumbs']);
		if(!empty($activity['thumbs'])) {
			foreach($activity['thumbs'] as &$thu) {
				$thu = tomedia($thu);
			}
		}
		$activity['rules'] = iunserializer($activity['rules']);
		$activity['share'] = iunserializer($activity['share']);
		$activity['starttime_cn'] = date('Y-m-d H:i:s', $activity['starttime']);
		$activity['endtime_cn'] = date('Y-m-d H:i:s', $activity['endtime']);
		$activity['endtime'] = $activity['endtime'] + 0;
		$activity['total_looknum'] = $activity['falselooknum'] + $activity['looknum'];
		$activity['total_sharenum'] = $activity['falsesharenum'] + $activity['sharenum'];
		$float = array('oldprice', 'price', 'vipprice', 'submitmoneylimit');
		foreach($float as $val) {
			$activity[$val] = floatval($activity[$val]);
		}
		$activity['total_joinnum'] = $activity['falsejoinnum'] + $activity['sailed'];
		if($type == 'all') {
			$store = store_fetch($activity['sid'], array('agentid', 'title', 'telephone', 'address', 'location_x', 'location_y'));
			$activity['store'] = $store;
			$activity['is_favor'] = gohome_goods_favorite_check($activity['id'], 'kanjia');
		}
	}
	return $activity;
}

function kanjia_get_activitylist($filter = array()) {
	global $_W, $_GPC;
	if(empty($filter)) {
		$filter = $_GPC;
	}
	if(empty($filter['psize'])) {
		$filter['psize'] = $_GPC['psize'];
	}
	if(empty($filter['page'])) {
		$filter['page'] = $_GPC['page'];
	}
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	if($_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	$keyword = trim($filter['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and name like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$sid = intval($filter['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$cateid = intval($filter['cateid']);
	if($cateid > 0) {
		$condition .= ' and cateid = :cateid';
		$params[':cateid'] = $cateid;
	}
	$status = isset($filter['status']) ? intval($filter['status']) : '-1';
	if($status > -1) {
		$condition .= " and status = :status";
		$params[':status'] = $status;
	} else {
		$condition .= " and status > 0 ";
	}
	if(!empty($filter['ids']) && is_array($filter['ids'])) {
		$ids_str = implode(',', $filter['ids']);
		$condition .= " and id in ({$ids_str})";
	}
	$page = max(1, intval($filter['page']));
	$psize = intval($filter['psize']) ? intval($filter['psize']) : 15;
	$activitys = pdo_fetchall('select * from ' . tablename('tiny_wmall_kanjia') . $condition . ' order by status asc, malldisplayorder desc, displayorder desc, id asc limit ' . ($page - 1) * $psize . ',' . $psize, $params);
	if(!empty($activitys)) {
		$store = store_fetchall(array('id', 'title'));
		$status = kanjia_status();
		$usestatus = kanjia_usestatus();
		$float = array('oldprice', 'price', 'vipprice', 'submitmoneylimit');
		foreach($activitys as &$val) {
			$val['store'] = $store[$val['sid']];
			$val['thumb'] = tomedia($val['thumb']);
			$val['status_cn'] = $status[$val['status']];
			$val['usestatus_cn'] = $usestatus[$val['usestatus']];
			$val['thumbs'] = iunserializer($val['thumbs']);
			$val['rules'] = iunserializer($val['rules']);
			$val['share'] = iunserializer($val['share']);
			$val['starttime_cn'] = date('m-d H:i', $val['starttime']);
			$val['endtime_cn'] = date('m-d H:i', $val['endtime']);
			if($val['oldprice'] > 0) {
				$val['discount'] = round($val['price'] / $val['oldprice'] * 10, 1);
			} else {
				$val['discount'] = 10;
			}
			foreach($float as $flo) {
				$val[$flo] = floatval($val[$flo]);
			}
			if($val['status'] == 1 && $val['endtime'] < TIMESTAMP) {
				$val['status'] = 3;
			}
			$val['userlist'] = kanjia_get_userlist(array('activityid' => $val['id']));
			$val['falesailed_total'] = $val['falsejoinnum'] + $val['sailed'];
			if($val['total'] != -1) {
				$orgin_total = $val['sailed'] + $val['total'];
				$val['sailed_percent'] = ($val['falesailed_total'] > $orgin_total) ? round($val['sailed'] / $orgin_total * 100, 1) : round(($val['falesailed_total']) / $orgin_total * 100, 1);
			}
		}
	}
	return $activitys;
}

function kanjia_member_takeinfo($activityid, $uid = 0) {
	global $_W;
	$activity = kanjia_get_activity($activityid);
	if(empty($activity)) {
		return false;
	}
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$condition = ' where a.uniacid = :uniacid and a.activityid = :activityid and a.uid = :uid and a.status = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':activityid' => $activityid,
		':uid' => $uid
	);
	$data = pdo_fetch('select a.*, b.nickname, b.avatar from ' . tablename('tiny_wmall_kanjia_userlist') . " as a left join " . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition}", $params);
	if(!empty($data)) {
		$data['price'] = floatval($data['price']);
		$data['createtime_cn'] = date('Y-m-d H:i:s', $data['createtime']);
		$data['per_price'] = ($activity['oldprice'] - $data['price']) / ($activity['oldprice'] - $activity['price']) *100;
		$helper = kanjia_get_helper($data['id']);
		$has_bargain = 0;
		if(!empty($helper)) {
			foreach ($helper as $val) {
				$has_bargain += $val['bargainprice'];
			}
		}
		$data['now_price_buy'] = 0;
		if($activity['submitmoneylimit'] > 0) {
			if($data['price'] <= $activity['submitmoneylimit'] && $data['price'] > $activity['price']) {
				$data['now_price_buy'] = 1;
			}
		} else {
			$data['now_price_buy'] = 1;
		}
		$data['has_bargain'] = $has_bargain;
		$data['still_bargain'] = $data['price'] - $activity['price'];
		$data['helper'] = $helper;
	}
	return $data;
}

function kanjia_get_helper($userid, $orderby = '') {
	global $_W;
	if(empty($userid)) {
		return false;
	}
	$condition = ' where a.uniacid = :uniacid and userid = :userid ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':userid' => $userid
	);
	$orderby_sql = ' order by id desc';
	if(!empty($orderby)) {
		$orderby_sql = " order by {$orderby}";
	}
	$data = pdo_fetchall('select a.*, b.nickname, b.avatar from ' . tablename('tiny_wmall_kanjia_helprecord') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition} {$orderby_sql}", $params);
	if(!empty($data)) {
		foreach($data as &$val) {
			$val['createtime_cn'] = date('Y-m-d H:i:s', $val['createtime']);
		}
	}
	return $data;
}

function kanjia_bargain_check($activityid, $uid) {
	global $_W;
	$activity = kanjia_get_activity($activityid);
	if(empty($activity)) {
		return error(-1, '活动不存在或已删除');
	}
	if($activity['status'] != 1) {
		return error(-1, '活动已失效');
	}
	if(TIMESTAMP < $activity['starttime']) {
		return error(-1, '活动未开始');
	}
	if(TIMESTAMP > $activity['endtime']) {
		return error(-1, '活动已结束');
	}
	$takeinfo = kanjia_member_takeinfo($activityid, $uid);
	if(empty($takeinfo)) {
		return error(-1, '该用户创建的该商品的砍价活动不存在');
	}
	//检测是否已帮好友砍价该活动商品
	$times =  pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_kanjia_helprecord') . ' where uniacid = :uniacid and activityid = :activityid and userid = :userid and authorid = :authorid and uid = :uid', array(':uniacid' => $_W['uniacid'], ':activityid' => $activityid, ':userid' => $takeinfo['id'], ':authorid' => $takeinfo['uid'], ':uid' => $_W['member']['uid']));
	if($times > 0) {
		return error(-1, '您已为好友进行过砍价，请勿重复');
	}
	//针对该活动，一个好友可以帮助几个人砍价
	if($activity['helplimit'] > 0) {
		$records = pdo_fetchall('select authorid from ' . tablename('tiny_wmall_kanjia_helprecord') . ' where uniacid = :uniacid and activityid = :activityid and uid = :uid group by authorid ', array(':uniacid' => $_W['uniacid'], ':activityid' => $activityid, ':uid' => $_W['member']['uid']));
		$helptimes = count($records);
		if($helptimes >= $activity['helplimit']) {
			return error(-1, '您针对该商品的可帮好友数量已达上限');
		}
	}
	//针对该商品该好友日帮砍次数检测
	if($activity['dayhelplimit'] > 0) {
		$starttime = strtotime(date("Y-m-d"), TIMESTAMP);
		$endtime = $starttime + 86400;
		$daytimes = pdo_fetchcolumn("select count(*) from " . tablename('tiny_wmall_kanjia_helprecord') . " where uniacid = :uniacid and activityid = :activityid and userid = :userid and authorid = :authorid and createtime >= {$starttime} and createtime < {$endtime}", array(':uniacid' => $_W['uniacid'], ':activityid' => $activityid, ':userid' => $takeinfo['id'], ':authorid' => $takeinfo['uid']));
		if($daytimes >= $activity['dayhelplimit']) {
			return error(-1, '好友该商品的单日帮砍次数已达上限');
		}
	}
	//判断砍价进度是否已完成
	if($activity['price'] == $takeinfo['price']) {
		return error(-1, '该商品已砍至活动底价');
	}
	//确定砍价金额
	$nowprice = $takeinfo['price'];
	$rules = array_sort($activity['rules'], 'range', SORT_DESC);
	$range_start = 0.5;
	$range_end = 1;
	if(!empty($rules)) {
		$length = count($rules);
		if($nowprice > $rules[$length -1]['range']) {
			for($i = 0; $i < $length; $i++) {
				if($nowprice > $rules[$i]['range']) {
					$range_start = $rules[$i]['range_start'];
					$range_end = $rules[$i]['range_end'];
					break;
				} else {
					continue;
				}
			}
		}
	}
	$bargainprice = round($range_start + mt_rand()/mt_getrandmax() * ($range_end - $range_start), 2);
	if($takeinfo['price'] - $bargainprice <= $activity['price']) {
		$bargainprice = $takeinfo['price'] - $activity['price'];
	}
	return error(0, array('activity' => $activity, 'takeinfo' => $takeinfo, 'bargainprice' => $bargainprice));
}

function kanjia_order_update($orderOrId, $type, $extra = array()) {
	global $_W;
	$order = $orderOrId;
	if(!is_array($order)) {
		$order = gohome_order_fetch($order);
	}
	if(empty($order)) {
		return error(-1, '订单不存在！');
	}
	if($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array(
			'is_pay' => 1,
			'status' => 3,
			'order_channel' => $extra['channel'],
			'pay_type' => $extra['type'],
			'final_fee' => $extra['card_fee'],
			'paytime' => TIMESTAMP,
			'transaction_id' => $extra['transaction_id'],
			'out_trade_no' => $extra['uniontid'],
		);
		pdo_update('tiny_wmall_gohome_order', $update, array('id' => $order['id']));
		//减库存
		pdo_query('update ' . tablename('tiny_wmall_kanjia') . " set sailed = sailed + {$order['num']} where uniacid = :uniacid and id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $order['goods_id']));
		gohome_goods_total_update($order, 1);
		//模板消息通知
		//通知店员
		gohome_order_clerk_notice($order['id'], 'pay');
		//打印订单
		gohome_order_print($order['id']);
		return error(0, '支付成功');
	} elseif($type == 'status') {
		if($order['is_pay'] == 1 && $extra['status'] > 2 && $extra['status'] != 5) {
			pdo_update('tiny_wmall_gohome_order', array('status' => $extra['status']), array('uniacid' =>$_W['uniacid'], 'id' => $order['id']));
			return error(0, '状态更新成功');
		}
	} elseif($type == 'confirm') {
		if($order['is_pay'] != 1 || $order['status'] == 1) {
			return error(-1, '该订单未支付');
		}
		if($order['status'] == 5) {
			return error(-1, '订单已核销');
		} elseif($order['status'] == 6) {
			return error(-1, '该订单已完成');
		} elseif($order['status'] == 7) {
			return error(-1, '该订单已取消');
		}
		pdo_update('tiny_wmall_gohome_order', array('status' => 5, 'applytime' => TIMESTAMP), array('uniacid' =>$_W['uniacid'], 'id' => $order['id']));
		//商户打款
		if($order['is_pay'] == 1) {
			if(in_array($order['pay_type'], array('wechat', 'alipay', 'credit', 'peerpay', 'qianfan', 'majia', 'eleme', 'meituan'))) {
				mload()->model('store');
				store_update_account($order['sid'], $order['store_final_fee'], 8, $order['id']);
			}
			if($order['agentid'] > 0) {
				$remark = '';
				mload()->model('agent');
				agent_update_account($order['agentid'], $order['agent_final_fee'], 8, $order['id'], $remark, 'gohome');
			}
		}
		if(check_plugin_perm('spread')) {
			pload()->model('spread');
			spread_order_balance($order['id'], 'gohome');
		}
		//订单完成后赠送积分
		$credit1_config = get_plugin_config('gohome.credit.credit1');
		if(!empty($credit1_config) && $credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if($credit1 > 0) {
					mload()->model('member');
					$result = member_credit_update($order['uid'], 'credit1', $credit1, array(0, "生活圈砍价订单完成, 赠送{$credit1}积分"));
					if(is_error($result)) {
						slog('credit1Update', "生活圈砍价下单送积分-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit1') ,$result['message']);
					}
				}
			}
		}
		gohome_order_clerk_notice($order['id'], 'confirm');
		return error(0, '核销成功');
	} elseif($type == 'cancel') {
		if($order['status'] == 5) {
			return error(-1, '订单处于待评价状态, 无法取消订单');
		} elseif($order['status'] == 6) {
			return error(-1, '订单已完成, 无法取消订单');
		} elseif($order['status'] == 7) {
			return error(-1, '订单已取消, 无法取消订单');
		}
		if($order['is_pay'] == 0) {
			pdo_update('tiny_wmall_gohome_order', array('status' => 7), array('uniacid' =>$_W['uniacid'], 'id' => $order['id']));
			return error(0, '取消成功');
		} else {
			//已支付，发起退款申请
			$update = array(
				'status' => 7,
				'refund_status' => 1,
				'refund_out_no' => date('YmdHis') . random(10, true),
				'refund_apply_time' => TIMESTAMP,
			);
			pdo_update('tiny_wmall_gohome_order', $update, array('uniacid' =>$_W['uniacid'], 'id' => $order['id']));
			$refund_result = gohome_order_begin_refund($order['id']);
			if(is_error($refund_result)) {
				gohome_order_refund_notice($order['id'], 'fail', "失败原因: {$refund_result['message']}");
			} else {
				gohome_order_refund_notice($order['id'], 'success');
				gohome_order_status_notice($order, 'cancel');
			}
			return error(0, array('is_refund' => 1, 'refund_message' => $refund_result['message'], 'refund_code' => $refund_result['errno']));
		}
	} elseif($type == 'end') {
		if($order['status'] == 1) {
			return error(-1, '该订单未支付');
		} elseif($order['status'] == 2) {
			return error(-1, '该订单待生效');
		} elseif($order['status'] == 5) {
			return error(-1, '该订单已核销，待评价');
		} elseif($order['status'] == 6) {
			return error(-1, '该订单已完成');
		} elseif($order['status'] == 7) {
			return error(-1, '该订单已取消');
		}
		pdo_update('tiny_wmall_gohome_order', array('status' => 5), array('uniacid' =>$_W['uniacid'], 'id' => $order['id']));
		//商户打款
		if($order['is_pay'] == 1) {
			if(in_array($order['pay_type'], array('wechat', 'alipay', 'credit', 'peerpay', 'qianfan', 'majia', 'eleme', 'meituan'))) {
				mload()->model('store');
				store_update_account($order['sid'], $order['store_final_fee'], 8, $order['id']);
			}
			if($order['agentid'] > 0) {
				$remark = '';
				mload()->model('agent');
				agent_update_account($order['agentid'], $order['agent_final_fee'], 8, $order['id'], $remark, 'gohome');
			}
		}
		if(check_plugin_perm('spread')) {
			pload()->model('spread');
			spread_order_balance($order['id'], 'gohome');
		}
		//订单完成后赠送积分
		$credit1_config = get_plugin_config('gohome.credit.credit1');
		if(!empty($credit1_config) && $credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if($credit1 > 0) {
					mload()->model('member');
					$result = member_credit_update($order['uid'], 'credit1', $credit1, array(0, "生活圈砍价订单完成, 赠送{$credit1}积分"));
					if(is_error($result)) {
						slog('credit1Update', "生活圈砍价下单送积分-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit1') ,$result['message']);
					}
				}
			}
		}
		return error(0, '订单成功设为已处理');
	}
}

function kanjia_get_userlist($filter = array()) {
	global $_W;
	$condition = ' where a.uniacid = :uniacid and a.activityid = :activityid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':activityid' => intval($filter['activityid'])
	);
	$sid = intval($filter['sid']);
	if($sid > 0) {
		$condition .= ' and a.sid = :sid';
		$params[':sid'] = $sid;
	}
	$status = intval($filter['status']);
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}
	$records = pdo_fetchall('select b.nickname, b.avatar from ' . tablename('tiny_wmall_kanjia_userlist') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition} group by a.uid", $params);
	return $records;
}

function kanjia_check_activity_order_num($activityOrId) {
	global $_W;
	$activity = $activityOrId;
	if(!is_array($activity)) {
		$activity = kanjia_get_activity($activity);
	}
	if(empty($activity)) {
		return error(-1, '活动不存在或已删除');
	}
	$joinlimit = $activity['joinlimit'];
	$totaljoin = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_gohome_order') . ' where uniacid = :uniacid and goods_id = :goods_id and is_pay = 1 and status > 1 and status < 7', array(':uniacid' => $_W['uniacid'], ':goods_id' => $activity['id']));
	if(!empty($joinlimit) && $totaljoin >= $joinlimit) {
		return error(-1, '该活动参与人数已达上限');
	}
	return error(0, '');
}

function gohoem_kanjia_record_fetchall($filter = array()){
	global $_W, $_GPC;
	if(empty($filter)) {
		$filter = $_GPC;
	}
	$condition = "where a.uniacid = :uniacid";
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$uid = intval($filter['uid']);
	if($uid > 0) {
		$condition .= " and a.uid = :uid";
		$params[':uid'] = $uid;
	}
	$status = isset($filter['status']) ? intval($filter['status']) : -1;
	if($status > -1) {
		$condition .= " and a.status = :status";
		$params[':status'] = $status;
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT a.*,b.name,b.price as new_price,b.oldprice as old_price,b.starttime,b.endtime,b.thumb, b.status as goods_status, b.total, b.submitmoneylimit FROM ' . tablename('tiny_wmall_kanjia_userlist') . ' as a left join ' . tablename('tiny_wmall_kanjia') . " as b on a.activityid = b.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['per_price'] = ($val['old_price'] - $val['price']) / ($val['old_price'] - $val['new_price']) *100;
			$val['thumb'] = tomedia($val['thumb']);
			$val['endtime'] = $val['endtime'] + 0;
			$val['endtime_cn'] = date('Y-m-d H:i:s', $val['endtime']);
			if($val['goods_status'] == 1 && $val['endtime'] < TIMESTAMP) {
				$val['goods_status'] = 3;
			}
		}
	}
	return $records;
}
