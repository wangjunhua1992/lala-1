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
			'type_pay_id' => trim($_GPC['type_pay_id']),
			'type_refund_id' => trim($_GPC['type_refund_id']),
			'version_notice' => trim($_GPC['version_notice']),
			'download_url' => trim($_GPC['download_url'])
		);
		set_plugin_config('qianfanApp', $data);
		imessage(error(0, '设置成功'), 'refresh', 'ajax');
	}
	$qianfan = get_plugin_config('qianfanApp');
}
include itemplate('config');