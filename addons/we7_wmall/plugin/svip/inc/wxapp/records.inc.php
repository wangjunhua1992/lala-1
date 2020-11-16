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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
icheckauth();
if($_config_plugin['basic']['status'] != 1) {
	imessage(error(-1, '超级会员功能未开启'), '', 'ajax');
}
if($op == 'list') {
	$month = svip_present_month();
	if(is_error($month)) {
		imessage($month, '', 'ajax');
	}
	$next = $month['endtime'] + 86400;
	$next = date('Y-m-d', $next);
	$config = get_plugin_config('svip.basic');
	$exchange_max = intval($config['exchange_max']);
	$type = trim($_GPC['type']);
	$records = array();
	if($type == 'redpacket') {
		$records = svip_redpacket_record_fetchall();
	} elseif($type == 'credit') {
		$records = svip_credit1_record_fetchall();
	}
	$result = array(
		'records' => $records,
		'next' => $next,
		'exchange_max' => $exchange_max
	);
	imessage(error(0, $result), '', 'ajax');
}
