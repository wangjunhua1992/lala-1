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
	$manager = $_W['manager'];
	if($_W['manager']['extra']['accept_voice_notice']) {
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1, 'is_pay' => 1));
		$audioSrc = "{$_W['siteroot']}addons/we7_wmall/resource/mp3/click.mp3";
		$app_manager = $_W['we7_wmall']['config']['app']['manager'];
		if(!empty($app_manager['phonic']['new'])) {
			$audioSrc = WE7_WMALL_URL . "resource/mp3/{$_W['uniacid']}/{$app_manager['phonic']['new']}";
		}
		$result = array(
			'audioSrc' => $audioSrc
		);
		if(!empty($order)){
			imessage(error(0, $result), '', 'ajax');
		} else {
			imessage(error(-1, ''), '', 'ajax');
		}
	} else {
		imessage(error(-1, ''), '', 'ajax');
	}
}
