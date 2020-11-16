<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '详细记录';
$condition = ' where uniacid = :uniacid and uid = :uid';
$params = array(
	':uniacid' => $_W['uniacid'],
	':uid' => $_W['member']['uid']
);
$id = intval($_GPC['min']);
if($id > 0) {
	$condition .= " and id < :id";
	$params[':id'] = $id;
}
$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_grant_record') . $condition . ' order by id desc limit 15', $params, 'id');
$labels = grant_types();
$min = 0;
if(!empty($records)) {
	foreach($records as &$row) {
		$row['credittype'] = '积分';
		if($row['credittype'] == 'credit2') {
			$row['credittype'] = '余额';
		}
		$row['grant'] = floatval($row['grant']);
		$row['css'] = $labels[$row['type']]['css'];
		$row['text'] = $labels[$row['type']]['text'];
		$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
	}
	$min = min(array_keys($records));
}
	$records = array_values($records);
	$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
	imessage($respon, '', 'ajax');
include itemplate('record');