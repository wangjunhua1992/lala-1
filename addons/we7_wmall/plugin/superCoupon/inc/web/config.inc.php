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

if($op == 'index') {
	$_W['page']['title'] = '超级代金券';
	if ($_W['ispost']) {
		if(!empty($_GPC['time_end'])) {
			$data = array();
			if(!empty($_GPC['time_start'])) {
				$time_cn = '';
				foreach($_GPC['time_start'] as $k => $start) {
					$start = trim($start);
					$end = trim($_GPC['time_end'][$k]);
					if(empty($start) || empty($end)) {
						continue;
					}
					$start = date('H:i', strtotime($start));
					$end = date('H:i', strtotime($end));
					$time_cn .= "{$start}~{$end} ";
					$data[] = array('start' => $start, 'end' => $end);
				}
			}
		}
		$dataConfig = array(
			'timelimit' => array('status' => intval($_GPC['timelimit_status']), 'time' => $data, 'time_cn' => $time_cn),
			'store_coupon_max' => intval($_GPC['store_coupon_max'])
		);
		set_plugin_config('superCoupon', $dataConfig);
		$superCoupon_agreement = trim($_GPC['superCoupon_agreement']);
		set_config_text('商家自营销协议', 'superCoupon_agreement', htmlspecialchars_decode($superCoupon_agreement));
		imessage(error(0, '设置代金券活动成功'), 'refresh', 'ajax');
	}
	$superCoupon_agreement = get_config_text('superCoupon_agreement');
	$limit = get_plugin_config('superCoupon');
	$time_limit = $limit['timelimit']['time'];
}
include itemplate('config');