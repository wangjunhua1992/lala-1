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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'setting';

if($op == 'setting') {
	$_W['page']['title'] = '应用设置';
	if($_W['ispost']) {
		$power = array(
			'basic' => array(
				'status' => intval($_GPC['basic']['status']),
				'thumb' => trim($_GPC['basic']['thumb']),
				'pluginname' => array_map("trim", $_GPC['basic']['pluginname'])
			),
			'pay_type' => array(
				'wechat' => array(
					'appid' => trim($_GPC['app']['wechat']['appid']),
					'mchid' => trim($_GPC['app']['wechat']['mchid']),
					'apikey' => trim($_GPC['app']['wechat']['apikey']),
				),
				'alipay' => array(
					'account' => trim($_GPC['app']['alipay']['account']),
					'partner' => trim($_GPC['app']['alipay']['partner']),
					'secret' => trim($_GPC['app']['alipay']['secret']),
				)
			),
			'contact' => array(
				'customer' => trim($_GPC['contact']['customer']),
				'servertime' => trim($_GPC['contact']['servertime'])
			)
		);
		if(!empty($_GPC['app_type'])) {
			foreach($_GPC['app_type'] as $key => $row) {
				if($row == 1) {
					$power['app'][] = $key;
				}
			}
		}
		if(!empty($_GPC['meal'])) {
			$meal = array();
			foreach ($_GPC['meal']['tel'] as $key => $val) {
				if (empty($val)) {
					continue;
				}
				$note = $_GPC['meal']['note'][$key];
				if (empty($note)) {
					continue;
				}
				$meal[] = array(
					'tel' => $val,
					'note' => $note
				);
			}
		}
		$power['contact']['meal'] = $meal;
		set_global_config('plugincenter', $power);
		imessage(error(0, '应用设置设置成功'), ireferer(), 'ajax');
	}
	$plugins = pdo_fetchall('select id,title,name from' . tablename('tiny_wmall_plugin'), array(), 'id');
	$power = get_global_config('plugincenter');
}

include itemplate('system/plugincenter_setting');