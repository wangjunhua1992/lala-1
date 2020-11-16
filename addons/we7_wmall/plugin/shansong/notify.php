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
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';

include(IA_ROOT . '/addons/we7_wmall/version.php');
include(IA_ROOT . '/addons/we7_wmall/defines.php');
include(IA_ROOT . '/addons/we7_wmall/model.php');
require IA_ROOT . '/addons/we7_wmall/class/TyAccount.class.php';

if(!empty($_GET)) {
	$ordersn = $_GET['orderno'];
	$order = pdo_get('tiny_wmall_order', array('ordersn' => $ordersn), array('id', 'uniacid'));
	if(!empty($order)) {
		$_W['uniacid'] = $order['uniacid'];
		$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
		$_W['acid'] = $_W['uniaccount']['acid'];
		$_W['_plugin'] = array(
			'name' => 'shansong'
		);
		mload()->model('plugin');
		pload()->classs('subscribe');
		$subscribe = new subscribe();
		$subscribe->start($_GET);
	}
}
exit('fail');


