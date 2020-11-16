<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
include(IA_ROOT . '/addons/we7_wmall/version.php');
include('defines.php');
include('model.php');
class We7_wmallModuleReceiver extends WeModuleReceiver {
	public function receive() {
		global $_W;
	}
}
