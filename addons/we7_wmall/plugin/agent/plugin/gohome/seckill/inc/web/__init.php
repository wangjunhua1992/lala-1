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
function seckill_all_times() {
	$data = array();
	for($i = 0; $i < 24; $i++) {
		$data[] = $i;
	}
	return $data;
}