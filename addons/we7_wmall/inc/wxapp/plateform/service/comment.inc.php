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
mload()->model('deliveryer');

if($ta == 'list') {
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$condition = ' WHERE a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (b.uid = :uid or a.mobile like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}
	$status = intval($_GPC['status']);
	if ($status > -1){
		$condition .= ' AND a.status = :status';
		$params[':status'] = $status;
	}
	$comments = pdo_fetchall('SELECT a.*, b.uid,b.openid FROM ' . tablename('tiny_wmall_order_comment') . ' AS a LEFT JOIN ' . tablename('tiny_wmall_order') . ' AS b ON a.oid = b.id ' . $condition . ' ORDER BY a.addtime DESC LIMIT ' . ($page - 1) * $psize.','.$psize, $params);
	if(!empty($comments)) {
		$stores = store_fetchall(array('id', 'title'));
		$deliveryers = deliveryer_all(true);
		foreach ($comments as &$row) {
			$row['store'] = (array)$stores[$row['sid']];
			$row['deliveryer'] = (array)$deliveryers[$row['deliveryer_id']];
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 2;
			$row['data'] = iunserializer($row['data']);
			$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
			$row['thumbs'] = iunserializer($row['thumbs']);
			if ($row['thumbs']){
				foreach ($row['thumbs'] as &$val){
					$val = tomedia($val);
				}
			}
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime_cn'] = date('Y-m-d H:i', $row['replytime']);
			$row['goods_quality'] = intval($row['goods_quality']);
			$row['delivery_service'] = intval($row['delivery_service']);
		}
	}
	$result = array(
		'records' => $comments
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'status'){
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	imessage(error(0, ''), '', 'ajax');
}

elseif($ta == 'reply') {
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
	pdo_update('tiny_wmall_order_comment', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($order['sid']);
	$reply = $update;
	$reply['replytime_cn'] = date('Y-m-d H:i', $update['replytime']);
	imessage(error(0, $reply), '', 'ajax');
}