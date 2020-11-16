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

$_W['kefu']['user'] = $kefu = array(
	'role' => 'kefu',
	'kefu_id' => $_W['plateformer']['uid'],
	'token' => $_W['plateformer']['token'],
	'nickname' => '平台客服',
	'avatar' => 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKTpvfS1sTVsx0EZQzVGWdm7MSXkRGoVWtibHByLjeV3WSpNo3NDvJ9Yrg4uUqvlELSciamLibKpJJRg/132'
);

if($ta == 'index') {
	$result = array(
		'user' => array(
			'kefu_status' => $plateformer['kefu_status'],
			'kefu_status_cn' => $plateformer['kefu_status_cn'],
			'busy_reply' => get_plugin_config('kefu.autoreply.busyReply')
		)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'update') {
	$type = trim($_GPC['type']);
	if($type == 'kefu_status') {
		$update = array(
			'kefu_status' => intval($_GPC['kefu_status'])
		);
		if($plateformer['role'] == 'agenter') {
			pdo_update('tiny_wmall_agent', $update, array('uniacid' => $_W['uniacid'], 'id' => $plateformer['id']));
		} else {
			pdo_update('users', $update, array('uid' => $plateformer['uid']));
			$_W['kefu']['user']['kefu_status'] = $update['kefu_status'];
			pload()->model('kefu');
			kefu_offline_reply();
		}
		imessage(error(0, '顾客即时消息状态切换成功'), '', 'ajax');
	} elseif($type == 'busy_reply') {
		set_plugin_config('kefu.autoreply.busyReply', array('content' => trim($_GPC['busy_reply'])));
		imessage(error(0, '忙碌状态自动回复内容设置成功'), '', 'ajax');
	}
}
