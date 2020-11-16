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

$_W['kefu']['user'] = array(
	'role' => 'deliveryer',
	'deliveryer_id' => $_W['deliveryer']['id'],
	'token' => $_W['deliveryer']['token'],
	'nickname' => $_W['deliveryer']['nickname'],
	'avatar' => tomedia($_W['deliveryer']['avatar'])
);

if($ta == 'index') {
	$result = array(
		'deliveryer' => $deliveryer
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'update') {
	$type = trim($_GPC['type']);
	if($type == 'kefu_status') {
		$update = array(
			'kefu_status' => intval($_GPC['kefu_status'])
		);
		pdo_update('tiny_wmall_deliveryer', $update, array('uniacid' => $_W['uniacid'], 'id' => $_deliveryer['id']));
		$_W['kefu']['user']['kefu_status'] = $update['kefu_status'];
		pload()->model('kefu');
		kefu_offline_reply();
		$message = '客服状态设置成功';
	} elseif($type == 'busy_reply') {
		$busy_reply = trim($_GPC['busy_reply']);
		deliveryer_set_extra('kefu_busy_reply', $busy_reply);
		$message = '忙碌状态自动回复内容设置成功';
	}
	imessage(error(0, $message), '', 'ajax');
}
