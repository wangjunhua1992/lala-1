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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	$wxapp = get_plugin_config('wxapp.basic');
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'default_sid' => intval($_GPC['default_sid']),
			'key' => trim($_GPC['key']),
			'secret' => trim($_GPC['secret']),
			'wxapp_consumer_notice_channel' => trim($_GPC['wxapp_consumer_notice_channel']),
			'store_url' => trim($_GPC['store_url']),
			'tpl_consumer_url' => trim($_GPC['tpl_consumer_url']),
		);
		if(!empty($wxapp['release_version'])) {
			$data['release_version'] = $wxapp['release_version'];
		}
		set_plugin_config('wxapp.basic', $data);
		imessage(error(0, '基础设置成功'), 'refresh', 'ajax');
	}
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']));
	include itemplate('config/basic');
}

if($op == 'payment') {
	$_W['page']['title'] = '支付方式';
	if($_W['ispost']) {
		load()->func('file');
		$config_old_payment = get_plugin_config('wxapp.payment');
		$config_payment = array(
			'wechat' => array(
				'type' => trim($_GPC['wechat']['type']) ? trim($_GPC['wechat']['type']) : 'default',
				'default' => array(
					'appid' => trim($_GPC['wxapp']['wechat']['appid']),
					'appsecret' => trim($_GPC['wxapp']['wechat']['appsecret']),
					'mchid' => trim($_GPC['wxapp']['wechat']['mchid']),
					'apikey' => trim($_GPC['wxapp']['wechat']['apikey']),
					'apiclient_cert' => $config_old_payment['wechat']['default']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['default']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['default']['rootca'],
				),
				'partner' => array(
					'appid' => trim($_GPC['wxapp']['partner']['appid']),
					'appsecret' => trim($_GPC['wxapp']['partner']['appsecret']),
					'sub_appid' => trim($_GPC['wxapp']['partner']['sub_appid']),
					'mchid' => trim($_GPC['wxapp']['partner']['mchid']),
					'sub_mch_id' => trim($_GPC['wxapp']['partner']['sub_mch_id']),
					'apikey' => trim($_GPC['wxapp']['partner']['apikey']),
					'apiclient_cert' => $config_old_payment['wechat']['partner']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['partner']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['partner']['rootca'],
				),
			),
			'wxapp' => array(),
		);
		if(!empty($_GPC['wxapp_type'])) {
			foreach($_GPC['wxapp_type'] as $key => $row) {
				if($row == 1) {
					$config_payment['wxapp'][] = $key;
				}
			}
		}
		$keys = array('apiclient_cert', 'apiclient_key', 'rootca');
		foreach($keys as $key) {
			if(!empty($_GPC['wxapp']['wechat'][$key]) || !empty($_GPC['wxapp']['partner'][$key])) {
				$text = trim($_GPC['wxapp']['wechat'][$key]) ? trim($_GPC['wxapp']['wechat'][$key]) : trim($_GPC['wxapp']['partner'][$key]);
				@unlink(MODULE_ROOT . "/cert/{$config_payment['wechat'][$config_payment['wechat']['type']][$key]}/{$key}.pem");
				@rmdir(MODULE_ROOT . "/cert/{$config_payment['wechat'][$config_payment['wechat']['type']][$key]}");
				$name = random(10);
				$status = ifile_put_contents("cert/{$name}/{$key}.pem", $text);
				$config_payment['wechat'][$config_payment['wechat']['type']][$key] = $name;
			}
		}
		set_plugin_config('wxapp.payment', $config_payment);
		imessage(error(0, '支付方式设置成功'), ireferer(), 'ajax');
	}
	$payment = get_plugin_config('wxapp.payment');
	include itemplate('config/payment');
}

if($op == 'wxtemplate') {
	$_W['page']['title'] = '微信模板消息';
	if($_W['ispost']) {
		$wxtemplate = array(
			'order' => array_map('trim', $_GPC['order']),
			'tiezi' => array_map('trim', $_GPC['tiezi']),
		);
		set_plugin_config('wxapp.wxtemplate', $wxtemplate);
		imessage(error(0, '微信模板消息设置成功'), 'refresh', 'ajax');
	}
	$wxtemplate = get_plugin_config('wxapp.wxtemplate');
	include itemplate('config/wxtemplate');
}

//删除原有证书
if($op == 'del_cert') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'default';
	load()->func('file');
	$config_payment = get_plugin_config('wxapp.payment');
	$keys = array('apiclient_cert', 'apiclient_key', 'rootca');
	foreach($keys as $key) {
		@unlink(MODULE_ROOT . "/cert/{$config_payment['wechat'][$type][$key]}/{$key}.pem");
		@rmdir(MODULE_ROOT . "/cert/{$config_payment['wechat'][$type][$key]}");
		$config_payment['wechat'][$type][$key] = '';
	}
	set_plugin_config('wxapp.payment', $config_payment);
	imessage(error(0, '证书已删除，请上传新证书！'), ireferer(), 'ajax');
}
