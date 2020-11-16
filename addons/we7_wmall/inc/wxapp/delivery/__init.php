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
mload()->model('deliveryer');
mload()->model('deliveryer.extra');
if($_W['_action'] != 'auth') {
	icheckdeliveryer();
	$_deliveryer = $deliveryer = $_W['deliveryer'];
	$relation = deliveryer_push_token($_W['deliveryer']);
	$_W['wxapp']['jpush_relation'] = $relation;
	$_W['is_agent'] = is_agent();
	$_W['agentid'] = 0;
	if($_W['is_agent']) {
		$_W['agentid'] = $_W['deliveryer']['agentid'];
		if(empty($_W['agentid'])) {
			imessage(error(-1, '未找到配送员所属的代理区域,请先给配送员分配所属的代理'), '', 'ajax');
		}
		$_W['we7_wmall']['config'] = get_system_config();
	}
	collect_wxapp_formid();
}
$config_takeout = $_W['we7_wmall']['config']['takeout'];
$config_delivery = $_W['we7_wmall']['config']['delivery'];

$errander_perm = check_plugin_perm('errander');
if($errander_perm) {
	$config_errander = get_plugin_config('errander');
}
$_W['role'] = 'deliveryer';
$_W['role_cn'] = "配送员:{$_W['deliveryer']['title']}";

if(!empty($_GPC['filter'])) {
	$_GPC['filter'] = json_decode(htmlspecialchars_decode($_GPC['filter']), true);
	foreach($_GPC['filter'] as $key => $val) {
		$_GPC[$key] = $val;
	}
}
