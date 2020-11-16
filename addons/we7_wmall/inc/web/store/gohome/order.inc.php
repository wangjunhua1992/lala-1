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
mload()->model('plugin');
pload()->model('gohome');


if($ta == 'list') {
	$_W['page']['title'] = '订单列表';
	$order_status = gohome_order_status();
	$refund_status = intval($_GPC['refund_status']);
	if($refund_status > 0) {
		$_GPC['status'] = 7;
	}
	if(!empty($_GPC['addtime']['start']) && !empty($_GPC['addtime']['end'])) {
		$_GPC['starttime'] = strtotime($_GPC['addtime']['start']);
		$_GPC['endtime'] = strtotime($_GPC['addtime']['end']);
	}
	$filter = $_GPC;
	$filter['sid'] = $sid;
	$data = gohome_order_fetchall($filter);
	$orders = $data['orders'];
	$pager = $data['pager'];
	include itemplate('store/gohome/order');
}

elseif($ta == 'remark') {
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		pdo_update('tiny_wmall_gohome_order', array('remark' => $remark), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		imessage(error(0,"订单成功添加备注"), ireferer(), 'ajax');
	}
	$order = gohome_order_fetch($id);
	include itemplate('store/gohome/orderOp');
}

elseif($ta == 'cancel') {
	$id = intval($_GPC['id']);
	$team_cancel = intval($_GPC['team_cancel']);
	$extra = array();
	if($team_cancel == 1) {
		$extra = array(
			'team_cancel' => 1
		);
	}
	$result = gohome_order_update($id, 'cancel', $extra);
	if(is_array($result['message']) && $result['message']['is_refund']) {
		imessage(error(0, "取消订单成功,{$result['message']['refund_message']}"), ireferer(), 'ajax');
	} else {
		imessage(error(0, '取消订单成功'), ireferer(), 'ajax');
	}
}

elseif($ta == 'detail') {
	$id = intval($_GPC['id']);
	$_W['page']['title'] = '订单详情';
	$order = gohome_order_fetch($id);
	$order_status = gohome_order_status();
	include itemplate('store/gohome/order');
}

elseif($ta == 'status') {
	$id = intval($_GPC['id']);
	$type = trim($_GPC['type']);

	$result = gohome_order_update($id, $type);
	imessage($result, ireferer(), 'ajax');
}

elseif($ta == 'confirm') {
	if($_W['ispost']) {
		$code = trim($_GPC['code']);
		$order = pdo_get('tiny_wmall_gohome_order', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'sid' => $sid, 'code' => $code));
		if(empty($order)) {
			imessage(error(-1, '订单不存在'), '', 'ajax');
		}
		$result = gohome_order_update($order, 'confirm');
		imessage($result, ireferer(), 'ajax');
	}
	include itemplate('store/gohome/orderOp');
}

elseif($ta == 'print') {
	$order_id = intval($_GPC['id']);
	$result = gohome_order_print($order_id);
	if(is_error($result)) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	imessage(error(0, '订单打印成功'), '', 'ajax');
}