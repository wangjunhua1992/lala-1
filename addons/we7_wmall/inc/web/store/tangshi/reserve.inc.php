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

if($ta == 'list') {
	$_W['page']['title'] = '桌台预定开放时间列表';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$reserves = pdo_fetchall('select * from ' . tablename('tiny_wmall_reserve') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include itemplate('store/tangshi/reserve');
}

if($ta == 'post') {
	$_W['page']['title'] = '新建桌台预定开放时间';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$time = trim($_GPC['time']) ? trim($_GPC['time']) : imessage(error(0,'预定时间段不能为空'), ireferer(), 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'time' => $time,
			'title' => trim($_GPC['title']),
			'table_cid' => intval($_GPC['table_cid']),
			'addtime' => time(),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_reserve', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_reserve', $data);
		}
		imessage(error(0, '编辑预定时间段成功'), iurl('store/tangshi/reserve/list'), 'ajax');
	}
	if($id > 0) {
		$item = pdo_get('tiny_wmall_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($item)) {
			imessage('预定时间段不存在或已删除', ireferer(), 'error');
		}
	}
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($categorys)) {
		imessage('创建预订开放时间段前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');

	}
	include itemplate('store/tangshi/reserve');
}

if($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}
	imessage(error(0, '删除预订开放时间段成功'), ireferer(), 'ajax');
}

if($ta == 'batch_post') {
	$_W['page']['title'] = '批量创建';
	if($_W['ispost']) {
		$start = strtotime($_GPC['time']);
		for($i = 0; $i < $_GPC['num']; $i++) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'time' => date('H:i', $start + $i * $_GPC['time_space'] * 60),
				'table_cid' => intval($_GPC['table_cid']),
				'addtime' => time(),
			);
			pdo_insert('tiny_wmall_reserve', $data);
		}
		imessage(error(0, '创建预定时间段成功'), iurl('store/tangshi/reserve/list'), 'ajax');
	}
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($categorys)) {
		imessage('创建预订开放时间段前,请先添加桌台类型', iurl('store/tangshi/table/category_post'), 'info');

	}
	include itemplate('store/tangshi/reserve');
}




