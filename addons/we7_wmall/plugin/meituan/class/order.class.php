<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
pload()->classs('meituan');
class order extends meituan{
	public function getOrder($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('order/queryById', $params);
		return $data;
	}

	public function confirmOrderLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('order/confirm', $params);
		return $data;
	}

	//取消订单
	public function cancelOrderLite($id, $type, $remark = '') {
		$params = array(
			'orderId' => $id,
			'reasonCode' => '2007',
			'reason' => $remark,
		);
		$data = $this->httpPost('order/cancel', $params);
		return $data;
	}

	//配送方式是商家自配送时，需要调用此接口将配送信息同步到美团外卖。
	public function updateOrderDeliverying($id, $deliveryer = array()) {
		$params = array(
			'orderId' => $id,
			'courierName' => $deliveryer['title'],
			'courierPhone' => $deliveryer['mobile'],
		);
		$data = $this->httpPost('order/delivering', $params);
		return $data;
	}

	//配送方式是商家自配送时，需要调用此接口将配送信息同步到美团外卖。
	public function receivedOrderLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('order/delivered', $params);
		return $data;
	}

	//同意退单/同意取消单
	public function agreeRefundLite($id, $reason = '同意退款') {
		$params = array(
			'orderId' => $id,
			'reason' => $reason,
		);
		$data = $this->httpPost('order/agreeRefund', $params);
		return $data;
	}

	//不同意退单/不同意取消单 驳回用户发起的退款申请。用户申请退款被商家第一次拒绝后，用户可以申请第二次退款（同时客服会介入），消费者的第二次申请退款商家不可以拒绝，只能“接受”或者“不响应”（等待客服处理）
	public function disagreeRefundLite($id, $reason = '') {
		$params = array(
			'orderId' => $id,
			'reason' => $reason,
		);
		$data = $this->httpPost('order/rejectRefund', $params);
		return $data;
	}

	//每次收到隐私号降级通知，都需要将offset重设为0，重新调用此接口获取最新降级订单的用户真实手机号。该接口所查到的是前一天零点至今的隐私号降级且订单状态为未完成的订单，其他状态的订单不提供。
	public function batchPullPhoneNumber($offset = 0) {
		$params = array(
			'developerId' => $this->app['developerId'],
			'degradOffset' => $offset,
			'degradLimit' => 1000,
		);
		$data = $this->httpPost('order/batchPullPhoneNumber', $params);
		return $data;
	}


}