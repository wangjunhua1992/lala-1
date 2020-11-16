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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'newAndOld';

if($ta == 'newAndOld') {
	$_W['page']['title'] = '新老顾客统计';

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
	}
	$stat = array(
		'total_success' => 0,
		'total_success_new' => 0,
		'total_success_old' => 0,
		'percent_new' => 0,
		'percent_old' => 0,
	);

	$condition_new = ' where uniacid = :uniacid and sid = :sid and stat_first_day >= :starttime and stat_first_day <= :endtime group by stat_first_day';
	$params_new = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	//新客人数
	$success_new = pdo_fetchall('select count(*) as num, stat_first_day as stat_day from ' . tablename('tiny_wmall_store_members') . $condition_new, $params_new, 'stat_day');
	if(!empty($success_new)) {
		$stat['total_success_new'] = array_sum(array_column($success_new, 'num'));
	}

	$condition_order = ' where uniacid = :uniacid and sid = :sid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day';
	$params_order = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':status' => 5,
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	//成交顾客数
	$success_total = pdo_fetchall('select count(distinct uid) as num, stat_day from ' . tablename('tiny_wmall_order') . $condition_order, $params_order, 'stat_day');
	if(!empty($success_total)) {
		$stat['total_success'] = array_sum(array_column($success_total, 'num'));
	}

	$stat['total_success_old'] = $stat['total_success'] - $stat['total_success_new'];
	$stat['percent_new'] = round($stat['total_success_new'] / $stat['total_success'] * 100, 2);
	$stat['percent_old'] = round($stat['total_success_old'] / $stat['total_success'] * 100, 2);

	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		$total_success = intval($success_total[$i]['num']);
		$total_success_new = intval($success_new[$i]['num']);
		$total_success_old = $total_success - $total_success_new;
		$records[$i] = array(
			'stat_day' => $i,
			'total_success' => $total_success,
			'total_success_new' => $total_success_new,
			'total_success_old' => $total_success_old,
			'percent_new' => round($total_success_new / $total_success * 100, 2),
			'percent_old' => round($total_success_old / $total_success * 100, 2),
		);
		$i = date('Ymd', strtotime($i) - 86400);
	}
	if($_W['isajax']) {
		$chart = array(
			'stat' => $stat,
			'fields' => array('total_success', 'total_success_new', 'total_success_old'),
			'titles' => array('成交顾客数','新客人数','老客人数'),
			'days' => array_keys($records)
		);
		if(!empty($records)) {
			foreach($records as $record) {
				foreach($chart['fields'] as $field) {
					$chart[$field][$record['stat_day']] += $record[$field];
				}
			}
		}
		foreach($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}
		message(error(0, $chart), '', 'ajax');
	}

	include itemplate('store/statistic/newAndOld');
}

elseif($ta == 'keep') {
	$_W['page']['title'] = '顾客留存统计';

	$stat = array(
		'active' => 0,
		'silence' => 0,
		'runoff' => 0,
		'giveup' => 0
	);
	$time30 = TIMESTAMP - 30 * 24 * 3600;
	$time60 = TIMESTAMP - 60 * 24 * 3600;
	$time90 = TIMESTAMP - 90 * 24 * 3600;
	$conditionAndParams = array(
		'active' => array(
			'condition' => ' where uniacid = :uniacid and sid = :sid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':starttime' => $time30,
				':endtime' => TIMESTAMP
			)
		),
		'silence' => array(
			'condition' => ' where uniacid = :uniacid and sid = :sid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':starttime' => $time60,
				':endtime' => $time30
			)
		),
		'runoff' => array(
			'condition' => ' where uniacid = :uniacid and sid = :sid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':starttime' => $time90,
				':endtime' => $time60
			)
		),
		'giveup' => array(
			'condition' => ' where uniacid = :uniacid and sid = :sid and success_last_time > :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':starttime' => 0,
				':endtime' => $time90
			)
		)
	);

	$total = 0;
	foreach($stat as $key => &$value) {
		$value = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . $conditionAndParams[$key]['condition'], $conditionAndParams[$key]['params']);
		$value = intval($value);
		$total += $value;
	}
	$stat['percent_active'] = round($stat['active'] / $total * 100, 2);
	$stat['percent_silence'] = round($stat['silence'] / $total * 100, 2);
	$stat['percent_runoff'] = round($stat['runoff'] / $total * 100, 2);
	$stat['percent_giveup'] = round($stat['giveup'] / $total * 100, 2);
	if($_W['isajax']) {
		$titles = array(
			'active' => '活跃顾客人数占比',
			'silence' => '沉默顾客人数占比',
			'runoff' => '流失顾客人数占比',
			'giveup' => '放弃顾客人数占比'
		);
		$legendData = array();
		foreach($titles as $key => $val) {
			$legendData[$key] = array(
				'value' => $stat['percent_' . $key],
				'name' => $val
			);
		}
		$chart = array(
			'titles' => array_values($titles),
			'legendData' => array_values($legendData)
		);
		message(error(0, $chart), '', 'ajax');
	}
	include itemplate('store/statistic/keep');
}

elseif($ta == 'scanAnalysis') {
	$_W['page']['title'] = '客流分析';

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
	}
	$total_scan = pdo_fetchall('select nums, stat_day from ' . tablename('tiny_wmall_member_scan_record') . ' where uniacid = :uniacid and sid = :sid and stat_day >= :starttime and stat_day <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $starttime, ':endtime' => $endtime), 'stat_day');
	$total = array_sum(array_column($total_scan, 'nums'));

	$condition_order = ' where uniacid = :uniacid and sid = :sid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day ';
	$params_order = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':status' => 5,
		':starttime' => $starttime,
		':endtime' => $endtime
	);

	$total_success = pdo_fetchall('select count(distinct uid) as nums, stat_day from ' . tablename('tiny_wmall_order') . $condition_order, $params_order, 'stat_day');
	$success = array_sum(array_column($total_success, 'nums'));
	if($total < $success) {
		$total = $success;
	}
	$percent_success = $total > 0 ? round($success / $total * 100, 2) : 0;

	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		$total_success_nums = intval($total_success[$i]['nums']);
		$total_scan_nums = intval($total_scan[$i]['nums']);
		if($total_scan_nums < $total_success_nums) {
			$total_scan_nums = $total_success_nums;
		}
		$records[$i] = array(
			'stat_day' => $i,
			'total_scan' => $total_scan_nums,
			'total_success' => $total_success_nums,
			'percent_success' => $total_scan_nums > 0 ? round($total_success_nums / $total_scan_nums * 100, 2) : 0.00
		);
		$i = date('Ymd', strtotime($i) - 86400);
	}
	if($_W['isajax']) {
		$chart = array(
			'fields' => array('total_scan', 'total_success'),
			'titles' => array('浏览顾客数','成交顾客数'),
			'days' => array_keys($records)
		);
		if(!empty($records)) {
			foreach($records as $record) {
				foreach($chart['fields'] as $field) {
					$chart[$field][$record['stat_day']] += $record[$field];
				}
			}
		}
		foreach($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}
		message(error(0, $chart), '', 'ajax');
	}
	include itemplate('store/statistic/scanAnalysis');
}
