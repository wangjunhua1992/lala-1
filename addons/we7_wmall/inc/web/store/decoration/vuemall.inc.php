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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'menu';

if($ta == 'menu') {
	$_W['page']['title'] = '菜单设置';
	if($_W['ispost']) {
		$menu = array_map('intval', $_GPC['menu']);
		store_set_data($sid, 'diymenu', $menu);
		imessage(error(0, '菜单设置成功'), ireferer(), 'ajax');
	}
	$config_menu = store_get_data($sid, 'diymenu');
	$menus = pdo_getall('tiny_wmall_store_menu', array('uniacid' => $_W['uniacid'], 'version' => 2), array('id', 'name'));
}

include itemplate('store/decoration/mall');