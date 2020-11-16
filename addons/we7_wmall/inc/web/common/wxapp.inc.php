<?php
/**
 * 啦啦外卖 - 做好用的外卖系统!
 * =========================================================
 * Copy right 2015-2038 太原多讯网络科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.duoxunwl.com/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 啦啦外卖团队
 * @客服QQ : 2622178042
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']);
if($op == 'link') {
	$getScene = trim($_GPC['scene']);
	if(empty($getScene)){
		$getScene = 'page';
	}
	$type = trim($_GPC['type']);
	if(empty($type)) {
		$type = 'wmall';
	}
	$addhost = $_GPC['addhost'] == 1 ? true : false;
	$data = wxapp_urls($type, $addhost);
	if($getScene == 'menu') {
		unset($data['errander']['business']);
		unset($data['errander']['scene']);
	}
	if($getScene != 'store') {
		unset($data['other']['table']);
	}
	include itemplate('public/wxappLink');
}
elseif($op == 'icon') {
	$type = trim($_GPC['type']);
	if(empty($type)) {
		$type = 'wmall';
	}
	include itemplate('public/wxappIcon');
}
