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
load()->func('communication');

class shanSong{

	public $config = array();
	public $order = array();
	public $store= array();
	public function __construct($id = 0, $sid = 0) {
		mload()->model('common');
		$this->config = get_plugin_config('shansong');
		if($id > 0) {
			$this->order = order_fetch($id);
		}
		if($sid == 0) {
			$sid = $this->order['sid'];
		}
		if($sid > 0) {
			$this->store = store_fetch($sid, array('id', 'title', 'telephone', 'address', 'location_x', 'location_y', 'data'));
		}
		if($this->config['type'] == 'store' || $this->order['delivery_type'] == 1) {//店内配送订单不走平台账户
			$this->config = array_merge($this->config, $this->store['data']['shansong']);
		}
		$api_urls = array(
			'open' => 'http://open.ishansong.com/',
			'sandbox' => 'http://open.s.bingex.com/'
		);
		$this->api_url = $api_urls['open'];
	}

	public function buildParams() {
		global $_W;
		$order_data = $this->order['data'];
		$goods_title = '';
		$i = 0;
		foreach($order_data['cart'] as $goods) {
			if($i > 1) {
				break;
			}
			$goods_title .= "{$goods['title']} ";
			$i++;
		}
		$goods_title .= "等{$this->order['num']}件商品";
		$sender_name = $this->store['title'];
		if($this->config['type'] == 'plateform') {
			$sid = $this->store['id'];
			$sender_name = "{$sender_name}商户ID:{$sid}";
		}
		$params = array(
			'partnerNo' => $this->config['partnerNO'],
			//'signature' => $this->config['md5'],
			'order' => array(
				'orderNo' => $this->order['ordersn'],
				'merchant' => array(
					'id' => $this->config['merchantid'],
					'mobile' => $this->config['mobile'],
					'name' => $_W['we7_wmall']['config']['mall']['title'],//平台名称
					'token' => $this->config['token'],
				),
				'sender' => array(
					'mobile' => $this->store['telephone'],//,必须传手机号
					'name' => $sender_name,//平台结算处理固定商户id
					'city' => $this->config['city'],//
					'addr' => $this->store['address'],
					'addrDetail' => $this->store['address'],
					'lat' => $this->store['location_x'],
					'lng' => $this->store['location_y'],
				),
				'receiverList' => array(
					array(
						'mobile' => $this->order['mobile'],
						'name' => $this->order['username'],
						'city' => $this->config['city'],//
						'addr' => $this->order['address'],
						'addrDetail' => $this->order['address'],
						'lat' => $this->order['location_x'],
						'lng' => $this->order['location_y'],
					),
				),
				'goods' => $goods_title,//
				'weight' => 4,//3公里以内，5公斤以下，12
				'addition' => 0,
				'remark' => $this->order['note'],
			),
		);
		$params['signature'] = $sign = $this->buildSign();
		return $params;
	}

	public function buildQueryParams() {
		$sign = $this->buildSign();
		$params = array(
			'partnerno' => $this->config['partnerNO'],
			'signature' => $sign,
			'mobile' => $this->config['mobile'],
			'orderno' => $this->order['ordersn'],
			'issorderno' => $this->order['data']['shansong']['issorderno'],
		);
		$str = '';
		foreach($params as $key => $val) {
			$str .= "&{$key}={$val}";
		}
		$str = ltrim($str, '&');
		return $str;
	}

	public function buildSign($params = array()) {
		if(empty($params)) {
			$str = $this->config['partnerNO'] . $this->order['ordersn'] . $this->config['mobile'] . $this->config['md5'];
		} else {
			$str = $this->config['partnerNO'] . $params['orderno'] . $params['mobile'] . $this->config['md5'];
		}
		$sign = strtoupper(md5($str));
		return $sign;
	}

	public function httpPost($action, $params = '', $type = 'post') {
		if($type == 'post') {
			$params = json_encode($params);
		}
		$response = ihttp_request($this->api_url.$action, $params, array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "请求接口出错:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['status'] == 'ER') {
			return error(-1, "错误号：{$result['errCode']}: 错误详情：{$result['errMsg']}");
		}
		return $result['data'];
	}

	public function queryDeliveryFee() {
		if($this->config['status'] != 1) {
			return error(-1, "闪送功能未开启");
		}
		$params = $this->buildParams();
		$response = $this->httpPost('openapi/order/v3/calc', $params);
		if(is_error($response)) {
			return error(-1, "查询订单费用失败:{$response['message']}");
		}
		//$response: amount实际费用单位分 distance米 weight公斤 cutAmount优惠金额（单位分）
		$response['amount'] = $response['amount'] / 100;
		$order_data_shansong = $this->order['data']['shansong'];
		if(empty($order_data_shansong)) {
			$order_data_shansong = array();
		}
		$order_data_shansong['fee'] = $response['amount'];
		$id = $this->order['id'];
		set_order_data($id, 'shansong', $order_data_shansong);
		return $response;
	}

	public function addOrder() {
		if($this->config['status'] != 1) {
			return error(-1, "闪送功能未开启");
		}
		$params = $this->buildParams();
		$response = $this->httpPost('openapi/order/v3/save', $params);
		if(is_error($response)) {
			return error(-1, "推送订单失败:{$response['message']}");
		}
		//返回闪送订单号
		$id = $this->order['id'];
		$order_data_shansong = $this->order['data']['shansong'];
		if(empty($order_data_shansong)) {
			$order_data_shansong = array();
		}
		$order_data_shansong['status'] = 1;
		$order_data_shansong['issorderno'] = $response;
		set_order_data($id, 'shansong', $order_data_shansong);
		return $response;
	}

	public function queryOrderStatus() {
		$action = 'openapi/order/v3/info';
		$getdata = $this->buildQueryParams();
		$action .= "?{$getdata}";
		$response = $this->httpPost($action, '', 'get');
		// orderStatusTxt courier闪送员手机号 courierName pickupPassword取件密码 orderStatus订单状态码
		return $response;
	}

	public function cancelOrder() {
		$action = 'openapi/order/v3/cancel';
		$getdata = $this->buildQueryParams();
		$action .= "?{$getdata}";
		$response = $this->httpPost($action, '', 'get');
		if(is_error($response)) {
			return error(-1, "取消订单失败:{$response['message']}");
		}
		// isCharge是否扣费（0-否，1-是） amount扣费金额（单位：分）
		if($response['amount'] > 0) {
			$response['amount'] = $response['amount'] / 100;
			$order_data_shansong = $this->order['data']['shansong'];
			if(empty($order_data_shansong)) {
				$order_data_shansong = array();
			}
			$order_data_shansong['fee'] = $response['amount'];
			$id = $this->order['id'];
			set_order_data($id, 'shansong', $order_data_shansong);
		}
		return $response;
	}
}


