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
if($ta == 'list') {
	$_W['page']['title'] = '用户评价';

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' WHERE a.uniacid = :uniacid AND a.sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$uid = intval($_GPC['uid']);
	if($uid > 0) {
		$condition .= ' AND a.uid = :uid';
		$params[':uid'] = $uid;
	}
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= " AND a.status = :status";
		$params[':status'] = $status;
	}

	$reply = isset($_GPC['reply']) ? intval($_GPC['reply']) : -1;
	if($reply == 0) {
		$condition .= " AND a.reply = ''";
	} elseif($reply == 1) {
		$condition .= " AND a.reply != ''";
	}

	$note = isset($_GPC['note']) ? intval($_GPC['note']) : -1;
	if($note == 1) {
		$condition .= " AND a.note != ''";
	}

	$goods_quality = isset($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : -1;
	if($goods_quality > 0) {
		$condition .= " AND a.goods_quality = {$_GPC['goods_quality']}";
	}
	$delivery_service = isset($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : -1;
	if($delivery_service > 0) {
		$condition .= " AND a.delivery_service = {$_GPC['delivery_service']}";
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (a.username LIKE '%{$keyword}%' OR a.mobile LIKE '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND a.addtime > :start AND a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order_comment') . ' AS a '.  $condition, $params);
	if(!empty($keyword)) {
		$condition .= " AND (b.ordersn LIKE '%{$keyword}%')";
	}
	$comments = pdo_fetchall('SELECT a.*, b.uid,b.openid FROM ' . tablename('tiny_wmall_order_comment') . ' AS a LEFT JOIN ' . tablename('tiny_wmall_order') . ' AS b ON a.oid = b.id ' . $condition . ' ORDER BY a.addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 2;
			$row['data'] = iunserializer($row['data']);
			$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
			$row['thumbs'] = iunserializer($row['thumbs']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'status') {
	if(empty($store['self_audit_comment'])) {
		imessage(error(-1, '店铺不能自己审核评论'), '', 'ajax');
	}
	$id = intval($_GPC['comment_id']);
	if(!empty($id)) {
		$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($comment)) {
			imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
		}
		pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
		store_comment_stat($comment['sid']);
	} else {
		$ids = $_GPC['id'];
		if(!empty($ids)) {
			foreach($ids as $value) {
				$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $value));
				if(empty($comment)) {
					imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
				}
				pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $value));
				store_comment_stat($comment['sid']);
			}
		}
	}

	imessage(error(0, '设置评论状态成功'), ireferer(), 'ajax');
}

if($ta == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$order = order_fetch($comment['oid']);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$update = array(
		'reply' => trim($_GPC['reply']),
		'replytime' => TIMESTAMP,
	);
	if($store['self_audit_comment'] == 1) {
		$update['status'] = 1;
	}
	pdo_update('tiny_wmall_order_comment', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($order['sid']);
	imessage(error(0, ''), '', 'ajax');
}

if($ta == 'group'){
	if($_W['ispost']) {
		$reply = trim($_GPC['reply']);
		if(empty($reply)) {
			imessage(error(-1, '回复内容不能为空！'), ireferer(), 'ajax');
		}
		$comment_ids = explode(',', $_GPC['comment_ids']);
		if(!empty($comment_ids)) {
			foreach($comment_ids as $id) {
				$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
				$order = order_fetch($comment['oid']);
				if(empty($order)) {
					imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
				}
				pdo_update('tiny_wmall_order_comment', array('reply' => $reply, 'replytime' => TIMESTAMP, 'status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
				store_comment_stat($order['sid']);
			}
		}
		imessage(error(0, '批量回复成功！'), iurl('store/service/comment'), 'ajax');
	}
	$ids = implode(',', $_GPC['id']);
	include itemplate('store/service/commentOp');
	die();
}

include itemplate('store/service/comment');




