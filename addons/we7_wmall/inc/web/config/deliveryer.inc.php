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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'settle';

if($op == 'settle') {
	$_W['page']['title'] = '配送员申请';
	if($_W['ispost']) {
		$settle = array(
			'mobile_verify_status' => intval($_GPC['mobile_verify_status']),
			'idCard' => intval($_GPC['idCard']),
		);
		set_config_text('配送员入驻协议', 'agreement_delivery', htmlspecialchars_decode($_GPC['agreement_delivery']));
		set_system_config('delivery.settle', $settle);
		imessage(error(0, '配送员申请设置成功'), ireferer(), 'ajax');
	}
	$settle = $_config['delivery']['settle'];
	$settle['agreement_delivery'] = get_config_text('agreement_delivery');
	include itemplate('config/deliveryer-settle');
}

elseif($op == 'cash') {
	$_W['page']['title'] = '提成及提现';
	$deliveryerCash = $_config['delivery']['cash'];
	if($_W['ispost']) {
		$form_type = trim($_GPC['form_type']);
		if($form_type == 'delivery_setting') {
			$deliveryerCash['is_errander'] = intval($_GPC['is_errander']);
			$deliveryerCash['is_takeout'] = intval($_GPC['is_takeout']);
			$deliveryerCash['collect_max_takeout'] = intval($_GPC['collect_max_takeout']);
			$deliveryerCash['collect_max_errander'] = intval($_GPC['collect_max_errander']);
			$deliveryerCash['perm_cancel'] = array(
				'status_takeout' => intval($_GPC['perm_cancel']['status_takeout']),
				'status_errander' => intval($_GPC['perm_cancel']['status_errander']),
			);
			$deliveryerCash['perm_transfer'] = array(
				'status_takeout' => intval($_GPC['perm_transfer']['status_takeout']),
				'max_takeout' => intval($_GPC['perm_transfer']['max_takeout']),
				'status_errander' => intval($_GPC['perm_transfer']['status_errander']),
				'max_errander' => intval($_GPC['perm_transfer']['max_errander']),
			);
			$deliveryer_takeout_fee_type = intval($_GPC['deliveryer_takeout_fee_type']);
			$deliveryer_takeout_fee = 0;
			if($deliveryer_takeout_fee_type == 1) {
				$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_1']);
			} elseif($deliveryer_takeout_fee_type == 2) {
				$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_2']);
			} elseif($deliveryer_takeout_fee_type == 3) {
				$deliveryer_takeout_fee = array(
					'start_fee' => floatval($_GPC['deliveryer_takeout_fee_3']['start_fee']),
					'start_km' => floatval($_GPC['deliveryer_takeout_fee_3']['start_km']),
					'pre_km' => floatval($_GPC['deliveryer_takeout_fee_3']['pre_km']),
					'max_fee' => floatval($_GPC['deliveryer_takeout_fee_3']['max_fee'])
				);
			} elseif($deliveryer_takeout_fee_type == 4) {
				$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_4']);
			}
			$deliveryer_errander_fee_type = intval($_GPC['deliveryer_errander_fee_type']);
			$deliveryer_errander_fee = 0;
			if($deliveryer_errander_fee_type == 1) {
				$deliveryer_errander_fee = floatval($_GPC['deliveryer_errander_fee_1']);
			} elseif($deliveryer_errander_fee_type == 2) {
				$deliveryer_errander_fee = floatval($_GPC['deliveryer_errander_fee_2']);
			} elseif($deliveryer_errander_fee_type == 3) {
				$deliveryer_errander_fee = array(
					'start_fee' => floatval($_GPC['deliveryer_errander_fee_3']['start_fee']),
					'start_km' => floatval($_GPC['deliveryer_errander_fee_3']['start_km']),
					'pre_km' => floatval($_GPC['deliveryer_errander_fee_3']['pre_km']),
					'max_fee' => floatval($_GPC['deliveryer_errander_fee_3']['max_fee'])
				);
			}
			$deliveryerCash['fee_delivery'] =  array(
				'takeout' => array(
					'deliveryer_fee_type' => $deliveryer_takeout_fee_type,
					'deliveryer_fee' => $deliveryer_takeout_fee
				),
				'errander' => array(
					'deliveryer_fee_type' => $deliveryer_errander_fee_type,
					'deliveryer_fee' => $deliveryer_errander_fee
				)
			);
		} elseif($form_type == 'getcash_setting') {
			$deliveryerCash['fee_getcash'] = array(
				'get_cash_fee_limit' => floatval($_GPC['fee_getcash']['get_cash_fee_limit']),
				'get_cash_fee_rate' => floatval($_GPC['fee_getcash']['get_cash_fee_rate']),
				'get_cash_fee_min' => floatval($_GPC['fee_getcash']['get_cash_fee_min']),
				'get_cash_fee_max' => floatval($_GPC['fee_getcash']['get_cash_fee_max']),
				'get_cash_period' => intval($_GPC['fee_getcash']['get_cash_period']),
			);
		}
		unset($deliveryerCash['sync'], $deliveryerCash['get_cash_fee_limit'], $deliveryerCash['get_cash_fee_rate'], $deliveryerCash['get_cash_fee_min'], $deliveryerCash['get_cash_fee_max'], $deliveryerCash['get_cash_period']);
		set_system_config('delivery.cash', $deliveryerCash);

		$deliveryerCash['perm_cancel'] = iserializer($deliveryerCash['perm_cancel']);
		$deliveryerCash['perm_transfer'] = iserializer($deliveryerCash['perm_transfer']);
		$deliveryerCash['fee_delivery'] = iserializer($deliveryerCash['fee_delivery']);
		$deliveryerCash['fee_getcash'] = iserializer($deliveryerCash['fee_getcash']);

		$update = $deliveryerCash;
		if($form_type == 'delivery_setting') {
			unset($update['fee_getcash']);
		} elseif($form_type == 'getcash_setting') {
			$update = array(
				'fee_getcash' => $update['fee_getcash']
			);
		}
		$sync = intval($_GPC['sync']);
		if($sync == 1) {
			pdo_update('tiny_wmall_deliveryer', $update, array('uniacid' => $_W['uniacid']));
		} elseif($sync == 2) {
			$deliveryer_ids = $_GPC['deliveryer_ids'];
			foreach($deliveryer_ids as $deliveryer_id) {
				pdo_update('tiny_wmall_deliveryer', $update, array('uniacid' => $_W['uniacid'], 'id' => intval($deliveryer_id)));
			}
		}
		imessage(error(0, '配送员设置成功'), ireferer(), 'ajax');
	}
	mload()->model('deliveryer');
	$deliveryers = deliveryer_all();
	include itemplate('config/deliveryer-cash');
}

elseif($op == 'extra') {
	$_W['page']['title'] = '其他设置';
	if($_W['ispost']) {
		$data = array(
			'takeout_rank_status' => intval($_GPC['takeout_rank_status']),
			'errander_rank_status' => intval($_GPC['errander_rank_status'])
		);
		set_system_config('delivery.extra', $data);
		imessage(error(0, '配送员的其他设置设置成功'), ireferer(), 'ajax');
	}
	$extra = $_config['delivery']['extra'];
	include itemplate('config/deliveryer-extra');
}
