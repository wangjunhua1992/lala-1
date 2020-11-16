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
mload()->model('cover');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'kanjia';

$routers = array(
	'gohome' => array(
		'title' => '生活圈首页入口',
		'url' => ivurl('/gohome/pages/home/index', array(), true),
		'do' => 'gohome',
	),
	'kanjia' => array(
		'title' => '砍价入口',
		'url' => ivurl('/gohome/pages/kanjia/index', array(), true),
		'do' => 'kanjia',
	),
	'pintuan' => array(
		'title' => '拼团入口',
		'url' => ivurl('/gohome/pages/pintuan/index', array(), true),
		'do' => 'pintuan',
	),
	'seckill' => array(
		'title' => '抢购入口',
		'url' => ivurl('/gohome/pages/seckill/index', array(), true),
		'do' => 'seckill',
	),
	'tongcheng' => array(
		'title' => '同城首页入口',
		'url' => ivurl('/gohome/pages/tongcheng/index', array(), true),
		'do' => 'tongcheng',
	),
	'haodian' => array(
		'title' => '好店首页入口',
		'url' => ivurl('/gohome/pages/haodian/index', array(), true),
		'do' => 'haodian',
	),
	'haodian_settle' => array(
		'title' => '好店入驻入口',
		'url' => ivurl('/gohome/pages/haodian/settle', array(), true),
		'do' => 'haodian_settle',
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