<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$config = $_config_plugin['relate'];
if($op == 'index') {
	$_W['page']['title'] = '推广员等级';
	$groups = pdo_fetchall('select * from ' . tablename('tiny_wmall_spread_groups') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(!empty($groups)) {
		foreach($groups as &$val) {
			$val['data'] = iunserializer($val['data']);
		}
	}
}
if($op == 'post') {
	$_W['page']['title'] = '编辑推广员等级';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$ad = pdo_get('tiny_wmall_spread_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$ad['data'] = iunserializer($ad['data']);
		$paotui = $ad['data']['paotui'];
		$gohome = $ad['data']['gohome'];
	}
	if($_W['ispost']) {
		$commission_type = trim($_GPC['commission_type']);
		if(empty($commission_type)) {
			imessage(error(-1, '请选择推广佣金计算方式'), '', 'ajax');
		}
		if($commission_type == 'ratio') {
			$commission1 =  floatval($_GPC['commission1_ratio']);
			$commission2 =  floatval($_GPC['commission2_ratio']);
		} elseif($commission_type == 'fixed') {
			$commission1 =  floatval($_GPC['commission1_fixed']);
			$commission2 =  floatval($_GPC['commission2_fixed']);
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'commission_type' => $commission_type,
			'title' => trim($_GPC['title']),
			'commission1' => $commission1,
			'commission2' => $commission2,
			'become_child_limit' => intval($_GPC['group_become_child_limit']),
			'valid_period' => trim($_GPC['valid_period']),
			'admin_update_rules' => trim($_GPC['admin_update_rules']),
			'group_condition' => floatval($_GPC['group_condition']),
		);
		$takeout_first_order_return_type = trim($_GPC['takeout_first_order_return_type']);
		$data['data']['takeout'] = array(
			'first_order_return_type' => $takeout_first_order_return_type,
			'first_order_return_bill1' => floatval($_GPC["takeout_first_order_return_bill1_{$takeout_first_order_return_type}"]),
			'first_order_return_bill2' => floatval($_GPC["takeout_first_order_return_bill2_{$takeout_first_order_return_type}"]),
		);
		if(check_plugin_perm('errander')) {
			$paotui_commission_type = trim($_GPC['paotui_commission_type']);
			$paotui_first_order_return_type = trim($_GPC['paotui_first_order_return_type']);
			$data['data']['paotui'] = array(
				'commission_type' => $paotui_commission_type,
				'commission1' => floatval($_GPC["paotui_commission1_{$paotui_commission_type}"]),
				'commission2' => floatval($_GPC["paotui_commission2_{$paotui_commission_type}"]),
				'first_order_return_type' => $paotui_first_order_return_type,
				'first_order_return_bill1' => floatval($_GPC["paotui_first_order_return_bill1_{$paotui_first_order_return_type}"]),
				'first_order_return_bill2' => floatval($_GPC["paotui_first_order_return_bill2_{$paotui_first_order_return_type}"]),
			);
		}
		if(check_plugin_perm('gohome')) {
			$gohome_commission_type = trim($_GPC['gohome_commission_type']);
			$gohome_first_order_return_type = trim($_GPC['gohome_first_order_return_type']);
			$data['data']['gohome'] = array(
				'commission_type' => $gohome_commission_type,
				'commission1' => floatval($_GPC["gohome_commission1_{$gohome_commission_type}"]),
				'commission2' => floatval($_GPC["gohome_commission2_{$gohome_commission_type}"]),
				'first_order_return_type' => $gohome_first_order_return_type,
				'first_order_return_bill1' => floatval($_GPC["gohome_first_order_return_bill1_{$gohome_first_order_return_type}"]),
				'first_order_return_bill2' => floatval($_GPC["gohome_first_order_return_bill2_{$gohome_first_order_return_type}"]),
			);
		}
		$data['data'] = iserializer($data['data']);
		if(!empty($ad['id'])) {
			pdo_update('tiny_wmall_spread_groups', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_spread_groups', $data);
		}
		imessage(error(0, '更新推广员等级成功'), iurl('spread/groups/index'), 'ajax');
	}
}
if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_spread_groups', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除推广员等级成功'), iurl('spread/groups/index'), 'ajax');
}
include itemplate('groups');