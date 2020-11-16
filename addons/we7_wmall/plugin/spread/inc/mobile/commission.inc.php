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
	$_W['page']['title'] = '推广佣金';
	$data = spread_commission_stat();
	$settle = get_plugin_config('spread.settle');
}


include itemplate('commission');