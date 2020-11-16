<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');

function get_majia_agent($key = '') {
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	if(empty($userAgent)) {
		return '';
	}
	$info = strstr($userAgent, "MAGAPPX");
	$info = explode("|",$info);
	$agent = array(
		'version' => $info[1],//客户端版本信息 4.0.0
		'client' => $info[2],//客户端信息可以获取区分android或ios 可以获取具体机型
		'site' => $info[3],  //站点名称  如lxh
		'device' => $info[4],//设备号    客户端唯一设备号
		'sign' => $info[5],  //签名     可以验证请求是否伪造 签名算法见下面详细说明
		'signCloud' => $info[6],//云端签名 这个签名马甲内部使用
		'token' => $info[7]   //用户token 用户的token信息
	);
	if(!empty($key)) {
		return $agent[$key];
	}
	return $agent;
}

function get_user_info() {
	global $_GPC;
	$token = $_GPC['__majia_token'];
	if(empty($token)) {
		return error(-1, '用户token信息为空');
	}
	$config = get_plugin_config('majiaApp');
	if(empty($config['hostname'])) {
		return error(-1, '后台未设置客户端域名');
	}
	if(empty($config['appsecret'])) {
		return error(-1, '后台未设置appsecret');
	}
	load()->func('communication');
	$url = "https://{$config['hostname']}/mag/cloud/cloud/getUserInfo?token={$token}&secret={$config['appsecret']}";
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, "{$result['code']}:{$result['msg']}");
	}
	return $result['data'];
}

function majiapay_build($params) {
	global $_W;
	$config = get_plugin_config('majiaApp');
	if(empty($config['appsecret'])) {
		return error(-1, '后台未设置appsecret');
	}
	if(empty($config['hostname'])) {
		return error(-1, '后台未设置hostname');
	}
	$params = array_elements(array('title', 'uniontid', 'amount', 'des', 'user_id', 'remark', 'trade_no'), $params);
	$sign_params = array(
		'uniacid' =>  $_W['uniacid'],
		'timestamp' => TIMESTAMP,
		'trade_no' => $params['trade_no'],
		'uniontid' => $params['uniontid'],
	);
	unset($params['uniontid']);
	$sign_params['sign'] = build_majia_sign($sign_params);
	$params['callback'] = WE7_WMALL_URL . 'payment/majia/notify.php?' .  http_build_query($sign_params, '&');
	$params['secret'] = $config['appsecret'];

	load()->func('communication');
	$url = "https://{$config['hostname']}/core/pay/pay/unifiedOrder?" . http_build_query($params, '&');
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, "{$result['code']}:{$result['msg']}");
	}
	return $result['data']['unionOrderNum'];
}

function get_order_pay_status($order_id) {
	load()->func('communication');
	$config = get_plugin_config('majiaApp');
	if(empty($config['hostname']) || empty($config['appsecret'])) {
		return error(-1, '请设置客户端域名和appsecret');
	}
	$url = "https://{$config['hostname']}/core/pay/pay/orderStatusQuery?unionOrderNum={$order_id}&secret={$config['appsecret']}";
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, $result['msg']);
	}
	//支付结果：1:支付成功 2:订单未支付 3:订单不存在
	return $result['paycode'];
}

function build_majia_sign($params) {
	global $_W;
	$secret = get_plugin_config('majiaApp.appsecret');
	if(empty($secret)) {
		return error(-1, '后台没有设置appsecret信息');
	}
	unset($params['sign']);
	ksort($params);
	$string = '';
	foreach($params as $key => $val) {
		if(empty($val)) {
			continue;
		}
		$string .= "{$key}={$val}&";
	}
	$string = trim($string, '&');
	$string = $string . "&secret={$secret}";
	$string = md5($string);
	$result = strtoupper($string);
	return $result;
}

function majia_order_notice($params) {
	global $_W;
	if(empty($params)) {
		return error(-1, '参数错误');
	}
	$config = get_plugin_config('majiaApp');
	if(empty($config['appsecret'])) {
		return error(-1, '后台未设置appsecret');
	}
	if(empty($config['hostname'])) {
		return error(-1, '后台未设置hostname');
	}
	$params['secret'] = $config['appsecret'];
	$params = array_elements(array('user_id', 'title', 'content', 'link', 'secret'), $params);

	load()->func('communication');
	$url = "http://{$config['hostname']}/mag/push/v1/push/sendPush?" . http_build_query($params, '&');
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, "{$result['code']}:{$result['msg']}");
	}
	return true;
}

function majia_send_assistant_msg($params) {
	global $_W;
	if(empty($params)) {
		return error(-1, '参数错误');
	}
	$config = get_plugin_config('majiaApp');
	if(empty($config['appsecret'])) {
		return error(-1, '后台未设置appsecret');
	}
	if(empty($config['hostname'])) {
		return error(-1, '后台未设置hostname');
	}
	$params['secret'] = $config['appsecret'];
	$params = array_elements(array('user_id', 'type', 'content', 'assistant_secret', 'secret', 'is_push'), $params);
	$params['content'] = json_encode($params['content']);

	load()->func('communication');
	$url = "http://{$config['hostname']}/mag/operative/v1/assistant/sendAssistantMsg?" . http_build_query($params, '&');

	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, "{$result['code']}:{$result['msg']}");
	}
	return true;
}

function majia_user_credit_add($uid, $amount = 0, $remark = '', $extra = array()) {
	global $_W;
	$config = get_plugin_config('majiaApp');
	if(empty($config['appsecret'])) {
		return error(-1, '后台未设置appsecret');
	}
	if(empty($config['hostname'])) {
		return error(-1, '后台未设置hostname');
	}
	if(empty($uid)) {
		return error(-1, '马甲用户uid不能为空');
	}
	$amount = floatval($amount);
	if($amount <= 0) {
		return error(-1, '马甲账户变动金额必须大于0');
	}
	$params = array(
		'secret' => $config['appsecret'],
		'user_id' => $uid,
		'amount' => $amount,
		'remark' => $remark,
		'out_trade_code' => $extra['out_trade_code'],
	);
	load()->func('communication');
	$url = "http://{$config['hostname']}/core/pay/pay/accountTransfer?" . http_build_query($params, '&');
	$response = ihttp_get($url);
	if(is_error($response)) {
		return $response;
	}
	$result = json_decode($response['content'], true);
	if($result['success'] != 1) {
		return error(-1, "{$result['code']}:{$result['msg']}");
	}
	return true;
}
