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

class Ttapp{
	protected $basic;
	protected $payment;
	protected $access_token;

	public function __construct() {
		global $_W;
		$ttapp = get_plugin_config('ttapp');
		$this->basic = array(
			'app_id' => $ttapp['basic']['key'],
			'app_secret' => $ttapp['basic']['secret']
		);
		$this->payment = array(
			'merchant_id' => $ttapp['payment']['mchid'],
			'app_id' => $ttapp['payment']['appid'],
			'secret' => $ttapp['payment']['secret']
		);
	}

	/**
	 * 获取小程序的全局唯一调用凭据access_token，有效期2小时（已测试通）
	*/
	public function getAccessToken() {
		global $_W;
		$cachekey = "accesstoken:{$this->basic['app_id']}";
		$cache = cache_load($cachekey);
		if(!empty($cache) && !empty($cache['token']) && $cache['expire'] > TIMESTAMP) {
			$this->access_token = $cache;
			return $cache['token'];
		}

		if(empty($this->basic['app_id']) || empty($this->basic['app_secret'])) {
			return error('-1', '未填写小程序的 appid 或 appsecret！');
		}
		$url = "https://developer.toutiao.com/api/apps/token?appid={$this->basic['app_id']}&secret={$this->basic['app_secret']}&grant_type=client_credential";
		$response = $this->requestApi($url);

		$record = array();
		$record['token'] = $response['access_token'];
		$record['expire'] = TIMESTAMP + $response['expires_in'] - 200;

		$this->account['access_token'] = $record;
		cache_write($cachekey, $record);
		return $record['token'];
	}

	/**
	 * 用tt.login得到的code值换取session_key和openId (已测试通)
	*/
	public function getOauthInfo($code = '') {
		global $_W, $_GPC;
		if(!empty($_GPC['code'])) {
			$code = $_GPC['code'];
		}
		$url = "https://developer.toutiao.com/api/apps/jscode2session?appid={$this->basic['app_id']}&secret={$this->basic['app_secret']}&code={$code}";
		return $response = $this->requestApi($url);
	}

	/**
	 * 发送模板消息(未测试)
	*/
	public function sendTplNotice($touser, $template_id, $postdata, $url, $form_id) {
		global $_W;
		if(empty($this->basic['app_id']) || empty($this->basic['app_secret'])) {
			return error(-1, '请完善头条小程序app_id和app_secret');
		}
		if(empty($touser)) {
			return error(-1, '参数错误, 粉丝openid不能为空');
		}
		if(empty($template_id)) {
			return error(-1, '参数错误, 模板标识不能为空');
		}
		if(empty($postdata) || !is_array($postdata)) {
			return error(-1, '参数错误, 请根据模板规则完善消息内容');
		}
		if(empty($form_id)) {
			$form_id = get_available_formid($touser);
			if(empty($form_id) || is_error($form_id)) {
				return error(-1, '没有可用的form_id用于发送头条小程序模版消息');
			}
		}
		$token = $this->getAccessToken();
		if(is_error($token)) {
			return $token;
		}
		$data = array(
			'access_token' => $token,
			'touser' => $touser,
			'template_id' => $template_id,
			'page' => $url,
			'form_id' => $form_id,
			'data' => $postdata,
		);
		$data = json_encode($data);
		$post_url = "https://developer.toutiao.com/api/apps/game/template/send";
		$response = ihttp_request($post_url, $data);
		if(is_error($response)) {
			return error(-1, "接口调用失败, 错误: {$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if(empty($result)) {
			return error(-1, "接口调用失败, 源数据: {$response['meta']}");
		} elseif(!empty($result['errcode'])) {
			return error(-1, "访问头条接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->errorCode($result['errcode'])}");
		}
		return true;
	}

	/**
	 * 生成二维码
	 * appname取值：toutiao今日头条 douyin抖音 pipixia皮皮虾 huoshan火山小视频
	*/

	public function createQRCode($params = array()) {
		global $_W;
		$access_token = $this->getAccessToken();
		if(empty($access_token)) {
			return error(-1, '请完善头条小程序的app_secret');
		}
		$elements = array('appname', 'path', 'width', 'line_color', 'background', 'set_icon');
		$params = array_elements($elements, $params);
		$params = array_filter($params);
		if(!empty($params['path'])) {
			$params['path'] = urlencode($params['path']);
		}
		$params['access_token'] = $access_token;
		$data = json_encode($params);
		$post_url = "https://developer.toutiao.com/api/apps/qrcode";
		$response = ihttp_request($post_url, $data);
		if(is_error($response)) {
			return error(-1, "接口调用失败, 错误: {$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if(empty($result)) {
			return error(-1, "接口调用失败, 源数据: {$response['meta']}");
		} elseif(!empty($result['errcode'])) {
			return error(-1, "访问头条接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->errorCode($result['errcode'])}");
		}
		return true;
	}

	/**
	 * 敏感信息解密
	*/
	public function pkcs7Encode($encrypt_data, $iv) {
		$key = base64_decode($_SESSION['session_key']);
		$result = iaes_pkcs7_decode($encrypt_data, $key, $iv);
		if (is_error($result)) {
			return error(1, '解密失败');
		}
		$result = json_decode($result, true);
		if(empty($result)) {
			return error(1, '解密失败');
		}
		if($result['watermark']['appid'] != $this->basic['app_id']) {
			return error(1, '解密失败');
		}
		unset($result['watermark']);
		return $result;
	}

	/**
	 * 构建tt.pay的orderInfo参数
	 */
	public function buildOrderIfo($params = array()) {
		global $_W;
		$orderInfo = array(
			'merchant_id' => $this->payment['merchant_id'], //头条分配给商户的商户号
			'app_id' => $this->payment['app_id'], //头条支付分配给商户的 app_id 非小程序的app_id
			'sign_type' => 'MD5',
			'timestamp' => TIMESTAMP,
			'version' => '2.0',
			'trade_type' => 'H5',
			'product_code' => 'pay',
			'payment_type' => 'direct',
			'out_order_no' => $params['out_order_no'],
			'uid' => $params['uid'],
			'total_amount' => $params['total_amount'] * 100, //金额 单位：分
			'currency' => 'CNY',
			'subject' => $params['subject'],
			'body' => $params['body'],
			'trade_time' => $params['trade_time'], //下单时间
			'valid_time' => 24*3600, //订单有效时间 单位：秒
			'notify_url' => $params['notify_url'],
			'alipay_url' => $params['alipay_url'],
			'wx_url' => $params['wx_url'],
			'wx_type' => $params['wx_type'],
			'risk_info' => array(
				'ip' => CLIENT_IP
			),
		);
		//删除value值为空的元素 即支付宝支付时wx_url、wx_type为空，微信支付时alipay_url为空
		$orderInfo = array_filter($orderInfo);
		$orderInfo['sign'] = $this->buildSign($orderInfo);
		return $orderInfo;
	}


	/**
	 * 构建tt.pay的orderInfo时计算签名
	 */
	public function buildSign($orderInfo) {
		global $_W;
		$sign = '';
		if(!empty($orderInfo)) {
			//sign、risk_info、value值为空的字段不参与签名
			unset($orderInfo['sign']);
			unset($orderInfo['risk_info']);
			ksort($orderInfo);
			$arr = array();
			foreach($orderInfo as $key => $value) {
				$arr[] = "{$key}={$value}";
			}
			$app_secret = $this->payment['secret'];
			$sign = implode('&', $arr) . $app_secret;
			$sign = md5($sign);
		}
		return $sign;
	}

	protected function requestApi($url, $post = '') {
		$response = ihttp_request($url, $post);
		$result = @json_decode($response['content'], true);
		if(is_error($response)) {
			return error($result['errcode'], "访问公众平台接口失败, 错误详情: {$this->errorCode($result['errcode'])}");
		}
		if(empty($result)) {
			return $response;
		} elseif(!empty($result['errcode'])) {
			return error($result['errcode'], "访问公众平台接口失败, 错误: {$result['errmsg']},错误详情：{$this->errorCode($result['errcode'])}");
		}
		return $result;
	}

	public function errorCode($code, $errmsg = '未知错误') {
		$errors = array(
			'0' => '请求成功',
			'-1' => '系统错误',
			'40001' => 'http 包体无法解析',
			'40002' => 'access_token 无效',
			'40014' => '参数无效',
			'40015' => 'appid 错误',
			'40017' => 'secret 错误',
			'40016' => 'appname 错误',
			'40018' => 'code 错误',
			'40019' => 'acode 错误',
			'40020' => 'grant_type 不是 client_credential',
			'40021' => 'width 超过指定范围',
			'40037' => '错误的模版 id',
			'40038' => '小程序被禁止发送消息通知',
			'40039' => 'form_id 不正确，或者过期',
			'40040' => 'form_id 已经被使用',
			'40041' => '错误的页面地址',
			'60003' => '频率限制（目前 5000 次/分钟）',
			'其它' => '参数为空',
		);
		$code = strval($code);
		if($errors[$code]) {
			return $errors[$code];
		} else {
			return $errmsg;
		}
	}

	public function result($errno, $message = '', $data = '') {
		exit(json_encode(array(
			'errno' => $errno,
			'message' => $message,
			'data' => $data,
		)));
	}
}