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
mload()->model('coupon');
icheckauth();
$_W['page']['title'] = '我的代金券';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
coupon_cron();

if($ta == 'list') {
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid and a.status = :status';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':status' => $status);

	$coupons = pdo_fetchall('select  a.*, a.id as aid, b.id,b.title,b.logo from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition . ' order by a.id desc limit 10', $params, 'aid');
	$min = 0;
	if(!empty($coupons)) {
		foreach($coupons as &$row) {
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('id', 'title'));
			$row['endtime'] = date('Y-m-d', $row['endtime']);
		}
		$min = min(array_keys($coupons));
	}
	include itemplate('member/coupon');
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid and a.status = :status and a.id < :id';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':status' => $status, ':id' => $id);
	$coupons = pdo_fetchall('select  a.*, a.id as aid, b.id,b.title,b.logo from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition . ' order by a.id desc limit 10', $params, 'aid');
	$min = 0;
	if(!empty($coupons)) {
		foreach($coupons as &$row) {
			$row['logo'] = tomedia($row['logo']);
			$row['endtime_cn'] = date('Y-m-d', $row['endtime']);
		}
		$min = min(array_keys($coupons));
	}
	$coupons = array_values($coupons);
	$respon = array('errno' => 0, 'message' => $coupons, 'min' => $min);
	imessage($respon, '', 'ajax');
}
