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
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		imessage(error(-1, '订单不存在'), '', 'ajax');
	}
	if($order['status'] > 1) {
		imessage(error(-1, '订单已接单'), '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

if($ta == 'location') {
	$location_x = floatval($_GPC['location_x']);
	$location_y = floatval($_GPC['location_y']);
	if(empty($location_x) || empty($location_y)) {
		message(ierror(-1, '地理位置不完善'), '', 'ajax');
	}
	$token = trim($_GPC['token']);
	$deliveryer = deliveryer_fetch($token, 'token');
	file_put_contents(MODULE_ROOT . '/aa.txt', var_export($deliveryer, 1));
	pdo_query('delete from ' . tablename('tiny_wmall_deliveryer_location_log') . ' where addtime <= :addtime', array(':addtime' => TIMESTAMP - 10 * 86400));
	pdo_update('tiny_wmall_deliveryer', array('location_x' => $location_x, 'location_y' => $location_y), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	$data = array(
		'uniacid' => $_W['uniacid'],
		'deliveryer_id' => $deliveryer['id'],
		'location_x' => $location_x,
		'location_y' => $location_y,
		'addtime' => TIMESTAMP,
		'addtime_cn' => date('Y-m-d H:i:s'),
	);
	pdo_insert('tiny_wmall_deliveryer_location_log', $data);
	message(ierror(0, ''), '', 'ajax');
}

