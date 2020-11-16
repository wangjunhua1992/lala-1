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

function member_sns_check($sns) {
	global $_W, $_GPC;
	if(empty($sns)) {
		$sns = $_GPC['sns'];
	}
	if(empty($sns)) {
		return false;
	}
	$snsinfo = array();
	if(($sns == 'wx') && !empty($_GPC['token'])) {
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$_GPC['token']}&openid={$_GPC['openid']}&lang=zh_CN";
		$data = file_get_contents($url);
		$snsinfo = json_decode($data, true);
		$snsinfo['openid'] = "sns_wx_{$snsinfo['openid']}";
		$snsinfo['sex'] = $snsinfo['sex'] == 1 ? '男' : '女';
		$snsinfo['headimgurl'] = rtrim(rtrim($snsinfo['headimgurl'], '0'), 132) . 132;
	} elseif($sns == 'qq') {
		$data = htmlspecialchars_decode($_GPC['userinfo']);
		$snsinfo = json_decode($data, true);
		$snsinfo['openid'] = "sns_qq_{$_GPC['openid']}";
		$snsinfo['headimgurl'] = $snsinfo['figureurl_qq_2'];
	}

	$data = array(
		'uniacid' => $_W['uniacid'],
		'openid' => '',
		'nickname' => $snsinfo['nickname'],
		'avatar' => $snsinfo['headimgurl'],
		'sex' => $snsinfo['sex'],
		'register_type' => "sns_{$sns}"
	);
	$openid = trim($_GPC['openid']);
	if($sns == 'qq') {
		$data['openid_qq'] = trim($_GPC['openid']);
		$openid = 'sns_qq_' . trim($_GPC['openid']);
	} elseif($sns == 'wx') {
		$data['openid_wx'] = trim($_GPC['openid']);
		$openid = 'sns_wx_' . trim($_GPC['openid']);
	}
	$member = get_member($openid);
	if(empty($member)) {
		$data['uid'] = date('His') . random(3, true);
		$data['is_sys'] = 2;
		$data['addtime'] = TIMESTAMP;
		$data['salt'] = random(6, true);
		$data['password'] = md5(md5($data['salt'] . rand(100000, 999999)) . $data['salt']);
		pdo_insert('tiny_wmall_members', $data);
		return true;
	} else {
		pdo_update('tiny_wmall_members', array('avatar' => $snsinfo['headimgurl']), array('id' => $member['id']));
	}
	return true;
}

if(is_weixin() || !empty($_GPC["we7_wmall_member_session_{$_W['uniacid']}"])) {
	header('location: ' . imurl('wmall/home/index'));
	die;
}
$sns = trim($_GPC['sns']);
if($_W['ispost'] && !empty($sns) && !empty($_GPC['openid'])) {
	member_sns_check($sns);
	echo 'ok';
	die;
}
if($_GET['openid']) {
	if ($sns == 'qq'){
		$_GET['openid'] = "sns_qq_{$_GET['openid']}";
	} elseif ($sns == 'wx') {
		$_GET['openid'] = "sns_wx_{$_GET['openid']}";
	}
	$member = get_member($_GET['openid']);
	if(!empty($member)) {
		$member['hash'] = $member['password'];
		$key = "we7_wmall_member_session_{$_W['uniacid']}";
		$cookie = array(
			'uid' => $member['uid'],
			'hash' => $member['hash'],
		);
		$cookie = base64_encode(json_encode($cookie));
		isetcookie($key, $cookie, 7 * 86400);
	}
	$forward = '';
	if(!empty($_GPC['forward'])) {
		$forward = $_W['siteroot'] . 'app/index.php?' . base64_decode(urldecode($_GPC['forward']));
	}
	$forward = empty($forward) ? imurl('wmall/home/index') : $forward;
	header('location: ' . $forward);
	die;
}
