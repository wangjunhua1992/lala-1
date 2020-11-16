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
$_W['page']['title'] = '推广海报';
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'post';
if($op == 'post') {
	if($_W['ispost']) {
		$data = base64_encode(json_encode($_GPC['data']));
		$poster = array(
			'data' => $data,
			'bg' => '',
		);
		foreach($_GPC['data']['items'] as $part) {
			if($part['id'] == 'background') {
				$poster['bg'] = $part['params']['imgurl'];
			} elseif($part['id'] == 'qrcode') {
				$poster['qrcode'] = $part;
			}
		}
		set_plugin_config('spread.poster', $poster);
		imessage(error(0, '海报参数保存成功'), iurl('spread/postera'), 'ajax');
	}
	$poster = get_plugin_config('spread.poster');
	if(!empty($poster['data'])) {
		$data = json_decode(base64_decode($poster['data']), true);
	}
}
if($op == 'clear') {
	load()->func('file');
	@rmdirs(MODULE_ROOT . '/resource/poster/qrcode/' . $_W['uniacid']);
	@rmdirs(MODULE_ROOT . '/resource/poster/spread/' . $_W['uniacid']);
	imessage('清除海报缓存成功', iurl('spread/postera'), 'success');
}
include itemplate('postera');