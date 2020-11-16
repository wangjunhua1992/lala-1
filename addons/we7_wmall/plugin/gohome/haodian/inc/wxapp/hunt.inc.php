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
icheckauth(true);
$config_mall = $_W['we7_wmall']['config']['mall'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$member = member_fetch();
	}
	$filter = array(
		'orderby' => 'click',
		'psize' => 4
	);
	$result = array(
		'hotStores' => haodian_store_fetchall($filter),
		'searchHistorys' => $member['search_data'],
	);
	imessage(error(0, $result), '', 'ajax');
}

if($op == 'truncate') {
	if($_W['member']['uid'] > 0) {
		pdo_update('tiny_wmall_members', array('search_data' => ''), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
	imessage(error(0, '清除历史记录成功'), '', 'ajax');
}

if($op == 'search') {
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$lat = trim($_GPC['lat']);
		$lng = trim($_GPC['lng']);
		$key = trim($_GPC['key']);
		$member = member_fetch();
		if(!empty($member)) {
			$num = count($member['search_data']);
			if($num >= 5) {
				array_pop($member['search_data']);
			}
			array_push($member['search_data'], $key);
			$search_data = iserializer(array_unique($member['search_data']));
			pdo_update('tiny_wmall_members', array('search_data' => $search_data), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		}
	}
	$key = trim($_GPC['key']);
	$filter = array(
		'keyword' => $key,
		'psize' => 100,
		'get_activity' => 1
	);
	$stores = haodian_store_fetchall($filter);
	$result = array(
		'stores' => $stores,
	);
	imessage(error(0, $result), '', 'ajax');
}


