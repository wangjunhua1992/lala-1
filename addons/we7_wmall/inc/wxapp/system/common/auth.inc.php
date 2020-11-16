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
global $_W, $_GPC;
mload()->classs('wxapp');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'openid';

if ($ta == 'openid') {
	$code = $_GPC['code'];
	if(empty($code)) {
		imessage(error(-1, '通信错误，请在微信中重新发起请求'), '', 'ajax');
	}
	$token = trim($_GPC['token']);
	if(empty($token)) {
		imessage(error(41009, '请重新登录'), '', 'ajax');
	}
	$oauth = pdo_get('tiny_wmall_oauth_fans', array('openid' => $token));
	if(!empty($oauth['oauth_openid'])) {
		imessage(error(0, ''), '', 'ajax');
	}
	$type = trim($_GPC['type']);
	if($type == 'manager') {
		$account = get_plugin_config('wxapp.manager');
	} elseif($type == 'deliveryer') {
		$account = get_plugin_config('wxapp.deliveryer');
	}
	$account_api = new Wxapp($account);
	$oauth = $account_api->getOauthInfo($code);
	if (!empty($oauth) && !is_error($oauth)) {
		$insert = array(
			'appid' => $account['key'],
			'openid' => $token,
			'oauth_openid' => $oauth['openid'],
			'type' => 'wxapp',
		);
		pdo_insert('tiny_wmall_oauth_fans', $insert);
		imessage(error(0, ''), '', 'ajax');
	} else {
		imessage(error(-1, $oauth['message']), '', 'ajax');
	}
}