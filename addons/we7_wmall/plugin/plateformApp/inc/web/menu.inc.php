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

$_W['page']['title'] = '新建自定义菜单';
$id = intval($_GPC['id']);
if($id > 0) {
	$_W['page']['title'] = '编辑菜单';
}
if($_W['ispost']) {
	$data = $_GPC['menu'];
	$data =  base64_encode(json_encode($data));
	set_plugin_config('plateformApp.menu', $data);
	imessage(error(0, '添加成功'), iurl('plateformApp/menu', array('id' => $id)), 'ajax');
}
mload()->model('plateform');
$menu = get_plateform_menu();
include itemplate('menu');