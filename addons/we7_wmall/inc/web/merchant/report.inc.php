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
	$_W['page']['title'] = '商户投诉';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0){
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > -1) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$addtime = isset($_GPC['addtime']) ? intval($_GPC['addtime']) : -1;
	if($addtime > 0) {
		$condition .= ' and addtime >= :addtime';
		$params[':addtime'] = strtotime("-{$addtime}days", strtotime(date('Y-m-d')));
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 40;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_report') . $condition, $params);
	$reports = pdo_fetchall('select * from ' . tablename('tiny_wmall_report') . $condition . ' ORDER BY id desc LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($reports)) {
		$stores = array();
		foreach($reports as &$row) {
			$row['thumbs'] = iunserializer($row['thumbs']);
			$stores[] = $row['sid'];
		}
		$stores_str = implode(',', $stores);
		$stores = pdo_fetchall('select id,title from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');
	}
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_report', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '设置状态成功'), '', 'ajax');
}
include itemplate('merchant/report');