<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
function get_qianfan_version() {
	$wap_token  = $_COOKIE['wap_token'];
	if(empty($wap_token)) {
		return -1;
	}
	$secret_key  = get_plugin_config('qianfanApp.appsecret');
	if(strpos($wap_token, '%2') !== false) {
		$wap_token = urldecode($wap_token);
	}
	$decode = authCode($wap_token, 'DECODE', $secret_key, 172800);
	$data = json_decode($decode, true);
	return $data['version'];
}

function fianfan_update() {
	return true;
	$version = get_qianfan_version();
	if($version == -1 || $version >= 220) {
		return true;
	}
	$config = get_plugin_config('qianfanApp');
	$notice = '<div class="modal modal-no-buttons modal-notice modal-qianfan-download">
					<div class="modal-inner">
						<div class="modal-title">
							<div>温馨提示</div>
						</div>
						<div class="modal-text">
							<div class="notice">
								'. $config['version_notice'] .'
							</div>
							<a href="' . $config['download_url'] . '" class="button button-big button-fill button-danger close-modal">现在去升级</a>
						</div>
					</div>
				</div>
				<script>
					$(function(){
						$.modal.prototype.defaults.modalCloseByOutside = false;
						$.iopenModal(".modal-qianfan-download", function(){});
					});
				</script>
			';
	echo $notice;
}

function get_order_pay_status($order_id) {
	if(is_array($order_id)) {
		$order_id = implode(',', $order_id);
	} else {
		$order_id = intval($order_id);
	}
	$params = array(
		'order_id' => $order_id
	);
	load()->func('communication');
	$sign = build_qianfan_sign($params);
	if(is_error($sign)) {
		return $sign;
	}
	$hostname = get_plugin_config('qianfanApp.hostname');
	$url = "http://{$hostname}.qianfanapi.com/api1_2/orders/query?order_id={$order_id}&sign={$sign}";
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['ret'] != 0) {
		return error(-1, $result['text']);
	}
	//支付结果：0：未支付，1:支付成功,2:支付成功，等待回调中（最后过一段时间再来请求）,3:支付出现异常，4:订单进入退款阶段
	if(!is_array($order_id)) {
		return $result['data'][$order_id]['result'];
	}
	return $result['data'];
}

function build_qianfan_sign($params) {
	global $_W;
	$secret = get_plugin_config('qianfanApp.appsecret');
	if(empty($secret)) {
		return error(-1, '后台没有设置appsecret信息');
	}
	unset($params['sign']);
	ksort($params);
	$string = '';
	foreach($params as $key => $val) {
		if(substr($val, 0, 1) != "@") {
			$string .= "{$key}={$val}&";
		}
	}
	$string = trim($string, '&');
	$string = $string . "&secret={$secret}";
	$string = md5($string);
	$result = strtoupper($string);
	return $result;
}

function qianfan_user_credit_add($uid, $amount = 0) {
	$config = get_plugin_config('qianfanApp');
	if(empty($config['hostname']) || empty($config['type_refund_id'])) {
		return error(-1, '后台未设置hostname和外卖退款类型ID');
	}
	$url = "http://{$config['hostname']}.qianfanapi.com/api1_2/balance/add";
	$params = array(
		'uid' => $uid,
		'type' => $config['type_refund_id'],
		'amount' => $amount * 100,
	);
	$params['sign'] = build_qianfan_sign($params);
	load()->func('communication');
	$response = ihttp_post($url, $params);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['ret'] != 0) {
		return error(-1, $result['text']);
	}
	return $result['data']['balance'];
}

//http://doc.qianfanyun.com/web/#/2?page_id=23
function qianfan_order_refund($uid, $amount = 0) {
	$config = get_plugin_config('qianfanApp');
	if(empty($config['hostname']) || empty($config['type_refund_id'])) {
		return error(-1, '后台未设置hostname和外卖退款类型ID');
	}
	$url = "http://{$config['hostname']}.qianfanapi.com/api1_2/orders/refund";
	$params = array(
		'order_id' => $uid,
		'out_trade_no' => $config['type_refund_id'],
		'gold' => $amount * 100,
		'cash' => $amount * 100,
		'reason' => $amount * 100,
	);
	$params['sign'] = build_qianfan_sign($params);
	load()->func('communication');
	$response = ihttp_post($url, $params);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['ret'] != 0) {
		return error(-1, $result['text']);
	}
	return true;
}






