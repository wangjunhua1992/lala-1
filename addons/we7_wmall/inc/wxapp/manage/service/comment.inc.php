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
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$type = intval($_GPC['type']);
	if($type == 1) {
		$condition .= ' and score >= 8';
	} elseif($type == 2) {
		$condition .= ' and score >= 4 and score <= 7';
	} elseif($type == 3) {
		$condition .= ' and score <= 3';
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$comments = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_comment') . $condition . ' order by id desc limit ' . ($page - 1) * $psize . ', ' . $psize, $params);
	if(!empty($comments)) {
		$comment_status = order_comment_status();
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
			$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
			$row['mobile'] = str_replace(substr($row['mobile'], 3, 6), '******', $row['mobile']);
			$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : '/static/img/avatar.png';
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
			$row['scores'] = score_format($row['score'] / 20);
			$row['status_cn'] = $comment_status[$row['status']]['text'];
			$row['status_css'] = $comment_status[$row['status']]['css'];
			$row['self_audit_comment'] = $store['self_audit_comment'];
			$row['goods_quality'] = floatval($row['goods_quality']);
			$row['delivery_service'] = floatval($row['delivery_service']);
		}
	}
	$result = array(
		'comments' => $comments,
		'store' => array(
			'comment_reply' => $store['comment_reply'],
			'self_audit_comment' => $store['self_audit_comment']
		)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'status') {
	if(empty($store['self_audit_comment'])) {
		imessage(error(-1, '店铺不能自己审核评论'), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	if($status > 2) {
		imessage(error(-1, '非法访问'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	store_comment_stat($comment['sid']);
	imessage(error(0, '更新状态成功'), ireferer(), 'ajax');
}

elseif($ta == 'reply') {
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	$reply = trim($_GPC['reply']);
	$update = array(
		'reply' => $reply,
		'replytime' => TIMESTAMP,
	);
	if($store['self_audit_comment'] == 1) {
		$update['status'] = 1;
	}
	pdo_update('tiny_wmall_order_comment', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	imessage(error(0, $update), '', 'ajax');
}

elseif($ta == 'comment_status'){
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	imessage(error(0, ''), '', 'ajax');
}
