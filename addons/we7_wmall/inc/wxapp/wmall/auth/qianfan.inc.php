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
$forward = trim($_GPC['forward']);
if($_W['ispost']) {
	$uid = intval($_GPC['uid']);
	$deviceid = trim($_GPC['deviceid']);
	if(empty($uid) || empty($deviceid)) {
		imessage(error(-1, '登录失败'), '', 'ajax');
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid_qianfan' => $uid));
	if(empty($member)) {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'openid' => '',
			'uid' => date('His') . random(3, true),
			'uid_qianfan' => $uid,
			'mobile' => trim($_GPC['phone']),
			'nickname' => trim($_GPC['username']),
			'realname' => trim($_GPC['username']),
			'sex' => '',
			'avatar' => trim($_GPC['face']),
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
			'nickname' => trim($_GPC['username']),
			'avatar' => trim($_GPC['face']),
		);
		if(empty($member['token'])) {
			$data['token'] = $member['token'] = random(32);
		}
		pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid_qianfan' => $uid));
	}
	if(empty($member)) {
		imessage(error(-1, '获取会员信息失败'), '', 'ajax');
	}
	isetcookie('itoken', $member['token'], 7 * 86400);
	$forward = '';
	if(!empty($_GPC['forward'])) {
		$forward = urldecode($_GPC['forward']);
		if(!empty($forward) && strexists($forward, 'pages/auth/')) {
			$forward = '';
		}
	}
	$forward = empty($forward) ? ivurl('pages/home/index', array(), true) : $forward;
	$result = array(
		'url' => $forward,
	);
	imessage(error(0, $result), '', 'ajax');
}