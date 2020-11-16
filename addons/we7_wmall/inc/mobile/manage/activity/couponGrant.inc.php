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
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$sid = intval($_GPC['__mg_sid']);
if($ta == 'index') {
	$_W['page']['title'] = '下单返券';
	if($_W['isajax']) {
		$activitytitle = trim($_GPC['title']);
		if(empty($activitytitle)) {
			imessage(error(-1, '活动名称不能为空'), '', 'ajax');
		}
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
		$condition = trim($_GPC['condition']);
		if(empty($condition)) {
			imessage(error(-1, '返券条件不能为空'), '', 'ajax');
		}
		$amount = trim($_GPC['amount']);
		if(empty($amount)) {
			imessage(error(-1, '预计发放总数量不能为空'), '', 'ajax');
		}
		if(!empty($_GPC['coupon'])) {
			foreach ($_GPC['coupon'] as $value) {
				$_GPC['coupon'] = $value;
				$discount = $value['discount'];
			}
			if(empty($discount)) {
				imessage(error(-1, '请添加优惠券'),'', 'ajax');
			}
			$title = "购物满{$condition}{$_W['Lang']['dollarSignCn']}可返{$discount}{$_W['Lang']['dollarSignCn']}代金券";
		} else {
			imessage(error(-1, '请添加优惠券'), '', 'ajax');
		}

		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponGrant',
			'status' => 1,
			'data' => iserializer($_GPC['coupon']),
		);

		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		$coupon = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'activity_id' => $status,
			'condition' => $condition,
			'amount' => $amount,
			'title' => $activitytitle,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'couponGrant',
			'status' => 1,
			'coupons' => iserializer($_GPC['coupon']),
		);
		pdo_insert('tiny_wmall_activity_coupon', $coupon);
		imessage(error(0, '设置下单返券活动成功'), 'refresh', 'ajax');
	}

}

include itemplate('activity/couponGrant');