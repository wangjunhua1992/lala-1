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
if ($ta == 'index') {
	$sid = intval($sid);
	$table_category = table_category_fetchall($sid);
	$filter = array(
		'cid' => intval($_GPC['cid']),
		'sid' => $sid
	);
	$tables = table_fetchall($filter);
	$status = table_status();
	foreach ($tables as &$val) {
		$val['status_cn'] = $status[$val['status']]['text'];
	}
	$result = array(
		'tables' => $tables,
		'table_category' => $table_category,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif ($ta == 'status') {
	$sid = intval($sid);
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	if($status == 1) {
		$update = array('status' => 2);
	} else {
		$update = array('status' => 1, 'order_id' => 0, 'cart_id' => 0);
	}
	if(!empty($update)) {
		pdo_update('tiny_wmall_tables', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		imessage(error(0, '设置桌台状态成功'), '', 'ajax');
	}
	imessage(error(-1, '桌台信息有误'), '', 'ajax');
}