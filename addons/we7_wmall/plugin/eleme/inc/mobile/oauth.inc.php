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
$sid = intval($_GPC['state']);
if(empty($sid)) {
	imessage("店铺id不能为空", '', 'info');
}

pload()->classs('eleme');
$app = new Eleme($sid);
$redirect_uri = imurl('eleme/oauth', array(), true);
$url = $app->getOauthCodeUrl($redirect_uri, $sid);
if(empty($_GPC['code'])) {
	header('Location: ' . $url);
	die;
} else {
	$url = $app->getAccessTokenByCode($_GPC['code'], $redirect_uri);
	if(is_error($url)) {
		imessage("进行饿了么授权失败:{$url['message']}", '', 'info');
	}
	imessage("授权成功", ireferer(), 'info');
}
die;
