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
$_W['page']['title'] = '佣金明细';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($op == 'index') {
	$condition = ' where uniacid = :uniacid and bd_id = :bd_id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':bd_id' => $_W['storebd_user']['id'],
	);
	$trade_type = isset($_GPC['trade_type']) ? intval($_GPC['trade_type']) : 0;
	if($trade_type > 0) {
		$condition .= " and trade_type = {$trade_type}";
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']);
	$current = pdo_fetchall('select * from' . tablename('tiny_wmall_storebd_current_log') . $condition . ' order by id desc limit ' . ($page-1)*$psize . ', ' . $psize, $params);
	if(!empty($current)) {
		foreach($current as &$v) {
			$v['addtime_cn'] = date('Y-m-d H:i', $v['addtime']);
		}
	}
	$result = array(
		'current' => $current,
	);
	imessage(error(0, $result), '', 'ajax');
}


