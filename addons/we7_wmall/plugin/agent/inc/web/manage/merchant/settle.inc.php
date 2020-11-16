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
	$_W['page']['title'] = '入驻列表';
	$condition = ' where uniacid = :uniacid and agentid = :agentid and addtype = 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > 0) {
		$condition .= " AND status = :status";
		$params[':status'] = $status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($lists)) {
		foreach($lists as &$li) {
			$li['user'] = store_manager($li['id']);
		}
	}
	$store_status = store_status();
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'audit') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$clerk = store_manager($store['id']);
	if(empty($clerk)) {
		imessage(error(-1, '获取门店申请人失败'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	$remark = trim($_GPC['remark']);
	store_settle_notice($store['id'], 'clerk', $remark);
	imessage(error(0, '门店审核成功'), '', 'ajax');
}
include itemplate('merchant/settle');

