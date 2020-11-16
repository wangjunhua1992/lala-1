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
icheckauth(true);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if($op == 'list') {
	$filter = $_GPC;
	$filter['uid'] = $_W['member']['uid'];
	$data = gohome_order_fetchall($filter);
	$result = array(
		'records' => $data['orders']
	);
	$_W['_nav'] = 1;
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = gohome_order_fetch($id, true);
	$qrcode = isurl('pages/gohome/order/detail', array('id' => $order['id'], 'code' => $order['code']), true);
	$result = array(
		'order' => $order,
		'qrcode' => $qrcode
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'cancel') {
	$id = intval($_GPC['id']);
	$result = gohome_order_update($id, 'cancel');
	imessage($result, '', 'ajax');
}

