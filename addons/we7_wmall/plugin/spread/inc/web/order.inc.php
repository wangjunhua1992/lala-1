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
if($op == 'index'){
	$_W['page']['title'] = '推广订单统计';
	$routers = array(
		'takeout' => array(
			'table' => 'tiny_wmall_order',
			'end_status' => array(5),
			'fields' => 'id, uid, ordersn, final_fee, endtime, data',
			'order_type_cn' => '外卖'
		)
	);
	if(check_plugin_perm('errander')) {
		$routers['errander'] = array(
			'table' => 'tiny_wmall_errander_order',
			'end_status' => array(3),
			'fields' => 'id, uid, order_sn as ordersn, final_fee, delivery_success_time as endtime, data',
			'order_type_cn' => '跑腿'
		);
	}
	if(check_plugin_perm('gohome')) {
		$routers['gohome'] = array(
			'table' => 'tiny_wmall_gohome_order',
			'end_status' => array(5, 6),
			'fields' => 'id, uid, ordersn, final_fee, endtime, data',
			'order_type_cn' => '生活圈'
		);
	}

	$spreaders = pdo_getall('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'is_spread' => 1, 'spread_status' => 1), array('uid', 'nickname', 'realname', 'avatar', 'spread1', 'spread2'), 'id');

	$condition = ' where uniacid = :uniacid and is_pay = 1 and spreadbalance = 1';
	$params = array(
		':uniacid' => $_W['uniacid']
	);

	$order_type = trim($_GPC['order_type']) ? trim($_GPC['order_type']) : 'takeout';
	if($order_type == 'gohome') {
		$condition .= ' and (status = 5 or status = 6)';
	} else {
		$condition .= ' and status = :status';
		$params[':status'] = $routers[$order_type]['end_status'][0];
	}

	$spreadid = intval($_GPC['spreadid']);
	$spread_type = intval($_GPC['spread_type']);
	if($spread_type == 1) {
		if($spreadid > 0) {
			$condition .= ' and spread1 = :spread1';
			$params[':spread1'] = $spreadid;
		} else {
			$condition .= ' and spread1 > 0';
		}
	} elseif($spread_type == 2) {
		if($spreadid > 0) {
			$condition .= ' and spread2 = :spread2';
			$params[':spread2'] = $spreadid;
		} else {
			$condition .= ' and spread2 > 0';
		}
	} else {
		if($spreadid > 0) {
			$condition .= ' and (spread1 = :spreadid or spread2 = :spreadid)';
			$params['spreadid'] = $spreadid;
		} else {
			$condition .= ' and spread1 > 0';
		}
	}

	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " and addtime > :start and addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename($routers[$order_type]['table']) .  $condition, $params);
	$orders = pdo_fetchall("select {$routers[$order_type]['fields']} from " . tablename($routers[$order_type]['table']) . $condition . ' order by addtime desc limit '.($pindex - 1) * $psize.','.$psize, $params, 'id');

	if(!empty($orders)) {
		foreach($orders as &$da) {
			$da['data']= iunserializer($da['data']);
			$da['endtime_cn'] = date('Y-m-d H:i:s', $da['endtime']);
		}
	}
	$pager = pagination($total, $pindex, $psize);

}
include itemplate('order');
