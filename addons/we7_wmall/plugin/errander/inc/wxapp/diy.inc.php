<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();

if(!$_config_plugin['status']) {
	imessage(error(-1, '平台暂未开启跑腿功能'), '', 'ajax');
}
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($op == 'index') {
	$id = intval($_GPC['id']);
	$diypage = get_errander_diypage($id);
	if(!empty($diypage['diypage'])) {
		$status = check_mall_status($diypage['diypage']['agentid']);
		if(is_error($status)) {
			imessage(error(-3000, $status['message']), '', 'ajax');
		}
		$_W['agentid'] = $diypage['diypage']['agentid'];
	}
	$diypage['basic']['params']['yinsihao'] = array(
		'status' => 0,
		'agreement' => ''
	);
	if(check_plugin_perm('yinsihao')) {
		$yinsihao = get_plugin_config('yinsihao.basic');
		if(!empty($yinsihao) && $yinsihao['status'] == 1) {
			$diypage['basic']['params']['yinsihao']['status'] = 1;
			$diypage['basic']['params']['yinsihao']['agreement'] = get_config_text('yinsihao:agreement');
		}
	}
	$condition = array();
	$params = json_decode(htmlspecialchars_decode($_GPC['extra']), true);
	if(!empty($params)) {
		$buyaddress_id = intval($params['buyaddress_id']);
		if($buyaddress_id > 0) {
			$buyaddress = member_errander_address_check($buyaddress_id);
			if(!is_error($buyaddress)) {
				$condition['buyaddress'] = $buyaddress;
			}
		} else {
			if(!empty($params['buyaddress'])) {
				$buyaddress = member_errander_address_check($params['buyaddress']);
				if(!is_error($buyaddress)) {
					$condition['buyaddress'] = $buyaddress;
				}
			}
		}
		$acceptaddress_id = intval($params['acceptaddress_id']);
		if($acceptaddress_id > 0) {
			$acceptaddress = member_errander_address_check($acceptaddress_id);
			if(!is_error($acceptaddress)) {
				$condition['acceptaddress'] = $acceptaddress;
			}
		}
		unset($params['buyaddress']);
		$condition = array_merge($condition, $params);
	}
	$order = errander_order_calculate_delivery_fee($diypage, $condition, intval($_GPC['is_calculate']));
	if(is_error($order)) {
		message($order, '', 'ajax');
	}
	mload()->model('redPacket');
	$redPackets = redPacket_available($order['delivery_fee'], array($id), array('scene' => 'paotui', 'agentid' => $diypage['diypage']['agentid']));
	if(!empty($redPackets) && $_config_plugin['redpacket']['status'] === 0) {
		$redPackets = array();
		if(!empty($_config_plugin['redpacket']['reason'])) {
			$diypage['basic']['params']['noredpacketnote'] = $_config_plugin['redpacket']['reason'];
		}
	}
	$result = array(
		'diy' => $diypage['diypage'],
		'basic' => $diypage['basic'],
		'redPackets' => $redPackets,
		'order' => $order,
		'buyaddress_id' => $buyaddress['id'],
		'buyaddress' => $buyaddress,
		'acceptaddress_id' => $acceptaddress['id'],
		'acceptaddress' => $acceptaddress,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'feeRule') {
	$id = $_GPC['id'];
	$result = array(
		'feeRule' => get_errander_rule_fee($id)
	);
	message(error(0, $result), '', 'ajax');
}