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
mload()->classs('ttapp');
load()->model('mc');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'openid';
$account_api = new Ttapp();
if ($ta == 'openid') {
	$code = $_GPC['code'];
	if(empty($code)) {
		imessage(error(-1, '通信错误，请在头条或者抖音中重新发起请求'), '', 'ajax');
	}
	$oauth = $account_api->getOauthInfo($code);
	if (!empty($oauth) && !is_error($oauth)) {
		$_SESSION['openid'] = $oauth['openid'];
		$_SESSION['openid_ttapp'] = $oauth['openid'];
		$_SESSION['session_key'] = $oauth['session_key'];
		$wmall_member = get_member($oauth['openid'], 'openid_ttapp');
		if(empty($wmall_member)) {
			$wmall_member = array(
				'uniacid' => $_W['uniacid'],
				'uid' => date('His') . random(3, true),
				'openid_ttapp' => $oauth['openid'],
				'is_sys' => 1,
				'status' => 1,
				'token' => random(32),
				'addtime' => TIMESTAMP,
				'register_type' => 'ttapp'
			);
			pdo_insert('tiny_wmall_members', $wmall_member);
		} else {
			$update = array(
				'openid_ttapp' => $oauth['openid'],
			);
			if(empty($wmall_member['uid'])) {
				$update['uid'] = date('His') . random(3, true);
			}
			pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'openid_ttapp' => $oauth['openid']));
		}
		$member = get_member($oauth['openid'], 'openid_ttapp');
		unset($member['password']);
		unset($member['salt']);
		$sessionid = $_W['session_id'];
		if($_GPC['istate']) {
			$sessionid = $member['openid_ttapp'];
		}
		$account_api->result(0, '', array('sessionid' => $sessionid, 'member' => $member));
	} else {
		$account_api->result(2000, $oauth['message']);
	}
} elseif($ta == 'userinfo') {
	$encrypt_data = $_GPC['encryptedData'];
	$iv = $_GPC['iv'];
	if(empty($_SESSION['session_key']) || empty($encrypt_data) || empty($iv)) {
		$account_api->result(2001, '请先登录');
	}
	$sign1 = sha1(htmlspecialchars_decode($_GPC['rawData'], ENT_QUOTES).$_SESSION['session_key']);
	$sign2 = sha1($_POST['rawData'].$_SESSION['session_key']);
	if($sign1 !== $_GPC['signature'] && $sign2 !== $_GPC['signature']) {
		$account_api->result(2010, '签名错误');
	}

	$userinfo = $account_api->pkcs7Encode($encrypt_data, $iv);
	if(is_error($userinfo)) {
		$account_api->result(2002, '解密出错');
	}

	$wmall_member = get_member($userinfo['openId'], 'openid_ttapp');
	if(empty($wmall_member)) {
		$wmall_member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => date('His') . random(3, true),
			'openid_ttapp' => $userinfo['openId'],
			'nickname' => $userinfo['nickName'],
			'realname' => '',
			'mobile' => '',
			'sex' => ($userinfo['gender'] == 1 ? '男' : '女'),
			'avatar' => rtrim($userinfo['avatarUrl']),
			'is_sys' => 1,
			'status' => 1,
			'token' => random(32),
			'addtime' => TIMESTAMP,
			'register_type' => 'ttapp',
		);
		pdo_insert('tiny_wmall_members', $wmall_member);
	} else {
		$update = array(
			'openid_ttapp' => $userinfo['openId'],
			'nickname' => $userinfo['nickName'],
			'sex' => ($userinfo['gender'] == 1 ? '男' : '女'),
			'avatar' => rtrim($userinfo['avatarUrl'], '0'),
		);
		if(empty($wmall_member['uid'])) {
			$update['uid'] = date('His') . random(3, true);
		}
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'openid_ttapp' => $userinfo['openId']));
	}
	$member = get_member($userinfo['openId'], 'openid_ttapp');
	unset($member['password']);
	unset($member['salt']);
	$account_api->result(0, '', $member);
}
elseif($ta == 'check') {
	if(!empty($_W['openid'])) {
		$account_api->result(0);
	} else {
		$account_api->result(1, 'session失效，请重新发起登录请求');
	}
}