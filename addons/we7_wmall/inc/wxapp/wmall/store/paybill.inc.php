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
mload()->model('paybill');
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

$sid = intval($_GPC['sid']);
$store = store_fetch($sid, array('agentid', 'cid', 'title', 'logo', 'is_paybill'));
if(empty($store)) {
	imessage(error(-1, '门店不存在'), '', 'ajax');
}
if($store['is_paybill'] == 0) {
	imessage(error(-1, '该门店暂未开启买单功能'), '', 'ajax');
}

if($ta == 'index') {
	$sid = $_GPC['sid'];
	$total_fee = floatval($_GPC['total_fee']);
	if(empty($total_fee)) {
		imessage(error(-1, "消费总额不能为空"), '', 'ajax');
	}
	$no_discount_part = $_GPC['no_discount_part'] ? floatval($_GPC['no_discount_part']) : 0;
	$couponId = intval($_GPC['couponId']);
	$condition = $total_fee - $no_discount_part;
	$coupon = pdo_fetch('select * from'. tablename('tiny_wmall_activity_coupon_record') . ' where uniacid = :uniacid and id = :id and sid = :sid and uid = :uid and `condition` <= :condition and endtime > :endtime and starttime <= :starttime', array(':uniacid' => $_W['uniacid'], ':id' => $couponId, ':sid' => $sid, ':uid' => $_W['member']['uid'], ':condition' => $condition, ':endtime' => TIMESTAMP, ':starttime' => TIMESTAMP));
	if($coupon['status'] > 1) {
		imessage(error(-1, "优惠券无效,请重新选择"), '', 'ajax');
	}
	$couponPrice = $coupon['discount'];
	$final_fee = $total_fee - $couponPrice;

	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'agentid' => $store['agentid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'serial_sn' => paybill_order_serial_sn($sid),
		'order_sn' => date('YmdHis') . random(6, true),
		'addtime' => TIMESTAMP,
		'total_fee' => $total_fee,
		'no_discount_part' => $no_discount_part,
		'discount_fee' => $couponPrice,
		'final_fee' => $final_fee,
		'stat_year' => date('Y'),
		'stat_month' => date('Ym'),
		'stat_day' => date('Ymd'),
		'note' => trim($_GPC['note']),
		'table_sn' => trim($_GPC['table_sn'])
	);
	pdo_insert('tiny_wmall_paybill_order', $order);
	$order_id = pdo_insertid();
	$extra['order_id'] = $order_id;
	coupon_consume($couponId, $extra);
	paybill_order_update_bill($order_id);
	imessage(error(0, $order_id), '', 'ajax');
}

elseif($ta == 'coupon') {
	$sid = $_GPC['sid'];
	$price = $_GPC['sum'];
	$coupons = coupon_consume_available($sid, $price, $_W['member']['uid']);
	if(!empty($coupons)) {
		$coupons = array_values($coupons);
		$num = count($coupons);
	} else {
		$coupons = array();
		$num = 0;
	}
	$respon = array('errno' => 0, 'message' => $coupons , 'num' => $num);
	imessage($respon, '', 'ajax');
}

elseif($ta == 'payment') {
	$payment = get_available_payment('paybill', $sid, true);
	$condition = 'where uniacid = :uniacid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);
	$tables = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables') . " {$condition} order by displayorder desc", $params);
	if(empty($tables)) {
		$tables = array(array());
	}
	$result = array(
		'store' => $store,
		'payment' => $payment,
		'tables' => $tables,
		'paybill_extra' => intval($_W['we7_wmall']['config']['paybill_extra'])
	);
	imessage(error(0, $result), '', 'ajax');
}
