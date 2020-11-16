<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('page');
global $_W, $_GPC;
icheckauth(false);
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
$config_mall = $_W['we7_wmall']['config']['mall'];
if($op == 'index') {
	$id = intval($_GPC['id']);
	if(empty($id)) {
		$id = $_config_plugin['diy']['shopPage']['home'];
	}
	if(empty($id)) {
		imessage(error(-1,'页面id不能为空'), '', 'ajax');
	}
	mload()->model('diy');
	$page = get_wxapp_diy($id, true, array('from' => 'wap'));
	if(empty($page)) {
		imessage(error(-1,'页面不能为空'), '', 'ajax');
	}
	$default_location = array();
	if(empty($_GPC['lat']) || empty($_GPC['lng'])) {
		$config_takeout = $_W['we7_wmall']['config']['takeout']['range'];
		if(!empty($config_takeout['map']['location_x']) && !empty($config_takeout['map']['location_y'])) {
			$_GPC['lat'] = $config_takeout['map']['location_x'];
			$_GPC['lng'] = $config_takeout['map']['location_y'];
			$default_location = array(
				'location_x' => $config_takeout['map']['location_x'],
				'location_y' => $config_takeout['map']['location_y'],
				'address' => $config_takeout['city'],
			);
		}
	}
	$result = array(
		'config' => $config_mall,
		'config_wxapp' => $_config_wxapp,
		'diy' => $page,
		'cart_sum' => $page['is_show_cart'] == 1 ? get_member_cartnum() : 0,
		'default_location' => $default_location,
		'userAgreement' => get_config_text('member:agreement')
	);
	if($page['is_has_allstore'] == 1) {
		$_GPC['cid'] = intval($page['cid']);
		$result['stores'] = store_filter();
	}
	if($page['is_has_hotGoods'] == 1) {
		mload()->model('goods');
		$result['hotGoods'] = goods_hotGoods_filter();
	}
	$result['superRedpacketData'] = array();
	if(check_plugin_perm('superRedpacket')) {
		pload()->model('superRedpacket');
		$result['superRedpacketData'] = superRedpacket_grant_show();
	}
	$_W['_share'] = array(
		'title' => $page['data']['page']['title'],
		'desc' => $page['data']['page']['desc'],
		'link' => ivurl('pages/diy/index', array('id' => $id), true),
		'imgUrl' => tomedia($page['data']['page']['thumb'])
	);
	$menufooter = $page['data']['page']['diymenu'];
	if($menufooter >= 0) {
		$_GPC['menufooter'] = 1;
		$_W['_menuid'] = $menufooter;
	} elseif($menufooter == -1) {
		$_GPC['menufooter'] = 0;
	}
	imessage(error(0, $result), '', 'ajax');
} elseif($op == 'store') {
	mload()->model('page');
	$result = store_filter();
	imessage(error(0, $result), '', 'ajax');
} elseif($op == 'information') {
	mload()->model('plugin');
	pload()->model('tongcheng');
	$informations = tongcheng_get_informations();
	$result = array(
		'informations' => $informations['informations'],
	);
	imessage(error(0, $result), '', 'ajax');
} elseif($op == 'haodian') {
	$store = haodian_store_fetchall(array('get_activity' => 1));
	$result = array(
		'store' => $store['store'],
	);
	imessage(error(0, $result), '', 'ajax');
}
