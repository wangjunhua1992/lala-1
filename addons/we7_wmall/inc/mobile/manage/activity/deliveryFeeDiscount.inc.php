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
	$_W['page']['title'] = '新建活动';
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
		$data = array();
		$title = array();
		if(!empty($_GPC['options'])) {
			foreach ($_GPC['options'] as $val) {
				$condition = trim($val['condition']);
				$back = trim($val['back']);
				if($condition && $back) {
					$data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
						'plateform_charge' => 0,
						'store_charge' => $back,
					);
					$title[] = "满{$condition}{$_W['Lang']['dollarSignCn']}减{$back}{$_W['Lang']['dollarSignCn']}配送费";
				}
			}
		}

		if(empty($data)) {
			imessage(error(-1, '满减活动不能为空'), '', 'ajax');
		}

		$title = implode(',', $title);
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'deliveryFeeDiscount',
			'status' => 1,
			'data' => iserializer($data),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置满减优惠优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'deliveryFeeDiscount');
	if(!empty($activity)) {
		foreach($activity['data'] as &$row) {
			if(!is_array($row)) {
				continue;
			}
			$data[] = $row;
		}
		$activity['data'] = $data;
	}
}

if($ta == 'del') {
	$status = activity_del($sid, 'discount');
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(), 'ajax');
}

include itemplate('activity/deliveryFeeDiscount');

