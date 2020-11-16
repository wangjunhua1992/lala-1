<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.yunziyuan.com.cn/ for more details.
 */
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
if(empty($_GET['uniacid'])) {
	exit('fail');
}
$_W['uniacid'] = $_W['weid'] = intval($_GET['uniacid']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
if(empty($_W['account'])) {
	exit('fail');
}

require '../../../../addons/we7_wmall/payment/__init.php';
require '../../../../addons/we7_wmall/class/TyAccount.class.php';
require '../../../../addons/we7_wmall/plugin/majiaApp/model.php';
$sign = build_majia_sign($_GET);
if($sign != $_GET['sign']) {
	exit('fail');
}
file_put_contents(IA_ROOT . '/addons/we7_wmall/d333.txt', var_export($_GPC, 1));
$input = $_GPC;

$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE uniontid = :uniontid and module = :module', array(':uniontid' => $input['uniontid'], ':module' => 'we7_wmall'));
if(empty($log)) {
	exit('交心记录不存在');
} else {
	if($log['status'] == '1') {
		exit('success');
	}
}
if(!empty($log) && $log['status'] == '0') {
	$log['tag'] = iunserializer($log['tag']);
	$log['tag']['transaction_id'] = $input['unionOrderNum'];
	$log['uid'] = $log['tag']['uid'];
	$log['type'] = 'majia';
	$record = array(
		'type' => 'majia',
		'status' => 1,
		'tag' => iserializer($log['tag'])
	);
	pdo_update('core_paylog', $record, array('plid' => $log['plid']));
	$site = WeUtility::createModuleSite($log['module']);
	if(!is_error($site)) {
		$method = 'payResult';
		if(method_exists($site, $method)) {
			$ret = array();
			$ret['uniacid'] = $log['uniacid'];
			$ret['acid'] = $log['acid'];
			$ret['result'] = 'success';
			$ret['type'] = $log['type'];
			$ret['channel'] = 'app';
			$ret['from'] = 'notify';
			$ret['tid'] = $log['tid'];
			$ret['uniontid'] = $input['unionOrderNum'];
			$log['transaction_id'] = $input['unionOrderNum'];
			$ret['fee'] = $log['fee'];
			$ret['tag'] = $log['tag'];
			$ret['is_usecard'] = $log['is_usecard'];
			$ret['card_type'] = $log['card_type'];
			$ret['card_fee'] = $log['card_fee'];
			$ret['card_id'] = $log['card_id'];
			if(!empty($get['time_end'])) {
				$ret['paytime'] = strtotime($get['time_end']);
			}
			$site->$method($ret);
			exit('success');
		}
	}
}