<?php
/**
 * 外送系�
 * @author 灯火阑珊
 * @QQ 570602783
 * 5.G��.ÿ���.������.Դ.��
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	$wxapp = get_plugin_config('wxapp.deliveryer');
	if(empty($wxapp)) {
		$wxapp = array();
	}
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'key' => trim($_GPC['key']),
			'secret' => trim($_GPC['secret']),
			'wxapp_deliveryer_notice_channel' => trim($_GPC['wxapp_deliveryer_notice_channel']),
			'tpl_deliveryer_url' => trim($_GPC['tpl_deliveryer_url']),
			'test' => array(
				'username' => trim($_GPC['test']['username']),
				'password' => trim($_GPC['test']['password']),
			),
		);
		$data = array_merge($wxapp, $data);
		set_plugin_config('wxapp.deliveryer', $data);
		imessage(error(0, '基础设置成功'), 'refresh', 'ajax');
	}
	include itemplate('config/deliveryer');
}

elseif($op == 'urls') {
	include itemplate('config/deliveryer');
}

elseif ($op == 'wxtemplate') {
	$_W['page']['title'] = '微信模板消息';
	if($_W['ispost']) {
		$wx_template = $_GPC['wechat'];
		set_plugin_config('wxapp.deliveryer.wxtemplate', $wx_template);
		imessage(error(0, '微信模板消息设置成功'), 'refresh', 'ajax');
	}
	$wechat = get_plugin_config('wxapp.deliveryer.wxtemplate');
	include itemplate('config/deliveryer');
}
