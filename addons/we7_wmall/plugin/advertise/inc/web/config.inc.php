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
$config_advertise = get_plugin_config('advertise.basic');
if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$hours = intval($_GPC['notify_before_hours']);
		if($hours < 0 || $hours > 24) {
			imessage(error(-1, '请设置为0-24之间的整数'), '', 'ajax');
		}
		$data = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 0,
			'notify_before_hours' => $hours,
		);
		set_plugin_config('advertise.basic', $data);
		imessage(error(0, '设置推广成功'), 'refresh', 'ajax');
	}
	include itemplate('config');
}
if($op == 'payment') {
	$_W['page']['title'] = '支付设置';
	if($_W['ispost']) {
		$payment = array(
			'alipay' => array(
				'status' => intval($_GPC['alipay']['status']),
				'account' => trim($_GPC['alipay']['account']),
				'partner' => trim($_GPC['alipay']['partner']),
				'secret' => trim($_GPC['alipay']['secret']),
			),
			'wechat' => array(),
			'credit' => array(),
		);
		set_plugin_config('advertise.payment', $payment);
		imessage(error(0, '设置推广成功'), 'refresh', 'ajax');
	}
	$config_payment = get_plugin_config('advertise.payment');
	include itemplate('payment');
}



/*if($op == 'pay') {
	$_W['page']['title'] = '支付页推广设置';
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 0,
			'num' => intval($_GPC['num'])
		);
		foreach($_GPC['days'] as $key => $day) {
			if(!empty($day)) {
				$data['prices'][] = array(
					'day' => $day,
					'fee' =>  $_GPC['fees'][$key],
				);
			}
		}
		set_plugin_config('advertise.pay', $data);
		imessage(error(0, '设置支付页推广成功'), 'refresh', 'ajax');
	}
	$config_pay = $config_advertise['pay'];
	include itemplate('pay');
}
if($op == 'complete') {
	$_W['page']['title'] = '订单完成页推广设置';
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 0,
			'num' => intval($_GPC['num'])
		);
		foreach($_GPC['days'] as $key => $day) {
			if(!empty($day)) {
				$data['prices'][] = array(
					'day' => $day,
					'fee' =>  $_GPC['fees'][$key],
				);
			}
		}
		set_plugin_config('advertise.complete', $data);
		imessage(error(0, '设置订单完成页推广成功'), 'refresh', 'ajax');
	}
	$config_complete = $config_advertise['complete'];
	include itemplate('complete');
}
if($op == 'top') {
	$_W['page']['title'] = '顶部推广设置';
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 0,
			'num' => intval($_GPC['num'])
		);
		foreach($_GPC['days'] as $key => $day) {
			$day = intval($day);
			$fee = floatval($_GPC['fees'][$key]);
			if(!empty($day)) {
				$data['prices'][] = array(
					'day' => $day,
					'fee' => $fee,
				);
			}
		}
		set_plugin_config('advertise.top', $data);
		imessage(error(0, '设置首页顶部推广成功'), 'refresh', 'ajax');
	}
	$config_top = $config_advertise['top'];
	include itemplate('top');
}*/