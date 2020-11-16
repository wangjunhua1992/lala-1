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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '页面配置';
	$titles = array(
		'navigationBarTitleText' => '导航栏标题文字内容',
		'navigationBarBackgroundColor' => '导航栏背景颜色',
		'navigationBarTextStyle' => '导航栏标题颜色',
	);
	$extpages = array(
		'pages/home/index' => array(
			'title' => '平台首页',
			'key' => 'pages/home/index',
		),
		'pages/home/category' => array(
			'title' => '商家分类页',
			'key' => 'pages/home/category',
		),
		'pages/member/mine' => array(
			'title' => '会员中心',
			'key' => 'pages/member/mine',
		),
		'pages/order/index' => array(
			'title' => '我的订单',
			'key' => 'pages/order/index',
		),
		'pages/member/coupon/index' => array(
			'title' => '我的代金券',
			'key' => 'pages/member/coupon/index',
		),
		'pages/member/address' => array(
			'title' => '我的收货地址',
			'key' => 'pages/member/address',
		),
		'pages/member/favorite' => array(
			'title' => '我的收藏',
			'key' => 'pages/member/favorite',
		),
		'pages/member/recharge' => array(
			'title' => '余额充值',
			'key' => 'pages/member/recharge',
		),
/*		'plugin/pages/channel/coupon' => array(
			'title' => '领券中心',
			'key' => 'plugin/pages/channel/coupon',
		),
		'plugin/pages/channel/brand' => array(
			'title' => '优选商家',
			'key' => 'plugin/pages/channel/brand',
		),*/
	);

	if($_W['ispost']) {
		foreach($extpages as $key => $val) {
			if(!empty($_GPC['pages'][$val['key']]['navigationBarTextStyle'])) {
				if(!in_array('black', $_GPC['pages'][$val['key']]) && !in_array('white', $_GPC['pages'][$val['key']])) {
					imessage(error(-1, "{$val['title']}的导航栏标题颜色仅支持black(黑)、white(白)两种颜色"), 'refresh', 'ajax');
				}
			}
		}
		set_plugin_config('wxapp.extPages', $_GPC['pages']);
		imessage(error(0, '页面配置成功'), 'refresh', 'ajax');
	}
	$config_extpage = get_plugin_config('wxapp.extPages');
	include itemplate('extpage');
}
