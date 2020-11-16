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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';
mload()->model('coupon');

if($op == 'list') {
	$_W['page']['title'] = '会员代金券';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$condition .= ' and a.type = :type';
		$params[':type'] = $type;
	}

	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and a.sid = :sid';
		$params[':sid'] = $sid;
	}

	$status = isset($_GPC['status']) ?intval($_GPC['status']) : 0;
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}

	if (empty($grant_starttime) || empty($grant_endtime)) {
		$grant_starttime = strtotime('-1 month');
		$grant_endtime = time();
	}

	if (empty($use_starttime) || empty($use_endtime)) {
		$use_starttime = strtotime('-1 month');
		$use_endtime = time();
	}

	if (!empty($_GPC['granttime']['start']) && !empty($_GPC['granttime']['end'])) {
		$grant_starttime = strtotime($_GPC['granttime']['start']);
		$grant_endtime = strtotime($_GPC['granttime']['end']);
		$condition .= ' and a.granttime >= :grant_starttime and a.granttime <= :grant_endtime';
		$params[':grant_starttime'] = $grant_starttime;
		$params[':grant_endtime'] = $grant_endtime;
	}

	if (!empty($_GPC['usetime']['start']) && !empty($_GPC['usetime']['end'])) {
		$use_starttime = strtotime($_GPC['usetime']['start']);
		$use_endtime = strtotime($_GPC['usetime']['end']);
		$condition .= ' and a.usetime >= :use_starttime and a.usetime <= :use_endtime';
		$params[':use_starttime'] = $use_starttime;
		$params[':use_endtime'] = $use_endtime;
	}

	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (b.realname like :keyword or b.mobile like :keyword)';
		$params[':keyword'] = "%{$keyword}%";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
	$coupons = pdo_fetchall('select a.*,b.avatar,b.realname from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid'. $condition . ' order by a.id desc limit '.($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$channel = coupon_channels();
	$coupon_status = coupon_status();
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除代金券记录成功'), '', 'ajax');
}

if($op == 'delAll') {
	if($_W['ispost']) {
		pdo_delete('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid']));
	}
	imessage(error(0, '删除代金券记录成功'), ireferer(), 'ajax');
}
include itemplate('member/coupon');

