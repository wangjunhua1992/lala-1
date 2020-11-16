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
$slide_type = trim($_GPC['slide_type']);
$slide_type_info = get_advertise_info($slide_type);
if($slide_type_info['status'] != 1) {
	imessage(error(-1, '该广告暂未开售'), '', 'ajax');
}
$amount = $store['account']['amount'];
$titles = advertise_get_types();
$_W['page']['title'] = "{$titles[$slide_type]['text']}推广";
if($ta == 'index') {
	if($_W['isajax']) {
		$slide_type = trim($_GPC['slide_type']);
		$slide_type_info = get_advertise_info($slide_type);
		if(!$slide_type_info['leave']) {
			imessage(error(-1, '该广告位已售空，请选择其他广告位'), '', 'ajax');
		}
		$day = intval($_GPC['day']);
		if(!$day) {
			imessage(error(-1, '请选择购买天数'), '', 'ajax');
		}
		$pay_type = trim($_GPC['pay_type']);
		if(!$pay_type) {
			imessage(error(-1, '请选择支付方式'), '', 'ajax');
		}
		$finalfee = $slide_type_info['prices'][$day]['fee'];
		if($pay_type == 'credit' && $amount < $finalfee) {
			imessage(error(-1,'余额不足，请选择其他支付方式'), '', 'ajax');
		}
		$update = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'type' => $slide_type,
			'displayorder' => 0,
			'title' => "{$titles[$slide_type]['text']}幻灯片展示{$day}天",
			'status' => 0,
			'addtime' => TIMESTAMP,
			'starttime' => TIMESTAMP,
			'endtime' => TIMESTAMP,
			'final_fee' => $finalfee,
			'pay_type' => $pay_type,
			'days' => $day,
			'is_pay' => 0,
			'order_sn' => date('YmdHis', time()).random(6, true),
			'data' => '',
		);
		pdo_insert('tiny_wmall_advertise_trade', $update);
		$id = pdo_insertid();
		imessage(error(0, array('id' => $id, 'sid' => $sid)), '', 'ajax');
	}
	include itemplate('advertise/slide');
}




