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
$_W['page']['title'] = '语音提示设置';
$ta = trim($_GPC['ta'])? trim($_GPC['ta']): 'index';
$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $_W['manager']['id']));
$prefs = iunserializer($clerk['prefs']);

if($ta == 'phonic_times') {
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$times = intval($_GPC['times']);
		$prefs[$type] = $times;
		pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	}
	imessage(error(0, '设置播放次数成功'), ireferer(), 'ajax');
}

if($ta == 'voice_status') {
	$voice_status = intval($_GPC['voice_status']);
	$prefs['voice_status'] = $voice_status;
	pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	imessage(error(0, '语音提示设置成功'), '', 'ajax');
}

if($ta == 'vibrance_status') {
	$vibrance_status = intval($_GPC['vibrance_status']);
	$prefs['vibrance_status'] = $vibrance_status;
	pdo_update('tiny_wmall_clerk', array('prefs' => iserializer($prefs)), array('uniacid' => $_W['uniacid']));
	imessage(error(0, '振动提示设置成功'), '', 'ajax');
}
include itemplate('more/phonic');
