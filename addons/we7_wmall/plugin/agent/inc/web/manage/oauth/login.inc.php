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
$_W['page']['title'] = '代理登录';
if(checksubmit()) {
	_login($_GPC['referer']);
	die;
}
isetcookie('__we7_wmall_agent', false, -100);
isetcookie('__agent_id', 0, -1000);
$setting = $_W['setting'];
include itemplate('oauth/login');

function _login($forward = '') {
	global $_GPC, $_W;
	load()->model('user');
	$mobile = trim($_GPC['mobile']);
	if(empty($mobile)) {
		imessage('请输入要登录的手机号', '', 'info');
	}
	$type = 'agenter';
	$types = explode(':', $mobile);
	if(count($types) == 2) {
		$type = $types[0];
		$mobile = $types[1];
	}
	$password = trim($_GPC['password']);
	if(empty($password)) {
		imessage('请输入密码', '', 'info');
	}
	$record = array();
	$agentid = 0;
	if($type == 'agenter') {
		$temp = pdo_get('tiny_wmall_agent', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		$agentid = $temp['id'];
	} elseif($type == 'operator') {
		$temp = pdo_get('tiny_wmall_agent_users', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		$agentid = $temp['agentid'];
	}
	if(!empty($temp)) {
		$password = md5(md5($temp['salt'] . $password) . $temp['salt']);
		if($password == $temp['password']) {
			$record = $temp;
		}
	}
	if(!empty($record)) {
		if(!$record['status']) {
			imessage('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！', '', 'info');
		}
		if(!empty($_W['siteclose'])) {
			imessage('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], '', 'info');
		}
		$cookie = array();
		if($type == 'agenter') {
			$cookie['id'] = $record['id'];
			$cookie['hash'] = $password;
		} elseif($type == 'operator') {
			$cookie['id'] = $record['agentid'];
			$cookie['hash'] = $password;
			$cookie['user_id'] = $record['id'];
			$record['title'] = $record['username'];
		}
		$cookie['operator_type'] = $type;
		$session = base64_encode(json_encode($cookie));
		isetcookie('__we7_wmall_agent', $session, 7 * 86400);
		if(empty($forward)) {
			$forward = $_GPC['forward'];
		}
		if(empty($forward)) {
			$forward = iurl('dashboard/index');
		}
		imessage("欢迎回来，{$record['title']}。", $forward, '', 'success');
	} else {
		imessage('登录失败，请检查您输入的用户名和密码！', '', 'error');
	}
}

