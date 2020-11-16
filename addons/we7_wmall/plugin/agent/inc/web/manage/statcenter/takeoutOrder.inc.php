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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '店铺订单统计统计';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid']), array('id', 'title'), 'id');

	$condition = ' WHERE uniacid = :uniacid and agentid = :agentid and order_type <= 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'final_fee';
	$plateform = pdo_fetch('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee FROM ' . tablename('tiny_wmall_order') . $condition . ' and status = 5 and is_pay = 1', $params);
	$plateform['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition . ' and status = 6', $params);
	$records = pdo_fetchall('SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, sid FROM ' . tablename('tiny_wmall_order') . $condition . " and status = 5 and is_pay = 1 group by sid order by {$orderby} desc", $params);
	$records_cancel = pdo_fetchall('select count(*) as total_cancel_order, sid from ' . tablename('tiny_wmall_order') . $condition .  " and status = 6 group by sid order by total_cancel_order desc", $params, 'sid');

	if(!empty($records)){
		foreach ($records as &$row) {
			$row['total_success_order'] = $row['total_success_order'] ? $row['total_success_order'] : 0;
			$row['store_final_fee'] = $row['store_final_fee'] ? $row['store_final_fee'] : 0;
			$row['final_fee'] = $row['final_fee'] ? $row['final_fee'] : 0;
			$row['total_cancel_order'] = $records_cancel[$row['sid']]['total_cancel_order'] ? $records_cancel[$row['sid']]['total_cancel_order'] : 0;
			$row['pre_final_fee'] = round($row['final_fee'] / $plateform['final_fee'], 4) * 100;
			$row['pre_success_order'] = round($row['total_success_order'] / $plateform['total_success_order'], 4) * 100;
			$row['pre_store_final_fee'] = round($row['store_final_fee'] / $plateform['store_final_fee'], 4) * 100;
			$row['pre_cancel_order'] = round($row['total_cancel_order'] / $plateform['total_cancel_order'], 4) * 100;
			$row['store_name'] = $stores[$row['sid']]['title'];
		}
	}
	if($_W['isajax']){
		$stat = array();
		$stat['final_fee'] = $plateform['final_fee'];
		$stat['total_success_order'] = $plateform['total_success_order'];
		$stat['store_final_fee'] = $plateform['store_final_fee'];
		$stat['total_cancel_order'] = $plateform['total_cancel_order'];
		if($orderby == 'total_success_order'){
			$title = '有效订单量';
		} elseif($orderby == 'final_fee') {
			$title = '营业额';
		} elseif($orderby == 'store_final_fee') {
			$title = '总收入';
		}
		$stat['title'] = $title;
		$i = 0;
		foreach ($records as $value) {
			if($i == 10){
				break;
			}
			$stat['sid'][] = $value['store_name'];
			if($orderby == 'total_success_order'){
				$stat['value'][] = $value['total_success_order'];
			} elseif($orderby == 'final_fee') {
				$stat['value'][] = $value['final_fee'];
			} elseif($orderby == 'store_final_fee') {
				$stat['value'][] = $value['store_final_fee'];
			}
			$i++;
		} 
		message(error(0, $stat), '', 'ajax');
	}
}
include itemplate('statcenter/takeoutOrder');