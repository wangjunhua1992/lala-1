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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '跑腿设置';
	$config_errander = get_agent_plugin_config('errander');
	if($_W['ispost']) {
		if($_GPC['form_type'] == 'redpacket_setting') {
			$redpacket = array(
				'status' => intval($_GPC['redpacket']['status']),
				'reason' => trim($_GPC['redpacket']['reason'])
			);
			set_agent_plugin_config('errander.redpacket', $redpacket);
		} else {
			$errander = array(
				'status' => intval($_GPC['status']),
				'map' => array(
					'location_x' => trim($_GPC['map']['lat']),
					'location_y' => trim($_GPC['map']['lng']),
				),
				'city' => trim($_GPC['city']),
				'serve_radius' => floatval($_GPC['serve_radius']),
				'mobile' => trim($_GPC['mobile']),
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
			$order['deliveryer_transfer_reason'] = array_filter($order['deliveryer_transfer_reason'], trim);
			$order['deliveryer_cancel_reason'] = array_filter($order['deliveryer_cencel_reason'], trim);
			$errander['deliveryer_fee'] = $errander['deliveryer_fee_type'] == 1 ? trim($_GPC['deliveryer_fee_1']) : trim($_GPC['deliveryer_fee_2']);
			$errander = array_merge($config_errander, $errander);
			set_agent_plugin_config('errander', $errander);
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
}
include itemplate('config');