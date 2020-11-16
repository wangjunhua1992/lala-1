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

$_W['page']['title'] = '新建自定义菜单';
$id = intval($_GPC['id']);
if($id > 0) {
	$_W['page']['title'] = '编辑菜单';
}
if($_W['ispost']) {
	$data = $_GPC['menu'];
	$data =  base64_encode(json_encode($data));
	set_plugin_config('deliveryerApp.menu', $data);
	imessage(error(0, '添加成功'), iurl('deliveryerApp/menu', array('id' => $id)), 'ajax');
}
mload()->model('deliveryer');
$menu = get_deliveryer_menu();
include itemplate('menu');