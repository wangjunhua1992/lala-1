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
mload()->model('table');
$colors = array('block-gray', 'block-red', 'block-primary', 'block-success', 'block-orange');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'category_list';

if($ta == 'category_list') {
	$_W['page']['title'] = '桌台类型';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$tables = pdo_fetchall('select *, count(*) as num from ' . tablename('tiny_wmall_tables') . ' where uniacid = :uniacid and sid = :sid group by cid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'cid');
	include itemplate('store/tangshi/category');
}

if($ta == 'category_post') {
	$_W['page']['title'] = '编辑桌台类型';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage('名称不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'limit_price' => trim($_GPC['limit_price']),
			'reservation_price' => trim($_GPC['reservation_price']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_tables_category', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_tables_category', $data);
		}
		imessage(error(0, '编辑餐桌类型成功'), iurl('store/tangshi/table/category_list'), 'ajax');

	}
	if($id > 0) {
		$item = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($item)) {
			imessage('餐桌类型不存在或已删除', ireferer(), 'error');
		}
	}
	include itemplate('store/tangshi/category');
}

if($ta == 'category_del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}
	imessage(error(0, '删除桌台类型成功'), ireferer(), 'ajax');
}

if($ta == 'table_del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}
	imessage(error(0, '删除桌台成功'), ireferer(), 'ajax');
}

if($ta == 'table_post') {
	$_W['page']['title'] = '餐桌管理';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($categorys)) {
		imessage('创建桌台前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');
	}
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage('桌台号不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'guest_num' => intval($_GPC['guest_num']),
			'cid' => intval($_GPC['cid']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_tables', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_tables', $data);
			$table_id = pdo_insertid();
			imessage(error(0, '添加桌台成功, 生成桌台二维码中'), iurl('store/common/qrcode/build', array('store_id' => $sid, 'table_id' => $table_id, 'type' => 'table')), 'ajax');
		}
		imessage(error(0, '编辑桌台成功'), iurl('store/tangshi/table/list'), 'ajax');
	}
	if($id > 0) {
		$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($item)) {
			imessage('桌台不存在或已删除', ireferer(), 'error');
		}
	}
	include itemplate('store/tangshi/table');
}

if($ta == 'list') {
	$_W['page']['title'] = '餐桌列表';
	$_GPC['t'] = $_GPC['t'] ? $_GPC['t'] : 'list';
	$table_status = table_status();
	$condition = 'where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= ' and cid = :cid';
		$params[':cid'] = $cid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and title like :title';
		$params[':title'] = "%{$keyword}%";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_tables') .  $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables') . $condition . ' order by displayorder desc limit ' . ($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['wxapp_url'] = "we7_wmall/wxappqrcode/store/{$_W['uniacid']}/{$sid}_table_{$row['id']}.png";
			$row['newsys_url'] = ivurl('tangshi/pages/table/goods', array('sid' => $sid, 'table_id' => $row['id']), true);
			if(!ifile_exists(tomedia($row['wxapp_url']))){
				unset($row['wxapp_url']);
			}
			if(!empty($row['qrcode'])) {
				$row['qrcode'] = iunserializer($row['qrcode']);
				$row['wx_url'] = $row['qrcode']['url'];
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	include itemplate('store/tangshi/table');
}

if($ta == 'table_status') {
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($item)) {
		exit('桌台不存在或已删除');
	}
	$update = array(
		'status' => intval($_GPC['status'])
	);
	if($update['status'] == -1) {
		$update['cart_id'] = 0;
		unset($update['status']);
	}
	pdo_update('tiny_wmall_tables', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	exit('success');
}

if($ta == 'wxapp_qrcode'){
	mload()->model('qrcode');
	$table_id = intval($_GPC['table_id']);
	$sid = intval($_GPC['store_id']);
	$path = "we7_wmall/wxappqrcode/store/{$_W['uniacid']}/{$sid}_table_{$table_id}.png";
	if($_W['ispost']) {
		$params = array(
			'url' => 'tangshi/pages/table/goods',
			'scene' => "sid:{$sid}/table_id:{$table_id}",
			'name' => $path
		);
		$res = qrcode_wxapp_build($params);
		if(is_error($res)){
			imessage($res, iurl("store/tangshi/table/list", array('t' => 'qrcode_wxapp')), 'ajax');
		}
		pdo_update('tiny_wmall_tables', array('wxapp_version' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $table_id));
		imessage(error(0, '生成二维码成功'), iurl("store/tangshi/table/list", array('t' => 'qrcode_wxapp')), 'ajax');
	}
}


