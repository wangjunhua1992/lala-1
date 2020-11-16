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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'yinsihao';
if($op == 'yinsihao') {
	$order_id = intval($_GPC['order_id']);
	$ordersn = trim($_GPC['ordersn']);
	$type = trim($_GPC['type']);
	$orderType = trim($_GPC['orderType']) ? trim($_GPC['orderType']) : 'waimai';
	$memberType = trim($_GPC['memberType']) ? trim($_GPC['memberType']) : 'accept';
	$config_basic = $_W['_plugin']['config']['basic'];
	if($type == 'store') {
		if(empty($config_basic['member_call_store_status'])) {
			imessage(error(-1000, ''), '', 'ajax');
		}
	}
	if($type == 'deliveryer') {
		if(empty($config_basic['member_call_deliveryer_status'])) {
			imessage(error(-1000, ''), '', 'ajax');
		}
	}
	$data = yinsihao_bind($order_id, $type, $ordersn, $orderType, $memberType);
	$type_cn = array(
		'store' => '商家',
		'deliveryer' => '配送员',
		'member' => '顾客'
	);
	if(is_error($data)) {
		slog('yinsihao', '隐私号绑定错误', array('order_id' => $order_id), "生成{$type_cn[$type]}隐私号错误: {$data['message']}");
		imessage($data, '', 'ajax');
	}
	$result = array(
		'data' => $data
	);
	imessage(error(0, $result), '', 'ajax');
}