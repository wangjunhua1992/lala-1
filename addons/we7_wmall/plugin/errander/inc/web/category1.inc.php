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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';
if($op == 'list') {
	$fees = array(
		'id' => 'fees',
		'params' => array(
		),
		'data' => array(

		)
	);
	//$page = get_wxapp_diy(4);
	//$page['data']['items']['M1520235624500'] = $fees;
}
include itemplate('category1');
die;



