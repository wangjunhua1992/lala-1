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

function imessage($msg, $redirect = '', $type = '') {
	global $_W, $_GPC;
	define('IN_IMESSAGE', 1);
	$_W['page']['title'] = '系统提示';
	$title = $msg;
	if(is_array($msg)) {
		$message = ((isset($msg['message']) ? $msg['message'] : ''));
		$title = ((isset($msg['title']) ? $msg['title'] : ''));
		$btn_text = ((isset($msg['btn_text']) ? $msg['btn_text'] : ''));
	}

	if($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	} elseif($redirect == 'referer') {
		$redirect = ireferer();
	} elseif ($redirect == 'close') {
		$redirect = 'javascript:;';
		$close = true;
	}

	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';
	} else {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success';
	}
	if($_W['isajax'] || !empty($_GET['isajax']) || $type == 'ajax') {
		$vars = array();
		if(is_array($msg)) {
			$msg['url'] = $redirect;
		}
		$vars['message'] = $msg;
		$vars['url'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
	$label = $type;
	if($type == 'error') {
		$label = 'danger';
	}
	if($type == 'ajax' || $type == 'sql') {
		$label = 'warning';
	}
	include itemplate('public/message', TEMPLATE_INCLUDEPATH);
	exit();
}

function get_mall_menu($menu_id = 0) {
	global $_W, $_GPC;
	$file = 'public/nav';
	if(check_plugin_perm('diypage')) {
		$menu_id = intval($menu_id);
		if(empty($menu_id)) {
			$key = 'takeout';
			if($_W['_controller'] == 'errander') {
				$key = 'errander';
			} elseif($_W['_controller'] == 'ordergrant') {
				$key = 'ordergrant';
			}
			$config_menu = get_plugin_config('diypage.menu');
			if(is_array($config_menu) && !empty($config_menu[$key])) {
				$menu_id = intval($config_menu[$key]);
			}
		}
		if($menu_id > 0) {
			$temp = pdo_get('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'id' => $menu_id));
			if(!empty($temp)) {
				$menu = json_decode(base64_decode($temp['data']), true);
				$file = 'diypage/menu';
			}
		}
	}
	include itemplate($file, TEMPLATE_INCLUDEPATH);
	return true;
}

function menu_active($url) {
	global $_W, $_GPC;
	if(strexists($url, 'http://') || strexists($url, 'https://') || empty($url)){
		return;
	}
	$ctrl = trim($_GPC['c']);
	$action = trim($_GPC['ac']);
	parse_str($url, $urls);
	$_router = implode('/', array($urls['ctrl'], $urls['ac'], $urls['op']));
	if($_W['_router'] == $_router) {
		return 'active';
	}
	return;
}

function get_mall_danmu() {
	global $_W, $_GPC;
	if(check_plugin_perm('diypage')) {
		$config_danmu = get_plugin_config('diypage.danmu');
		if(is_array($config_danmu) && $config_danmu['params']['status'] == 1) {
			$file = 'diypage/danmu';
			include itemplate($file, TEMPLATE_INCLUDEPATH);
		}
	}
	return true;
}

function get_mall_superRedpacket() {
	global $_W, $_GPC;
	icheckauth(false);
	if(check_plugin_perm('superRedpacket')) {
		$activity_id = pdo_fetchcolumn('select activity_id from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and channel = :channel and status = 1 and is_show = 0', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':channel' => 'superRedpacket'));
		if($activity_id > 0) {
			$file = 'superRedpacket/index';
			include itemplate($file, TEMPLATE_INCLUDEPATH);
		}
	}
	return true;
}

function iregister_jssdk($debug = false){
	global $_W;
	if (defined('HEADER')) {
		echo '';
		return;
	}
	$sysinfo = array(
		'uniacid' 	=> $_W['uniacid'],
		'acid' 		=> $_W['acid'],
		'siteroot' 	=> $_W['siteroot'],
		'siteurl' 	=> $_W['siteurl'],
		'attachurl' => $_W['attachurl'],
		'cookie' 	=> array('pre'=>$_W['config']['cookie']['pre'])
	);
	if(!empty($_W['acid'])) {
		$sysinfo['acid'] = $_W['acid'];
	}
	if(!empty($_W['openid'])) {
		$sysinfo['openid'] = $_W['openid'];
	}
	if(defined('MODULE_URL')) {
		$sysinfo['MODULE_URL'] = MODULE_URL;
	}
	$sysinfo = json_encode($sysinfo);
	$jssdkconfig = json_encode($_W['account']['jssdkconfig']);
	$debug = $debug ? 'true' : 'false';

	$script = <<<EOF
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
	window.sysinfo = window.sysinfo || $sysinfo || {};
	// jssdk config 对象
	jssdkconfig = $jssdkconfig || {};
	// 是否启用调试
	jssdkconfig.debug = $debug;
	jssdkconfig.jsApiList = [
		'checkJsApi',
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
		'onMenuShareQQ',
		'onMenuShareWeibo',
		'hideMenuItems',
		'showMenuItems',
		'hideAllNonBaseMenuItem',
		'showAllNonBaseMenuItem',
		'translateVoice',
		'startRecord',
		'stopRecord',
		'onRecordEnd',
		'playVoice',
		'pauseVoice',
		'stopVoice',
		'uploadVoice',
		'downloadVoice',
		'chooseImage',
		'previewImage',
		'uploadImage',
		'downloadImage',
		'getNetworkType',
		'openLocation',
		'getLocation',
		'hideOptionMenu',
		'showOptionMenu',
		'closeWindow',
		'scanQRCode',
		'chooseWXPay',
		'openProductSpecificView',
		'addCard',
		'chooseCard',
		'openCard'
	];
	wx.config(jssdkconfig);
</script>
EOF;
	echo $script;
}