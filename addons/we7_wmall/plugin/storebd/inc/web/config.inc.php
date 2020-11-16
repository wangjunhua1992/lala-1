<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$basic = array(
			'status' => intval($_GPC['status']),
			'fee_getcash' => $_GPC['fee_getcash'],
			'setting_meta_title' => trim($_GPC['setting_meta_title'])
		);
		set_plugin_config('storebd.basic', $basic);
		imessage(error(0, '设置成功'), ireferer(), 'ajax');
	}
	$basic = get_plugin_config('storebd.basic');
}

elseif($op == 'cover') {
	$_W['page']['title'] = '店铺推广员入口';
	$urls = array(
		'index' => ivurl('/package/pages/storebd/index', array(), true),
	);
}

include itemplate('config');







