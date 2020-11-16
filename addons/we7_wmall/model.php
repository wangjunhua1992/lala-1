<?php
defined('IN_IA') or exit('Access Denied');
include('init.php');

function p($data) {
	echo '<pre style="padding-left: 200px">';
	print_r($data);
	echo '</pre>';
}

class Mloader {
	private $cache = array();
	public function __construct() {
		global $_W;
		if(empty($_W['LangType'])) {
			$_W['LangType'] = 'zh-cn';
		}
	}

	function func($name) {
		global $_W;
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/function/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$filelang = IA_ROOT . "/addons/we7_wmall/function/lang/{$_W['LangType']}/" . $name . '.func.php';
			if (file_exists($filelang)) {
				include $filelang;
			}
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Helper Function /addons/we7_wmall/function/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}

	function model($name) {
		global $_W;
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include $file;
			$filelang = IA_ROOT . "/addons/we7_wmall/model/lang/{$_W['LangType']}/" . $name . '.mod.php';
			if (file_exists($filelang)) {
				include $filelang;
			}
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Model /addons/we7_wmall/model/' . $name . '.mod.php', E_USER_NOTICE);
			return false;
		}
	}

	function classs($name) {
		global $_W;
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall/class/' . $name . '.class.php';
		if (file_exists($file)) {
			include $file;
			$filelang = IA_ROOT . "/addons/we7_wmall/class/lang/{$_W['LangType']}/" . $name . '.class.php';
			if (file_exists($filelang)) {
				include $filelang;
			}
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Class /addons/we7_wmall/class/' . $name . '.class.php', E_USER_ERROR);
			return false;
		}
	}
}

function icache_load($name) {
	static $we7_wmall_cache;
	if (!empty($we7_wmall_cache[$name])) {
		return $we7_wmall_cache[$name];
	}
	$data = $we7_wmall_cache[$name] = icache_read($name);
	return $data;
}

function icache_read($name) {
	$cachedata = pdo_get('tiny_wmall_cache', array('name' => $name), array('value'));
	$cachedata = $cachedata['value'];
	if (empty($cachedata)) {
		return '';
	}
	$cachedata = iunserializer($cachedata);
	if (is_array($cachedata) && !empty($cachedata['expire']) && !empty($cachedata['data'])) {
		if ($cachedata['expire'] > TIMESTAMP) {
			return $cachedata['data'];
		} else {
			return '';
		}
	} else {
		return $cachedata;
	}
}

function icache_write($name, $data, $expire = 0) {
	if (empty($name) || !isset($data)) {
		return false;
	}
	$record = array();
	$record['name'] = $name;
	if (!empty($expire)) {
		$cache_data = array(
			'expire' => TIMESTAMP + $expire,
			'data' => $data
		);
	} else {
		$cache_data = $data;
	}
	$record['value'] = iserializer($cache_data);
	return pdo_insert('tiny_wmall_cache', $record, true);
}

function icache_delete($name) {
	$sql = 'DELETE FROM ' . tablename('tiny_wmall_cache') . ' WHERE `name`=:name';
	$params = array();
	$params[':name'] = $name;
	$result = pdo_query($sql, $params);
	return $result;
}

function icache_clean($prefix = '') {
	global $_W;
	if (empty($prefix)) {
		$sql = 'DELETE FROM ' . tablename('tiny_wmall_cache');
		$result = pdo_query($sql);
		if ($result) {
			unset($_W['cache']);
		}
	} else {
		$sql = 'DELETE FROM ' . tablename('tiny_wmall_cache') . ' WHERE `name` LIKE :name';
		$params = array();
		$params[':name'] = "{$prefix}:%";
		$result = pdo_query($sql, $params);
	}
	return $result;
}

function iwurl($segment, $params = array(), $script = './index.php?') {
	list($controller, $action, $do) = explode('/', $segment);
	$url = $script;
	if (!empty($controller)) {
		$url .= "c={$controller}&";
	}
	if (!empty($action)) {
		$url .= "a={$action}&";
	}
	if (!empty($do)) {
		$url .= "do={$do}&";
	}
	if (!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}
	return $url;
}

function iurl($segment, $params = array(), $addhost = false) {
	global $_W;
	list($ctrl, $ac, $op, $ta) = explode('/', $segment);
	$params = array_merge(array('ctrl' => $ctrl, 'ac' => $ac, 'op' => $op, 'ta' => $ta, 'do' => 'web', 'm' => 'we7_wmall'), $params);
	$url = iwurl('site/entry', $params);
	if(($_W['_controller'] == 'store' || $ctrl == 'store') && ($params['agent'] != 1)) {
		$params['i'] = $_W['uniacid'];
		$url = iwurl('site/entry', $params, './wmerchant.php?');
	} else if(defined('IN_AGENT') || $params['agent'] == 1) {
		unset($params['agent']);
		$params['i'] = $_W['uniacid'];
		$url = iwurl('site/entry', $params, './wagent.php?');
	}
	if($addhost) {
		$url = $_W['siteroot'] . 'web/' . substr($url, 2);
	}
	return $url;
}

function imurl($segment, $params = array(), $addhost = false) {
	global $_W;
	list($ctrl, $ac, $op, $ta) = explode('/', $segment);
	$basic = array('ctrl' => $ctrl, 'ac' => $ac, 'op' => $op, 'ta' => $ta, 'do' => 'mobile', 'm' => 'we7_wmall');
	$params = array_merge($basic, $params);
	$url = murl('entry', $params);
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/app/' . substr($url, 2);
	}
	return $url;
}

function iaurl($segment, $params = array(), $addhost = false) {
	global $_W;
	list($ctrl, $ac, $op, $ta) = explode('/', $segment);
	$basic = array('op' => $op, 'ta' => $ta, 'do' => 'mobile', 'm' => 'we7_wmall', 'from' => 'vue');
	$params = array_merge($basic, $params);
	$str = '';
	$url = "./wxapp.php?i={$_W['uniacid']}{$str}&c=entry&";
	if (!empty($ctrl)) {
		$url .= "ctrl={$ctrl}&";
	}
	if (!empty($ac)) {
		$url .= "ac={$ac}&";
	}
	$queryString = http_build_query($params, '', '&');
	$url .= $queryString;
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/app/' . substr($url, 2);
	}
	return $url;
}

function ivurl($segment, $params = array(), $addhost = false) {
	global $_W;
	$segment = explode('?', $segment);
	$dir = 'vue';
	if(defined('IN_VUE') && $_W['ilang'] == 'zhcn2uy' && $_W['LangType'] == 'uy') {
		$dir = 'vueuy';
	}
	if(!empty($params['dir'])) {
		$dir = trim($params['dir']);
		unset($params['dir']);
	}
	$basic = array('i' => $_W['uniacid']);
	if(!empty($params['nouniacid'])) {
		unset($basic['i']);
		unset($params['nouniacid']);
	}
	$query = array();
	if(!empty($segment[1])) {
		parse_str($segment[1], $query);
	}
	if($dir != 'vue' && !empty($_W['LangType']) && $_W['LangType'] != 'zh-cn') {
		$params['lang'] = $_W['LangType'];
	}
	if(!empty($_W['MapType']) && $_W['MapType'] != 'gaode') {
		$params['map'] = $_W['MapType'];
	}
	$params = array_merge($params, $query, $basic);
	$query = http_build_query($params);
	$segment = trim($segment[0], "/");
	$url = !empty($query) ? "{$segment}?{$query}" : $segment;
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/addons/we7_wmall/template/' . $dir . '/index.html?menu=#/' . trim($url, "/");
	}
	return $url;
}

function ipurl($segment, $params = array(), $addhost = false) {
	global $_W;
	$segment = explode('?', $segment);
	$query = array();
	if(!empty($segment[1])) {
		parse_str($segment[1], $query);
	}
	$params = array_merge($params, $query, array('i' => $_W['uniacid']));
	$query = http_build_query($params);
	$segment = trim($segment[0], "/");
	$url = "{$segment}?{$query}";
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/addons/we7_wmall/template/plateform/index.html?menu=#/' . trim($url, "/");
	}
	return $url;
}

function isurl($segment, $params = array(), $addhost = false) {
	global $_W;
	$segment = explode('?', $segment);
	$query = array();
	if(!empty($segment[1])) {
		parse_str($segment[1], $query);
	}
	$params = array_merge($params, $query, array('i' => $_W['uniacid']));
	$query = http_build_query($params);
	$segment = trim($segment[0], "/");
	$url = "{$segment}?{$query}";
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/addons/we7_wmall/template/manager/index.html?menu=#/' . trim($url, "/");
	}
	return $url;
}

function idurl($segment, $params = array(), $addhost = false) {
	global $_W;
	$segment = explode('?', $segment);
	$query = array();
	if(!empty($segment[1])) {
		parse_str($segment[1], $query);
	}
	$params = array_merge($params, $query, array('i' => $_W['uniacid']));
	$query = http_build_query($params);
	$segment = trim($segment[0], "/");
	$url = "{$segment}?{$query}";
	if($addhost) {
		$oauth_host = $_W['siteroot'];
		if(!empty($_W['we7_wmall']['config']['oauth']['oauth_host'])) {
			$oauth_host = $_W['we7_wmall']['config']['oauth']['oauth_host'];
		}
		$oauth_host = rtrim($oauth_host, "/");
		$url = $oauth_host . '/addons/we7_wmall/template/deliveryer/index.html?menu=#/' . trim($url, "/");
	}
	return $url;
}

function ifilter_url($params) {
	global $_W;
	if(empty($params)) {
		return '';
	}
	$query_arr = array();
	$parse = parse_url($_W['siteurl']);
	if(!empty($parse['query'])) {
		$query = $parse['query'];
		parse_str($query, $query_arr);
	}
	$params = explode(',', $params);
	foreach($params as $val) {
		if(!empty($val)) {
			$data = explode(':', $val);
			$query_arr[$data[0]] = trim($data[1]);
	}
	}
	$query_arr['page'] = 1;
	$query = http_build_query($query_arr);
	if($_W['_controller'] == 'store') {
		return './wmerchant.php?' . $query;
	} elseif(defined('IN_AGENT')) {
		return './wagent.php?' . $query;
	}
	return './index.php?' . $query;
}

function module_familys() {
	return array(
		'basic' => array(
			'title' => '外送基础版',
			'css' => 'label label-success'
		),
		'errander' => array(
			'title' => '外送+跑腿',
			'css' => 'label label-success'
		),
		'errander_deliveryerApp' => array(
			'title' => '外送+跑腿+配送员app',
			'css' => 'label label-success'
		),
		'vip' => array(
			'title' => 'vip版',
			'css' => 'label label-success'
		),
		'wxapp' => array(
			'title' => '小程序版',
			'css' => 'label label-success'
		),

	);
}

/*
 * $type (1:广告页, 2:首页幻灯片)
 * get_index_slide
 * */
function sys_fetch_slide($type = 'homeTop', $format = false, $agentid = 0) {
	global $_W;
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_slide') .'where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1 order by displayorder desc' ,array(':uniacid' => $_W['uniacid'], ':agentid' => $agentid > 0 ? $agentid : $_W['agentid'], ':type' => $type));
	if($type == 'startpage') {
		shuffle($slides);
	}
	if($format) {
		foreach($slides as &$slide) {
			$slide['thumb'] = tomedia($slide['thumb']);
			$slide['link'] = $slide['wxapp_link'];
		}
	}
	return $slides;
}

function tpl_format($params) {
	$send = array(
		'first' => array(
			'value' => $params['title'],
			'color' => '#ff510'
		),
		'keyword1' => array(
			'value' => "#{$params['ordersn']}",//订单编号
			'color' => '#ff510'
		),
		'keyword2' => array(
			'value' => $params['final_fee'],//订单金额
			'color' => '#ff510'
		),
		'keyword3' => array(
			'value' => $params['pay_type_cn'],//付款方式
			'color' => '#ff510'
		),
		'keyword4' => array(
			'value' => $params['delivery_title'],//配送方式
			'color' => '#ff510'
		),
		'keyword5' => array(
			'value' => $params['status_cn'],//订单状态
			'color' => '#ff510'
		),
		'remark' => array(
			'value' => $params['remark'],
			'color' => '#ff510'
		),
	);
	return $send;
}

function format_wxapp_tpl($data) {
	$send = array(
		'keyword1' => array(
			'value' => $data[0],
			'color' => '#ff510'
		),
		'keyword2' => array(
			'value' => $data[1],
			'color' => '#ff510'
		),
		'keyword3' => array(
			'value' => $data[2],
			'color' => '#ff510'
		),
		'keyword4' => array(
			'value' => $data[3],
			'color' => '#ff510'
		),
		'keyword5' => array(
			'value' => $data[4],
			'color' => '#ff510'
		),
		'keyword6' => array(
			'value' => $data[5],
			'color' => '#ff510'
		),
		'keyword7' => array(
			'value' => $data[6],
			'color' => '#ff510'
		)
	);
	return $send;
}

function icheck_verifycode($mobile, $code) {
	global $_W;
	$isexist = pdo_fetch('select * from ' . tablename('uni_verifycode') . ' where uniacid = :uniacid and receiver = :receiver and verifycode = :verifycode and createtime >= :createtime', array(':uniacid' => $_W['uniacid'], ':receiver' => $mobile, ':verifycode' => $code, ':createtime' => time()-1800));
	if(!empty($isexist)) {
		return true;
	}
	return false;
}

function sys_wechat_tpl_format($params) {
	$send = array();
	foreach($params as $key => $param) {
		$send[$key] = array(
			'value' => $param,
			'color' => '#ff510',
		);
	}
	return $send;
}

function i($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	return authcode($string, $operation, $key, $expiry);
}

function Jpush_response_parse($response) {
	if(is_error($response)) {
		return $response;
	}
	$result = @json_decode($response['content'], true);
	if(!empty($result['error'])) {
		return error(-1, "错误代码: {$result['error']['code']}, 错误信息: {$result['error']['message']}");
	}
	return true;
}

function mktTransfers_get_openid($id, $openid, $money, $type = 'store') {
	global $_W;
	$payment_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
	if(in_array($payment_wechat['type'], array('borrow', 'borrow_partner'))) {
		$oauth = pdo_get('tiny_wmall_oauth_fans', array('appid' => $payment_wechat[$payment_wechat['type']]['appid'], 'openid' => $openid));
		if(empty($oauth)) {
			if($type == 'store') {
				$status = store_getcash_notice($id, $money, 'borrow_openid');
			} elseif($type == 'deliveryer') {
				$status = deliveryer_getcash_notice($id, $money, 'borrow_openid');
			} elseif($type == 'agent') {
				$status = sys_notice_agent_getcash($id, $money, 'borrow_openid');
			} elseif($type == 'spread') {
				$status = sys_notice_spread_getcash($id, 'borrow_openid');
			} elseif($type == 'storebd') {
				$status = sys_notice_storebd_user_getcash($id, 0, 'borrow_openid');
			}
			if(is_error($status)) {
				return error(-1, '获取身份信息失败,请重新提交提现申请' . $status['message']);
			} else {
				return error(-1, '平台需要获取您的微信信息，并且给您微信发了一条消息，请再微信中模板消息中确认');
			}
		} else {
			$openid = $oauth['oauth_openid'];
		}
	}
	return $openid;
}

function tocategory($category, $separator = ',') {
	global $_W;
	if(empty($category)) {
		return '';
	}
	$category_arr = explode('|', $category);
	$category_temp = array();
	if(!empty($category_arr)) {
		foreach($category_arr as $row) {
			$row = intval($row);
			if($row) {
				$category_temp[] = $row;
			}
		}
	}
	if(empty($category_temp)) {
		return '';
	}
	$category = implode(',', $category_temp);
	$data = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store_category') . " where uniacid = :uniacid and id in ({$category})", array(':uniacid' => $_W['uniacid']), 'id');
	if(empty($data)) {
		return $data;
	}
	$return = array();
	foreach($data as $da) {
		$return[] = $da['title'];
	}
	return implode($separator, $return);
}

function totime($times, $separator = ',') {
	$times = iunserializer($times);
	if(empty($times)) {
		return '';
	}
	$return = array();
	foreach($times as $time) {
		$return[] = "{$time['start_hour']}~{$time['end_hour']}";
	}
	return implode($separator, $return);
}
function toplateform($key, $all = false) {
	$plateform = array(
		'we7_wmall' => array(
			'css' => 'label label-default',
			'text' => '本平台',
			'color' => '',
		),
		'eleme' => array(
			'css' => 'label label-primary',
			'text' => '饿了么平台',
			'color' => '',
		),
		'meituan' => array(
			'css' => 'label label-warning',
			'text' => '美团平台',
			'color' => '',
		),
	);
	if(empty($all)) {
		return $plateform[$key]['text'];
	} else {
		return $plateform[$key];
	}
}

function flog($name, $message, $filename = 'we7_wmall', $clean = false) {
	$filename = IA_ROOT . "/addons/we7_wmall/resource/logs/{$filename}.txt";
	if($clean) {
		@unlink($filename);
	}
	load()->func('file');
	mkdirs(dirname($filename));
	$content = date('Y-m-d H:i:s') . " {$name} :开始==================\n";
	$content .= var_export($message, 1);
	$content .= "\n";
	$content .= date('Y-m-d H:i:s') . " {$name} :结束==================\n";
	$content .= "\n";
	$fp = fopen($filename, 'a+');
	fwrite($fp, $content);
	fclose($fp);
	return true;
}

function slog($type, $title, $params, $message) {
	global $_W;
	//wxtplNotice, managerappJpush, deliveryerappJpush, alidayuSms, alidayuCall, ordergrant, credit1Update, credit2Update, couponGrant, shareRedpacket, longurl2short, yinsihao
	if($_W['we7_wmall']['global']['slog_status'] == 2) {
		return true;
	}
	if(empty($type)) {
		return error(-1, '错误类型不能为空');
	}
	if(empty($message)) {
		return error(-1, '错误信息不能为空');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'type' => $type,
		'title' => $title,
		'params' => iserializer($params),
		'message' => iserializer($message),
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_system_log', $data);
	return true;
}

function getcash_channels($type = '', $key = 'all') {
	$data = array(
		'wxapp' => array(
			'type' => 'wxapp',
			'text' => '提现到微信-小程序',
			'css' => 'label label-info',
		),
		'weixin' => array(
			'type' => 'weixin',
			'text' => '提现到微信-公众号',
			'css' => 'label label-info',
		),
		'bank' => array(
			'type' => 'bank',
			'text' => '提现到银行卡',
			'css' => 'label label-success',
		),
		'alipay' => array(
			'type' => 'alipay',
			'text' => '提现到支付宝',
			'css' => 'label label-warning',
		)
	);
	if(empty($type)) {
		return $data;
	}
	if($key == 'all') {
		return $data[$type];
	} elseif($key == 'text') {
		return $data[$type]['text'];
	} elseif($key == 'css') {
		return $data[$type]['css'];
	}
}

function getcash_toaccount_status($status = '', $key = 'all', $mobile = false) {
	global $_W;
	$data = array(
		'1' => array(
			'text' => '处理中',
			'css' => $mobile ? 'c-info' : 'label-info',
		),
		'2' => array(
			'text' => '打款成功',
			'css' => $mobile ? 'c-danger' : 'label-success',
		),
		'3' => array(
			'text' => '打款失败',
			'css' => $mobile ? 'c-primary' : 'label-danger',
		),
	);
	if(empty($status)) {
		return $data;
	}
	if($key == 'all') {
		return $data[$status];
	} elseif($key == 'text') {
		return $data[$status]['text'];
	} elseif($key == 'css') {
		return $data[$status]['css'];
	}
}

function record_member_scan() {
	global $_W, $_GPC;
	$uid = intval($_W['member']['uid']);
	if(empty($uid)) {
		return true;
	}
	$sid = intval($_GPC['sid']);
	$stat_day = date('Ymd');
	$cache_key = "scan:{$_W['uniacid']}:{$stat_day}:{$sid}:{$uid}";
	$cache = cache_read($cache_key);
	if(!empty($cache)) {
		return true;
	}
	$record = pdo_fetch('select * from ' . tablename('tiny_wmall_member_scan_record') . ' where uniacid = :uniacid and sid = :sid and stat_day = :stat_day ', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':stat_day' => $stat_day));
	if(!empty($record)) {
		$uids = trim($record['uids'], ',');
		$uids = explode(',', $uids);
		if(!in_array($uid, $uids)) {
			$uids[] = $uid;
			$update = array(
				'uids' => ',' . implode(',', $uids) . ',',
				'nums' => count($uids)
			);
			pdo_update('tiny_wmall_member_scan_record', $update, array('uniacid' => $_W['uniacid'], 'id' => $record['id']));
			cache_write($cache_key, 1);
		}
	} else {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'uids' => ',' . $uid . ',',
			'nums' => 1,
			'stat_day' => date('Ymd')
		);
		pdo_insert('tiny_wmall_member_scan_record', $insert);
		cache_write($cache_key, 1);
	}
	return true;
}



