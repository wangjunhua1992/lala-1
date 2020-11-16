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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '配送区域列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_update('tiny_wmall_area_list', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
				area_plateform_area_all(true);
			}
			imessage(error(0, '编辑成功'), iurl('area/area/list'), 'success');
		}
	}

	$condition = ' where uniacid = :uniacid and parentid = 0';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > -1) {
		$condition .= " and status = :status ";
		$params[':status'] = $status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (id = :id or title like :keyword)';
		$params[':id'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_area_list') . $condition, $params);
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_area_list') . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($categorys)) {
		foreach ($categorys as $key => &$val) {
			$val['child'] = pdo_fetchall('select * from' . tablename('tiny_wmall_area_list') . 'where uniacid = :uniacid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':parentid' => $key));
		}
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('areaList');
}

elseif($op == 'post') {
	$_W['page']['title'] = '配送区域编辑';
	$id = intval($_GPC['id']);
	if($id > 0){
		$category = pdo_get('tiny_wmall_area_list', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$category['map'] = array(
			'lat' => $category['location_x'],
			'lng' => $category['location_y']
		);
	}
	if($_W['ispost']){
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'status' => intval($_GPC['status']),
			'parentid' => intval($_GPC['parentid']),
			'location_x' => $_GPC['map']['lat'],
			'location_y' => $_GPC['map']['lng'],
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(empty($_GPC['id'])) {
			pdo_insert('tiny_wmall_area_list', $data);
		} else {
			pdo_update('tiny_wmall_area_list', $data, array('uniacid' => $_W['uniacid'], 'id' => $_GPC['id']));
		}
		area_plateform_area_all(true);
		imessage(error(0, '配送区域编辑成功'), iurl('area/area/list'), 'ajax');
	}
	include itemplate('areaPost');
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_area_list', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	area_plateform_area_all(true);
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'child') {
	$_W['page']['title'] = '编辑子区域';
	$parentid = intval($_GPC['parentid']);
	$id = intval($_GPC['id']);
	$parents = pdo_fetchall('select id, title from' . tablename('tiny_wmall_area_list') . ' where uniacid = :uniacid and parentid = 0', array(':uniacid' => $_W['uniacid']));
	if($id > 0){
		$item = pdo_get('tiny_wmall_area_list', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$item['map'] = array(
			'lat' => $item['location_x'],
			'lng' => $item['location_y']
		);
	}
	if($_W['ispost']){
		if(empty($_GPC['title'])){
			imessage(error(-1, '子区域名称不能为空'), '', 'ajax');
		}
		$data = array(
			'parentid' => $parentid,
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'location_x' => $_GPC['map']['lat'],
			'location_y' => $_GPC['map']['lng'],
			'displayorder' => intval($_GPC['displayorder']),
		);
		if($id > 0){
			pdo_update('tiny_wmall_area_list', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_area_list', $data);
		}
		area_plateform_area_all(true);
		imessage(error(0, '编辑区域成功'), iurl('area/area/list'), 'ajax');
	}
	include itemplate('areaPost');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_area_list', array('uniacid' => $_W['uniacid'], 'id' => $id));
	pdo_delete('tiny_wmall_area_list', array('uniacid' => $_W['uniacid'], 'parentid' => $id));
	area_plateform_area_all(true);
	imessage(error(0, '删除区域成功'), iurl('area/area/list'), 'ajax');
}