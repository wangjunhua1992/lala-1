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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($op == 'index') {
	$_W['page']['title'] = '对接兑吧设置';
	if($_W['ispost']) {
		$bargain = array(
			'status' => intval($_GPC['status']),
			'appkey' => trim($_GPC['appkey']),
			'appsecret' => trim($_GPC['appsecret']),
		);
		set_plugin_config('creditshop', $bargain);
		imessage(error(0, '对接兑吧设置成功'), 'refresh', 'ajax');
	}
	$config = get_plugin_config('creditshop');

	$urls = array(
		'enter' => imurl('creditshop/enter', array(), true),
		'consume' => "{$_W['siteroot']}addons/we7_wmall/plugin/creditshop/notify.php?i={$_W['uniacid']}&channel=consume",
		'notice' => "{$_W['siteroot']}addons/we7_wmall/plugin/creditshop/notify.php?i={$_W['uniacid']}&channel=notice",
	);
}

include itemplate('config');