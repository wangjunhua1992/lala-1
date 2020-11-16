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
	$_W['page']['title'] = '门店新用户';
	if($_W['isajax']) {
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
		$back = trim($_GPC['back']);
		if(empty($back)) {
			imessage(error(-1, '活动金额不能为空'), '', 'ajax');
		}
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => "本店新用户立减{$back}{$_W['Lang']['dollarSignCn']}",
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'newMember',
			'status' => 1,
			'data' => array(
				'back' => $back,
				'plateform_charge' => 0,
				'store_charge' => $back,
			),
		);
		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置新用户立减优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'newMember');
}

if($ta == 'del') {
	$status = activity_del($sid, 'newMember');
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(), 'ajax');
}

include itemplate('activity/newMember');
