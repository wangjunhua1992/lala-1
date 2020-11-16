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
mload()->model('activity');
mload()->model('coupon');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
if($ta == 'post'){
	$_W['page']['title'] = '下单返券优惠';
	$activity = activity_get($sid, 'couponGrant');
	if(!empty($activity)) {
		imessage('门店已有下单返券活动, 如需重新添加领券活动，请先撤销其他领券活动', '', 'info');
	}
	if($_W['ispost']) {
		$starttime = trim($_GPC['starttime']);
		if(empty($starttime)) {
			imessage(error(-1, '活动开始时间不能为空'), '', 'ajax');
		}
		$endtime = trim($_GPC['endtime']);
		if(empty($endtime)) {
			imessage(error(-1, '活动结束时间不能为空'), '', 'ajax');
		}
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);
		if($starttime >= $endtime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}
		$_GPC['coupons'] = str_replace('&nbsp;', '#nbsp;', $_GPC['coupons']);
		$_GPC['coupons'] = json_decode(str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($_GPC['coupons']))), true);
		$_GPC['coupons'] = $_GPC['coupons'][0];
		$discount = $_GPC['coupons']['discount'];
		if(empty($discount)) {
			imessage(error(-1, '请先添加优惠券'), '', 'ajax');
		}
		$condition = trim($_GPC['condition']);
		$title = "购物满{$_GPC['condition']}{$_W['Lang']['dollarSignCn']}可返{$discount}{$_W['Lang']['dollarSignCn']}代金券";
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponGrant',
			'status' => 1,
			'data' => iserializer($_GPC['coupons']),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		$coupon = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'activity_id' => $status,
			'condition' => trim($_GPC['condition']),
			'title' => trim($_GPC['title']),
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponGrant',
			'status' => 1,
			'amount' => intval($_GPC['amount']),
			'coupons' => iserializer($_GPC['coupons']),
		);
		pdo_insert('tiny_wmall_activity_coupon', $coupon);
		activity_cron();
		imessage(error(0, '设置下单返券优惠成功'), iurl('store/activity/couponGrant/list'), 'ajax');
	}
}

if($ta == 'list') {
	$_W['page']['title'] = '下单返券优惠列表';
	$coupons = activity_get($sid, 'couponGrant');
	if(!empty($coupons)) {
		$coupon = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'activity_id' => $coupons['id']));
		$coupons['condition'] = $coupon['condition'];
	}
}

if($ta == 'detail') {
	$_W['page']['title'] = '活动信息';
	$id = intval($_GPC['id']);
	if(empty($id)) {
		imessage(error(-1, '该活动不存在或已删除'), ireferer(),'ajax');
	}
	$activity_id = intval($_GPC['activity_id']);
	$item = coupon_fetch($id, $activity_id);
}

if($ta == 'del') {
	$type = trim($_GPC['type']);
	$status = activity_del($sid, $type);
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(),'ajax');
}
include itemplate('store/activity/couponGrant');