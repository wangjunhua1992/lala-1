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
$sid = intval($_GPC['sid']);
$url = iurl('store/order/');
$forward = trim($_GPC['referer']);
$account = store_account($sid);
if(empty($account)) {
	$config_settle = $_W['we7_wmall']['config']['settle'];
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $_W['agentid'],
		'sid' => $sid,
		'fee_limit' => $config_settle['get_cash_fee_limit'],
		'fee_rate' => $config_settle['get_cash_fee_rate'],
		'fee_min' => $config_settle['get_cash_fee_min'],
		'fee_max' => $config_settle['get_cash_fee_max'],
	);
	pdo_insert('tiny_wmall_store_account', $insert);
}
header('location: ' . $url);
exit();
