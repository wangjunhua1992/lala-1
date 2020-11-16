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

if($ta == 'index') {
	$_W['page']['title'] = '平台新用户';
	if($_W['ispost']) {
		if(empty($_W['ismanager'])) {
			imessage(error(-1, '您没有权限进行该操作'), '', 'ajax');
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
		$back = trim($_GPC['back']);
		if(empty($back)) {
			imessage(error(-1, '立减活动不能为空'), '', 'ajax');
		}
		$title = "平台新用户立减{$back}{$_W['Lang']['dollarSignCn']}";
		if(check_plugin_perm('iglobal') && $_W['we7_wmall']['config']['iglobal']['lang'] == 'zhcn2uy') {
			$title = "تەكشەمىز جاڭا قاريدارلارعا 平台新用户立减{$back}{$_W['Lang']['dollarSignCn']} يۋان تيىمدىلىك جاسايدى";
		}
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'mallNewMember',
			'status' => 1,
			'data' => array(
				'back' => $back,
				'plateform_charge' => 0,
				'store_charge' => $back,
			),
		);
		if(!empty($_W['ismanager'])) {
			$activity['data']['agent_charge'] = trim($_GPC['agent_charge']);
			$activity['data']['plateform_charge'] = trim($_GPC['plateform_charge']);
			if($activity['data']['agent_charge'] > $back) {
				$activity['data']['agent_charge'] = $back;
				$activity['data']['plateform_charge'] = 0;
				$activity['data']['store_charge'] = 0;
			} elseif($activity['data']['plateform_charge'] > $back) {
				$activity['data']['plateform_charge'] = $back;
				$activity['data']['agent_charge'] = 0;
				$activity['data']['store_charge'] = 0;
			} elseif($activity['data']['plateform_charge'] + $activity['data']['agent_charge'] > $back) {
				$activity['data']['plateform_charge'] = $back - $activity['data']['agent_charge'];
				$activity['data']['store_charge'] = 0;
			} else {
				$activity['data']['store_charge'] = round($back - $activity['data']['agent_charge'] - $activity['data']['plateform_charge'], 2);
			}
			if($activity['data']['store_charge'] < 0) {
				$activity['data']['store_charge'] = 0;
			}
		} elseif(!empty($_W['isagenter'])) {
			$activity['data']['agent_charge'] = trim($_GPC['agent_charge']);
			if($activity['data']['agent_charge'] > $back) {
				$activity['data']['agent_charge'] = $back;
				$activity['data']['plateform_charge'] = 0;
				$activity['data']['store_charge'] = 0;
			} else {
				$activity['data']['store_charge'] = round($back - $activity['data']['agent_charge'], 2);
			}
			if($activity['data']['store_charge'] < 0) {
				$activity['data']['store_charge'] = 0;
			}
		}

		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置新用户立减优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'mallNewMember');
	if(empty($_W['ismanager']) && empty($activity)) {
		imessage('该门店没有设置平台新用户优惠活动,如需设置,请联系平台负责人设置', ireferer(), 'info');
	}
}

if($ta == 'del') {
	$status = activity_del($sid, 'mallNewMember');
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(), 'ajax');
}
include itemplate('store/activity/mallNewMember');