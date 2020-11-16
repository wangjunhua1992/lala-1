<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W;
$activity = pdo_get('tiny_wmall_freelunch', array('uniacid' => $_W['uniacid']));
if(!empty($activity['share'])) {
	$activity['share'] = iunserializer($activity['share']);
	$activity['share']['imgUrl'] = tomedia($activity['share']['imgUrl']);
	$_W['_share'] = $activity['share'];
}
