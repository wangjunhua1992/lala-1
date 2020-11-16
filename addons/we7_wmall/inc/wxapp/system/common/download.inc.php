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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'delivery';
if ($ta == 'delivery') {
	$config_deliveryer = $_W['we7_wmall']['config']['app']['deliveryer'];
	$result = array(
		'link_ios' => $config_deliveryer['ios_download_link'],
		'link_android' => $config_deliveryer['android_download_link']
	);
	imessage(error(0, $result), '', 'ajax');
}elseif ($ta == 'manager') {
	$config_manager = $_W['we7_wmall']['config']['app']['manager'];
	$result = array(
		'link_ios' => $config_manager['ios_download_link'],
		'link_android' => $config_manager['android_download_link']
	);
	imessage(error(0, $result), '', 'ajax');
}elseif ($ta == 'customer') {
	$config_customer = $_W['we7_wmall']['config']['app']['customer'];
	$result = array(
		'link_ios' => $config_customer['ios_download_link'],
		'link_android' => $config_customer['android_download_link']
	);
	imessage(error(0, $result), '', 'ajax');
}elseif ($ta == 'plateform') {
	$config_plateform = get_plugin_config('plateformApp.app');
	$result = array(
		'link_ios' => $config_plateform['ios_download_link'],
		'link_android' => $config_plateform['android_download_link']
	);
	imessage(error(0, $result), '', 'ajax');
}