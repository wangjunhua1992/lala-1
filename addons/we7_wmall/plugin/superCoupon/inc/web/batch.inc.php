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

if ($op == 'index') {
	$_W['page']['title'] = '设置超级代金券';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));
	if ($_W['ispost']) {
		$datasync = array(
			'status' => intval($_GPC['superCoupon']['status']),
			'max_limit' => intval($_GPC['superCoupon']['max_limit']),
		);
		$sync = intval($_GPC['sync']);
		if ($sync == 1) {
			foreach ($stores as $key => $value) {
				store_set_data($value['id'], 'superCoupon', $datasync);
			}
		} elseif ($sync == 2) {
			$store_ids = $_GPC['store_ids'];
			foreach ($store_ids as $key => $value) {
				store_set_data($value, 'superCoupon', $datasync);
			}	
		}
		set_plugin_config('superCoupon.sync', $datasync);
		imessage(error(0, '超级代金券同步成功'), iurl('superCoupon/batch'), 'ajax');
	}
	$datasync = get_plugin_config('superCoupon.sync');
}
include itemplate('batch');