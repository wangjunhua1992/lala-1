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
$_config = get_agent_system_config();

if($op == 'range') {
	$_W['page']['title'] = '服务范围';
	if($_W['ispost']) {
		$range = array(
			'map' => array(
				'location_x' => trim($_GPC['map']['lat']),
				'location_y' => trim($_GPC['map']['lng']),
			),
			'city' => trim($_GPC['city']),
			'serve_radius' => floatval($_GPC['serve_radius']),
		);
		set_agent_system_config('takeout.range', $range);
		imessage(error(0, '外卖服务范围设置成功'), ireferer(), 'ajax');
	}
	$range = $_config['takeout']['range'];
	$range['map']['lat'] = $range['map']['location_x'];
	$range['map']['lng'] = $range['map']['location_y'];
	include itemplate('config/takeout-range');
}

if($op == 'order') {
	$_W['page']['title'] = '订单相关';
	if($_W['ispost']) {
		$order = array(
			'show_no_pay' => intval($_GPC['show_no_pay']),
			'auto_refresh' => intval($_GPC['auto_refresh']),
			'reserve_order_auto_notice_deliveryer' => intval($_GPC['reserve_order_auto_notice_deliveryer']),
			'deliveryer_collect_notify_clerk' => intval($_GPC['deliveryer_collect_notify_clerk']),
			'dispatch_mode' => intval($_GPC['dispatch_mode']),
			'can_collect_order' => intval($_GPC['can_collect_order']),
			'deliveryer_transfer_reason' => explode("\n", trim($_GPC['deliveryer_transfer_reason'])),
			'deliveryer_cancel_reason' => explode("\n", trim($_GPC['deliveryer_cancel_reason'])),
			'dispatch_sort' => trim($_GPC['dispatch_sort']),
			'max_dispatching' => intval($_GPC['max_dispatching']),
			'show_acceptaddress_when_firstdelivery' => intval($_GPC['show_acceptaddress_when_firstdelivery']),
		);
		$order['deliveryer_transfer_reason'] = array_filter($order['deliveryer_transfer_reason'], trim);
		$order['deliveryer_cancel_reason'] = array_filter($order['deliveryer_cancel_reason'], trim);
		set_agent_system_config('takeout.order', $order);
		imessage(error(0, '订单相关设置成功'), ireferer(), 'ajax');
	}
	$order = $_config['takeout']['order'];
	if(!empty($order['deliveryer_transfer_reason'])) {
		$order['deliveryer_transfer_reason'] = implode("\n", $order['deliveryer_transfer_reason']);
	}
	if(!empty($order['deliveryer_cancel_reason'])) {
		$order['deliveryer_cancel_reason'] = implode("\n", $order['deliveryer_cancel_reason']);
	}
	include itemplate('config/takeout-order');
}
