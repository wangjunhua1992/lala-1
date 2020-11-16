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
$op = trim($_GPC['op'])? trim($_GPC['op']): 'list';
mload()->classs('pinyin');
$pinyin = new pinyin();
if($op == 'list') {
	$_W['page']['title'] = '区域列表';
	if($_W['ispost']) {
		if(!empty($_GPC['hids'])) {
			foreach($_GPC['hids'] as $k => $v) {
				$spell = $pinyin->getAllPY($_GPC['title'][$k]);
				$initial= $pinyin->getFirstPY($_GPC['title'][$k]);
				$initial = strtoupper(substr($initial, 0, 1));
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'spell' => $spell,
					'initial' => $initial,
				);
				pdo_update('tiny_wmall_agent_area', $data, array('id' => $v, 'uniacid' => $_W['uniacid']));
			}
			imessage(error(0, '区域设置成功'), ireferer(), 'ajax');
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_agent_area') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$areas = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_agent_area') . ' where uniacid = :uniacid ORDER BY displayorder DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid']));
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '编辑区域';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$title = trim($_GPC['title']);
		$spell = $pinyin->getAllPY($title);
		$initial= $pinyin->getFirstPY($title);
		$initial = strtoupper(substr($initial, 0, 1));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'displayorder' => intval($_GPC['displayorder']),
			'spell' => $spell,
			'initial' => $initial,
			'status' => 1
		);
		if($id > 0) {
			pdo_update('tiny_wmall_agent_area', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			pdo_insert('tiny_wmall_agent_area', $data);
		}
		imessage(error(0, '编辑区域成功'), iurl('agent/area/list'), 'ajax');
	}
	if($id > 0) {
		$area = pdo_get('tiny_wmall_agent_area', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_agent_area', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}
	imessage(error(0, '删除区域成功'), '', 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_agent_area', array('status' => $status), array('id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, ''), '', 'ajax');
}
include itemplate('area');