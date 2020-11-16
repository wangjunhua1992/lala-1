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
	$_W['page']['title'] = '活动使用场景';
	if($_W['ispost']) {
		$data = $_GPC['scenes'];
		$update = array();
		foreach($data as $key => $val) {
			$val = intval($val);
			if(empty($val)) {
				continue;
			}
			$update[$key] = array(
				'wheel_id' => $val,
				'url' => imurl('wheel/activity/index', array('id' => $val), true),
				'url_wxapp' => "plugin/pages/wheel/index?id={$val}",
			);
		}
		set_plugin_config('wheel.scene', $update);
		imessage(error(0, '编辑成功'), ireferer(), 'ajax');
	}
	$pages = array(
		'pay' => array(
			'name' => '支付页面',
			'key' => 'pay',
		),
	);
	$wheels = pdo_getall('tiny_wmall_wheel', array('uniacid' => $_W['uniacid']), array('id', 'title'));
	$scenes = get_plugin_config('wheel.scene');
	include itemplate('scene');
}