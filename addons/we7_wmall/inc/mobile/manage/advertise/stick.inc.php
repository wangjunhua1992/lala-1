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
mload()->model('plugin');
pload()->model('advertise');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$advertise_stick = get_advertise_info('stick');
if($advertise_stick['status'] != 1) {
	imessage(error(-1, '该广告暂未开售'), '', 'ajax');
}
$amount = $store['account']['amount'];
$_W['page']['title'] = '商家置顶推广';
if($ta == 'index') {
	$displayorder_fees = $advertise_stick['prices'];
	$sailed = $advertise_stick['sailed'];
	foreach($displayorder_fees as $key => &$val) {
		if(in_array($key, $sailed)) {
			$val['sailed'] = 1;//已售
		} else {
			$val['sailed'] = 0;
		}
	}
	if($_W['isajax']) {
		$displayorder = intval($_GPC['displayorder']);
		if(!$displayorder) {
			imessage(error(-1, '请选择置顶位置'), '', 'ajax');
		}
		if(empty($advertise_stick['leave'])) {
			imessage(error(-1, '商家置顶广告位已售空，请选择其他广告位'), '', 'ajax');
		}
		$day = intval($_GPC['day']);
		if(!$day) {
			imessage(error(-1, '请选择购买天数'), '', 'ajax');
		}
		$pay_type = $_GPC['pay_type'];
		if(!$pay_type) {
			imessage(error(-1, '请选择支付方式'), '', 'ajax');
		}
		$finalfee = $advertise_stick['prices'][$displayorder]['fees'][$day]['fee'];
		if($pay_type == 'credit' && $amount < $finalfee) {
			imessage(error(-1,'余额不足，请选择其他支付方式'), '', 'ajax');
		}
		$stickData = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'type' => 'stick',
			'displayorder' => $displayorder,
			'title' => "置顶No.{$displayorder},{$day}天",
			'status' => 0,
			'addtime' => TIMESTAMP,
			'starttime' => TIMESTAMP,
			'endtime' => TIMESTAMP,
			'is_pay' => 0,
			'order_sn' => date('YmdHis', time()).random(6, true),
			'final_fee' => $finalfee,
			'pay_type' => $pay_type,
			'days' => $day,
			'data' => iserializer(array(
				'displayorder' => $store['displayorder']
			)),
		);
		pdo_insert('tiny_wmall_advertise_trade', $stickData);
		$id = pdo_insertid();
		imessage(error(0, array('id' => $id, 'sid' => $sid)), '', 'ajax');
	}
	include itemplate('advertise/stick');
}
