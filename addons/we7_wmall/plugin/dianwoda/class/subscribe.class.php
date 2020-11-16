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
pload()->classs('dianwoda');
class subscribe extends dianwoda{
	public $type = '';
	public $data = array();

	public function start($get, $data, $originBody) {
		global $_W;
		$this->type = $get['type'];
		$this->data = $data;
		//校验签名是否正确
		$common = array(
			'timestamp' => $get['timestamp'],
			'nonce' => $get['nonce'],
			'type' => $get['type'],
		);

		$sign = $this->buildSign($common, $originBody);
		if(!empty($get['sign']) && $sign != $get['sign']) {
			slog('dianwoda', '点我达错误', array('ordersn' => $data['content']['order_original_id']), "订单回调签名验证失败");
			exit('Sign error');
		}
		$this->parse();
	}

	public function parse(){
		global $_W;
		if($this->type == 'dianwoda.order.status-update') {
			$content = $this->data['content'];
			$ordersn = $content['order_original_id'];
			if(strexists($ordersn, '_')) {
				$snArr = explode('_', $ordersn);
				$ordersn = $snArr[1];
			}
			$order = pdo_get('tiny_wmall_order', array('ordersn' => $ordersn), array('id', 'sid'));
			if(empty($order)) {
				exit('order is not exit');
			}
			$orderStatus = $content['order_status'];
			switch ($orderStatus) {
				case 'created':		//骑手转单

					break;
				case 'dispatched':	//骑手接单
					$deliveryer = array(
						'id' => $content['rider_code'],
						'title' => $content['rider_name'],
						'mobile' => $content['rider_mobile'],
					);
					$status = order_deliveryer_update_status($order['id'], 'delivery_assign', array('role' => 'dianwoda', 'deliveryer' => $deliveryer));
					break;
				case 'arrived':		//骑手到店
					$status = order_deliveryer_update_status($order['id'], 'delivery_instore', array('role' => 'dianwoda'));
					break;
				case 'obtained':	//骑手离店
					$status = order_deliveryer_update_status($order['id'], 'delivery_takegoods', array('role' => 'dianwoda'));
					break;
				case 'completed':	//货品送达
					$status = order_status_update($order['id'], 'end', array('role' => 'dianwoda'));
					break;
				case 'abnormal':	//签收异常

					break;
				case 'canceled':	//取消订单
					//点我达取消订单 将订单重新放入待抢订单列表
					$status = order_status_update($order['id'], 'notify_deliveryer_collect', array('force' => 1, 'channel' => 're_notify_deliveryer_collect', 'role' => 'dianwoda'));
					break;
			}
			if(is_error($status)) {
				slog('dianwoda', '点我达错误', array('order_id' => $order['id']), "订单状态{$orderStatus}回调错误：{$status['message']}");
			}
		}
		return true;
	}
}