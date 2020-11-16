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
function mload() {
	static $mloader;
	if(empty($mloader)) {
		$mloader = new Mloader();
	}
	return $mloader;
}

function pload() {
	mload()->model('plugin');
	static $ploader;
	if(empty($ploader)) {
		$ploader = new Ploader();
	}
	return $ploader;
}

function check_plugin_perm($name) {
	global $_W;
	static $_plugins = array();
	if(isset($_plugins[$name])) {
		return $_plugins[$name];
	}
	$dir = WE7_WMALL_PLUGIN_PATH . $name . '/inc';
	if(!is_dir($dir)) {
		$_plugins[$name] = false;
		return $_plugins[$name];
	}
	if(!empty($_W['_plugins'])) {
		if(empty($_W['_plugins'][$name])) {
			$_plugins[$name] = false;
			return $_plugins[$name];
		}
	} else {
		$plugin = pdo_get('tiny_wmall_plugin', array('name' => $name), array('id', 'name'));
		if(empty($plugin)) {
			$_plugins[$name] = false;
			return $_plugins[$name];
		}
	}
	$perms = $_W['_accountperm'];
	if(empty($perms)) {
		mload()->model('common');
		$perms = get_account_perm();
	}
	if(empty($perms) || in_array($name, $perms['plugins'])) {
		$_plugins[$name] = true;
	} else {
		$_plugins[$name] = false;
	}
	return $_plugins[$name];
}

function check_plugin_exist($name) {
	global $_W;
	static $_plugins_exist = array();
	if(isset($_plugins_exist[$name])) {
		return $_plugins_exist[$name];
	}
	if(!empty($_W['_plugins'])) {
		$_plugins_exist[$name] = false;
		if(in_array($name, array_keys($_W['_plugins']))) {
			$_plugins_exist[$name] = true;
		}
	} else {
		$plugin = pdo_get('tiny_wmall_plugin', array('name' => $name), array('id', 'name'));
		if(empty($plugin)) {
			$_plugins_exist[$name] = false;
			return $_plugins_exist[$name];
		}
		$_plugins_exist[$name] = true;
	}
	return $_plugins_exist[$name];
}

function fans_info_query($openid) {
	global $_W;
	load()->func('communication');
	static $account_api;
	if(empty($account_api)) {
		$account_api = WeAccount::create();
	}
	$fan = $account_api->fansQueryInfo($openid, true);
	if(!is_error($fan) && $fan['subscribe'] == 1) {
		$fan['nickname'] = stripcslashes($fan['nickname']);
		$fan['remark'] = !empty($fan['remark']) ? stripslashes($fan['remark']) : '';
	} else {
		$fan = array();
	}
	return $fan;
}

