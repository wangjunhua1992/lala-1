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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$sid = intval($_GPC['__mg_sid']);
if($ta == 'index') {
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$activity = activity_getall($sid, $status);
	$all_activity = store_all_activity();
	foreach ($activity as $key => &$val) {
		$val['until'] = round(($val['endtime'] - time()) / 86400);
		if($key == 'couponGrant' || $key == 'couponCollect') {
			$val['coupon_detail'] = coupon_fetch(0, $val['id']);
		}
		$val['type_cn'] = $all_activity[$key]['title'];
		$val['starttime_cn'] = date('Y-m-d', $val['starttime']);
		$val['endtime_cn'] = date('Y-m-d', $val['endtime']);
		$val['addtime_cn'] = date('Y-m-d', $val['addtime']);
		if($key == 'bargain'){
			unset($activity['bargain']);
		}
	}
	$result = array(
		'activity' => $activity
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'del') {
	$type = $_GPC['type'];
	$status = activity_del($sid, $type);
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, '撤销活动成功'), '', 'ajax');
}