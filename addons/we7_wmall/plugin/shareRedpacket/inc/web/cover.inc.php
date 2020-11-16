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
mload()->model('cover');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$routers = array(
	'index' => array(
		'title' => '红包活动入口',
		'url' => ivurl('/package/pages/shareRedpacket/index', array(), true),
		'do' => 'shareRedpacket',
	),
);
$router = $routers[$op];
$_W['page']['title'] = $router['title'];

if($_W['ispost']) {
	$keyword = trim($_GPC['keyword']) ? trim($_GPC['keyword']) : imessage(error(-1, '关键词不能为空'), '', 'ajax');
	$cover = array(
		'keyword' => trim($_GPC['keyword']),
		'title' => trim($_GPC['title']),
		'thumb' => trim($_GPC['thumb']),
		'description' => trim($_GPC['description']),
		'do' => $router['do'],
		'url' => $router['url'],
		'status' => intval($_GPC['status']),
	);
	cover_build($cover);
	imessage(error(0, '设置封面成功'), ireferer(), 'ajax');
}
$cover = cover_fetch(array('do' => $router['do']));
$cover = array_merge($cover, $router);
include itemplate('cover');