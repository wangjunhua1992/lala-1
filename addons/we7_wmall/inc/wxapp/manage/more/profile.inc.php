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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'info';

if($ta == 'mobile') {
	$_W['page']['title'] = '修改手机号';
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
		$repassword = trim($_GPC['repassword']) ? trim($_GPC['repassword']) : imessage(error(-1, '请重复输入密码'), '', 'ajax');
		if($password != $repassword) {
			imessage(error(-1, '两次输入的密码不一样'), '', 'ajax');
		}
		$code = trim($_GPC['code']);
		$status = icheck_verifycode($mobile, $code);
		if(!$status) {
			imessage(error(-1, '验证码错误'), '', 'ajax');
		}
		$manager = pdo_fetch('select * from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $_W['manager']['id']));
		if(!empty($manager)) {
			imessage(error(-1, '该手机号已被其他用户绑定'), '', 'ajax');
		}
		$password =  md5(md5($_W['manager']['salt'] . $password) . $_W['manager']['salt']);
		pdo_update('tiny_wmall_clerk', array('mobile' => $mobile, 'password' => $password), array('id' =>  $_W['manager']['id'], 'uniacid' => $_W['uniacid']));
		imessage(error(0, '修改成功'), '', 'ajax');
	}
}

elseif($ta == 'title') {
	$_W['page']['title'] = '修改管理员名称';
	if($_W['ispost']) {
		if(!$_GPC['title']) {
			imessage(error(-1, '管理员名称不能为空'), '', 'ajax');
		}
		$data = array(
			'title' => trim($_GPC['title'])
		);
		pdo_update('tiny_wmall_clerk',  $data, array('id' => $_W['manager']['id'], 'uniacid' => $_W['uniacid']));
		imessage(error(0, '修改管理员名称成功'), '', 'ajax');
	}
}

elseif($ta == 'password') {
	if($_W['ispost']) {
		$password = trim($_GPC['password']);
		$newpassword = trim($_GPC['newpassword']);
		if(empty($password)) {
			imessage(error(-1, '密码不能为空'), '', 'ajax');
		}

		if(empty($newpassword)) {
			imessage(error(-1, '新密码不能为空'), '', 'ajax');
		}
		$length = strlen($newpassword);
		if($length < 8 || $length > 20) {
			imessage(error(-1, '请输入8-20密码'), '', 'ajax');
		}
		if(!preg_match(IREGULAR_PASSWORD, $newpassword)) {
			imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
		}
		$password =  md5(md5($_W['manager']['salt'] . $password) . ($_W['manager']['salt']));
		if($password != $_W['manager']['password']) {
			imessage(error(-1, '原密码错误'), '', 'ajax');
		}
		$data = array(
			'password' => md5(md5($_W['manager']['salt'] . $newpassword) . $_W['manager']['salt']),
		);
		pdo_update('tiny_wmall_clerk',  $data, array('id' => $_W['manager']['id'], 'uniacid' => $_W['uniacid']));
		imessage(error(0, '修改密码成功'), '', 'ajax');
	}
}

include itemplate('more/profile');
