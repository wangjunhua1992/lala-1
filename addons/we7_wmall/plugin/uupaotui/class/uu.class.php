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

class uuPaoTui{

	protected $app = null;
	protected $app_secret;
	public $config = array();

	public function __construct($sid = '') {
		$this->config = array();
		if(!empty($sid)) {
			$this->config = store_get_data($sid, 'uupaotui');
			$this->city = get_plugin_config('uupaotui.city');
		} else {
			$this->config = get_plugin_config('uupaotui');
			$this->city = $this->config['city'];
		}
		$this->openid = $this->config['openid']; //'24bed917784843c7918f1c0530430770';
		$this->appid = $this->config['appid']; //'167d337e02944dd5976e4714d093bbf5';
		$this->appkey = $this->config['appkey'];//'S3';
		$this->api_url = 'http://openapi.uupaotui.com/v2_0/';
	}

	public function buildParams($params) {
		$common_params = array(
			'nonce_str'=> random(10),//不超过32位
			'timestamp'=> TIMESTAMP,
			'openid'=> $this->openid,
			'appid'=> $this->appid,
		);
		$params = array_merge($params, $common_params);
		$params['sign'] = $this->buildSign($params);
		return $params;
	}

	public function buildSign($params) {
		ksort($params);
		$arr = array();
		foreach ($params as $key => $value) {
			if(!empty($value) || $value === 0) {
				$arr[] = $key.'='.$value;
			}
		}
		$arr[] = 'key='.$this->appkey;
		$str = strtoupper(implode('&', $arr));
		return strtoupper(md5($str));
	}

	public function httpPost($action, $params = array()) {
		$buildparams = $this->buildParams($params);
		$response = ihttp_request($this->api_url.$action, $buildparams);
		if(is_error($response)) {
			return error('-2', "请求接口出错:{$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if($result['return_code'] != 'ok') {
			return error(-1, "错误详情：{$result['return_msg']}");
		}
		return $result;
	}

	public function getOrderPrice($id) {
		global $_W;
		if($this->config['status'] != 1) {
			return error(-1, 'UU跑腿未开启');
		}
		$order = order_fetch($id);
		if(!in_array($order['status'], array(2, 3))) {
			return error(-1, '订单不是待配送状态');
		}
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('telephone', 'address', 'location_x', 'location_y'));
		$params = array(
			'origin_id' => $order['ordersn'],
			'from_address' => $store['address'],
			//'from_usernote'=> '',//起始地址具体门牌号
			'to_address'=> $order['address'],
			//'to_usernote'=> '',//
			'city_name'=> $this->city,
			'subscribe_type' => 0,//预约类型 0实时订单 1预约取件时间
			//'county_name' => '',//订单所在县级地名称
			//'subscribe_time'=> '',//预约时间
			//'coupon_id'=> '',//
			'send_type'=> 0, //订单小类 0帮我送(默认) 1帮我买
			'to_lat'=> $order['location_x'],
			'to_lng' => $order['location_y'],
			'from_lat' => $store['location_x'],
			'from_lng'=> $store['location_y'],
		);
		$response = $this->httpPost('getorderprice.ashx', $params);
		return $response;
	}

	public function addOrder($id, $data) {
		global $_W;
		$order = order_fetch($id);
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('telephone', 'address', 'location_x', 'location_y'));
		$params = array(
			'price_token' => $data['price_token'],
			'order_price' => $data['total_money'],
			'balance_paymoney' => $data['need_paymoney'],
			'receiver'=> $order['username'],
			'receiver_phone' => $order['mobile'],
			'callback_url'=> WE7_WMALL_URL . '/plugin/uupaotui/notify.php',//订单提交成功后及状态变化的回调地址
			'push_type' => 0,
			'push_str' => '',//推送跑男的手机号，push_type为0这里就传空字符串
			'special_type'=> 0,
			'callme_withtake'=> 0,
			'pubusermobile'=> $store['telephone'], //发件人电话，（如果为空则是用户注册的手机号）
		);
		if(!empty($order['note'])) {
			$params['note'] = $order['note']; //'测试接口，不要接单',
		}
		$response = $this->httpPost('addorder.ashx', $params);
		return $response;
	}

	public function cancelOrder($id, $reason) {
		if(empty($reason)) {
			return error(-1, '请输入取消原因');
		}
		$params = array(
			//'order_code' => 'U379080018092716031329936093',
			'origin_id' => $id,
			'reason'=> $reason,
		);
		$response = $this->httpPost('cancelorder.ashx', $params);
		return $response;
	}

	public function getOrderDetail($id) {
		$params = array(
			//'order_code' => '',
			'origin_id' => $id,
		);
		$response = $this->httpPost('getorderdetail.ashx', $params);
		return $response;
	}

	public function queryCityList() {
		$response = $this->httpPost('getcitylist.ashx','');
		return $response;
	}
}


