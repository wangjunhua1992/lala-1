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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$routers = array(
	'index' => array(
		'title' => '超级会员',
		'url' => ivurl('package/pages/svip/index', array(), true),
		'do' => 'svip',
	)
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