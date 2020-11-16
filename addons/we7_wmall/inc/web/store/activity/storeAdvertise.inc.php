<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 17-12-22
 * Time: 下午2:19
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$config_advertise = get_plugin_config('advertise');
if($ta == 'index') {
	$_W['page']['title'] = '首页顶部';
	$config_top = $config_advertise['top'];
	$prices = $config_top['prices'];
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $_W['sid'],
			'type' => 1,
			'type_cn' => 'top',
			'title' => '首页顶部5天展示',
			'starttime' => TIMESTAMP - 86400,
			'endtime' => TIMESTAMP + 86400 * 4,
			'status' => 1,
			'addtime' => TIMESTAMP,
		);
		$days = ($data['endtime']-$data['starttime'])/86400;
		$params = array('price' => 100, 'type' => 'wechat', 'days' => $days);
		$data['data'] = iserializer($params);
		pdo_insert('tiny_wmall_advertise_trade', $data);
		$id = pdo_insertid();
	//广告位情况
	//$advertise = pdo_fetchall('select * from'.tablename('tiny_wmall_advertise_trade'). 'where uniacid = :uniacid and status = 1 and type = :type',array(':unacid' => $_W['unacid'], ':type' => 'top'), 'sid');

}
include itemplate('store/activity/storeAdvertise');