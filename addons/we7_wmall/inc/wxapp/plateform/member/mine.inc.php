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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$plateformer = $_W['plateformer'];
	$result = array(
		'plateformer' => $plateformer,
		'user' => array(
			'token' => "{$plateformer['usertype']}:{$plateformer['token']}",
			'perms' => $plateformer['perms'],
			'role' => $plateformer['role']
		),
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'password') {
	$plateformer = $_W['plateformer'];
	$password = trim($_GPC['password']);
	if(empty($password)) {
		imessage(error(-1, '当前密码不能为空'), '', 'ajax');
	}
	$newpassword = trim($_GPC['newpassword']);
	if(empty($newpassword)) {
		imessage(error(-1, '新密码密码不能为空'), '', 'ajax');
	}
	$repasswrod = trim($_GPC['repassword']);
	if(empty($repasswrod)) {
		imessage(error(-1, '请确认新密码'), '', 'ajax');
	}
	if($newpassword != $repasswrod) {
		imessage(error(-1, '两次密码输入不一致'), '', 'ajax');
	}
	if($plateformer['usertype'] == 'plateform') {
		load()->model('user');
		$password = user_hash($password, $plateformer['salt']);
		if($password != $plateformer['password']) {
			imessage(error(-1, '当前密码错误'), '', 'ajax');
		}
		$update = array(
			'password' => user_hash($newpassword, $plateformer['salt'])
		);
		pdo_update('users', $update, array('username' => $plateformer['username']));
	} else {
		$password = md5(md5($plateformer['salt'] . $password) . $plateformer['salt']);
		if($password != $plateformer['password']) {
			imessage(error(-1, '当前密码错误'), '', 'ajax');
		}
		$update = array(
			'password' => md5(md5($plateformer['salt'] . $newpassword) . $plateformer['salt'])
		);
		pdo_update('tiny_wmall_agent', $update, array('uniacid' => $_W['uniacid'], 'mobile' => $plateformer['username']));
	}
	imessage(error(0, '修改成功'), '', 'ajax');
}

