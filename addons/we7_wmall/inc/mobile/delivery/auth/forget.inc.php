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
$_W['page']['title'] = '找回密码';
$config_mall = $_W['we7_wmall']['config']['mall'];

if($_W['isajax']) {
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
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($deliveryer)) {
		imessage(error(-1, '此手机号未注册'), '', 'ajax');
	}
	if($deliveryer['status'] != 1) {
		imessage(error(-1, '此手机号注册的配送员账号已被删除, 如需继续使用请联系管理员'), '', 'ajax');
	}
	$update = array(
		'salt' => random(6, true),
	);
	$update['password'] = md5(md5($update['salt'] . $password) . $update['salt']);
	pdo_update('tiny_wmall_deliveryer', $update, array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	$deliveryer['hash'] = $update['password'];
	$key = "we7_wmall_deliveryer_session_{$_W['uniacid']}";
	$cookie = base64_encode(json_encode($deliveryer));
	isetcookie($key, $cookie, 7 * 86400);
	$forward = trim($_GPC['forward']);
	if(empty($forward)) {
		$forward = imurl('delivery/home/index');
	}
	imessage(error(0, $forward), '', 'ajax');
}

include itemplate('auth/forget');