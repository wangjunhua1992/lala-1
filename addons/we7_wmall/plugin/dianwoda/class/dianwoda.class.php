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

class dianwoda{

	public $development = 0;
	public $config = array();
	public $order = array();
	public $store = array();
	public $cityCode = 0;
	public $sellerId = 0;
	public $orderOriginalId = 0;

	public function __construct($id = 0, $sid = 0) {
		global $_W;
		mload()->model('common');
		mload()->model('order');
		mload()->model('store');
		$this->config = get_plugin_config('dianwoda');
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
			$this->config = array_merge($this->config, $this->store['data']['dianwoda']);
		}
		$this->sellerId = $this->config['merchantid'];
		$this->orderOriginalId = $this->order['ordersn'];
		if($this->development == 1) {
			$this->config = array(
				'appkey' => 't1000152',
				'appsecret' => '52863ed1228351ab92146eccbb0bf9f5',
				'accesstoken' => 'TEST2018-a444-4e50-b785-f48ba984bd9c',
				'merchantid' => 126,
				'cityCode' => 140100
			);
			$this->sellerId = $this->config['appkey'] . '_' . $this->config['merchantid'];
			$this->orderOriginalId = $this->config['appkey'] . '_' . $this->order['ordersn'];
		}
		$this->cityCode = $this->config['cityCode'];
		$api_urls = array(
			'https://open.dianwoda.com/gateway?', //生产接口
			'https://open-test.dianwoda.com/gateway?' //测试接口
		);
		$this->api_url = $api_urls[$this->development];
	}

	//毫秒级时间戳
	public function msectime() {
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		return $msectime;
	}

	public function buildQueryParams($params) {
		//common平台参数 body业务参数
		$common = array(
			'appkey' => $this->config['appkey'],
			'timestamp' => $this->msectime(),
			'nonce' => random(6, true),
			'api' => $params['api'],
		);
		if($params['access_token']) {
			$common['access_token'] = $params['access_token'];
		}
		$body = '';
		if(is_array($params['body'])) {
			$body = json_encode($params['body']);
		}
		$sign = $this->buildSign($common, $body);
		$allParams = $common;
		$allParams['sign'] = $sign;
		$str = '';
		foreach($allParams as $key => $val) {
			$str .= "&{$key}={$val}";
		}
		$str = ltrim($str, '&');
		return $str;
	}

	public function buildSign($common, $body) {
		global $_W;
		ksort($common);
		$str = '';
		foreach($common as $key => $val) {
			$str .= "&{$key}={$val}";
		}
		$str = ltrim($str, '&');
		$str .= '&body=' . $body . '&secret=' . $this->config['appsecret'];
		$str = sha1($str);
		return $str;
	}

	public function httpPost($params) {
		$paramsStr = $this->buildQueryParams($params);
		$url = $this->api_url . $paramsStr;
		$post = !empty($params['body']) ? json_encode($params['body']) : '';
		$response = ihttp_request($url, $post, array('Content-Type' => 'application/json'));
		if(is_error($response)) {
			return error('-2', "请求接口出错:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['code'] != 'success') {
			$code = $result['code'];
			$message = $result['message'];
			if(!empty($result['sub_code'])) {
				$code .= '--' . $result['sub_code'];
				$message .= '--' . $result['sub_message'];
			}
			return error(-1, "错误码：{$code}, 错误详情：{$message}");
		}
		if(empty($result['data'])) {
			return true;
		} else {
			return $result['data'];
		}
	}

	//获取点我达目前开放承接（已开启）的行政区划列表
	public function queryCityCodes() {
		$params = array(
			'api' => 'dianwoda.data.city.code',
			'access_token' => $this->config['accesstoken']
		);
		$result = $this->httpPost($params);
		return $result;
	}

	//订单运费预估
	public function orderCostEstimate() {
		if($this->config['status'] != 1 && $this->development == 0) {
			return error(-1, '平台未开启点我达功能');
		}
		$order = $this->order;
		$store = $this->store;
		$params = array(
			'api' => 'dianwoda.order.cost.estimate',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'city_code' => $this->cityCode,
				'seller_id' => $this->sellerId,
				'seller_name' => $store['title'],
				'seller_mobile' => $store['telephone'],
				'seller_address' => $store['address'],
				'seller_lat' => $store['location_x'],
				'seller_lng' => $store['location_y'],

				'consignee_address' => $order['address'] . (empty($order['number']) ? '' : "-{$order['number']}"),
				'consignee_lat' => $order['location_x'],
				'consignee_lng' => $order['location_y'],

				'cargo_type' => '04', //04食品
				'cargo_weight' => 0,
			)
		);
		$result = $this->httpPost($params);
		return $result;
	}

	//推送订单
	public function orderCreate() {
		if($this->config['status'] != 1 && $this->development == 0) {
			return error(-1, '平台未开启点我达功能');
		}
		$order = $this->order;
		$store = $this->store;
		$params = array(
			'api' => 'dianwoda.order.create',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId,
				'order_create_time' => $order['addtime'] * 1000,
				'order_remark' => $order['note'],
				'serial_id' => $order['serial_sn'],
				'order_price' => $order['final_fee'] * 100,
				'city_code' => $this->cityCode,
				'seller_id' => $this->sellerId,
				'seller_name' => $store['title'],
				'seller_mobile' => $store['telephone'],
				'seller_address' => $store['address'],
				'seller_lat' => $store['location_x'],
				'seller_lng' => $store['location_y'],
				'consignee_name' => $order['username'],
				'consignee_mobile' => $order['mobile'],
				'consignee_address' => $order['address'] . (empty($order['number']) ? '' : "-{$order['number']}"),
				'consignee_lat' => $order['location_x'],
				'consignee_lng' => $order['location_y'],
				'cargo_type' => '04', //04食品
				'cargo_weight' => 0,
				'cargo_num' => $order['num'],
				//'items' => array(), //商品信息
			)
		);
		$result = $this->httpPost($params);
		if(is_error($result)) {
			slog('dianwoda', '点我达错误', array('order_id' => $order['id']), "推送订单错误: {$result['message']}");
		}
		return $result;
	}

	//取消订单
	public function orderCancel() {
		if($this->config['status'] != 1 && $this->development == 0) {
			return error(-1, '平台未开启点我达功能');
		}
		$params = array(
			'api' => 'dianwoda.order.cancel',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId,
				'cancel_type' => 0,
				'cancel_reason' => '用户取消订单'
			)
		);
		$result = $this->httpPost($params);
		if(is_error($result)) {
			slog('dianwoda', '点我达错误', array('order_id' => $this->order['id']), "取消订单错误: {$result['message']}");
			return $result;
		}
		return error(0, '取消订单成功');
	}

	//获取订单信息
	public function orderQuery() {
		$params = array(
			'api' => 'dianwoda.order.query',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId
			)
		);
		$result = $this->httpPost($params);
		return $result;
	}

	//商家确认已出餐
	public function orderMealDone() {
		$params = array(
			'api' => 'dianwoda.order.mealdone',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId,
				'time_meal_ready' => $this->msectime()
			)
		);
		$result = $this->httpPost($params);
		if(is_error($result)) {
			return $result;
		}
		return error(0, '商家已成功确认出餐');
	}

	//修改订单备注信息
	public function orderRemarkUpdate($remark) {
		$params = array(
			'api' => 'dianwoda.order.remark.update',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId,
				'order_info_content' => trim($remark)
			)
		);
		$result = $this->httpPost($params);
		if(is_error($result)) {
			return $result;
		}
		return error(0, '修改订单信息成功');
	}

	//获取配送员位置
	public function orderPositionQuery() {
		$params = array(
			'api' => 'dianwoda.order.position.query',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'order_original_id' => $this->orderOriginalId,
			)
		);
		$result = $this->httpPost($params);
		return $result;
	}

	//商家运力审核要求
	public function sellerTransportationConfirm() {
		$params = array(
			'api' => 'dianwoda.seller.transportation.confirm',
			'access_token' => $this->config['accesstoken'],
			'body' => array(
				'city_code' => $this->cityCode,
				'seller_id' => $this->sellerId,
				'seller_name' => '茶不思',
				'seller_mobile' => '18234096432',
				'seller_address' => '山西太原',
				'seller_lat' => '37.791326',
				'seller_lng' => '112.610306',
			)
		);
		$result = $this->httpPost($params);
		if(is_error($result)) {
			return $result;
		}
		return error(0, '查询成功');
	}
}


