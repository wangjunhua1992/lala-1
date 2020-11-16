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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
if($op == 'index') {
	$_W['page']['title'] = '页面选择';
	mload()->model('diy');
	$pages = array(
		'home' => array(
			'name' => '平台首页',
			'url' => 'pages/home/index',
			'key' => 'home',
			'save_key' => 'use_diy_home',
			'pages' => get_wxapp_pages(array('type' => 1), array('id', 'name')),
		),
		'member' => array(
			'name' => '会员中心',
			'url' => 'pages/member/mine',
			'key' => 'member',
			'save_key' => 'use_diy_member',
			'pages' => get_wxapp_pages(array('type' => 2), array('id', 'name')),
		)
	);
	if($_W['ispost']) {
		foreach($pages as $value) {
			$setting = intval($_GPC[$value['save_key']]);
			set_plugin_config("wxapp.diy.{$value['save_key']}", $setting);
		}
		$data = $_GPC['shopPages'];
		set_plugin_config('wxapp.diy.shopPage', $data);
		imessage(error(0, '编辑成功'), ireferer(), 'ajax');
	}
	$config_diy = get_plugin_config('wxapp.diy');
}
include itemplate('diyShop');