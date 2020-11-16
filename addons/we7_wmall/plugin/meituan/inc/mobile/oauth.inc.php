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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'storemap';
$sid = intval($_GPC['state']);
if(empty($sid)) {
	imessage("店铺id不能为空", '', 'info');
}
pload()->classs('meituan');
$app = new Meituan($sid);
if($op == 'storemap') {
	$url = $app->getStoremapUrl();
} elseif($op == 'releasebinding') {
	$config_meituan = store_get_data($sid, 'meituan');
	if(empty($config_meituan['basic']['status'])) {
		imessage("您还没有进行美团对接,不能进行解绑操作", '', 'error');
	}
	$url = $app->getReleasebindingUrl();
}
if(is_error($url)) {
	imessage("获取url失败:{$url['message']}", '', 'error');
}
header('Location: ' . $url);
die;