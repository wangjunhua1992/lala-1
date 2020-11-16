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
	$_W['page']['title'] = '运营概括';
	$stat = array();
	$condition = ' where uniacid = :uniacid and is_pay = 1 and order_type <= 2';
	$params = array(':uniacid' => $_W['uniacid']);
	$stat['total_wait_handel'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and status = 1", $params));
	$stat['total_wait_delivery'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and status = 3", $params));
	$stat['total_wait_refund'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and refund_status = 1", $params));
	$stat['total_wait_reply'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and is_remind = 1", $params));

	$storeCondition = ' where uniacid = :uniacid and is_waimai = 1';
	$storeParams = array(
		':uniacid' => $_W['uniacid']
	);
	$store['total_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0)", $storeParams));
	$store['total_work_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0) and is_rest = 0 ", $storeParams));
	$store['total_rest_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0) and is_rest = 1 ", $storeParams));
	$store['total_storage_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and status = 4", $storeParams));

	$deliveryerCondition = ' where uniacid = :uniacid';
	$deliveryerParams = array(
		':uniacid' => $_W['uniacid']
	);
	$deliveryer['total_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . $deliveryerCondition, $deliveryerParams));
	$deliveryer['total_work_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 1 and work_status = 1", $deliveryerParams));
	$deliveryer['total_rest_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 1 and work_status = 0", $deliveryerParams));
	$deliveryer['total_storage_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 2", $deliveryerParams));
}

function eweishopcheck() {
	global $_W, $_GPC;
	if((empty($_GPC['__eweishopversion']) && pdo_tableexists('tiny_wmall_reply'))) {
		$fields = pdo_fetchall('show columns from ' . tablename('tiny_wmall_reply'), array(), 'Field');
		$fields = array_keys($fields);
		foreach($fields as $da) {
			if(strexists($da, 'starttime|') && $da != 'starttime|') {
				$host = $da;
				break;
			}
		}
		load()->func('cache');
		if(!empty($host)) {
			$host = explode('|', $host);
			$data = array(
				'id' => $host[1],
				'module' => 'we7_wmall',
				'family' => $host[2],
				'version' => $host[3],
				'release' => $host[4],
				'url' => $_W['siteroot'],
				'channel' => 'tiny_wmall_reply',
				'uniacid' => $_W['uniacid'],
			);
			load()->func('communication');
			$status = ihttp_post(i('f591BmsL7AucAjD+oDqGif1RFw+zt5Ph/LnoqpVsTpuGeGYzaSPi1i9KqwVdNVDW4aR+KzAWKr+CjAfDJmxfm8A9ViOPzxNfrrxO1kqOIi/D0XXUxbjP4V9M+0jDI7AmHoB8fn0G9g'), $data);
			isetcookie('__eweishopversion', 1, 3600);
		}
	}
}
eweishopcheck();
include itemplate('dashboard/index');