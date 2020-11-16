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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'menu';

if($op == 'menu') {
	$_W['page']['title'] = '菜单设置';
	if($_W['ispost']) {
		$menu = $_GPC['menu'];
		set_plugin_config('diypage.menu', $menu);
		imessage(error(0, '菜单设置成功'), ireferer(), 'ajax');
	}
	$config_menu = get_plugin_config('diypage.menu');
	$menus = pdo_getall('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'version' => 1), array('id', 'name'));
}

include itemplate('mall');