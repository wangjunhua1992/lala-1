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

if($_W['ispost']) {
	$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
	$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($clerk)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}
	$password = md5(md5($clerk['salt'] . trim($_GPC['password'])) . $clerk['salt']);
	if($password != $clerk['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}
	if(empty($clerk['token'])) {
		$token = $clerk['token'] = random(32);
		pdo_update('tiny_wmall_clerk', array('token' => $token), array('uniacid' => $_W['uniacid'], 'id' => $clerk['id']));
	}
	$sids = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id']), array(), 'sid');
	if(empty($sids)) {
		imessage(error(-1, '您没有绑定店铺，请先绑定店铺'), '', 'ajax');
	}
	$result = array(
		'clerk' => $clerk,
		'sids' => array_keys($sids)
	);
	imessage(error(0, $result), '', 'ajax');
}

$config_mall = $_W['we7_wmall']['config']['mall'];
$result = array(
	'config' => array(
		'logo' => tomedia($config_mall['logo']),
		'title' => $config_mall['title'],
	)
);
imessage(error(0, $result), '', 'ajax');
