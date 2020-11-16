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

$task_types = svip_task_types();
if($op == 'list') {
	$_W['page']['title'] = '任务中心';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$title = trim($_GPC['title'][$k]);
				if(empty($title)) {
					continue;
				}
				$data = array(
					'title' => $title,
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_svip_task', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage(error(0, '修改成功'), iurl('svip/task/list'), 'ajax');
		}
	}
	$task_type = trim($_GPC['task_type']);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	$status_all = svip_task_status();
	$data = svip_task_getall();
	$pager = $data['pager'];
	$tasks = $data['tasks'];
	include itemplate('task');
}

elseif($op == 'post') {
	$_W['page']['title'] = '编辑任务';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$data = $_GPC['data'];
		$starttime = strtotime($data['activity_starttime']);
		$endtime = strtotime($data['activity_endtime']);
		if(empty($starttime)) {
			imessage(error(-1, '请设置任务开始时间'), '', 'ajax');
		}
		if($starttime >= $endtime) {
			imessage(error(-1, '开始时间不能小于结束时间'), '', 'ajax');
		}
		$update = array(
			'uniacid' => $_W['uniacid'],
			'type' => $data['activity_type'],
			'title' => $data['title'],
			'content' => $data['content'],
			'displayorder' => $data['displayorder'],
			'status' => 1,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'data' => base64_encode(json_encode($data)),
		);
		if($starttime > TIMESTAMP) {
			$update['status'] = 2;
		}
		if(!empty($id)) {
			pdo_update('tiny_wmall_svip_task', $update, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$update['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_svip_task', $update);
			$id = pdo_insertid();
		}
		imessage(error(0, '设置会员任务成功'), iurl('svip/task/list'), 'ajax');
	}
	if(!empty($id)) {
		$task = pdo_fetch('select * from ' . tablename('tiny_wmall_svip_task') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if(!empty($task)) {
			$data = json_decode(base64_decode($task['data']), true);
		}
	}
	$data['activity_types'] = $task_types;
	include itemplate('task');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_svip_task', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除任务成功'), '', 'ajax');
}

elseif($op == 'takepartlist') {
	$_W['page']['title'] = '任务参与记录';
	$task_type = trim($_GPC['task_type']);
	$task_id = intval($_GPC['task_id']);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if(!empty($_GPC['endtime']['start']) && !empty($_GPC['endtime']['end'])) {
		$_GPC['starttime'] = strtotime($_GPC['endtime']['start']);
		$_GPC['endtime'] = strtotime($_GPC['endtime']['end']);
	}
	$data = svip_task_takepart_records();
	$tasks = pdo_getall('tiny_wmall_svip_task', array('uniacid' => $_W['uniacid']), array('id', 'title'));
	$pager = $data['pager'];
	$records = $data['records'];
	include itemplate('takepartlist');
}

elseif($op == 'del_takepartlist') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_svip_task_records', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除任务记录成功'), '', 'ajax');
}
