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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';
if($op == 'list') {
	$_W['page']['title'] = '菜单列表';
	$condition = ' where uniacid = :uniacid and `version` = 1';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_diypage_menu') .  $condition, $params);
	$menus = pdo_fetchall('select * from ' . tablename('tiny_wmall_diypage_menu') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '新建自定义菜单';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$_W['page']['title'] = '编辑菜单';
	}
	if($_W['ispost']) {
		$data = $_GPC['menu'];
		$menudata = array(
			'uniacid' => $_W['uniacid'],
			'name' => $data['name'],
			'data' => base64_encode(json_encode($data)),
			'updatetime' => TIMESTAMP
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_diypage_menu', $menudata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$menudata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_diypage_menu', $menudata);
			$id = pdo_insertid();
		}
		imessage(error(0, '添加成功'), iurl('diypage/menu/post', array('id' => $id)), 'ajax');
	}
	if(!empty($id)) {
		$menu = diypage_menu($id);
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除菜单成功'), ireferer(), 'ajax');
}
include itemplate('menu');