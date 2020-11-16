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
function build_menu() {
	global $_W, $_GPC;
	if(defined('IN_PLUGIN')) {
		if(defined('IN_AGENT')) {
			if(defined('IN_AGENT_PLUGIN')) {
				include itemplate("tabs");
			} else {
				include itemplate("{$_W['_controller']}/tabs");
			}
		} else {
			include itemplate('tabs');
		}
	} elseif(defined('IN_MERCHANT')) {
		$file = WE7_WMALL_PATH . "template/web/{$_W['_controller']}/{$_W['_action']}/tabs.html";
		if(is_file($file)) {
			include itemplate("{$_W['_controller']}/{$_W['_action']}/tabs");
		} else if($_W['_controller'] == 'store' && defined('IN_GOHOME_APLUGIN')) {
			include itemplate("{$_W['_controller']}/gohome/tabs");
		}
	} else {
		include itemplate("{$_W['_controller']}/tabs");
	}
	return true;
}

function imessage($msg, $redirect = '', $type = '') {
	global $_W, $_GPC;
	define('IN_IMESSAGE', 1);
	$_W['page']['title'] = '系统提示';
	if($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}
	if($redirect == 'referer') {
		$redirect = ireferer();
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
	if (empty($msg) && !empty($redirect)) {
		header('location: '.$redirect);
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
mload()->model('coupon');
coupon_lala();

