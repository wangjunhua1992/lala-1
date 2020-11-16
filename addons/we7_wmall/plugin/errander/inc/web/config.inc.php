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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '跑腿设置';
	$config_errander = get_plugin_config('errander');
	if($_W['ispost']) {
		if($_GPC['form_type'] == 'errander_setting') {
			$errander = array(
				'status' => intval($_GPC['status']),
				'close_reason' => trim($_GPC['close_reason']),
				'map' => array(
					'location_x' => trim($_GPC['map']['lat']),
					'location_y' => trim($_GPC['map']['lng']),
				),
				'city' => trim($_GPC['city']),
				'serve_radius' => floatval($_GPC['serve_radius']),
				'mobile' => trim($_GPC['mobile']),
				'pay_time_limit' => intval($_GPC['pay_time_limit']),
				'handle_time_limit' => intval($_GPC['handle_time_limit']),
				'auto_success_hours' => intval($_GPC['auto_success_hours']),
				'delivery_before_limit' => intval($_GPC['delivery_before_limit']),
				'delivery_timeout_limit' => intval($_GPC['delivery_timeout_limit']),
				'auto_refresh' => intval($_GPC['auto_refresh']),
				'verification_code' => intval($_GPC['verification_code']),
				'dispatch_mode' => intval($_GPC['dispatch_mode']),
				'can_collect_order' => intval($_GPC['can_collect_order']),
				'deliveryer_fee_type' => intval($_GPC['deliveryer_fee_type']),
				'deliveryer_collect_max' => intval($_GPC['deliveryer_collect_max']),
				'over_collect_max_notify' => intval($_GPC['over_collect_max_notify']),
				'deliveryer_transfer_status' => intval($_GPC['deliveryer_transfer_status']),
				'deliveryer_transfer_max' => intval($_GPC['deliveryer_transfer_max']),
				'deliveryer_transfer_reason' => explode("\n", trim($_GPC['deliveryer_transfer_reason'])),
				'deliveryer_cancel_reason' => explode("\n", trim($_GPC['deliveryer_cancel_reason'])),
			);
			if($errander['deliveryer_fee_type'] == 1) {
				$errander['deliveryer_fee'] = trim($_GPC['deliveryer_fee_1']);
			} elseif($errander['deliveryer_fee_type'] == 2) {
				$errander['deliveryer_fee'] = trim($_GPC['deliveryer_fee_2']);
			} elseif($errander['deliveryer_fee_type'] == 3) {
				$errander['deliveryer_fee'] = array(
					'start_fee' => floatval($_GPC['deliveryer_fee_3']['start_fee']),
					'start_km' => floatval($_GPC['deliveryer_fee_3']['start_km']),
					'pre_km' => floatval($_GPC['deliveryer_fee_3']['pre_km']),
					'max_fee' => floatval($_GPC['deliveryer_fee_3']['max_fee']),
					);
			}
			$errander = array_merge($config_errander, $errander);
			set_plugin_config('errander', $errander);
			set_config_text('跑腿服务用户协议', 'agreement_errander', htmlspecialchars_decode($_GPC['agreement']));
		} elseif($_GPC['form_type'] == 'redpacket_setting') {
			$redpacket = array(
				'status' => intval($_GPC['redpacket']['status']),
				'reason' => trim($_GPC['redpacket']['reason'])
			);
			set_plugin_config('errander.redpacket', $redpacket);
		} else {
			$data['credit1'] = array(
				'status' => intval($_GPC['credit1']['status']),
				'grant_type' => intval($_GPC['credit1']['grant_type'])
			);
			$data['credit1']['grant_num'] = ($data['credit1']['grant_type'] == 1 ? intval($_GPC['credit1']['grant_num_1']) : intval($_GPC['credit1']['grant_num_2']));
			set_plugin_config('errander.credit', $data);
		}
		imessage(error(0, '跑腿设置保存成功'), 'refresh', 'ajax');
	}
	$config_errander['map']['lat'] = $config_errander['map']['location_x'];
	$config_errander['map']['lng'] = $config_errander['map']['location_y'];
	if(!empty($config_errander['deliveryer_transfer_reason'])) {
		$config_errander['deliveryer_transfer_reason'] = implode("\n", $config_errander['deliveryer_transfer_reason']);
	}
	if(!empty($config_errander['deliveryer_cancel_reason'])) {
		$config_errander['deliveryer_cancel_reason'] = implode("\n", $config_errander['deliveryer_cancel_reason']);
	}
	$agreement_errander = get_config_text('agreement_errander');
	include itemplate('config');
}

elseif($op == 'share') {
	$_W['page']['title'] = '跑腿分享设置';
	if($_W['ispost']) {
		$share = array(
			'title' => trim($_GPC['title']),
			'imgUrl' => trim($_GPC['imgUrl']),
			'desc' => trim($_GPC['desc']),
			'link' => trim($_GPC['link']),
		);
		set_plugin_config('errander.share', $share);
		imessage(error(0, '跑腿分享设置成功'), ireferer(), 'ajax');
	}
	$share = $_config_plugin['share'];
	include itemplate('share');
}