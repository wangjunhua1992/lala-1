<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 17-12-6
 * Time: 下午5:29
 */

defined('IN_IA') or exit('Access Denied');
pload()->classs('dada');
class subscribe extends DaDa{
	public $notice = '';

	public function buildSign($param) {
		$params = array(
			'client_id' => $param['client_id'],
			'order_id' => $param['order_id'],
			'update_time' => $param['update_time']
		);
		$str = $params['client_id'] . $params['update_time'] . $params['order_id'];
		$sign = md5($str);
		return $sign;
	}

	public function checkSign($param) {
		$signature = $param["signature"];
		unset($param["signature"]);
		if ($signature != $this->buildSign($param)) {
			return false;
		}
		return true;
	}

	public function start() {
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			$postStr = file_get_contents('php://input');
			//file_put_contents(MODULE_ROOT . '/dd'.$postStr['order_status'].'txt', var_export($postStr, 1));
			$postStr = json_decode($postStr, true);
			if(!$this->checkSign($postStr)) {
				exit('Check Sign Fail.');
			}
			$this->notice = $postStr;
			$this->parse();
		}
	}

	public function parse(){
		global $_W;
		$ordersn = $this->notice['order_id'];
		$order = pdo_get('tiny_wmall_order', array('ordersn' => $ordersn), array('id', 'sid'));
		if(empty($order)) {
			exit('order is not exit');
		}
		$statusDd = $this->notice['order_status'];
		flog('回调参数', $this->notice, 'dada');
		if($statusDd == 2) {
			//待取货
			$deliveryer = array(
				'id' => 0,
				'title' => $this->notice['dm_name'],
				'mobile' => $this->notice['dm_mobile'],
			);
			order_deliveryer_update_status($order['id'], 'delivery_assign', array('role' => 'dada', 'deliveryer' => $deliveryer));
		}elseif($statusDd == 3 || $statusDd == 8) {
			//已取货，配送中
			order_deliveryer_update_status($order['id'], 'delivery_takegoods', array('role' => 'dada'));
		}elseif($statusDd == 4) {
			//已完成
			order_status_update($order['id'], 'end', array('role' => 'dada'));
		}elseif($statusDd == 5) {
			//已取消 达达取消订单，平台将该订单重新放入待抢订单
			order_status_update($order['id'], 'notify_deliveryer_collect', array('force' => 1, 'channel' => 're_notify_deliveryer_collect', 'role' => 'dada'));
		}elseif($statusDd == 7) {

		}elseif($statusDd == 9) {

		}elseif($statusDd == 10) {

		}elseif($statusDd == 1000) {

		}
		echo '{"message":"ok"}';
		die;
	}
}