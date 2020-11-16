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
global $_W,$_GPC;
$url = trim($_GPC['url']);

if (!empty($unisetting['jsauth_acid'])) {
	$jsauth_acid = $unisetting['jsauth_acid'];
} else {
	if ($_W['account']['level'] < 3 && !empty($unisetting['oauth']['account'])) {
		$jsauth_acid = $unisetting['oauth']['account'];
	} else {
		$jsauth_acid = $_W['acid'];
	}
}
if (!empty($jsauth_acid)) {
	$account_api = WeAccount::create($jsauth_acid);
	if (!empty($account_api)) {
		$_W['account']['jssdkconfig'] = $account_api->getJssdkConfig($url);
		$_W['account']['jsauth_acid'] = $jsauth_acid;
	}
}
$result = array(
	'jssdkconfig' => $_W['account']['jssdkconfig'],
);
imessage(error(0, $result), '', 'ajax');

















