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
mload()->model('page');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth(false);

if($op == 'index') {
	if(in_array($_W['ochannel'], array('wxapp', 'ttapp')) && $_W['is_agent'] && $_W['agentid'] == -1) {
		imessage(error(-2, '您所在的区域暂未获取到同城信息,建议您手动搜索地址或切换到此前常用的地址再试试'), '', 'ajax');
	}
	mload()->model('diy');
	if($_config_wxapp['diy']['use_diy_gohome'] != 1) {
		$pageOrid = get_wxapp_defaultpage('gohome');
		$config_share = $_config_plugin['share'];
		$share = array(
			'title' => $config_share['title'],
			'desc' => $config_share['detail'],
			'link' => empty($config_share['link']) ? ivurl('gohome/pages/home/index', array(), true) : $config_share['link'],
			'imgUrl' => tomedia($config_share['thumb'])
		);
	} else {
		//使用自定义页面
		$pageOrid = $_config_wxapp['diy']['shopPage']['gohome'];
		if(empty($pageOrid)) {
			imessage(error(-1, '未设置生活圈DIY页面'), '', 'ajax');
		}
	}
	$page = get_wxapp_diy($pageOrid, true, array('pagepath' => 'gohome'));
	if(empty($page)) {
		imessage(error(-1, '页面不能为空'), '', 'ajax');
	}
	$_W['_share'] = array(
		'title' => $page['data']['page']['title'],
		'desc' => $page['data']['page']['desc'],
		'link' => ivurl('gohome/pages/home/index', array(), true),
		'imgUrl' => tomedia($page['data']['page']['thumb'])
	);
	if($_config_wxapp['diy']['use_diy_gohome'] != 1) {
		$_W['_share'] = $share;
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
	if(empty($result)) {
		$result = array(
			'diy' => $page,
			'config' => $_W['we7_wmall']['config']['mall'],
			'cart_sum' => $page['is_show_cart'] == 1 ? get_member_cartnum() : 0,
		);
		$result['config']['default_location'] = $default_location;
	}
	$_W['_nav'] = 1;
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'information') {
	$filter = $_GPC;
	$filter['status'] = 3;
	pload()->model('tongcheng');
	$informations = tongcheng_get_informations($filter);
	$result = array(
		'informations' => $informations['informations'],
	);
	imessage(error(0, $result), '', 'ajax');
}
elseif($op == 'cart') {
	$result = array(
		'cart_sum' => get_member_cartnum()
	);
	imessage(error(0, $result), '', 'ajax');
}