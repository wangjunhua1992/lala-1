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
icheckauth();
$config_ordergrant = get_plugin_config('ordergrant');
$config_share = $config_ordergrant['share'];
if($_GPC['ac'] == 'share') {
	if($config_share['status'] == 0) {
		imessage('该活动未开启', '', 'info');
	}
} else {
	if($config_ordergrant['status'] == 0 ) {
		imessage('该活动未开启', '', 'info');
	}
	$order_days_amount = ordergrant_member_init();
}
