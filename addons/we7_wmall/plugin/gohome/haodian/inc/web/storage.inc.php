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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '商户回收站';
	$filter = array(
		'haodian_status' => 7,
	);
	$store_data = haodian_store_fetchall($filter);
	$stores = $store_data['store'];
	$pager = $store_data['pager'];
	include itemplate('storage');
}

elseif($op == 'restore') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_store', array('haodian_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '恢复门店成功'), '', 'ajax');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($store['is_waimai'] == 1) {
		imessage(error(-1, '该好店开启了外卖功能，无法彻底删除'), '', 'ajax');
	}
	pdo_delete('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除好店成功'), '', 'ajax');
}