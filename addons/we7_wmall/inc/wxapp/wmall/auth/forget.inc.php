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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
		$password = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '请输入密码'), '', 'ajax');
		$length = strlen($password);
		if($length < 8 || $length > 20) {
			imessage(error(-1, '请输入8-20密码'), '', 'ajax');
		}
		if(!preg_match(IREGULAR_PASSWORD, $password)) {
			imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
		}
		$code = trim($_GPC['code']);
		$status = icheck_verifycode($mobile, $code);
		if(!$status) {
			imessage(error(-1, '验证码错误'), '', 'ajax');
		}
		$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($member)) {
			imessage(error(-1, '此手机号未注册'), '', 'ajax');
		}
		$update = array(
			'mobile_audit' => 1,
			'salt' => random(6, true),
		);
		$update['password'] = md5(md5($update['salt'] . trim($password)) . $update['salt']);
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
		$result = array(
			'member' => $member,
		);
		imessage(error(0, $result), '', 'ajax');
	}
	$config_mall['logo'] = tomedia($config_mall['logo']);
	$result = array(
		'captcha' => iaurl('system/common/captcha', array('state' => "we7sid-{$_W['session_id']}", 't' => TIMESTAMP, ), true),
		'config_mall' => $config_mall,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'captcha') {
	$result = array(
		'captcha' => iaurl('system/common/captcha', array('state' => "we7sid-{$_W['session_id']}", 't' => TIMESTAMP, ), true),
	);
	imessage(error(0, $result), '', 'ajax');
}

