<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 17-12-23
 * Time: 下午6:27
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$slide_type = trim($_GPC['slide_type']);
$config_advertise = get_plugin_config('advertise.type');
$titles = advertise_get_types();
$_W['page']['title'] = "{$titles[$slide_type]['text']}广告设置";
if($op == 'index') {
	if($_W['ispost']) {
		$update = array();
		foreach($_GPC['days'] as $key => $day) {
			$day = intval($day);
			if($day > 0) {
				$update['prices'][$day] = array(
					'day' => $day,
					'fee' => intval($_GPC['fees'][$key]),
				);
			}
		}
		$update['num'] = intval($_GPC['num']);
		$update['status'] = intval($_GPC['status']);
		set_plugin_config("advertise.type.{$slide_type}", $update);
		imessage(error(0, '首页顶部幻灯片广告设置成功'), 'refresh', 'ajax');
	}
	$config_slide= get_plugin_config("advertise.type.{$slide_type}");
	include itemplate('slide');
}
if($op == 'home'){

}
