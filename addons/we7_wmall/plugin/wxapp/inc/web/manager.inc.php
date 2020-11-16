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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	$wxapp = get_plugin_config('wxapp.manager');
	if(empty($wxapp)) {
		$wxapp = array();
	}
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'key' => trim($_GPC['key']),
			'secret' => trim($_GPC['secret']),
			'wxapp_manager_notice_channel' => trim($_GPC['wxapp_manager_notice_channel']),
			'tpl_manager_url' => trim($_GPC['tpl_manager_url']),
			'test' => array(
				'username' => trim($_GPC['test']['username']),
				'password' => trim($_GPC['test']['password']),
			),
		);
		$data = array_merge($wxapp, $data);
		set_plugin_config('wxapp.manager', $data);
		imessage(error(0, '基础设置成功'), 'refresh', 'ajax');
	}
	include itemplate('config/manager');
}

elseif($op == 'urls') {
	include itemplate('config/manager');
}

elseif ($op == 'wxtemplate') {
	$_W['page']['title'] = '微信模板消息';
	if($_W['ispost']) {
		$wx_template = $_GPC['wechat'];
		set_plugin_config('wxapp.manager.wxtemplate', $wx_template);
		imessage(error(0, '微信模板消息设置成功'), 'refresh', 'ajax');
	}
	$wechat = get_plugin_config('wxapp.manager.wxtemplate');
	include itemplate('config/manager');
}