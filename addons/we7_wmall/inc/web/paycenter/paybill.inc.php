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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '买单';
	$config = $_W['we7_wmall']['config'];
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'note' => trim($_GPC['note'][$k])
				);
				pdo_update('tiny_wmall_paybill_order', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '编辑备注成功'), iurl('paycenter/paybill/index'), 'success');
	}
	$condition = ' WHERE a.uniacid = :uniacid and is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= " and a.agentid = :agentid";
		$params[':agentid'] = $agentid;
	}
	$pay_type = trim($_GPC['pay_type']);
	if(!empty($_GPC['pay_type'])) {
		$condition .= " and a.pay_type = :pay_type";
		$params[':pay_type'] = $pay_type;
	}
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND a.sid = :sid';
		$params[':sid'] = $sid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (b.nickname LIKE '%{$keyword}%' OR b.mobile LIKE '%{$keyword}%' OR a.order_sn LIKE '%{$keyword}%')";
	}
	$uid = intval($_GPC['uid']);
	if($uid > 0) {
		$condition .= ' AND a.uid = :uid';
		$params[':uid'] = $uid;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}

	$condition .= " AND a.addtime > :start AND a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' .  $condition, $params);
	$orders = pdo_fetchall('SELECT a.*,b.nickname,b.mobile,b.avatar FROM ' . tablename('tiny_wmall_paybill_order') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' ORDER BY addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	$pager = pagination($total, $pindex, $psize);
	include itemplate('paycenter/paybill');
}

elseif($op == 'change_note') {
	$id = intval($_GPC['id']);
	$data = pdo_get('tiny_wmall_paybill_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($_W['ispost']) {
		if(empty($id)) {
			imessage(error(-1, '顾客不存在'), ireferer(), 'ajax');
		}
		$note = trim($_GPC['note']);
		pdo_update('tiny_wmall_paybill_order', array('note' => $note), array('uniacid' => $_W['uniacid'], 'id' => $id));
		imessage(error(0, '备注修改成功'), ireferer(), 'ajax');
	}
	include itemplate('paycenter/paybillOp');
	die();
}