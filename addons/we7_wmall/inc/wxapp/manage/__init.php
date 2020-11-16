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
mload()->model('manage');
mload()->model('clerk');
$relation = array();
if($_W['_action'] != 'auth') {
	icheckmanage();
	$sids = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $_W['manager']['id']), array(), 'sid');
	if(empty($sids)) {
		imessage(error(41009, '您没有管理店铺的权限'), '', 'ajax');
	}
	$relation = clerk_push_token($_W['manager']['id']);
	$_W['wxapp']['jpush_relation'] = $relation;
	if($_W['_action'] != 'home') {
		$sid = intval($_GPC['sid']) ? intval($_GPC['sid']) : intval($_GPC['__mg_sid']);
		if(empty($sid)) {
			imessage(error(-1000, '请先选择要管理的门店'), '', 'ajax');
		}
		$permiss = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $_W['manager']['id']));
		if(empty($permiss)) {
			isetcookie('__mg_sid', 0, -1000);
			imessage(error(41009, '您没有该门店的管理权限'), '', 'ajax');
		}
		$extra = iunserializer($permiss['extra']);
		if(empty($extra)) {
			$extra = array(
				'accept_wechat_notice' => 0,
				'accept_voice_notice' => 0
			);
		}
		$_W['manager']['extra'] = $extra;
		$_W['manager']['kefu_status_cn'] = to_kefustatus($permiss['kefu_status']);
		isetcookie('__mg_sid', $sid, 86400 * 7);
		$_GPC['__mg_sid'] = $sid;
		$store = store_fetch($sid);
		if($_W['is_agent']) {
			$_W['agentid'] = $store['agentid'];
		}
		$store['account'] = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $store['id']));
		if(!empty($store['account'])) {
			$store['account']['wechat'] = iunserializer($store['account']['wechat']);
			$store['account']['alipay'] = iunserializer($store['account']['alipay']);
			$store['account']['bank'] = iunserializer($store['account']['bank']);
		}
		$_W['we7_wmall']['store'] = $store;
	}
	collect_wxapp_formid();
}
if($_GPC['from'] == 'vue' && !empty($_GPC['filter'])) {
	$_GPC['filter'] = json_decode(htmlspecialchars_decode($_GPC['filter']), true);
	foreach($_GPC['filter'] as $key => $val) {
		$_GPC[$key] = $val;
	}
}
$_W['role'] = 'clerker';
$_W['role_cn'] = "店铺店员:{$_W['manager']['title']}";

