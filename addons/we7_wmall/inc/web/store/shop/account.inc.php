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
$_W['page']['title'] = '账户修改';

$id = $_W['clerk']['id'];
$clerk = pdo_get('tiny_wmall_clerk', array('id' => $id, 'uniacid' => $_W['uniacid']));
if($_W['ispost']) {
	$mobile = trim($_GPC['mobile']);
	if(!is_validMobile($mobile)) {
		imessage(error(-1, '手机号格式错误'), ireferer(), 'ajax');
	}
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));
	if(!empty($is_exist)) {
		imessage(error(-1, '该手机号已绑定其他店员, 请更换手机号'), ireferer(), 'ajax');
	}
	$openid = trim($_GPC['wechat']['openid']);
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
	if(!empty($is_exist)) {
		imessage(error(-1, '该微信信息已绑定其他店员, 请更换微信信息'), ireferer(), 'ajax');
	}

	$data = array(
		'uniacid' => $_W['uniacid'],
		'mobile' => $mobile,
		'title' => trim($_GPC['title']),
		'openid' => $openid,
		'openid_wxapp' => trim($_GPC['wechat']['openid_wxapp']),
		'nickname' => trim($_GPC['wechat']['nickname']),
		'avatar' => trim($_GPC['wechat']['avatar']),
	);

	$password = trim($_GPC['password']);
	if(!empty($password)) {
		$data['salt'] = random(6);
		$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
	}
	pdo_update('tiny_wmall_clerk', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '修改成功'), iurl('store/shop/account', array('id' => $id)), 'ajax');
}
include itemplate('store/shop/account');







