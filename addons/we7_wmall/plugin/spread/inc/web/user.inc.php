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
mload()->model('member');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$_W['page']['title'] = '推广关系';
if($op == 'index') {
	$condition = " where uniacid = :uniacid";
	$params[':uniacid'] = $_W['uniacid'];
	$keywords = trim($_GPC['membername']);
	if(!empty($keywords)) {
		$condition .= " and (nickname like '%{$keywords}%' or realname like '%{$keywords}%' or mobile like '%{$keywords}%')";
	}
	if (!empty($_GPC['spreadtime']['start']) && !empty($_GPC['spreadtime']['end'])) {
		$spreadtime_starttime = strtotime($_GPC['spreadtime']['start']);
		$spreadtime_endtime = strtotime($_GPC['spreadtime']['end']);
		$condition .= ' and spreadtime >= :spreadtime_starttime and spreadtime <= :spreadtime_endtime';
		$params[':spreadtime_starttime'] = $spreadtime_starttime;
		$params[':spreadtime_endtime'] = $spreadtime_endtime;
	}
	$spread = isset($_GPC['spread']) ? intval($_GPC['spread']) : 0;
	if($spread > 0) {
		$condition .= " and (spread1 = :spread or spread2 = :spread)";
		$params[':spread'] = $spread;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_members') . $condition, $params);
	$members = pdo_fetchall('select id,avatar,uid,nickname,realname,addtime,spreadfixed from' . tablename('tiny_wmall_members') . $condition . ' LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($members as &$v) {
		$data = member_spread($v['uid']);
		$v['spread1'] = $data['spread1'];
		$v['spread2'] = $data['spread2'];
	}
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'unbind') {
	$id = intval($_GPC['id']);
	$dat = pdo_update('tiny_wmall_members', array('spread1' => 0,'spread2' => 0, 'spreadfixed' => 0), array('id' => $id));
	imessage(error(0, '解除推广关系成功'), '', 'ajax');
}

include itemplate('user');