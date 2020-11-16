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
	$_W['page']['title'] = '呼叫记录';
	$condition = 'where a.uniacid = :uniacid and a.sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_table_call_record') . " as a {$condition}", $params);
	$data = pdo_fetchall('select a.*,b.title as table_title from ' . tablename('tiny_wmall_table_call_record') . ' as a left join '. tablename('tiny_wmall_tables') . " as b on a.table_id = b.id {$condition} order by a.id desc limit " . ($pindex - 1) * $psize . ', '.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'status') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	if($_W['ispost']) {
		foreach($ids as $id) {
			pdo_update('tiny_wmall_table_call_record', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		imessage(error(0, '设为已处理成功'), iurl('store/tangshi/call'), 'ajax');
	}
}

if($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	if($_W['ispost']) {
		foreach($ids as $id) {
			pdo_delete('tiny_wmall_table_call_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		}
		imessage(error(0, '删除成功'), iurl('store/tangshi/call'), 'ajax');
	}
}

include itemplate('store/tangshi/call');



