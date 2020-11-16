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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$condition = ' uniacid = :uniacid and status = 4';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)){
		$condition .= ' AND (id = :id or title like :keyword)';
		$params[':id'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT id,logo,title,address,telephone,agentid,label,deltime FROM ' . tablename('tiny_wmall_store') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT ' . ($page - 1) * $psize.','.$psize, $params);
	if (!empty($records)){
		foreach($records as &$val){
			$val['deltime_cn'] = date('Y-m-d H:i', $val['deltime']);
			$val['logo'] = tomedia($val['logo']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}elseif($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$tables = array(
		'tiny_wmall_activity_bargain',
		'tiny_wmall_activity_bargain_goods',
		'tiny_wmall_activity_coupon',
		'tiny_wmall_activity_coupon_grant_log',
		'tiny_wmall_activity_coupon_record',
		'tiny_wmall_assign_board',
		'tiny_wmall_assign_queue',
		'tiny_wmall_clerk',
		'tiny_wmall_goods',
		'tiny_wmall_goods_category',
		'tiny_wmall_goods_options',
		'tiny_wmall_order_cart',
		'tiny_wmall_order_stat',
		'tiny_wmall_printer',
		'tiny_wmall_printer_label',
		'tiny_wmall_reply',
		'tiny_wmall_report',
		'tiny_wmall_reserve',
		'tiny_wmall_sms_send_log',
		'tiny_wmall_store_account',
		'tiny_wmall_store_activity',
		'tiny_wmall_store_clerk',
		'tiny_wmall_store_current_log',
		'tiny_wmall_store_deliveryer',
		'tiny_wmall_store_favorite',
		'tiny_wmall_store_getcash_log',
		'tiny_wmall_store_members',
		'tiny_wmall_tables',
		'tiny_wmall_tables_category',
		'tiny_wmall_tables_scan',
	);
	foreach($tables as $table) {
		if(pdo_tableexists($table) && pdo_fieldexists($table, 'sid')) {
			pdo_delete($table, array('uniacid' => $_W['uniacid'], 'sid' => $id));
		}
	}
	imessage(error(0, ''), '', 'ajax');
}elseif($ta == 'restore') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_store', array('status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '恢复门店成功'), '', 'ajax');
}
