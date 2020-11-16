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
mload()->model('cron');
global $_W, $_GPC;
$task = cache_read('we7_wmall:task');
if(!empty($task) && $task['expiretime'] > TIMESTAMP) {
	exit('process');
}
cache_write('we7_wmall:task', array('expiretime' => TIMESTAMP + 120));

$accounts = pdo_getall('tiny_wmall_config', array());
if(empty($accounts)) {
	exit('success');
}
ignore_user_abort();
set_time_limit(0);
foreach($accounts as &$account) {
	$_W['uniacid'] = $account['uniacid'];
	if(empty($_W['uniacid']) || $_W['uniacid'] == -1) {
		continue;
	}
	if(empty($account['sysset'])) {
		continue;
	}
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	if(empty($_W['uniaccount'])) {
		continue;
	}
	$_W['we7_wmall']['config'] = get_system_config();
	cron_order();
}
cache_delete('we7_wmall:task');
exit('success');









