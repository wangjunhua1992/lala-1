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
mload()->model('cron');
mload()->model('clerk');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'task';

set_time_limit(0);
if($op == 'task') {
	cron_order();
	exit('success');
}

elseif($op == 'order_notice') {
	clerk_info_init();
	if($_GPC['_ac'] == 'takeout' && $_GPC['_status_order_notice']) {
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));
		if(!empty($order)) {
			exit('success');
		}
		exit('error');
	} elseif($_GPC['_ctrl'] == 'errander' && $_GPC['_ac'] == 'order' && $_GPC['_status_errander_notice']) {
		$order = pdo_get('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));
		if(!empty($order)) {
			exit('success');
		}
		exit('error');
	} elseif($_GPC['_ctrl'] == 'store' && $_GPC['_ac'] == 'order' && $_GPC['_status_store_order_notice']) {
		$sid = intval($_GPC['__sid']);
		$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1, 'is_pay' => 1));
		if(!empty($order)) {
			exit('success');
		}
		exit('error');
	}
}

elseif($op == 'ignoreupdate') {
	if((empty($_GPC['__ieweishopversion']))) {
		load()->model('cloud');
		$manifest_cloud = cloud_m_upgradeinfo('we7_wmall');
		//print_r($manifest_cloud);
		if(!is_error($manifest_cloud)) {
			if(!empty($manifest_cloud['site_branch']['version'])) {
				$version = $manifest_cloud['site_branch']['version']['version'];
				$module = pdo_get('modules', array('name' => 'we7_wmall'));
				if(!empty($version) && $module['version'] != $version) {
					pdo_run("update ims_modules set version = '{$version}' where name = 'we7_wmall' ");
					load()->model('cache');
					load()->model('setting');
					load()->object('cloudapi');
					cache_updatecache();
				}
			}
		}
		isetcookie('__eweishopversion', 1, 1800);
	}
	exit('success');
}