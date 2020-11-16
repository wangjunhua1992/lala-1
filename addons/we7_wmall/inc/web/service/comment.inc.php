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
mload()->model('deliveryer');

if($op == 'list') {
	$_W['page']['title'] = '用户评价';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' WHERE a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if ($deliveryer_id > 0) {
		$condition .= ' AND a.deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND a.sid = :sid';
		$params[':sid'] = $sid;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
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
	$type = isset($_GPC['type'])? intval($_GPC['type']) : -1;
	if($type > -1) {
		$condition .= ' AND a.type = :type';
		$params[':type'] = $type;
	}
	$is_share = isset($_GPC['is_share']) ? intval($_GPC['is_share']) : -1;
	if($is_share > -1) {
		$condition .= ' AND a.is_share = :is_share';
		$params[':is_share'] = $is_share;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (a.username LIKE '%{$keyword}%' OR a.mobile LIKE '%{$keyword}%')";
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
	$deliveryers = deliveryer_all();
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and status != 4', array(':uniacid' => $_W['uniacid']), 'id');
}

if($op == 'status') {
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

if($op == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$order = order_fetch($comment['oid']);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$reply = trim($_GPC['reply']);
	pdo_update('tiny_wmall_order_comment', array('reply' => $reply, 'replytime' => TIMESTAMP, 'status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($order['sid']);
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'share') {
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	$is_share = intval($_GPC['is_share']);
	$update = array(
		'is_share' => $is_share,
	);
	if($is_share > 0) {
		$update['status'] = 1;
	}
	pdo_update('tiny_wmall_order_comment', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '操作成功'), ireferer(), 'ajax');
}

if($op == 'group') {
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
		imessage(error(0, '批量回复成功！'), iurl('service/comment'), 'ajax');
	}
	$ids = implode(',', $_GPC['id']);
	include itemplate('service/commentOp');
	die();
}

if($op == 'falsecomment') {
	$_W['page']['title'] = '编辑虚拟评价';
	$stores = pdo_fetchall('select id, title, uniacid, agentid from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and status != 4', array(':uniacid' => $_W['uniacid']), 'id');
	if($_W['ispost']) {
		$sid = intval($_GPC['sid']);
		if(empty($sid)) {
			imessage(error(-1, '请选择门店'), '', 'ajax');
		}
		$avatar = trim($_GPC['avatar']);
		if(empty($avatar)) {
			imessage(error(-1, '请选择用户头像'), '', 'ajax');
		}
		$addtime = strtotime(trim($_GPC['addtime']));
		$replytime = strtotime(trim($_GPC['replytime']));
		if($replytime < $addtime) {
			imessage(error(-1, '商户回复时间不能早于评论时间'), '', 'ajax');
		}
		$good_goods = array();
		$good_goods['good'] = array_map('trim', explode(',', str_replace('，', ',', $_GPC['good_goods'])));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $stores[$sid]['agentid'],
			'sid' => $sid,
			'username' => trim($_GPC['username']),
			'avatar' => $avatar,
			'mobile' => trim($_GPC['mobile']),
			'goods_quality' => intval($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : 5,
			'delivery_service' => intval($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : 5,
			'note' => trim($_GPC['note']),
			'data' => iserializer($good_goods),
			'thumbs' => iserializer($_GPC['thumbs']),
			'reply' => trim($_GPC['reply']),
			'replytime' => $replytime,
			'addtime' => $addtime,
			'type' => '2',
		);
		$data['score'] = $data['goods_quality'] + $data['delivery_service'];
		pdo_insert('tiny_wmall_order_comment', $data);
		imessage(error(0, '编辑虚拟评价成功'), iurl('service/comment/list'), 'ajax');
	}
}
include itemplate('service/comment');




