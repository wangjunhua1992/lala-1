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
pload()->classs('uu');
class subscribe{
	public $notice = array();
	public $order = array();

	public function start($data) {
		$this->notice = $data;
		$order_id= $this->notice['origin_id'];
		$this->order = pdo_get('tiny_wmall_order', array('ordersn' => $order_id), array('id', 'sid'));
		if(empty($this->order)) {
			exit('order is not exist');
		}
		$checksign = $this->buildSign($data);
		if($checksign != $data['sign']) {
			exit('Check Sign Fail.');
		}
		$this->parse();
	}

	public function buildSign($params) {
		unset($params['sign']);
		ksort($params);
		$arr = array();
		foreach ($params as $key => $value) {
			if(!empty($value)) {
				$arr[] = $key.'='.$value;
			}
		}
		mload()->model('store');
		$config_uupaotui = store_get_data($this->order['sid'], 'uupaotui');
		$arr[] = 'key='.$config_uupaotui['appkey'];
		$str = strtoupper(implode('&', $arr));
		$sign = strtoupper(md5($str));
		return $sign;
	}

	public function parse(){
		global $_W;
		mload()->model('order');
		$order = $this->order;
		$statusDd = $this->notice['state'];
		if($statusDd == 3) {
			//跑男抢单
			$deliveryer = array(
				'id' => 0,
				'title' => $this->notice['driver_name'],
				'mobile' => $this->notice['driver_mobile'],
			);
			order_deliveryer_update_status($order['id'], 'delivery_assign', array('role' => 'uupaotui', 'deliveryer' => $deliveryer));
			//order_deliveryer_update_status($order['id'], 'delivery_assign', array('role' => 'dada', 'deliveryer' => $deliveryer));
		} elseif($statusDd == 4) {
			//已到达
			order_deliveryer_update_status($order['id'], 'delivery_instore', array('role' => 'uupaotui'));
		}elseif($statusDd == 5) {
			//已取货，配送中
			order_deliveryer_update_status($order['id'], 'delivery_takegoods', array('role' => 'uupaotui'));
		} elseif($statusDd == 10) {
			//已完成
			order_status_update($order['id'], 'end', array('role' => 'uupaotui'));
		} elseif($statusDd == -1) {
			//已取消 UU跑腿取消订单，平台将该订单重新放入待抢订单
			order_status_update($order['id'], 'notify_deliveryer_collect', array('force' => 1, 'channel' => 're_notify_deliveryer_collect', 'role' => 'uupaotui'));
		}elseif($statusDd == 6) {
			//到达目的地

		}elseif($statusDd == 1) {
			//下单成功
			//order_status_update($order['id'], 'notify_deliveryer_collect', array('role' => 'uupaotui'));
		}elseif($statusDd == 9) {

		}elseif($statusDd == 10) {

		}elseif($statusDd == 1000) {

		}
		echo '{"message":"ok"}';
		die;
	}
}