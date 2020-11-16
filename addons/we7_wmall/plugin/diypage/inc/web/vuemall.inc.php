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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'menu';

if($op == 'menu') {
	$_W['page']['title'] = '菜单设置';
	if($_W['ispost']) {
		$menu = array_map('intval', $_GPC['menu']);
		set_plugin_config('diypage.vuemenu', $menu);
		$wxappmenu = array_map('intval', $_GPC['wxappmenu']);
		set_plugin_config('wxapp.wxappmenu', $wxappmenu);
		if(check_plugin_perm('ttapp')) {
			set_plugin_config('ttapp.wxappmenu', $wxappmenu);
		}
		imessage(error(0, '菜单设置成功'), ireferer(), 'ajax');
	}
	$config_menu = get_plugin_config('diypage.vuemenu');
	$config_wxappmenu = get_plugin_config('wxapp.wxappmenu');
	$menus = pdo_getall('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'version' => 2), array('id', 'name'));
}

include itemplate('vue/mall');