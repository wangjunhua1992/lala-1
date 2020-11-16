<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function queue_add($data) {
	global $_W;
	$is_exist = pdo_get('tiny_wmall_queue', array('uniacid' => $_W['uniacid'], 'type' => $data['type'], 'order_id' => $data['order_id']), array('id'));
	if($is_exist) {
		return error(-1, '队列已存在');
	}
	$update = array(
		'uniacid' => $_W['uniacid'],
		'type' => $data['type'],
		'addtime' => TIMESTAMP,
		'order_id' => $data['order_id'],
		'data' => iserializer($data['data']),
	);
	pdo_insert('tiny_wmall_queue', $update);
	return true;
}

function queue_update($type = '') {
	global $_W;
	$condition = ' where uniacid => :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	if(!empty($type)) {
		$condition .= ' and type => :type';
		$params[':type'] = $type;
	}
	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_queue') . $condition . ' order by id desc limit 5');
	if(empty($records)) {
		return true;
	}
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	mload()->model('order');
	foreach($records as $record) {
		$order = order_fetch($record['order_id']);
		if(empty($order)) {
			return error(-1, '订单不存在或已删除');
		}
		//更新配送员账户
		mload()->model('deliveryer');
		deliveryer_order_num_update($order['deliveryer_id']);
		if($order['delivery_type'] == 2) {
			if($order['plateform_deliveryer_fee'] > 0) {
				deliveryer_update_credit2($order['deliveryer_id'], $order['plateform_deliveryer_fee'], 1, $order['id']);
			}
			if($order['pay_type'] == 'delivery') {
				$note = "{$order['id']}属于货到支付单,您线下收取客户{$order['final_fee']}{$_W['Lang']['dollarSignCn']},平台从您的账户扣除该费用";
				deliveryer_update_credit2($order['deliveryer_id'], -$order['final_fee'], 3, $order['id'], $note);
			}
		}
		//更新商家账户
		if($order['is_pay'] == 1) {
			mload()->model('store');
			if(in_array($order['pay_type'], array('wechat', 'alipay', 'credit', 'peerpay', 'qianfan', 'majia', 'eleme', 'meituan')) || ($order['delivery_type'] == 2 && $order['pay_type'] == 'delivery')) {
				store_update_account($order['sid'], $order['store_final_fee'], 1, $order['id']);
			} else {
				$remark = "编号为{$order['id']}的订单属于线下支付,平台需要扣除{$order['plateform_serve_fee']}{$_W['Lang']['dollarSignCn']}服务费";
				store_update_account($order['sid'], -$order['plateform_serve_fee'], 3, $order['id'], $remark);
			}
			if($order['agentid'] > 0) {
				mload()->model('agent');
				$remark = '';
				agent_update_account($order['agentid'], $order['agent_final_fee'], 1, $order['id'], $remark, 'takeout');
			}
		}
		pdo_query('UPDATE ' . tablename('tiny_wmall_store') . " set sailed = sailed + 1 WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $order['sid']));

		//订单完成对客户赠送积分
		$credit1_config = $config_takeout['grant_credit']['credit1'];
		if($credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if($credit1 > 0) {
					mload()->model('member');
					$result = member_credit_update($order['uid'], 'credit1', $credit1, array(0, "外送模块订单完成, 赠送{$credit1}积分"));
					if(is_error($result)) {
						slog('credit1Update', "下单送积分-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit1') ,$result['message']);
					}
				}
			}
		}
		//赠送余额
		$cash_grant = order_fetch_discount($order['id'], 'cashGrant');
		if(!empty($cash_grant) && $cash_grant['fee'] > 0) {
			mload()->model('member');
			$result = member_credit_update($order['uid'], 'credit2', $cash_grant['fee'], array(0, "外送模块订单完成, 赠送{$cash_grant['fee']}{$_W['Lang']['dollarSignCn']}"), true);
			if(is_error($result)) {
				slog('credit2Update', "下单返余额-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid'], 'credit_type' => 'credit2') ,$result['message']);
			}
		}
		//赠送优惠券
		$result = order_coupon_grant($order['id']);
		if(is_error($result)) {
			slog('couponGrant', "满赠优惠券-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid']) ,$result['message']);
		}
		mload()->model('plugin');
		//赠送红包
		if($order['mall_first_order'] == 1 && check_plugin_perm('shareRedpacket')) {
			pload()->model('shareRedpacket');
			$result = shareRedpacket_sharer_grant($order['uid']);
			if(is_error($result)) {
				slog('shareRedpacket', "分享赠送红包-order_id:{$order['id']}", array('order_id' => $order['id'], 'uid' => $order['uid']) ,$result['message']);
			}
		}
		if(check_plugin_perm('ordergrant')) {
			pload()->model('ordergrant');
			ordergrant_grant($order['id']);
		}
		if(check_plugin_perm('spread')) {
			pload()->model('spread');
			member_spread_confirm($order['id']);
			spread_order_balance($order['id']);
		}
		pdo_delete('tiny_wmall_queue', array('uniacid' => $_W['uniacid'], 'id' => $record['id']));
	}
	return true;
}