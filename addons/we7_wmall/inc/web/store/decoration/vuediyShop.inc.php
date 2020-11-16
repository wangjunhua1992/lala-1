<?php
/**
 * 外送系�
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'index';
if($ta == 'index') {
	$_W['page']['title'] = '页面选择';
	$pages = array(
		'home' => array(
			'name' => '商户首页',
			'url' => 'pages/shop/index?sid=' . $sid,
			'key' => 'home',
			'save_key' => 'use_diy_home',
			'pages' => store_get_diypages($sid, 6),
		),
		'category' => array(
			'name' => '商户分类页',
			'url' => 'pages/shop/category?sid=' . $sid,
			'key' => 'category',
			'save_key' => 'use_diy_category',
			'pages' => store_get_diypages($sid, 7),
		),
	);
	if($_W['ispost']) {
		$setting = array(
			'use_diy_home' => intval($_GPC['use_diy_home']),
			'use_diy_category' => intval($_GPC['use_diy_category']),
			'shop_page' => array_map('intval', $_GPC['shop_page'])
		);
		store_set_data($sid, 'diypage', $setting);
		imessage(error(0, '编辑成功'), ireferer(), 'ajax');
	}
	$config_diypage = store_get_data($sid, 'diypage');
}
include itemplate('store/decoration/diyShop');