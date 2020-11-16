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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '超级会员红包列表';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'));
	$status = svip_redpacket_status();
	$filter = array(
		'can_exchange' => isset($_GPC['can_exchange']) ? intval($_GPC['can_exchange']) : -1,
		'sid' => isset($_GPC['sid']) ? intval($_GPC['sid']) : -1,
		'status' => isset($_GPC['status']) ? intval($_GPC['status']) : -1,
	);
	$data = svip_redpacket_fetchall($filter);
	$redpackets = $data['redpackets'];
	$pager = $data['pager'];
}

elseif($op == 'post') {
	$_W['page']['title'] = '超级会员红包编辑';
	$id = intval($_GPC['id']);
	$redpacket = svip_redpacket_fetch($id);
	if($id > 0 && empty($redpacket)) {
		imessage(error(-1, '红包不存在或已删除'), '', 'ajax');
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
		$discount = floatval($_GPC['discount']);
		if($discount <= 0) {
			imessage(error(-1, '红包金额必须大于零'), '', 'ajax');
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
		$exchange_cost = floatval($_GPC['exchange_cost']);
		if($exchange_cost < 0) {
			imessage(error(-1, '兑换所需奖励金需不能小于零'), '', 'ajax');
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
		$data = array(
			'discount' => $discount,
			'condition' => $condition,
			'use_days_limit' => $use_days_limit,
			'amount' => $amount,
			'can_exchange' => intval($_GPC['can_exchange']),
			'exchange_cost' => floatval($_GPC['exchange_cost']),
			'starttime' => $starttime,
			'endtime' => $endtime,
			'data' => array(
				'discount_bear' => array(
					'plateform_charge' => $plateform_charge,
					'agent_charge' => $agent_charge,
					'store_charge' => $store_charge,
				),
			)
		);
		if(empty($redpacket)) {
			//新建红包
			$data['uniacid'] = $_W['uniacid'];
			$data['agentid'] = $_W['agentid'];
			$data['sid'] = 0;
			$data['title'] = '平台通用红包';
			$data['addtime'] = TIMESTAMP;
			$data['data'] = iserializer($data['data']);
			pdo_insert('tiny_wmall_svip_redpacket', $data);
			imessage(error(0, '超级会员红包新建成功'), iurl('svip/redpacket/list'), 'ajax');
		} else {
			if($redpacket['sid'] > 0) {
				//更新门店活动信息
				$activity = array(
					'uniacid' => $_W['uniacid'],
					'agentid' => $redpacket['agentid'],
					'sid' => $redpacket['sid'],
					'title' => "{$discount}{$_W['Lang']['dollarSignCn']}会员红包",
					'starttime' => $starttime,
					'endtime' => $endtime,
					'type' => 'svipRedpacket',
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
						'can_exchange' => intval($_GPC['can_exchange']),
						'exchange_cost' => floatval($_GPC['exchange_cost']),
					),
				);
				$activity['data'] = iserializer($activity['data']);
				mload()->model('activity');
				$status = activity_set($redpacket['sid'], $activity);
				if(is_error($status)) {
					imessage($status, '', 'ajax');
				}
			}
			if(!empty($redpacket['data'])) {
				$data['data'] = array_merge($redpacket['data'], $data['data']);
			}
			$data['data'] = iserializer($data['data']);
			pdo_update('tiny_wmall_svip_redpacket', $data, array('id' => $id));
			imessage(error(0, '超级会员红包编辑成功'), iurl('svip/redpacket/list'), 'ajax');
		}
	}

}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	$redpacket = svip_redpacket_fetch($id);
	if(empty($redpacket)) {
		imessage(error(-1, '红包不存在或已删除'), '', 'ajax');
	}
	if($redpacket['sid'] > 0) {
		mload()->model('activity');
		$status = activity_del($redpacket['sid'], 'svipRedpacket');
		if(is_error($status)) {
			imessage($status, ireferer(), 'ajax');
		}
		imessage(error(0, '撤销门店超级会员红包成功'), iurl('svip/redpacket/list'), 'ajax');
	} else {
		pdo_delete('tiny_wmall_svip_redpacket', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		imessage(error(0, '撤销平台超级会员红包成功'), iurl('svip/redpacket/list'), 'ajax');
	}
}

elseif($op == 'can_exchange') {
	$id = intval($_GPC['id']);
	$redpacket = svip_redpacket_fetch($id);
	if(empty($redpacket)) {
		imessage(error(-1, '红包不存在或已删除'), '', 'ajax');
	}
	$can_exchange = intval($_GPC['can_exchange']);
	pdo_update('tiny_wmall_svip_redpacket', array('can_exchange' => $can_exchange), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	if($redpacket['sid'] > 0) {
		mload()->model('activity');
		$activity = activity_get($redpacket['sid'],'svipRedpacket');
		if(!empty($activity)) {
			$activity['data']['can_exchange'] = $can_exchange;
			$activity['data'] = iserializer($activity['data']);
			$status = activity_set($redpacket['sid'], $activity);
			if(is_error($status)) {
				imessage($status, '', 'ajax');
			}
		}
	}
	imessage(error(0, '红包的可兑换状态变更成功'), '', 'ajax');
}

include itemplate('redpacket');