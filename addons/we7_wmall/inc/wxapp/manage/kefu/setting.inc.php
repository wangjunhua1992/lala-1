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
	'role' => 'clerk',
	'clerk_id' => $_W['manager']['id'],
	'token' => $_W['manager']['token'],
	'unionid' => $sid,
	'nickname' => $_W['manager']['nickname'],
	'avatar' => tomedia($_W['manager']['avatar'])
);

if($ta == 'index') {
	$store = store_fetch($sid, array('kefu_status', 'data'));
	$clerk = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $_W['manager']['id']), array('sid', 'clerk_id', 'kefu_status'));
	if(!empty($clerk)) {
		$clerk['kefu_status_cn'] = to_kefustatus($clerk['kefu_status']);
	}
	$result = array(
		'store' => $store,
		'clerk' => $clerk
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'kefu_status') {
	$value = intval($_GPC['value']);
	$update = array(
		'kefu_status' => $value
	);
	pdo_update('tiny_wmall_store', $update, array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '顾客即时消息状态切换成功'), '', 'ajax');
}

elseif($ta == 'update') {
	$type = trim($_GPC['type']);
	if($type == 'kefu_status') {
		$update = array(
			'kefu_status' => intval($_GPC['kefu_status'])
		);
		pdo_update('tiny_wmall_store_clerk', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $_W['manager']['id']));
		$_W['kefu']['user']['kefu_status'] = $update['kefu_status'];
		pload()->model('kefu');
		kefu_offline_reply();
		$message = '店员的客服状态设置成功';
	} elseif($type == 'busy_reply') {
		$busy_reply = trim($_GPC['busy_reply']);
		store_set_data($sid, 'kefu.busy_reply', $busy_reply);
		$message = '忙碌状态自动回复内容设置成功';
	}
	imessage(error(0, $message), '', 'ajax');
}
