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
mload()->model('plugin');
pload()->model('majiaApp');

$forward = trim($_GPC['forward']);
$token = trim($_GPC['token']);
if(empty($token)) {
	$forward = ivurl('pages/auth/majia', array(), true);
	header('location: ' . $forward);
	die;
}

isetcookie('__majia_token', $token, 1800);
$_GPC['__majia_token'] = $token;

$userinfo = get_user_info();
if(is_error($userinfo)) {
	imessage($userinfo['message'], '', 'ajax');
}
$uid = intval($userinfo['user_id']);
$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid_majia' => $uid));
if(empty($member)) {
	$member = array(
		'uniacid' => $_W['uniacid'],
		'openid' => '',
		'uid' => date('His') . random(3, true),
		'uid_majia' => $uid,
		'mobile' => trim($userinfo['phone']),
		'nickname' => trim($userinfo['name']),
		'realname' => trim($userinfo['name']),
		'sex' => ($userinfo['sex'] == 1 ? '男' : '女'),
		'avatar' => trim($userinfo['head']),
		'is_sys' => 2, //模拟用户
		'status' => 1,
		'token' => random(32),
		'addtime' => TIMESTAMP,
		'salt' => random(6, true),
		'register_type' => 'app',
	);
	$member['password'] = md5(md5($member['salt'] . trim($deviceid)) . $member['salt']);
	pdo_insert('tiny_wmall_members', $member);
} else {
	$data = array(
		'nickname' => trim($userinfo['name']),
		'avatar' => trim($userinfo['head']),
	);
	if(empty($member['token'])) {
		$data['token'] = $member['token'] = random(32);
	}
	pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid_majia' => $uid));
}
if(!empty($member)) {
	isetcookie('itoken', $member['token'], 7 * 86400);
}
$forward = '';
if(!empty($_GPC['forward'])) {
	$forward = urldecode($_GPC['forward']);
	if(!empty($forward) && strexists($forward, 'pages/auth/')) {
		$forward = '';
	}
}
$forward = empty($forward) ? ivurl('pages/home/index', array(), true) : $forward;
header('location: ' . $forward);
die;
