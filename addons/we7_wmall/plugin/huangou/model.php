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

function huangou_get_store_goods($sid) {
	global $_W;
	$activity = pdo_get('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1, 'type' => 'huangou'), array('id', 'data'));
	if(empty($activity)) {
		return error(-1, '门店没有可用的换购活动');
	}
	$activity['data'] = iunserializer($activity['data']);
	$huangou_bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'huangou'), array('id', 'title', 'content'));
	$huangou_id = "bargain_{$huangou_bargain['id']}";
	$huangou_goods = goods_filter($sid, array('cid' => $huangou_id));
	return array(
		'activity' => $huangou_bargain,
		'huangou_goods' => $huangou_goods,
		'price_limit' => floatval($activity['data']['price_limit'])
	);
}