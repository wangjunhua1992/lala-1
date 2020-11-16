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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$_W['page']['title'] = '排号管理';
	$queues = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($queues)) {
		foreach($queues as &$val) {
			$val['total'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') .' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $val['id']));
			$val['now_number'] = pdo_fetchcolumn('select number from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $val['id']));
		}
	}
}

elseif($ta == 'status') {
	$_W['page']['title'] = '排号管理';
	if($_W['isajax']) {
		$status = intval($_GPC['status']);
		$queue_id = intval($_GPC['id']);
		$now_number = trim($_GPC['number']);
		$board = pdo_fetch('select id,number from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id));
		if(empty($now_number)) {
			imessage(error(-1, '当前队列没有在排队号码'), ireferer(), 'ajax');
		}
		if($now_number != $board['number']) {
			imessage(error(-1, '处理排号顺序有误刷新之后重试'), ireferer(), 'ajax');
		}
		pdo_update('tiny_wmall_assign_board', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $board['id']));
		$status = assign_notice($sid, $board['id'], $status);
		if(!is_error($status)) {
			pdo_update('tiny_wmall_assign_board', array('is_notify' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $board['id']));
		}
		assign_notice_queue($board['id'], $queue_id);
		imessage(error(0, '改变状态成功'), ireferer(), 'ajax');
	}
}

elseif($ta == 'notice') {
	if($_W['isajax']) {
		$queue_id = intval($_GPC['id']);
		$now_number = trim($_GPC['number']);
		$board = pdo_fetch('select id,number from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id));
		if(empty($now_number)) {
			imessage(error(-1, '当前队列没有在排队号码'), ireferer(), 'ajax');
		}
		if($now_number != $board['number']) {
			imessage(error(-1, '处理排号顺序有误刷新之后重试'), ireferer(), 'ajax');
		}
		$status = assign_notice($sid, $board['id'], 2);
		if(!is_error($status)) {
			pdo_update('tiny_wmall_assign_board', array('is_notify' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $board['id']));
			imessage(error(0, '通知成功'), ireferer(), 'ajax');
		}
		imessage(error(-1, "通知失败:{$status['message']}"), ireferer(), 'ajax');
	}
}

elseif($ta == 'record') {
	$_W['page']['title'] = '队列详情';
	$queue_id = intval($_GPC['id']);
	$queue = pdo_get('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));
	if(empty($queue)) {
		imessage('队列不存在或已删除', ireferer(), 'error');
	}
	$min = 0;
	$status = assign_board_status();
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_board') . " where uniacid = :uniacid and sid = :sid and queue_id = :queue_id order by id desc limit 20", $params, 'id');
	foreach($data as &$val) {
		if($val['status'] == 1) {
			$val['before_num'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id and id < :id", array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id, ':id' => $val['id']));
		}
	}
	$min = min(array_keys($data));
}

elseif($ta == 'more') {
	$id = intval($_GPC['min']);
	$queue_id = intval($_GPC['id']);
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $queue_id, ':id' => $id);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_board') . " where uniacid = :uniacid and sid = :sid and queue_id = :queue_id and id < :id order by id desc limit 20", $params, 'id');
	$min = min(array_keys($data));
	$data = array_values($data);
	$respon = array('errno' => 0, 'message' => $data, 'min' => $min);
	imessage($respon, '', 'ajax');
}

elseif($ta == 'del') {
	$id = intval($_GPC['id']);
	if($_W['isajax']) {
		pdo_delete('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'id' => $id));
		imessage(error(0, '删除排号成功'), ireferer(), 'ajax');
	}
}

elseif ($ta == 'queue_post') {
	
	if ($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => trim($_GPC['title']),
			'guest_num' => trim($_GPC['guest_num']),
			'notify_num' => trim($_GPC['notify_num']),
			'starttime' => trim($_GPC['starttime']),
			'endtime' => trim($_GPC['endtime']),
			'prefix' => trim($_GPC['prefix']),
		);
		pdo_insert('tiny_wmall_assign_queue', $data);
		imessage(error(0, ''), iurl('manage/order/assign'), 'ajax');
	}
}

include itemplate('order/assign');