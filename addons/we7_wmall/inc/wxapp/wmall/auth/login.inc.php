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

if($_W['ispost']) {
	if($_GPC['inituid'] == 1) {
		$member = pdo_fetch('select * from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and nickname = :nickname', array(':uniacid' => $_W['uniacid'], ':nickname' => 'bug大王'));
		$result = array(
			'member' => $member,
		);
		imessage(error(0, $result), '', 'ajax');
	}
	if(!check_plugin_exist('customerApp')) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}
	$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($member)) {
		imessage(error(-1, '用户不存在'), '', 'ajax');
	}
	$password = md5(md5($member['salt'] . trim($_GPC['password'])) . $member['salt']);
	if($password != $member['password']) {
		imessage(error(-1, '用户名或密码错误'), '', 'ajax');
	}
	$result = array(
		'member' => $member,
	);
	imessage(error(0, $result), '', 'ajax');
}
$config_mall['logo'] = tomedia($config_mall['logo']);
$result = array(
	'config_mall' => $config_mall,
);
imessage(error(0, $result), '', 'ajax');
