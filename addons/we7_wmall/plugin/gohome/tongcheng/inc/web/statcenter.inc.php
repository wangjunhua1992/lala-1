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
	$_W['page']['title'] = '帖子统计';
	$condition = ' WHERE uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
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
	if($_W['isajax']) {
		$stat = array();
		$stat['total_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(final_fee), 2) from ' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1', $params));
		$stat['total_tiezi_price_fee'] = floatval(pdo_fetchcolumn('select round(sum(price), 2) from ' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1', $params));
		$stat['total_tiezi_stick_fee'] = floatval(pdo_fetchcolumn('select round(sum(stick_price), 2) from ' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1', $params));

		$chart = array(
			'stat' => $stat,
			'fields' => array('total_success_order', 'final_fee', 'tiezi_price', 'tiezi_stick_price'),
			'titles' => array('有效帖子数', '营业总额', '发帖收入', '帖子置顶收入'),
		);
		for($i = $starttime; $i <= $endtime;) {
			$chart['days'][] = $i;
			foreach($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}
			$i = date('Ymd', strtotime($i) + 86400);
		}
		$records = pdo_fetchall('SELECT stat_day, round(sum(final_fee), 2) as final_fee, round(sum(price), 2) as tiezi_price, round(sum(stick_price), 2) as tiezi_stick_price
		FROM ' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1 group by stat_day', $params);
		if(!empty($records)) {
			foreach($records as &$record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}

		$total_records = pdo_fetchall('SELECT stat_day, count(*) as total_success_order FROM' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1 and (type = 0 or type = 1) group by stat_day', $params, 'stat_day');
		if(!empty($total_records)) {
			foreach($total_records as $val) {
				if(in_array($val['stat_day'], $chart['days'])) {
					$chart['total_success_order'][$val['stat_day']] += $val['total_success_order'];
				}
			}
		}
		foreach($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}
		imessage(error(0, $chart), '', 'ajax');
	}

	$records_temp = pdo_fetchall('SELECT stat_day, round(sum(final_fee), 2) as final_fee, round(sum(price), 2) as tiezi_price, round(sum(stick_price), 2) as tiezi_stick_price
	 FROM ' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1 group by stat_day', $params, 'stat_day');
	$total_records = pdo_fetchall('SELECT stat_day, count(*) as total_success_order FROM' . tablename('tiny_wmall_tongcheng_order') . $condition . ' and is_pay = 1 and (type = 0 or type = 1) group by stat_day', $params, 'stat_day');
	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		if(empty($records_temp[$i])) {
			$records[] = array(
				'stat_day' => $i,
				'total_success_order' => 0,
				'final_fee' => 0,
				'tiezi_price' => 0,
				'tiezi_stick_price' => 0,
			);
		} else {
			$records[] = $records_temp[$i];
		}
		$i = date('Ymd', strtotime($i) - 86400);
	}
}

include itemplate('statcenter');