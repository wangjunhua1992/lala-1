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
pload()->classs('shansong');
class subscribe extends shanSong{
	public $notice = '';

	public function checkSign($param) {
		$signature = $param["signature"];
		if ($signature != $this->buildSign($param)) {
			return false;
		}
		return true;
	}

	public function start($data) {
		$this->notice = $data;
		if(!$this->checkSign($this->notice)) {
			exit('Check Sign Fail.');
		}
		$this->parse();
	}

	public function parse(){
		$ordersn = $this->notice['orderno'];
		$order = pdo_get('tiny_wmall_order', array('ordersn' => $ordersn), array('id', 'sid'));
		if(empty($order)) {
			exit('order is not exit');
		}
		mload()->model('order');
		$statusDd = $this->notice['statuscode'];
		if($statusDd == 30) {
			//已抢单
			$deliveryer = array(
				'id' => 0,
				'title' => $this->notice['couriername'],
				'mobile' => $this->notice['couriermobile'],
			);
			order_deliveryer_update_status($order['id'], 'delivery_assign', array('role' => 'shansong', 'deliveryer' => $deliveryer));
		} elseif($statusDd == 42) {
			//到店
			order_deliveryer_update_status($order['id'], 'delivery_instore', array('role' => 'shansong'));
		}  elseif($statusDd == 44) {
			//已取货，配送中
			order_deliveryer_update_status($order['id'], 'delivery_takegoods', array('role' => 'shansong'));
		} elseif($statusDd == 60) {
			//已完成
			order_status_update($order['id'], 'end', array('role' => 'shansong'));
		} elseif($statusDd == 64) {
			//已取消 闪送取消订单，平台将该订单重新放入待抢订单
			order_status_update($order['id'], 'notify_deliveryer_collect', array('force' => 1, 'channel' => 're_notify_deliveryer_collect', 'role' => 'shansong'));
		}
		echo '{"message":"ok"}';
		die;
	}
}