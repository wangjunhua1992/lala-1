<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
pload()->classs('eleme');
class order extends Eleme{
	public function getOrder($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.getOrder', $params);
		return $data;
	}

	public function confirmOrderLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.confirmOrderLite', $params);
		return $data;
	}

	//取消订单
	public function cancelOrderLite($id, $type, $remark = '') {
		$params = array(
			'orderId' => $id,
			'type' => $type,
			'remark' => $remark,
		);
		$data = $this->httpPost('eleme.order.cancelOrderLite', $params);
		return $data;
	}

	//同意退单/同意取消单
	public function agreeRefundLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.agreeRefundLite', $params);
		return $data;
	}

	//不同意退单/不同意取消单
	public function disagreeRefundLite($id, $reason = '') {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.disagreeRefundLite', $params);
		return $data;
	}
	//获取订单配送记录
	public function getDeliveryStateRecord($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.getDeliveryStateRecord', $params);
		return $data;
	}
	//订单确认送达
	public function receivedOrderLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.receivedOrderLite', $params);
		return $data;
	}

	//回复催单
	public function replyReminder($id, $type, $content = '') {
		$params = array(
			'remindId' => $id,
			'type' => $type,
			'content' => $content,
		);
		$data = $this->httpPost('eleme.order.replyReminder', $params);
		return $data;
	}

	//呼叫配送时查询配送价格, 若价格发生变动, 会抛出异常(error_code:DELIVERY_PRICE_RISE), 需再次调用重新呼叫
	public function callDelivery($id, $fee = 0) {
		$params = array(
			'orderId' => $id,
			'fee' => $fee,
		);
		$data = $this->httpPost('eleme.order.callDelivery', $params);
		return $data;
	}
	//取消呼叫配送
	public function cancelDelivery($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.cancelDelivery', $params);
		return $data;
	}
	//获取店铺未回复的催单
	public function getUnreplyReminders($shopId) {
		$params = array(
			'shopId' => $shopId,
		);
		$data = $this->httpPost('eleme.order.getUnreplyReminders', $params);
		return $data;
	}
	//查询店铺未处理订单
	public function getUnprocessOrders($shopId) {
		$params = array(
			'shopId' => $shopId,
		);
		$data = $this->httpPost('eleme.order.getUnprocessOrders', $params);
		return $data;
	}
	//配送异常或者物流拒单后选择自行配送(推荐)
	public function deliveryBySelfLite($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.deliveryBySelfLite', $params);
		return $data;
	}
	//配送异常或者物流拒单后选择不再配送
	public function noMoreDeliveryLite($shopId) {
		$params = array(
			'shopId' => $shopId,
		);
		$data = $this->httpPost('eleme.order.noMoreDeliveryLite', $params);
		return $data;
	}
	//获取订单退款信息
	public function getRefundOrder($id) {
		$params = array(
			'orderId' => $id,
		);
		$data = $this->httpPost('eleme.order.getRefundOrder', $params);
		return $data;
	}

}