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

function zhunshibao_check($order) {
	global $_W;
	if($order['order_type'] != 1) {
		return error(-1, '不是外卖单');
	}
	if($order['delivery_time'] != '立即送出') {
		return error(-1, '非及时送达订单');
	}
	if($order['zhunshibao_status'] != 1) {
		return error(-1, '未购买准时宝');
	}
	$config = get_plugin_config('zhunshibao');
	$store = store_fetch($order['sid'], array('delivery_time', 'data'));
	if(is_error($store)) {
		return error(-1, '门店信息有误');
	}
	if(empty($store['delivery_time'])) {
		return error(-1, '门店未设置预计送达时间');
	}

	$overtime = $order['endtime'] - $order[$config['basic']['start_time']] - $store['delivery_time'] * 60;
	if($overtime > 0) {
		$message = '';
		$rule = array();
		$log = array();
		if(!empty($store['data']['zhunshibao']['rule'])) {
			$store['data']['zhunshibao']['rule'] = array_sort($store['data']['zhunshibao']['rule'], 'time', SORT_DESC);
			foreach($store['data']['zhunshibao']['rule'] as $val) {
				if($order['endtime'] > $order[$config['basic']['start_time']] + $store['delivery_time'] * 60 + $val['time'] * 60) {
					$rule = $val;
					break;
				}
			}
		}
		if(!empty($rule)) {
			$update = array(
				'zhunshibao_status' => 2,
				'zhunshibao_compensate' => 0
			);
			$over_min = floor($overtime / 60);
			$log['overtime'] = $over_min;
			$message .= "订单超时{$over_min}分钟，达到准时宝超时{$rule['time']}分钟赔付条件，";
			$order_final_fee = $order['final_fee'] - floatval($order['zhunshibao_price']);
			if($store['data']['zhunshibao']['fee_type'] == 1) {
				$log['compensate_type'] = 1;
				$log['compensate_type_cn'] = '固定金额';
				$log['compensate_value'] = "{$rule['fee']}";
				$log['compensate_fee'] = "{$_W['Lang']['dollarSign']}{$rule['fee']}";
				$log['compensate_fee_cn'] = "（固定金额 {$_W['Lang']['dollarSign']}{$rule['fee']}）";
				$update['zhunshibao_compensate'] = $rule['fee'];
				$message .= "赔付金额：{$_W['Lang']['dollarSign']}{$rule['fee']}，赔付方式：固定金额";
			} elseif($store['data']['zhunshibao']['fee_type'] == 2) {
				$log['compensate_type'] = 2;
				$log['compensate_type_cn'] = '支付比例';
				$log['compensate_value'] = "{$rule['fee']}";
				$update['zhunshibao_compensate'] = round($order_final_fee * $rule['fee'] / 100, 2);
				$log['compensate_fee'] = "{$_W['Lang']['dollarSign']}{$update['zhunshibao_compensate']}";
				$log['compensate_fee_cn'] = "（订单支付 {$_W['Lang']['dollarSign']}{$order_final_fee} * {$rule['fee']}%）";
				$message .= "赔付金额：{$log['compensate_fee']} =（{$_W['Lang']['dollarSign']}{$order_final_fee} * {$rule['fee']}%），赔付方式：支付比例";
			}
			$log['note'] = $message;
			$order['data']['zhunshibao'] = $log;
			$update['data'] = iserializer($order['data']);
		}
	}
	if(!empty($update)) {
		pdo_update('tiny_wmall_order', $update, array('id' => $order['id']));
	}
	return true;
}

function zhunshibao_update_status($orderOrId, $status) {
	global $_W;
	$order = $orderOrId;
	if(!is_array($order)) {
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $order), array('id', 'agentid', 'uid', 'ordersn', 'zhunshibao_compensate', 'zhunshibao_status'));
	}
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	if($order['zhunshibao_status'] == 0 || $order['zhunshibao_status'] == 1) {
		return error(-1, '不需要进行赔付');
	}
	if($order['zhunshibao_status'] != 2) {
		return error(-1, '订单超时赔付已处理');
	}
	$update = array('zhunshibao_status' => $status);
	if($status == 3) {
		$log = array(
			$order['uid'],
			"准时宝赔偿, 订单号:{$order['ordersn']}, 赔付金额:{$order['zhunshibao_compensate']}{$_W['Lang']['dollarSignCn']}",
			'we7_wmall'
		);
		mload()->model('member');
		member_credit_update($order['uid'], 'credit2', $order['zhunshibao_compensate'], $log);
		if($order['agentid'] > 0) {
			mload()->model('agent');
			$remark = "准时宝赔付{$order['zhunshibao_compensate']}{$_W['Lang']['dollarSignCn']},订单id：{$order['id']}";
			agent_update_account($order['agentid'], -$order['zhunshibao_compensate'], 3, $order['id'], $remark);
		}
	}
	pdo_update('tiny_wmall_order', $update, array('id' => $order['id']));
	return error(0, '订单超时处理成功');
}