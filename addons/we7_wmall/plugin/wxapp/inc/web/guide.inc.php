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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';

if($op == 'index'){
	$_W['page']['title'] = '小程序启动图';
	if($_W['ispost']) {
		$data = $_GPC['guide'];
		$data =  base64_encode(json_encode($data));
		set_plugin_config('wxapp.guide', $data);
		imessage(error(0, '保存成功'), iurl('wxapp/guide/index'), 'ajax');
	}
	$guide = get_plugin_config('wxapp.guide');
	$guide = json_decode(base64_decode($guide), true);
}

include itemplate('guide');