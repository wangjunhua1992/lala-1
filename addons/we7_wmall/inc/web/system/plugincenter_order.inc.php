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
mload()->model('plugincenter');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '订单列表';
	$plugins = pdo_fetchall('select id, title, name from ' . tablename('tiny_wmall_plugin') . ' where 1', array());
	if(!empty($_GPC['addtime']['start']) && !empty($_GPC['addtime']['end'])) {
		$_GPC['starttime'] = strtotime($_GPC['addtime']['start']);
		$_GPC['endtime'] = strtotime($_GPC['addtime']['end']);
	}
	$data = getall_plugincenter_order();
	$orders = $data['orders'];
	$pager = $data['pager'];
}

include itemplate('system/plugincenter_order');