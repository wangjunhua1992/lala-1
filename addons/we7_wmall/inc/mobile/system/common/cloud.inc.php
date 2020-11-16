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
mload()->model('cloud');
global $_W, $_GPC;
$_W['ifrom'] = trim($_GPC['ifrom']);
$ta  = trim($_GPC['ta']);
$post = file_get_contents('php://input');

if($ta == 'touch') {
	imessage(error(0, 'success'), '', 'ajax');
}

if($ta == 'build') {
	$data = cloud_w_parse_build($post);
	imessage($data, '', 'ajax');
}

if($ta == 'schema') {
	cloud_w_parse_schema($post);
}

if($ta == 'download') {
	$data = cloud_w_parse_download($post);
	imessage($data, '', 'ajax');
}

if($ta == 'stat') {
	$data = cloud_w_order_stat();
	imessage($data, '', 'ajax');
}

if($ta == 'vue') {
	$isvue = is_file(MODULE_ROOT . '/template/vue/index.html');
	$result = array(
		'vue' => intval($isvue)
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'itime') {
	set_system_config('itime', 0);
	imessage(error(0, $result), '', 'ajax');
}




