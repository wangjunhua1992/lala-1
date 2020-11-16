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
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '配送员回收站';
	$condition = ' WHERE uniacid = :uniacid and status = 2';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (title like '%{$keyword}%' or nickname like '%{$keyword}%' or mobile like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer') . $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'delete') {
	$ids = $_GPC['id'];
	if(!$ids) {
		imessage(error(-1, '配送员不存在或已被删除'), '', 'ajax');
	}
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
		pdo_delete('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
		pdo_delete('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
		mlog(4002, $id, '平台删除配送员');
	}
	deliveryer_all(true);
	imessage(error(0, '删除配送员成功'), '', 'ajax');
}

elseif($op == 'recover') {
	$id = intval($_GPC['id']);
	if(!$id) {
		imessage(error(-1, '配送员不存在或已被删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_deliveryer', array('status' => 1, 'deltime' => ''), array('uniacid' => $_W['uniacid'], 'id' => $id));
	mlog(4008, $id, '平台恢复配送员');
	deliveryer_all(true);
	imessage(error(0, '恢复配送员成功'), '', 'ajax');
}
include itemplate('deliveryer/storage');