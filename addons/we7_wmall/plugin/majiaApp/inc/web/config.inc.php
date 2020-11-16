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
		$data = array(
			'appname' => trim($_GPC['appname']),
			'hostname' => trim($_GPC['hostname']),
			'appsecret' => trim($_GPC['appsecret']),
		);
		set_plugin_config('majiaApp', $data);
		imessage(error(0, '设置马甲App成功'), 'refresh', 'ajax');
	}
	$majia = get_plugin_config('majiaApp');
}
include itemplate('config');