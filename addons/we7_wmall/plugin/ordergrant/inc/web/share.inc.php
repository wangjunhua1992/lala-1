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
$_W['page']['title'] = '订单分享设置';
if($_W['ispost']) {
	if(intval($_GPC['share_grant_days_limit']) <= 0) {
		imessage(error(-1, '下单后有效分享天数必须大于0'), 'refresh', 'ajax');
	}
	$share = array(
		'title' => trim($_GPC['title']),
		'desc' => trim($_GPC['desc']),
		'imgUrl' => trim($_GPC['imgUrl']),
	);
	$data = array(
		'status' => intval($_GPC['status']),
		'grantType' => trim($_GPC['grantType']),
		'grantType_cn' => '积分',
		'share_grant' => floatval($_GPC['share_grant']),
		'share_grant_max' => floatval($_GPC['share_grant_max']),
		'share_grant_days_limit' => intval($_GPC['share_grant_days_limit']),
		'share' => $share
	);
	if($data['grantType'] == 'credit2') {
		$data['grantType_cn'] = $_W['Lang']['dollarSignCn'];
	}
	set_plugin_config('ordergrant.share', $data);
	imessage(error(0, '分享订单设置成功'), 'refresh', 'ajax');
}
$config_share = get_plugin_config('ordergrant.share');
include itemplate('share');