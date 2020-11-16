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
$config_mall = $_W['we7_wmall']['config']['mall'];
$_W['page']['title'] = $config_mall['title'];

$url = imurl('wmall/home/index');
if($config_mall['version'] == 2) {
	$url = imurl('wmall/store/goods', array('sid' => $config_mall['default_sid']));
} elseif($config_mall['is_to_nearest_store'] == 1) {
	$url = imurl('wmall/home/near');
}

$slides = sys_fetch_slide('startpage');
if(empty($slides)) {
	header('location:' . $url);
	die;
}
include itemplate('home/guide');

