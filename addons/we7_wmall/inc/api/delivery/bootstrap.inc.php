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
global $_W, $_GPC, $_POST;
mload()->classs('TyAccount');
mload()->func('common');
mload()->model('common');
mload()->model('store');
mload()->model('order');
mload()->model('deliveryer');
mload()->model('deliveryer.extra');
define('IN_DELIVERYAPP', 1);

if(in_array($do, array('login'))) {
	$result = api_check_sign($_POST, $_POST['sign']);
	if(!$result) {
		//message(ierror(-1, '签名错误'), '', 'ajax');
	}
} else {
	$token = trim($_POST['token']);
	if(empty($token)) {
		message(ierror(-1, '身份验证失败, 请重新登录'), '', 'ajax');
	}
	$deliveryer = deliveryer_fetch($token, 'token');
	if(empty($deliveryer)) {
		message(ierror(-1, '身份验证失败, 请重新登录'), '', 'ajax');
	}
	if(empty($deliveryer['is_errander']) && empty($deliveryer['is_takeout'])) {
		message(ierror(-1, '您没有抢单的权限, 请联系平台管理员分配接单权限'), '', 'ajax');
	}
	$_W['deliveryer'] = $_W['we7_wmall']['deliveryer']['user'] = $deliveryer;
	$_W['is_agent'] = is_agent();
	$_W['agentid'] = 0;
	if($_W['is_agent']) {
		mload()->model('agent');
		$_W['agentid'] = $_W['deliveryer']['agentid'];
		if(empty($_W['agentid'])) {
			message(ierror(-1, '未找到配送员所属的代理区域,请先给配送员分配所属的代理'), '', 'ajax');
		}
	}
}

$_W['we7_wmall']['config'] = get_system_config();
$config_takeout = $_W['we7_wmall']['config']['takeout'];
$config_delivery = $_W['we7_wmall']['config']['delivery'];
$config_errander = get_plugin_config('errander');
$_W['role'] = 'deliveryer';
$_W['role_cn'] = "配送员:{$_W['deliveryer']['title']}";

