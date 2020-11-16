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
$op = trim($_GPC['op'])? trim($_GPC['op']): 'index';

if($op == 'index') {
	$_W['page']['title'] = '错误日志';
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and title like '%{$keyword}%' ";
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$starttime = $todaytime = strtotime(date('Y-m-d'));
	$endtime = $todaytime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime(trim($_GPC['addtime']['start']));
			$endtime = strtotime(trim($_GPC['addtime']['end']));
			$condition .= ' and addtime >= :starttime and addtime <= :endtime';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$condition .= ' and addtime >= :addtime';
			$params[':addtime'] = $starttime;
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 100;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_system_log') .  $condition, $params);
	$logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_system_log') . $condition .' order by id desc limit '.($pindex - 1) * $psize . ',' . $psize, $params);

	if(!empty($logs)) {
		foreach($logs as &$row) {
			$row['params'] = json_encode(iunserializer($row['params']));
			$row['message'] = iunserializer($row['message']);
		}
	}
	$pager = pagination($total,$pindex,$psize);
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id){
		pdo_delete('tiny_wmall_system_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除错误日志成功'), ireferer(), 'ajax');
}

if($op == 'delAll'){
	if($_W['ispost']){
		pdo_run("TRUNCATE ims_tiny_wmall_system_log");
	}
	imessage(error(0, '删除错误日志成功'), ireferer(), 'ajax');
}
include itemplate('system/slog');