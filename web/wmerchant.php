<?php
define('IN_IA', true);
define('STARTTIME', microtime());
define('IA_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('TIMESTAMP', time());

$_W = $_GPC = array();
$configfile = IA_ROOT . "/data/config.php";

if(!file_exists($configfile)) {
	exit('配置文件不存在或是不可读，请检查“data/config”文件或是重新安装！');
}

require $configfile;
require IA_ROOT . '/framework/version.inc.php';
require IA_ROOT . '/framework/const.inc.php';
require IA_ROOT . '/framework/class/loader.class.php';
load()->func('global');
load()->func('compat');
load()->func('pdo');
load()->classs('account');
load()->model('cache');
load()->model('account');
load()->model('setting');
load()->model('user');
load()->model('permission');
load()->func('tpl');
load()->classs('db');
load()->web('common');
error_reporting(0);
define('CLIENT_IP', getip());

$_W['config'] = $config;
$_W['config']['db']['tablepre'] = !empty($_W['config']['db']['master']['tablepre']) ? $_W['config']['db']['master']['tablepre'] : $_W['config']['db']['tablepre'];
$_W['timestamp'] = TIMESTAMP;
$_W['charset'] = $_W['config']['setting']['charset'];
$_W['clientip'] = CLIENT_IP;
$_W['token'] = token();

unset($configfile, $config);

// 附件地址绝对路径
define('ATTACHMENT_ROOT', IA_ROOT .'/attachment/');
if(!in_array($_W['config']['setting']['cache'], array('mysql', 'file', 'memcache'))) {
	$_W['config']['setting']['cache'] = 'mysql';
}
load()->func('cache');

if(function_exists('date_default_timezone_set')) {
	date_default_timezone_set($_W['config']['setting']['timezone']);
}

if(!empty($_W['config']['setting']['memory_limit']) && function_exists('ini_get') && function_exists('ini_set')) {
	if(@ini_get('memory_limit') != $_W['config']['setting']['memory_limit']) {
		@ini_set('memory_limit', $_W['config']['setting']['memory_limit']);
	}
}
if(isset($_W['config']['setting']['https']) && $_W['config']['setting']['https'] == '1') {
	$_W['ishttps'] = $_W['config']['setting']['https'];
} else {
	$_W['ishttps'] = $_SERVER['SERVER_PORT'] == 443 ||
	(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') ||
	strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https' ||
	strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https' ? true : false;
}
$_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$_W['ispost'] = $_SERVER['REQUEST_METHOD'] == 'POST';

$_W['sitescheme'] = $_W['ishttps'] ? 'https://' : 'http://';
$_W['script_name'] = htmlspecialchars(scriptname());
$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
$_W['siteroot'] = htmlspecialchars($_W['sitescheme'] . $_SERVER['HTTP_HOST'] . $sitepath);

if(substr($_W['siteroot'], -1) != '/') {
	$_W['siteroot'] .= '/';
}
$urls = parse_url($_W['siteroot']);
$urls['path'] = str_replace(array('/web', '/app', '/payment/wechat', '/payment/alipay', '/api'), '', $urls['path']);
$_W['siteroot'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '').$urls['path'];
$_W['siteurl'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '') . $_W['script_name'] . (empty($_SERVER['QUERY_STRING'])?'':'?') . $_SERVER['QUERY_STRING'];

if(MAGIC_QUOTES_GPC) {
	$_GET = istripslashes($_GET);
	$_POST = istripslashes($_POST);
	$_COOKIE = istripslashes($_COOKIE);
}
$cplen = strlen($_W['config']['cookie']['pre']);
foreach($_COOKIE as $key => $value) {
	if(substr($key, 0, $cplen) == $_W['config']['cookie']['pre']) {
		$_GPC[substr($key, $cplen)] = $value;
	}
}
unset($cplen, $key, $value);

$_GPC = array_merge($_GET, $_POST, $_GPC);
$_GPC = ihtmlspecialchars($_GPC);
if(!$_W['isajax']) {
	$input = file_get_contents("php://input");
	if (!empty($input)) {
		$__input = @json_decode($input, true);
		if (!empty($__input)) {
			$_GPC['__input'] = $__input;
			$_W['isajax'] = true;
		}
	}
	unset($input, $__input);
}

setting_load();
define('DEVELOPMENT', $_W['setting']['copyright']['develop_status'] == 1 || $_W['config']['setting']['development'] == 1);
if(DEVELOPMENT) {
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}
$_W['page'] = array();
$_W['page']['copyright'] = $_W['setting']['copyright'];

header('Content-Type: text/html; charset=' . $_W['charset']);

if(!function_exists('uni_setting_load')) {
	function uni_setting_load($name = '', $uniacid = 0) {
		global $_W;
		$uniacid = empty($uniacid) ? $_W['uniacid'] : $uniacid;
		$cachekey = "unisetting:{$uniacid}";
		$unisetting = cache_load($cachekey);
		if (empty($unisetting)) {
			$unisetting = pdo_get('uni_settings', array('uniacid' => $uniacid));
			if (!empty($unisetting)) {
				$serialize = array('site_info', 'stat', 'oauth', 'passport', 'uc', 'notify',
					'creditnames', 'default_message', 'creditbehaviors', 'payment',
					'recharge', 'tplnotice', 'mcplugin', 'statistics', 'bind_domain');
				foreach ($unisetting as $key => &$row) {
					if (in_array($key, $serialize) && !empty($row)) {
						$row = (array)iunserializer($row);
					}
				}
			} else {
				$unisetting = array();
			}
			cache_write($cachekey, $unisetting);
		}
		if (empty($unisetting)) {
			return array();
		}
		if (empty($name)) {
			return $unisetting;
		}
		if (!is_array($name)) {
			$name = array($name);
		}
		return array_elements($name, $unisetting);
	}
}

if(!function_exists('uni_permission')) {
	function uni_permission($uid = 0, $uniacid = 0) {
		global $_W;
		$uid = empty($uid) ? $_W['uid'] : intval($uid);
		$uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
		$founders = explode(',', $_W['config']['setting']['founder']);
		if (in_array($uid, $founders)) {
			return 'founder';
		}

		$sql = 'SELECT `role` FROM ' . tablename('uni_account_users') . ' WHERE `uid`=:uid AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':uid'] = $uid;
		$pars[':uniacid'] = $uniacid;
		$role = pdo_fetchcolumn($sql, $pars);
		if(empty($role)) {
			return false;
		}
		return in_array($role, array('manager', 'owner')) ? 'manager' : 'operator';
	}
}

$session = json_decode(authcode($_GPC['__session']), true);
if (empty($session)) {
	$session = json_decode(base64_decode($_GPC['__session']), true);
}
if(is_array($session)) {
	if($_GPC['_sid'] > 0 || $_GPC['__sid'] > 0) {
		$user = user_single(array('uid'=> $session['uid']));
		if(is_array($user) && ($session['hash'] == md5($user['password'] . $user['salt']) || $session['hash'] == $user['hash'])) {
			$_W['uid'] = $user['uid'];
			$_W['username'] = $user['username'];
			$user['currentvisit'] = $user['lastvisit'];
			$user['currentip'] = $user['lastip'];
			$user['lastvisit'] = $session['lastvisit'];
			$user['lastip'] = $session['lastip'];
			$_W['user'] = $user;
			$founders = explode(',', $_W['config']['setting']['founder']);
			$_W['isfounder'] = in_array($_W['uid'], $founders);
			if($_GPC['sid'] > 0) {
				isetcookie('__sid', $_GPC['sid'], 7*86400);
			}
			unset($founders);
		} else {
			isetcookie('__session', false, -100);
			isetcookie('__sid', 0, -1000);
		}
	} else {
		isetcookie('__session', false, -100);
		isetcookie('__sid', 0, -1000);
	}
	unset($user);
}
unset($session);

if(!empty($_GPC['i'])) {
	$_W['uniacid'] = intval($_GPC['i']);
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['account']['acid'];
	if(!empty($_W['uid'])) {
		$_W['role'] = uni_permission($_W['uid'], $_W['uniacid']);
		if(empty($_W['role'])) {
			exit('您没有管理该公众号的权限');
		}
		if($_W['role'] == 'owner') {
			$_W['role'] = 'manager';
		}
	}
}

if(empty($_W['uniacid'])) {
	exit('公众号信息错误');
}

if((empty($_W['isfounder']) && empty($_W['role']))) {
	$session = json_decode(base64_decode($_GPC['__we7_wmall_agent']), true);
	if(is_array($session)) {
		if($_GPC['_sid'] > 0 || $_GPC['__sid'] > 0) {
			$agent = pdo_get('tiny_wmall_agent', array('id' => $session['id']));
			$agent_password = $agent['password'];
			if($session['operator_type'] == 'operator') {
				$agent_user = pdo_get('tiny_wmall_agent_users', array('id' => $session['user_id']));
				$agent_password = $agent_user['password'];
				if($agent_user['agentid'] != $agent['id']) {
					exit('代理信息错误');
				}
			}
			if(is_array($session) && $session['hash'] == $agent_password) {
				$_W['role'] = 'agenter';
				$_W['we7_wmall']['agent'] = $_W['agent'] = $agent;
				$_W['agentid'] = $agent['id'];
				if(!empty($agent_user)) {
					$_W['role'] = 'agent_operator';
					$_W['we7_wmall']['agent_user'] = $agent_user;
					$_W['agent']['realname'] = $_W['we7_wmall']['agent_user']['username'];
				}
				if($_GPC['sid'] > 0) {
					isetcookie('__sid', $_GPC['sid'], 7*86400);
				}
				isetcookie('__agent_id', $agent['id'], 7*86400);
				if($_W['uniacid'] != $_GPC['uniacid']) {
					isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
				}
			} else {
				isetcookie('__we7_wmall_agent', false, -1000);
				isetcookie('__agent_id', 0, -1000);
				isetcookie('__sid', 0, -1000);
			}
		} else {
			isetcookie('__we7_wmall_agent', false, -1000);
			isetcookie('__agent_id', 0, -1000);
			isetcookie('__sid', 0, -1000);
		}
	}
	unset($session);
}

if(empty($_W['isfounder']) && $_W['role'] != 'manager' && $_W['role'] != 'operator' && $_W['role'] != 'agenter' && $_W['role'] != 'agent_operator') {
	$session = json_decode(base64_decode($_GPC['__we7_wmall_store']), true);
	if(is_array($session)) {
		$user = pdo_get('tiny_wmall_clerk', array('id' => $session['clerk_id']));
		if(is_array($user) && $session['hash'] == $user['password']) {
			$perms = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $user['id'], 'role' => 'manager'), array(), 'sid');
			if(empty($perms)) {
				exit('您的申请是店员身份,没有权限管理店铺！');
			}
			$sids = array_keys($perms);
			$_W['role'] = 'merchanter';
			$_W['clerk'] = $user;
			$_W['we7_wmall']['store'] = pdo_get('tiny_wmall_store', array('id' => $sids[0]), array('title', 'id'));
			isetcookie('__sid', $user['sid'], 7*86400);
			if($_W['uniacid'] != $_GPC['uniacid']) {
				isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
			}
		} else {
			isetcookie('__we7_wmall_store', false, -100);
			isetcookie('__sid', 0, -1000);
		}
		unset($user);
	}
	unset($session);
}
$unisetting = uni_setting_load();
if(empty($_W['setting']['upload'])) {
	$_W['setting']['upload'] = array_merge($_W['config']['upload']);
}
if(!empty($unisetting['remote']['type'])) {
	$_W['setting']['remote'] = $unisetting['remote'];
}
if(!empty($_W['setting']['remote'][$_GPC['i']]['type'])) {
	$_W['setting']['remote'] = $_W['setting']['remote'][$_GPC['i']];
}
$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
if (!empty($_W['setting']['remote']['type'])) {
	if ($_W['setting']['remote']['type'] == 1) {
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
	} elseif ($_W['setting']['remote']['type'] == 2) {
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'].'/';
	} elseif ($_W['setting']['remote']['type'] == 3) {
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'].'/';
	} elseif ($_W['setting']['remote']['type'] == 4) {
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'].'/';
	}
}

$entry = array(
	'module' => 'we7_wmall',
	'do' => 'web',
);
define('IN_SYS', true);
define('IN_MODULE', $entry['module']);
load()->web('template');

$site = WeUtility::createModuleSite($entry['module']);
if(!is_error($site)) {
	$method = 'doWeb' . ucfirst($entry['do']);
	exit($site->$method());
}
exit();