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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth();
if($_config_plugin['card_apply_status'] != 1) {
	imessage(error(-1, '配送会员卡功能未开启'), '', 'ajax');
}
if($op == 'index') {
	$member = $_W['member'];
	$result = array(
		'member' => $member
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'exchange') {
	$code = trim($_GPC['code']);
	$uid = $_W['member']['uid'];
	if(empty($code) || empty($uid)) {
		imessage(error(-1, '参数错误'), '', 'ajax');
	}
	$delivery_code = pdo_get('tiny_wmall_delivery_cards_code', array('uniacid' => $_W['uniacid'], 'code' => $code));
	if(empty($delivery_code)) {
		imessage(error(-1, '兑换码不存在'), '', 'ajax');
	}
	if($delivery_code['status'] == 2) {
		imessage(error(-1, '兑换码已被使用'), '', 'ajax');
	}
	if($delivery_code['status'] == 3 || $delivery_code['endtime'] < TIMESTAMP) {
		imessage(error(-1, '兑换码已过期'), '', 'ajax');
	}
	$update = array(
		'uid' => $uid,
		'status' => 2,
		'exchangetime' => TIMESTAMP,
	);
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('setmeal_id', 'setmeal_day_free_limit', 'setmeal_deliveryfee_free_limit', 'setmeal_starttime', 'setmeal_endtime'));
	if(empty($member)) {
		imessage(error(-1, '会员不存在或已删除'), '', 'ajax');
	}
	$delivery_card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $delivery_code['deliverycard_id']));
	if(empty($delivery_card)) {
		imessage(error(-1, '套餐不存在或已被删除'), '', 'ajax');
	}
	$update_member = array(
		'setmeal_id' => $delivery_code['deliverycard_id'],
		'setmeal_day_free_limit' => $delivery_card['day_free_limit'],
		'setmeal_deliveryfee_free_limit' => $delivery_card['delivery_fee_free_limit'],
		'setmeal_starttime' => TIMESTAMP,
		'setmeal_endtime' => TIMESTAMP + $delivery_code['days'] * 86400,
	);
	if($member['setmeal_endtime'] >= TIMESTAMP) {
		if($member['setmeal_id'] == $delivery_code['deliverycard_id']){
			$update_member['setmeal_starttime'] = $member['setmeal_starttime'];
			$update_member['setmeal_endtime'] = $member['setmeal_endtime'] + $delivery_code['days'] * 86400;
		} else {
			imessage(error(-1, '兑换套餐与当前套餐不匹配'), '', 'ajax');
		}
	}
	pdo_update('tiny_wmall_delivery_cards_code', $update, array('uniacid' => $_W['uniacid'], 'code' => $code));
	pdo_update('tiny_wmall_members', $update_member, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	imessage(error(0, '兑换成功'), '', 'ajax');
}