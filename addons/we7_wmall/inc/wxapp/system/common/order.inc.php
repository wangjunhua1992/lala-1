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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'check';
if($ta == 'check') {
	$id = intval($_GPC['id']);
	$order_from = trim($_GPC['order_from']);
	if(empty($order_from)) {
		$order_from = 'takeout';
	}
	$routers = array(
		'takeout' => array(
			'table' => 'tiny_wmall_order',
			'stop_status' => 1
		),
		'gohome' => array(
			'table' => 'tiny_wmall_gohome_order',
			'stop_status' => 1
		),
	);
	$router = $routers[$order_from];
	$order = pdo_get($router['table'], array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		imessage(error(-1, '订单不存在'), '', 'ajax');
	}
	if($order['status'] > $router['stop_status']) {
		imessage(error(-1, '订单已接单'), '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}
