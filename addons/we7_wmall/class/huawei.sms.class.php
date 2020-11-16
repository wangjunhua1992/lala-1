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

class HuaweiSms{

	public function __construct() {

	}

	/**
	 * 华为云发送短信API 文档地址：https://support.huaweicloud.com/api-msgsms/sms_05_0001.html
	*/
	public function sendCode($templateId, $mobile, $content, $sid) {
		global $_W;
		$config_sms = $_W['we7_wmall']['config']['sms'];
		if(!is_array($config_sms['set'])) {
			return error(-1, '平台没有设置短信参数');
		}
		if(empty($config_sms['set']['status'])) {
			return error(-1, '平台已关闭短信功能');
		}
		$appKey = $config_sms['set']['huawei']['app_key'];
		$appSecret = $config_sms['set']['huawei']['app_secret'];
		$signature = $config_sms['set']['huawei']['signature'];
		$sender = $config_sms['set']['huawei']['sender']; //签名通道号

		$url = 'https://api.rtc.huaweicloud.com:10443/sms/batchSendSms/v1';
		$header = array(
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
			'X-WSSE' => $this->buildWsseHeader($appKey, $appSecret)
		);
		$templateParas = '';
		if(is_array($content)) {
			$content = array_values($content);
			$templateParas = json_encode($content);
		}
		$post = array(
			'from' => $sender,
			'to' => $mobile,
			'templateId' => $templateId,
			'templateParas' => $templateParas,
			'signature' => $signature,
		);

		$result = ihttp_request($url, $post, $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if($result['code'] != '000000') {
			$errMsg = "错误码：{$result['code']}, 错误信息：{$result['description']}";
			return error(-1, $errMsg);
		}
		return true;
	}

	/**
	 * 华为云AXE模式绑定接口 文档地址：https://support.huaweicloud.com/api-PrivateNumber/privatenumber_02_0022.html
	*/

	public function yinsihaoBind($params) {
		$elements = array('appKey', 'appSecret', 'virtualNum', 'bindNum', 'bindExpiredTime');
		$params = array_elements($elements, $params);
		if(empty($params['appKey'])) {
			return error(-1, 'appKey不能为空');
		}
		if(empty($params['appSecret'])) {
			return error(-1, 'appSecret不能为空');
		}
		if(empty($params['virtualNum'])) {
			return error(-1, '隐私号段不能为空');
		}
		if(empty($params['bindNum'])) {
			return error(-1, '要加密的电话号码不能为空');
		}
		if(empty($params['bindExpiredTime'])) {
			return error(-1, '绑定关系的过期时间不能为空');
		}

		$url = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/extendnumber/v1.0';
		$header = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
			'X-WSSE' => $this->buildWsseHeader($params['appKey'], $params['appSecret'], 'UTC', true)
		);
		$post = array(
			'virtualNum' => $this->mobileAddCountryCode($params['virtualNum']), //X号码，如+8615364816001
			'bindNum' => $this->mobileAddCountryCode($params['bindNum']), //A号码，如+8615364816001
			'displayNumMode' => 0,
			'bindExpiredTime' => $params['bindExpiredTime'], //单位小时 0-720 默认为168 0为永不过期,
			'recordFlag' => true,
		);
		$post = json_encode($post);
		$result = ihttp_request($url, $post, $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!empty($result['resultcode'])) {
			$errMsg = "错误码：{$result['resultcode']}, 错误信息：{$result['resultdesc']}";
			return error(-1, $errMsg);
		}
		return array(
			'SecretNo' => $this->mobileDelCountryCode($result['virtualNum']),
			'Extension' => $result['extendNum'],
			'SubsId' => $result['subscriptionId']
		);
	}

	/**
	 * 华为云AXE模式解绑接口 文档地址：https://support.huaweicloud.com/api-PrivateNumber/privatenumber_02_0023.html
	*/
	public function yinsihaoUnbind($params) {
		if(empty($params['appKey'])) {
			return error(-1, 'appKey不能为空');
		}
		if(empty($params['appSecret'])) {
			return error(-1, 'appSecret不能为空');
		}
		if(empty($params['subscriptionId']) && (empty($params['virtualNum']) || empty($params['extendNum']))) {
			return error(-1, '解绑参数不完整');
		}
		$url = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/extendnumber/v1.0';
		$header = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
			'X-WSSE' => $this->buildWsseHeader($params['appKey'], $params['appSecret'], 'UTC', true)
		);
		$data = array(
			'subscriptionId' => $params['subscriptionId'],
			'virtualNum' => $this->mobileAddCountryCode($params['virtualNum'], '86', false),
			'extendNum' => $params['extendNum']
		);
		$data = http_build_query($data);
		$url = $url . '?' . $data;

		$result = $this->ihttp_request($url, '', $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!empty($result['resultcode'])) {
			$errMsg = "错误码：{$result['resultcode']}, 错误信息：{$result['resultdesc']}";
			return error(-1, $errMsg);
		}
		return true;
	}

	/**
	 * 华为云语音通知API 文档地址：https://support.huaweicloud.com/api-VoiceCall/rtc_05_0013.html
	*/
	public function singleCall($called_num, $content, $type = 'clerk') {
		global $_W;
		$config_sms = $_W['we7_wmall']['config']['sms'];
		$config_notice = $_W['we7_wmall']['config']['notice']['sms'][$type];
		$url = '' . '/rest/httpsessions/callnotify/v2.0';
		$app_key = $config_sms[''];
		$access_token = $this->getAccessToken();

		$bindNbr = $this->mobileAddCountryCode($config_notice['']); // 绑定号码,使用CallEnabler业务号码
		$displayNbr = $this->mobileAddCountryCode($config_notice['called_show_num']); //主显号码,被叫终端上显示的主叫号码
		$calleeNbr = $this->mobileAddCountryCode($called_num);
		$playInfoList = array();
		if(!empty($content)) {
			$playInfoList[] = array(
				'templateId' => $config_notice['tts_code'],
				'templateParas' => json_encode(array_values($content)),
				'collectInd' => 0,
				'replayAfterCollection' => false,
				'collectContentTriggerReplaying' => 1
			);
		}
		$urlParams = array(
			'version' => 2.0,
			'app_key' => $app_key,
			'access_token' => $access_token
		);
		$url = $url . '?' . http_build_query($urlParams);
		$header = array(
			'Content-Type' => 'application/json; charset=UTF-8'
		);
		$post = array(
			'bindNbr' => $bindNbr,
			'displayNbr' => $displayNbr,
			'calleeNbr' => $calleeNbr,
			'playInfoList' => $playInfoList,
		);
		$result = ihttp_request($url, $post, $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!empty($result['resultcode'])) {
			$errMsg = "错误码：{$result['resultcode']}, 错误信息：{$result['resultdesc']}";
			return error(-1, $errMsg);
		}
		return true;
	}

	/**
	 * 华为云大客户SP简单认证API 文档：https://support.huaweicloud.com/api-VoiceCall/rtc_05_0002.html
	*/
	public function getAccessToken() {
		global $_W;
		$cache = cache_read("huawei:accessToken:{$_W['uniacid']}");
		if(!empty($cache)) {
			if($cache['expires_in'] <= TIMESTAMP) {
				//令牌过期, 刷新令牌
				$params = array(
					'refresh_token' => $cache['refresh_token']
				);
				$cache = $this->refreshAccessToken($params);
			}
			return $cache;
		}

		$config = array();
		$url =  '' . '/rest/fastlogin/v1.0';
		$app_key = $config[''];
		$username = ''; //开发者账号
		$password = ''; //业务开通后会以短信的方式通知

		$urlParams = array(
			'app_key' => $app_key,
			'username' => $username,
		);
		$url = $url . '?' . http_build_query($urlParams);
		$header = array(
			'Accept' => 'application/json',
			'Authorization' => $password,
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
		);
		$result = ihttp_request($url, '', $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!empty($result['resultcode'])) {
			$errMsg = "错误码：{$result['resultcode']}, 错误信息：{$result['resultdesc']}";
			return error(-1, $errMsg);
		}

		$data = array(
			'access_token' => $result['access_token'],
			'refresh_token' => $result['refresh_token'],
			'expires_in' => $result['expires_in']
		);
		cache_write("huawei:accessToken:{$_W['uniacid']}", $data);
		return $data;
	}

	/**
	 * 华为云刷新授权API 文档：https://support.huaweicloud.com/api-VoiceCall/rtc_05_0003.html
	 */
	public function refreshAccessToken($params) {
		global $_W;
		$config = array();
		$url = '' . '/omp/oauth/refresh';
		$header = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
		);
		$post = array(
			'app_key' => $config[''],
			'grant_type' => 'refresh_token',
			'app_secret' => $config[''],
			'refresh_token' => $params['refresh_token']
		);
		$result = ihttp_request($url, $post, $header);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!empty($result['resultcode'])) {
			$errMsg = "错误码：{$result['resultcode']}, 错误信息：{$result['resultdesc']}";
			return error(-1, $errMsg);
		}
		$data = array(
			'access_token' => $result['access_token'],
			'refresh_token' => $result['refresh_token'],
			'expires_in' => $result['expires_in']
		);
		cache_write("huawei:accessToken:{$_W['uniacid']}", $data);
		return $data;
	}

	public function buildWsseHeader($appKey, $appSecret, $timezone = 'Asia/Shanghai', $hashRawOutput = 'false') {
		date_default_timezone_set($timezone);
		$Created = date('Y-m-d\TH:i:s\Z'); //Created
		$nonce = uniqid(); //Nonce
		$base64 = base64_encode(hash('sha256', ($nonce . $Created . $appSecret), $hashRawOutput)); //PasswordDigest
		return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $appKey, $base64, $nonce, $Created);
	}

	public function mobileAddCountryCode($mobile, $countryCode = '86', $plusIsTransfer = false) {
		if(!empty($mobile) && substr($mobile, 0, 1) != '+') {
			$mobile = ltrim($mobile, 0);
			$plus = $plusIsTransfer ? '%2B' : '+';
			$mobile = $plus . $countryCode . $mobile;
		}
		return $mobile;
	}

	public function mobileDelCountryCode($mobile, $countryCode = '86') {
		if(!empty($mobile) && substr($mobile, 0, 1) == '+') {
			$start = strlen($countryCode) + 1;
			$mobile = substr($mobile, $start);
		}
		return $mobile;
	}

	public function ihttp_request($url, $post = '', $extra = array(), $timeout = 60)
	{
		$urlset = parse_url($url);
		if (empty($urlset['path'])) {
			$urlset['path'] = '/';
		}
		if (!empty($urlset['query'])) {
			$urlset['query'] = "?{$urlset['query']}";
		}
		if (empty($urlset['port'])) {
			$urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
		}
		if (strexists($url, 'https://') && !extension_loaded('openssl')) {
			if (!extension_loaded("openssl")) {
				message('请开启您PHP环境的openssl');
			}
		}
		if (function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = curl_init();
			//5.6版本兼容
			if (ver_compare(phpversion(), '5.6') >= 0) {
				curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			}
			if (!empty($extra['ip'])) {
				$extra['Host'] = $urlset['host'];
				$urlset['host'] = $extra['ip'];
				unset($extra['ip']);
			}
			curl_setopt($ch, CURLOPT_URL, $urlset['scheme'] . '://' . $urlset['host'] . ($urlset['port'] == '80' ? '' : ':' . $urlset['port']) . $urlset['path'] . $urlset['query']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			@curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			if ($post) {
				if (is_array($post)) {
					$filepost = false;
					foreach ($post as $name => $value) {
						if ((is_string($value) && substr($value, 0, 1) == '@') || (class_exists('CURLFile') && $value instanceof CURLFile)) {
							$filepost = true;
							break;
						}
					}
					if (!$filepost) {
						$post = http_build_query($post);
					}
				}
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}
			//请求方式为DELETE
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			if (!empty($GLOBALS['_W']['config']['setting']['proxy'])) {
				$urls = parse_url($GLOBALS['_W']['config']['setting']['proxy']['host']);
				if (!empty($urls['host'])) {
					curl_setopt($ch, CURLOPT_PROXY, "{$urls['host']}:{$urls['port']}");
					$proxytype = 'CURLPROXY_' . strtoupper($urls['scheme']);
					if (!empty($urls['scheme']) && defined($proxytype)) {
						curl_setopt($ch, CURLOPT_PROXYTYPE, constant($proxytype));
					} else {
						curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
						curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
					}
					if (!empty($GLOBALS['_W']['config']['setting']['proxy']['auth'])) {
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['_W']['config']['setting']['proxy']['auth']);
					}
				}
			}
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSLVERSION, 1);
			if (defined('CURL_SSLVERSION_TLSv1')) {
				curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
			}
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
			if (!empty($extra) && is_array($extra)) {
				$headers = array();
				foreach ($extra as $opt => $value) {
					if (strexists($opt, 'CURLOPT_')) {
						curl_setopt($ch, constant($opt), $value);
					} elseif (is_numeric($opt)) {
						curl_setopt($ch, $opt, $value);
					} else {
						$headers[] = "{$opt}: {$value}";
					}
				}
				if (!empty($headers)) {
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				}
			}
			$data = curl_exec($ch);
			$status = curl_getinfo($ch);
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			curl_close($ch);
			if ($errno || empty($data)) {
				return error(1, $error);
			} else {
				return ihttp_response_parse($data);
			}
		}
		//$method = empty($post) ? 'GET' : 'POST';
		$method = 'DELETE';
		$fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
		$fdata .= "Host: {$urlset['host']}\r\n";
		if (function_exists('gzdecode')) {
			$fdata .= "Accept-Encoding: gzip, deflate\r\n";
		}
		$fdata .= "Connection: close\r\n";
		if (!empty($extra) && is_array($extra)) {
			foreach ($extra as $opt => $value) {
				if (!strexists($opt, 'CURLOPT_')) {
					$fdata .= "{$opt}: {$value}\r\n";
				}
			}
		}
		$body = '';
		if ($post) {
			if (is_array($post)) {
				$body = http_build_query($post);
			} else {
				$body = urlencode($post);
			}
			$fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
		} else {
			$fdata .= "\r\n";
		}
		if ($urlset['scheme'] == 'https') {
			$fp = fsockopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
		} else {
			$fp = fsockopen($urlset['host'], $urlset['port'], $errno, $error);
		}
		stream_set_blocking($fp, true);
		stream_set_timeout($fp, $timeout);
		if (!$fp) {
			return error(1, $error);
		} else {
			fwrite($fp, $fdata);
			$content = '';
			while (!feof($fp))
				$content .= fgets($fp, 512);
			fclose($fp);
			return ihttp_response_parse($content, true);
		}
	}

	public function yinsihaoBind1() {
		$realUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/extendnumber/v1.0'; // APP接入地址+接口访问URI
		$APP_KEY = '0t9F9mj0u5L2SOA9R0U8Qetd88Z4'; // APP_Key
		$APP_SECRET = 'lz8Ryy8DK9Y4Vhw6sElROdED3B3m'; // APP_Secret
		$virtualNum = '+8617129103521'; // AXE中的X号码
		$bindNum = '+8618234096432'; // AXE中的A号码

		// 请求Headers
		$headers = [
			'Accept: application/json',
			'Content-Type: application/json;charset=UTF-8',
			'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
			'X-WSSE: ' . $this->buildWsseHeader($APP_KEY, $APP_SECRET)
		];
		// 请求Body,可按需删除选填参数
		$data = json_encode([
			'virtualNum' => $virtualNum,
			'bindNum' => $bindNum,
			'displayNumMode' => 0,
			'bindExpiredTime' => 1,
		]);

		$context_options = [
			'http' => [
				'method' => 'POST', // 请求方法为POST
				'header' => $headers,
				'content' => $data,
				'ignore_errors' => true // 获取错误码,方便调测
			],
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false
			] // 为防止因HTTPS证书认证失败造成API调用失败,需要先忽略证书信任问题
		];
		//发送请求
		$response = file_get_contents($realUrl, false, stream_context_create($context_options));
		$result = json_decode($response, true);
		return array(
			'SecretNo' => $this->mobileDelCountryCode($result['virtualNum']),
			'Extension' => $result['extendNum'],
			'SubsId' => $result['subscriptionId']
		);
		return false;
	}

}