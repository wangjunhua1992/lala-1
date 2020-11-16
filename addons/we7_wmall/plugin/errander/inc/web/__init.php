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
if(empty($_config_plugin['map']['location_x']) || empty($_config_plugin['map']['location_y'])) {
	$_config_plugin['map'] = $_W['_plugin']['config']['map'] = array(
		'location_x' => '39.90923',
		'location_y' => '116.397428',
	);
}
$_W['_errander_process'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and status >= 1 and status < 3', array(':uniacid' => $_W['uniacid']));
$_W['_errander_refund'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and (refund_status = 1 or refund_status = 2)', array(':uniacid' => $_W['uniacid']));
