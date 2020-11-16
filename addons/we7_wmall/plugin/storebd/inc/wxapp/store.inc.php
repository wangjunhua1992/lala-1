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
	$condition = " where a.uniacid = :uniacid and a.bd_id = :bd_id";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':bd_id' => $_W['storebd_user']['id'],
	);
	// $storebd_user = pdo_get('tiny_wmall_storebd_user', array('uniacid' => $_W['uniacid'], 'id' => $bd_id));
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']);
	$stores = pdo_fetchall('select a.*, b.title, b.logo from ' . tablename('tiny_wmall_storebd_store') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id' . $condition . ' order by a.id desc limit ' . ($page-1)*$psize . ', ' . $psize, $params);
	if(!empty($stores)) {
		foreach($stores as &$value) {
			$value['fee_takeout'] = iunserializer($value['fee_takeout']);
			$value['fee_instore'] = iunserializer($value['fee_instore']);
			$value['addtime_cn'] = date('Y-m-d H:i', $value['addtime']);
			$value['logo'] = tomedia($value['logo']);
		}
	}

	$result = array(
		'stores' => $stores,
		//'storebd_user' => $storebd_user
	);
	imessage(error(0, $result), '', 'ajax');
}
