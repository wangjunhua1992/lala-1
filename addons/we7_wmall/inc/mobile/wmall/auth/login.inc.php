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
$_W['page']['title'] = '登录';
$config_mall = $_W['we7_wmall']['config']['mall'];

if(is_weixin() || !empty($_GPC["we7_wmall_member_session_{$_W['uniacid']}"])) {
	header('location: ' . ireferer());
	die;
}
if($_W['isajax']) {
	$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($member)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}
	$password = md5(md5($member['salt'] . trim($_GPC['password'])) . $member['salt']);
	if($password != $member['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}
	$member['hash'] = $password;
	$key = "we7_wmall_member_session_{$_W['uniacid']}";
	$cookie = array(
		'uid' => $member['uid'],
		'hash' => $member['hash'],
	);
	$cookie = base64_encode(json_encode($cookie));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);
	if(empty($forward)) {
		$forward = imurl('wmall/home/index');
	} else {
		$forward = $_W['siteroot'] . 'app/index.php?' . base64_decode(urldecode($forward));
	}
	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/login');