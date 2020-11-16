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
	$config = $_W['we7_wmall']['config']['mall'];
	if($_W['ispost']) {
		$result = plateform_login($_POST);
		if(is_error($result)) {
			imessage(error(-1, $result['message']), '', 'ajax');
		}
		$user = plateform_fetch($result['token'], 'token');
		$result = array(
			'user' => array(
				'token' => "{$user['usertype']}:{$user['token']}",
				'perms' => $user['perms'],
				'role' => $user['role']
			),
		);
		imessage(error(0, $result), '', 'ajax');
	}
	$result = array(
		'config' => $config
	);
	imessage(error(0, $result), '', 'ajax');
}