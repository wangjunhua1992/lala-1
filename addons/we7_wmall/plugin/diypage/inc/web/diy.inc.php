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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';
if($op == 'list') {
	$_W['page']['title'] = '自定义页面';
	$condition = ' where uniacid = :uniacid and version = 1';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_diypage') .  $condition, $params);
	$pages = pdo_fetchall('select * from ' . tablename('tiny_wmall_diypage') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '新建自定义页面';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$_W['page']['title'] = '编辑自定义页面';
		$page = get_diypage_diy($id);
	}
	$diymenus = diypage_menus();
	$activitys = store_all_activity();
	if($_W['ispost']) {
		$data = $_GPC['data'];
		$diydata = array(
			'uniacid' => $_W['uniacid'],
			'name' =>  $data['page']['name'],
			'type' => 1,
			'diymenu' => $data['page']['diymenu'],
			'data' => base64_encode(json_encode($data)),
			'updatetime' => TIMESTAMP
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_diypage', $diydata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$diydata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_diypage', $diydata);
			$id = pdo_insertid();
		}
		imessage(error(0, '编辑成功'), iurl('diypage/diy/post', array('id' => $id)), 'ajax');
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
		pdo_delete('tiny_wmall_diypage', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除自定义页面成功'), ireferer(), 'ajax');
}
include itemplate('diy');