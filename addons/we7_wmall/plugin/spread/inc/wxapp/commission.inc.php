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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($op == 'index') {
	$can_set_account = 0;
	if(in_array('bank', $_config_plugin['settle']['cashcredit']) || in_array('alipay', $_config_plugin['settle']['cashcredit'])) {
		$can_set_account = 1;
	}
	$result = array(
		'data' => spread_commission_stat(),
		'settle' => $_config_plugin['settle'],
		'can_set_account' => $can_set_account
	);
	imessage(error(0, $result),'','ajax');
}
