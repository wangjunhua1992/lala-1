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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';

if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$zhunshibao = array(
			'start_time' => trim($_GPC['start_time']),
		);
		set_plugin_config('zhunshibao.basic', $zhunshibao);
		$dataProtocol = $_GPC['protocol'];
		set_config_text('准时宝服务协议', "zhunshibao:agreement", htmlspecialchars_decode($dataProtocol));
		imessage(error(0, '设置成功'), 'refresh', 'ajax');
	}
	$config = $_config_plugin;
	$config = $config['basic'];
	$protocol = get_config_text("zhunshibao:agreement");
}

elseif($op == 'setting') {
	$_W['page']['title'] = '批量设置';
	$stores = store_fetchall(array('id', 'title'));
	if($_W['ispost']) {
		$zhunshibao_GPC = $_GPC['zhunshibao'];
		$zhunshibao = array(
			'status' => intval($zhunshibao_GPC['status']),
			'price_type' => intval($zhunshibao_GPC['price_type']),
			'fee_type' => intval($zhunshibao_GPC['fee_type'])
		);
		if($zhunshibao['price_type'] == 1) {
			$zhunshibao['price'] = floatval($zhunshibao_GPC['price1']);
		} elseif($zhunshibao['price_type'] == 2) {
			$zhunshibao['price'] = floatval($zhunshibao_GPC['price2']);
		}

		if($zhunshibao['fee_type'] == '1') {
			$rule = $_GPC['rule'];
		} elseif($zhunshibao['fee_type'] == '2') {
			$rule = $_GPC['rule_type_2'];
		}
		if(!empty($rule)) {
			foreach($rule['time'] as $key => $val) {
				if(empty($val)) {
					continue;
				}
				$price = $rule['fee'][$key];
				if(empty($price)) {
					continue;
				}
				$zhunshibao['rule'][] = array(
					'time' => intval($val),
					'fee' => floatval($price)
				);
			}
		}
		mload()->model('activity');
		$extra_sync = intval($_GPC['extra_sync']);
		if($extra_sync == 1) {
			foreach($stores as $val) {
				store_set_data($val['id'], 'zhunshibao', $zhunshibao);
				$activity = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $val['id'],
					'title' => '准时宝',
					'type' => 'zhunshibao',
					'status' => $zhunshibao['status'],
				);
				activity_set($val['id'], $activity);
			}
		} elseif($extra_sync == 2) {
			$store_ids = $_GPC['store_ids'];
			foreach($store_ids as $storeid) {
				store_set_data($storeid, 'zhunshibao', $zhunshibao);
				$activity = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $storeid,
					'title' => '准时宝',
					'type' => 'zhunshibao',
					'status' => $zhunshibao['status'],
				);
				activity_set($storeid, $activity);
			}
		}
		set_plugin_config('zhunshibao.setting', $zhunshibao);
		imessage(error(0, '批量配置成功'), ireferer(), 'ajax');
	}
	$setting = $_config_plugin['setting'];
}

include itemplate('config');