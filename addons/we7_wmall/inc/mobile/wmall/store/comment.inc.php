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
$_W['page']['title'] = '评价列表';
icheckauth(false);
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	imessage('门店不存在或已经删除', ireferer(), 'error');
}
$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

$stat = store_comment_stat($sid);
$stat['all'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['good'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 8', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['middle'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 4 and score <= 7', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['bad'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score <= 3', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));

$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status = 1';
$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
$type = intval($_GPC['type']);
if($type == 1) {
	$condition .= ' and a.score >= 8';
} elseif($type == 2) {
	$condition .= ' and a.score >= 4 and a.score <= 7';
} elseif($type == 3) {
	$condition .= ' and a.score <= 3';
}
$note = intval($_GPC['note']);
if($note > 0) {
	$condition .= " and a.note != ''";
}
$id = intval($_GPC['min']);
if($id > 0) {
	$condition .= " and a.id < :id";
	$params[':id'] = $id;
}
$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition . ' order by a.id desc limit 5', $params, 'aid');
$min = 0;
if(!empty($comments)) {
	foreach ($comments as &$row) {
		$row['data'] = iunserializer($row['data']);
		$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
		$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
		$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
		$row['replytime'] = date('Y-m-d H:i', $row['replytime']);
		$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : WE7_WMALL_TPL_URL . 'static/img/head.png';
		$row['thumbs'] = iunserializer($row['thumbs']);
		if(!empty($row['thumbs'])) {
			foreach($row['thumbs'] as &$item) {
				$item = tomedia($item);
			}
		}
	}
	$min = min(array_keys($comments));
}

if($_W['ispost']) {
	$comments = array_values($comments);
	$respon = array('errno' => 0, 'message' => $comments, 'min' => $min);
	imessage($respon, '', 'ajax');
}
$activity = store_fetch_activity($sid);
include itemplate('store/comment');
