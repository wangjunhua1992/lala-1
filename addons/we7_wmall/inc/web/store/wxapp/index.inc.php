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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'template';

if($ta == 'index'){
	$_W['page']['title'] = '页面配置';
	$extpages = array(
		'pages_store_goods' => array(
			'title' => '商品列表页配色',
			'key' => 'pages_store_goods'
		),
	);
	if($_W['ispost']) {
		store_set_data($sid, 'wxapp.extPages', $_GPC['pages']);
		imessage(error(0, '页面配置成功'), 'refresh', 'ajax');
	}
	$config_extpage = store_get_data($sid, 'wxapp.extPages');
}

if($ta == 'cover'){
	mload()->model('qrcode');
	$_W['page']['title'] = '小程序入口';
	$urls = array(
		'goods' => array(
			'title' => '点餐页',
			'url' => 'pages/store/goods',
			'path' => '',
			'legel' => 0,
		),
		'home' => array(
			'title' => '自定义首页',
			'url' => 'pages/store/home',
			'path' => '',
			'legel' => 0,
		),
		'reserve' => array(
			'title' => '预定',
			'url' => 'tangshi/pages/reserve/index',
			'path' => '',
			'legel' => 0,
		),
		'assign' => array(
			'title' => '排号',
			'url' => 'tangshi/pages/assign/assign',
			'path' => '',
			'legel' => 0,
		),
		'paybill' => array(
			'title' => '买单',
			'url' => 'pages/store/paybill',
			'path' => '',
			'legel' => 0,
		),
	);
	foreach($urls as $key => &$val) {
		$path = "we7_wmall/wxappqrcode/store/{$_W['uniacid']}/{$sid}_store_{$key}.png";
		if(ifile_exists($path)){
			$val['legel'] = 1;
			$val['path'] = $path;
		}
	}

	if($_W['ispost']) {
		$type = trim($_GPC['type']);
		$params = array(
			'url' => $urls[$type]['url'],
			'scene' => "store:{$sid}",
			'name' => "we7_wmall/wxappqrcode/store/{$_W['uniacid']}/{$sid}_store_{$type}.png"
		);
		$res = qrcode_wxapp_build($params);
		if(is_error($res)){
			imessage($res, iurl("store/wxapp/index/cover"), 'ajax');
		}
		imessage(error(0, '生成二维码成功'), iurl("store/wxapp/index/cover"), 'ajax');
	}
}

include itemplate('store/wxapp/index');