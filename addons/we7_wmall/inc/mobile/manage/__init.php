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
		imessage('您没有管理店铺的权限', ivurl('pages/home/index', array(), true), 'error');
	}
	$relation = clerk_push_token($_W['manager']['id']);
	if($_W['_action'] != 'home') {
		$sid = intval($_GPC['sid']) ? intval($_GPC['sid']) : intval($_GPC['__mg_sid']);
		if(empty($sid)) {
			if(count($sids) == 1) {
				$temp = array_keys($sids);
				$sid = $temp[0];
			}
		}
		if(empty($sid)) {
			if($_W['isajax']) {
				imessage(error(-1, '请先选择要管理的门店'), '', 'ajax');
			}
			imessage('请先选择要管理的门店', imurl('manage/home/index'), 'error');
		}
		$permiss = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $_W['manager']['id']));
		if(empty($permiss)) {
			isetcookie('__mg_sid', 0, -1000);
			imessage('您没有该门店的管理权限', ireferer(), 'error');
		}
		$extra = iunserializer($permiss['extra']);
		if(empty($extra)) {
			$extra = array(
				'accept_wechat_notice' => 0,
				'accept_voice_notice' => 0
			);
		}
		$_W['manager']['extra'] = $extra;
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
		if($_W['_action'] == 'activity' ) {
			$op = $_W['_op'];
			$activity_types = store_all_activity();
			$activity_types = array_keys($activity_types);
			if(in_array($op, $activity_types)) {
				$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
				if(($ta == 'index' || $ta == 'post') && $_W['we7_wmall']['config']['store']['activity']['perm'][$op]['status'] != 1) {
					imessage('平台没有开启该活动，请联系平台管理员开通', ireferer(), 'error');
				}
			}
		}
	}
}
$_W['role'] = 'clerker';
$_W['role_cn'] = "店铺店员:{$_W['manager']['title']}";

