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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'order';
$_config = get_agent_system_config('', $_W['agentid']);

if($ta == 'order') {
	if($_W['ispost']) {
		$_GPC['clerk'] = $_GPC['notify_rule_clerk'];
		$_GPC['deliveryer'] = $_GPC['notify_rule_deliveryer'];
		$order = array(
			'notify_rule_clerk' => array(
				'notify_delay' => intval($_GPC['clerk']['notify_delay']),
				'notify_frequency' => intval($_GPC['clerk']['notify_frequency']),
				'notify_total' => intval($_GPC['clerk']['notify_total']),
				'notify_phonecall_time' => intval($_GPC['clerk']['notify_phonecall_time']),
			),
			'notify_rule_deliveryer' => array(
				'notify_delay' => intval($_GPC['deliveryer']['notify_delay']),
				'notify_frequency' => intval($_GPC['deliveryer']['notify_frequency']),
				'notify_total' => intval($_GPC['deliveryer']['notify_total']),
				'notify_phonecall_time' => intval($_GPC['deliveryer']['notify_phonecall_time']),
			),
			'show_no_pay' => intval($_GPC['show_no_pay']),
			'auto_refresh' => intval($_GPC['auto_refresh']),
			'deliveryer_collect_notify_clerk' => intval($_GPC['deliveryer_collect_notify_clerk']),
			'timeout_limit' => intval($_GPC['timeout_limit']),
			'delivery_timeout_limit' => intval($_GPC['delivery_timeout_limit']),
			'delivery_before_limit' => intval($_GPC['delivery_before_limit']),
			'dispatch_mode' => intval($_GPC['dispatch_mode']),
			'can_collect_order' => intval($_GPC['can_collect_order']),
			'deliveryer_collect_max' => intval($_GPC['deliveryer_collect_max']),
			'over_collect_max_notify' => intval($_GPC['over_collect_max_notify']),
			'deliveryer_transfer_status' => intval($_GPC['deliveryer_transfer_status']),
			'deliveryer_transfer_max' => intval($_GPC['deliveryer_transfer_max']),
			'deliveryer_transfer_reason' => explode("\n", trim($_GPC['deliveryer_transfer_reason'])),
			'deliveryer_cancel_reason' => explode("\n", trim($_GPC['deliveryer_cancel_reason'])),
			'dispatch_sort' => trim($_GPC['dispatch_sort']),
			'max_dispatching' => intval($_GPC['max_dispatching']),
			'show_acceptaddress_when_firstdelivery' => intval($_GPC['show_acceptaddress_when_firstdelivery']),
		);
		$order['deliveryer_transfer_reason'] = array_filter($order['deliveryer_transfer_reason'], trim);
		$order['deliveryer_cancel_reason'] = array_filter($order['deliveryer_cancel_reason'], trim);
		set_agent_system_config('takeout.order', $order, $_W['agentid']);
		imessage(error(0, ''), '', 'ajax');
	}
	$order = $_config['takeout']['order'];
	if(!empty($order['deliveryer_transfer_reason'])) {
		$order['deliveryer_transfer_reason'] = implode("\n", $order['deliveryer_transfer_reason']);
	}
	if(!empty($order['deliveryer_cancel_reason'])) {
		$order['deliveryer_cancel_reason'] = implode("\n", $order['deliveryer_cancel_reason']);
	}
	$result = array(
		'order' => $order,
	);
	imessage(error(0, $result), '', 'ajax');
} elseif($ta == 'mall'){
	if($_W['ispost']) {
		$mall = array(
			'version' => 1,
			'store_orderby_type' => trim($_GPC['store_orderby_type']),
			'store_overradius_display' => intval($_GPC['store_overradius_display']),
		);
		set_agent_system_config('mall', $mall, $_W['agentid']);
		imessage(error(0, ''), '', 'ajax');
	}
	$mall = $_config['mall'];
	$result = array(
		'mall' => $mall,
	);
	imessage(error(0, $result), '', 'ajax');
}
