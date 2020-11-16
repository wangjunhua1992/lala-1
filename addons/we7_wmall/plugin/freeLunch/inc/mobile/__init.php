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
$freelunch = freelunch_record_init();
if(is_error($freelunch)) {
	imessage($freelunch['message'], '', 'info');
}
$_share = array(
	'title' => $freelunch['share']['title'],
	'desc' => $freelunch['share']['desc'],
	'link' => !empty($freelunch['share']['link']) ? $freelunch['share']['link'] : imurl('freeLunch/freeLunch/index', array(), true),
	'imgUrl' => tomedia($freelunch['share']['imgUrl'])
);

