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
	$_W['page']['title'] = '换购设置';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));
	if($_W['ispost']) {
		$huangou = array(
			'status' => intval($_GPC['status']),
		);
		$extra_sync = intval($_GPC['extra_sync']);
		if($extra_sync == 1) {
			foreach($stores as $val) {
				store_set_data($val['id'], 'huangou', $huangou);
			}
		} elseif($extra_sync == 2) {
			$store_ids = $_GPC['store_ids'];
			foreach($store_ids as $storeid) {
				store_set_data($storeid, 'huangou', $huangou);
			}
		}
		set_plugin_config('huangou', $huangou);
		imessage(error(0, '设置成功'), ireferer(), 'ajax');
	}
	$config = $_config_plugin;
}
include itemplate('config');