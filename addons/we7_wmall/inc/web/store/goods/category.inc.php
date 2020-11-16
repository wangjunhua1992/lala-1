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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'post') {
	$_W['page']['title'] = '编辑商品分类';
	$id = intval($_GPC['id']);
	if($id > 0){
		$item = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(!empty($item['week'])){
			$item['week'] = explode(",", $item['week']);
		}
	}
	if($_W['ispost']) {
		if(!empty($_GPC['title'])) {
			$data['sid'] = $sid;
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['thumb'] = trim($_GPC['thumb']);
			$data['displayorder'] = intval($_GPC['displayorder']);
			$data['min_fee'] = intval($_GPC['min_fee']);
			$data['description'] = trim($_GPC['description']);
			$data['is_showtime'] = intval($_GPC['is_showtime']);
			if($_GPC['is_showtime']) {
				if(empty($_GPC['start_time']) && empty($_GPC['end_time']) && empty($_GPC['week'])) {
					imessage(error(-1, '请完善可售时间段信息'), '', 'ajax');
				}
				if((!empty($_GPC['start_time']) && empty($_GPC['end_time'])) || (empty($_GPC['start_time']) && !empty($_GPC['end_time']))) {
					imessage(error(-1, '请完整填写分类显示时段'), '', 'ajax');
				}
				if(!empty($_GPC['start_time'])){
					$data['start_time'] = date('H:i', strtotime($_GPC['start_time']));
				}
				if(!empty($_GPC['end_time'])){
					$data['end_time'] = date('H:i', strtotime($_GPC['end_time']));
				}
				$week = implode(",", $_GPC['week']);
				$data['week'] = $week;
			}
			if($id > 0){
				pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			} else {
				pdo_insert('tiny_wmall_goods_category', $data);
			}
		}

		imessage(error(0, '编辑商品分类成功'), iurl('store/goods/category/list'), 'ajax');
	}
}

if($ta == 'child'){
	$_W['page']['title'] = '编辑子分类';
	$parentid = intval($_GPC['parentid']);
	$id = intval($_GPC['id']);
	$parents = pdo_fetchall('select id, title from' . tablename('tiny_wmall_goods_category') . ' where uniacid = :uniacid and sid = :sid and parentid = 0', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if($id > 0){
		$item = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']){
		if(!empty($_GPC['title'])){
			$data['sid'] = $sid;
			$data['parentid'] = $parentid;
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['thumb'] = trim($_GPC['thumb']);
			$data['displayorder'] = intval($_GPC['displayorder']);
			if($id > 0){
				pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			} else {
				pdo_insert('tiny_wmall_goods_category', $data);
			}
		}
		imessage(error(0, '编辑商品分类成功'), iurl('store/goods/category/list'), 'ajax');
	}
}

if($ta == 'list') {
	$_W['page']['title'] = '分类列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'min_fee' => trim($_GPC['min_fee'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'description' => trim($_GPC['description'][$k]),
				);
				pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑成功'), iurl('store/goods/category/list'), 'ajax');
		}
	}

	$condition = ' where uniacid = :uniacid AND sid = :sid and parentid = 0';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 100;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_goods_category') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_category') . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($lists)) {
		foreach ($lists as $key => &$val) {
			if(!empty($val['week'])){
				$val['week'] = explode(",", $val['week']);
			}
			$val['child'] = pdo_fetchall('select * from' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':parentid' => $key));
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'del') {
	$id = intval($_GPC['id']);
	$goodsdata = pdo_fetch('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and (cid = :cid or child_id = :child_id)', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':cid' => $id, ':child_id' => $id));
	if($goodsdata) {
		imessage(error(-1, '存在属于该分类的商品，请先删除该分类下的商品后再删除该分类'), iurl('store/goods/category/list'), 'ajax');
	}
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'parentid' => $id));
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $id));
	imessage(error(0, '删除商品分类成功'), iurl('store/goods/category/list'), 'ajax');
}

if($ta == 'export') {
	$_W['page']['title'] = '批量导入';
	if($_W['ispost']) {
		$file = upload_file($_FILES['file'], 'excel');
		if(is_error($file)) {
			imessage(error(-1, $file['message']), '', 'ajax');
		}
		$data = read_excel($file);
		if(is_error($data)) {
			imessage(error(-1, $data['message']), '', 'ajax');
		}
		unset($data[1]);
		if(empty($data)) {
			imessage(error(-1, '没有要导入的数据'), '', 'ajax');
		}
		foreach($data as $da) {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => trim($da[0]),
				'displayorder' => intval($da[1]),
				'status' =>  intval($da[2]),
			);
			pdo_insert('tiny_wmall_goods_category', $insert);
			$id = pdo_insertid();
			if(!empty($da[3])) {
				$childs = explode('-', $da[3]);
				foreach($childs as $child) {
					$child = trim($child);
					if(!empty($child)) {
						$child_insert = array(
							'uniacid' => $_W['uniacid'],
							'parentid' => $id,
							'sid' => $sid,
							'title' => $child,
							'displayorder' => 0,
							'status' =>  intval($da[2]),
						);
					}
					pdo_insert('tiny_wmall_goods_category', $child_insert);
				}
			}
		}
		imessage(error(0, '导入商品分类成功'), iurl('store/goods/category/list'), 'ajax');
	}
}
if($ta == 'status') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_goods_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid , 'id' => $id));
		imessage(error(0, ''), '', 'ajax');
	}
}

include itemplate('store/goods/category');