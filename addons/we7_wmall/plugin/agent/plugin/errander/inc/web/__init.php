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
if(empty($_config_plugin['map']['location_x']) || empty($_config_plugin['map']['location_y'])) {
	$_config_plugin['map'] = $_W['_plugin']['config']['map'] = array(
		'location_x' => '39.90923',
		'location_y' => '116.397428',
	);
}
$_W['_errander_process'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and agentid = :agentid and status >= 1 and status < 3', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
$_W['_errander_refund'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and agentid = :agentid and (refund_status = 1 or refund_status = 2)', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
