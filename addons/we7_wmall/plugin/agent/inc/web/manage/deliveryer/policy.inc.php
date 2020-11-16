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

if($op == 'index') {
	$_W['page']['title'] = '配送员配送策略';
	if($_W['ispost']) {
		$form_type = trim($_GPC['form_type']);
		if($form_type == 'deliveryer') {
			$special_deliveryer = array(
				'fee_takeout' => array(
					'status' => intval($_GPC['fee_takeout']['status']),
					'type' => intval($_GPC['fee_takeout']['type']),
					'fee' => floatval($_GPC['fee_takeout']['fee']),
					'rate' => floatval($_GPC['fee_takeout']['rate'])
				)
			);
			if(check_plugin_perm('errander')) {
				$special_deliveryer['fee_errander'] = array(
					'status' => intval($_GPC['fee_errander']['status']),
					'type' => intval($_GPC['fee_errander']['type']),
					'fee' => floatval($_GPC['fee_errander']['fee']),
					'rate' => floatval($_GPC['fee_errander']['rate'])
				);
			}
			set_agent_system_config('takeout.special.deliveryer', $special_deliveryer);
			imessage(error(0, '一键增加配送员提成设置成功'), ireferer(), 'ajax');
		}
	}
	$special = get_agent_system_config('takeout.special');
	include itemplate('deliveryer/policy');
}
