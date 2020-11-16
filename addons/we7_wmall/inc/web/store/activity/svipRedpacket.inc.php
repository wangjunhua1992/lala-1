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
	if(!check_plugin_perm('svip')) {
		imessage(error(-1, '平台暂无超级会员插件的使用权限'), '', 'error');
	}
	$config = get_plugin_config('svip.basic');
	if($config['status'] != 1) {
		imessage(error(-1, '平台暂未开启超级会员功能'), '', 'error');
	}
	$_W['page']['title'] = '超级会员门店红包';

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
		$discount = floatval($_GPC['discount']);
		$config_svip = get_plugin_config('svip');
		$redpacket_min = floatval($config_svip['basic']['store_redpacket_min']);
		if($discount < $redpacket_min) {
			imessage(error(-1, "红包金额不能小于{$redpacket_min}{$_W['Lang']['dollarSignCn']}"), '', 'ajax');
		}
		$condition = floatval($_GPC['condition']);
		if($condition < 0) {
			$condition = 0;
		}
		$use_days_limit = intval($_GPC['use_days_limit']);
		if($use_days_limit <= 0) {
			imessage(error(-1, '红包有效期必须大于零'), '', 'ajax');
		}
		$amount = floatval($_GPC['amount']);
		if($amount <= 0) {
			imessage(error(-1, '每日限领红包数量必须大于零'), '', 'ajax');
		}
		$plateform_charge = 0;
		$agent_charge = 0;
		$store_charge = $discount;
		if(!empty($_W['ismanager'])) {
			$plateform_charge = floatval($_GPC['plateform_charge']);
			$agent_charge = floatval($_GPC['agent_charge']);
			if($agent_charge > $discount) {
				$agent_charge = $discount;
				$plateform_charge = 0;
				$store_charge = 0;
			} elseif($plateform_charge > $discount) {
				$plateform_charge = $discount;
				$agent_charge = 0;
				$store_charge = 0;
			} elseif($plateform_charge + $agent_charge > $discount) {
				$plateform_charge = $discount - $agent_charge;
				$store_charge = 0;
			} else {
				$store_charge = round($discount - $agent_charge - $plateform_charge, 2);
			}
			if($store_charge < 0) {
				$store_charge = 0;
			}
		} elseif(!empty($_W['isagenter'])) {
			$agent_charge = floatval($_GPC['agent_charge']);
			if($agent_charge > $discount) {
				$agent_charge = $discount;
				$plateform_charge = 0;
				$store_charge = 0;
			} else {
				$store_charge = round($discount - $agent_charge, 2);
			}
			if($store_charge < 0) {
				$store_charge = 0;
			}
		}

		$times = array();
		if(!empty($_GPC['start_hour'])) {
			foreach($_GPC['start_hour'] as $skey => $sval) {
				if(!empty($sval) && !empty($_GPC['end_hour'][$skey])) {
					$times[] = array(
						'start_hour' => $sval,
						'end_hour' => $_GPC['end_hour'][$skey]
					);
				}
			}
		}

		$store = store_fetch($sid, array('id', 'title', 'agentid'));
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $store['agentid'],
			'sid' => $sid,
			'title' => "{$discount}{$_W['Lang']['dollarSignCn']}会员红包",
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'svipRedpacket',
			'status' => 1,
			'data' => array(
				'discount' => $discount,
				'condition' => $condition,
				'use_days_limit' => $use_days_limit,
				'amount' => $amount,
				'discount_bear' => array(
					'plateform_charge' => $plateform_charge,
					'agent_charge' => $agent_charge,
					'store_charge' => $store_charge,
				),
				//'can_exchange' => intval($_GPC['can_exchange']),
				'times' => $times
			),
		);
		$activity['data'] = iserializer($activity['data']);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		mload()->model('plugin');
		pload()->model('svip');
		$redpacket = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $store['agentid'],
			'sid' => $sid,
			'title' => $store['title'],
			'discount' => $discount,
			'condition' => $condition,
			'use_days_limit' => $use_days_limit,
			'amount' => $amount,
			//'can_exchange' => intval($_GPC['can_exchange']),
			'starttime' => $starttime,
			'endtime' => $endtime,
			'data' => array(
				'discount_bear' => array(
					'plateform_charge' => $plateform_charge,
					'agent_charge' => $agent_charge,
					'store_charge' => $store_charge,
				),
				'times' => $times
			)
		);
		$status = svip_set_store_redpacket($sid, $redpacket);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置超级会员门店红包成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'svipRedpacket');
}

if($ta == 'del') {
	$status = activity_del($sid, 'svipRedpacket');
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(), 'ajax');
}
include itemplate('store/activity/svipRedpacket');