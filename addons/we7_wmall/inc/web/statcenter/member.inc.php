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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'newAndOld';

if($op == 'newAndOld') {
	$_W['page']['title'] = '新老顾客统计';

	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));
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

	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$table_new = 'tiny_wmall_store_members';
		$condition_new = ' where uniacid = :uniacid and sid = :sid and stat_first_day >= :starttime and stat_first_day <= :endtime group by stat_first_day';
		$params_new = array(
			':uniacid' => $_W['uniacid'],
			':sid' => $sid,
			':starttime' => $starttime,
			':endtime' => $endtime
		);
		$condition_order = ' where uniacid = :uniacid and sid = :sid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day';
		$params_order = array(
			':uniacid' => $_W['uniacid'],
			':sid' => $sid,
			':status' => 5,
			':starttime' => $starttime,
			':endtime' => $endtime
		);
	} else {
		$table_new = 'tiny_wmall_members';
		$condition_new = ' where uniacid = :uniacid and stat_first_day >= :starttime and stat_first_day <= :endtime group by stat_first_day';
		$params_new = array(
			':uniacid' => $_W['uniacid'],
			':starttime' => $starttime,
			':endtime' => $endtime
		);
		$condition_order = ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day';
		$params_order = array(
			':uniacid' => $_W['uniacid'],
			':status' => 5,
			':starttime' => $starttime,
			':endtime' => $endtime
		);
	}
	//新客人数
	$success_new = pdo_fetchall('select count(*) as num, stat_first_day as stat_day from ' . tablename($table_new) . $condition_new, $params_new, 'stat_day');
	if(!empty($success_new)) {
		$stat['total_success_new'] = array_sum(array_column($success_new, 'num'));
	}

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

	include itemplate('statcenter/newAndOld');
}

elseif($op == 'keep') {
	$_W['page']['title'] = '顾客留存统计';

	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));
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
			'condition' => ' where uniacid = :uniacid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':starttime' => $time30,
				':endtime' => TIMESTAMP
			)
		),
		'silence' => array(
			'condition' => ' where uniacid = :uniacid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':starttime' => $time60,
				':endtime' => $time30
			)
		),
		'runoff' => array(
			'condition' => ' where uniacid = :uniacid and success_last_time >= :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':starttime' => $time90,
				':endtime' => $time60
			)
		),
		'giveup' => array(
			'condition' => ' where uniacid = :uniacid and success_last_time > :starttime and success_last_time < :endtime ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':starttime' => 0,
				':endtime' => $time90
			)
		)
	);

	$sid = intval($_GPC['sid']);
	$table = $sid > 0 ? 'tiny_wmall_store_members' : 'tiny_wmall_members';
	if($sid > 0) {
		foreach($conditionAndParams as &$cp) {
			$cp['condition'] .= ' and sid = :sid ';
			$cp['params'][':sid'] = $sid;
		}
	}

	$total = 0;
	foreach($stat as $key => &$value) {
		$value = pdo_fetchcolumn('select count(*) from ' . tablename($table) . $conditionAndParams[$key]['condition'], $conditionAndParams[$key]['params']);
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
	include itemplate('statcenter/keep');
}

elseif($op == 'scanAnalysis') {
	$_W['page']['title'] = '客流分析';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));

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
	$sid = intval($_GPC['sid']);
	$total_scan = pdo_fetchall('select nums, stat_day from ' . tablename('tiny_wmall_member_scan_record') . ' where uniacid = :uniacid and sid = :sid and stat_day >= :starttime and stat_day <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $starttime, ':endtime' => $endtime), 'stat_day');
	$total = array_sum(array_column($total_scan, 'nums'));

	$condition_order = ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day ';
	$params_order = array(
		':uniacid' => $_W['uniacid'],
		':status' => 5,
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	if($sid > 0) {
		$condition_order = ' where uniacid = :uniacid and sid = :sid and status = :status and stat_day >= :starttime and stat_day <= :endtime group by stat_day ';
		$params_order = array(
			':uniacid' => $_W['uniacid'],
			':sid' => $sid,
			':status' => 5,
			':starttime' => $starttime,
			':endtime' => $endtime
		);
	}

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
	include itemplate('statcenter/scanAnalysis');
}

elseif($op == 'finalFee') {
	$_W['page']['title'] = '实付金额分布';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));

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

	$conditionAndParams = array(
		'0to20' => array(
			'condition' => ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime and final_fee > 0 and final_fee <= 20  ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':status' => 5,
				':starttime' => $starttime,
				':endtime' => $endtime
			)
		),
		'20to30' => array(
			'condition' => ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime and final_fee > 20 and final_fee <= 30  ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':status' => 5,
				':starttime' => $starttime,
				':endtime' => $endtime
			)
		),
		'30to40' => array(
			'condition' => ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime and final_fee > 30 and final_fee <= 40  ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':status' => 5,
				':starttime' => $starttime,
				':endtime' => $endtime
			)
		),
		'40to50' => array(
			'condition' => ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime and final_fee > 40 and final_fee <= 50  ',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':status' => 5,
				':starttime' => $starttime,
				':endtime' => $endtime
			)
		),
		'50up' => array(
			'condition' => ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime and final_fee > 50',
			'params' => array(
				':uniacid' => $_W['uniacid'],
				':status' => 5,
				':starttime' => $starttime,
				':endtime' => $endtime
			)
		),
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		foreach($conditionAndParams as &$cp) {
			$cp['condition'] .= ' and sid = :sid ';
			$cp['params'][':sid'] = $sid;
		}
	}

	$data = array();
	foreach($conditionAndParams as $key => $value) {
		$data[$key] = pdo_fetchall('select count(*) as nums, stat_day from ' . tablename('tiny_wmall_order') . " {$value['condition']} group by stat_day", $value['params'], 'stat_day');
	}

	$stat = array(
		'total' => 0,
	);
	if(!empty($data)) {
		foreach($data as $key => $value) {
			$stat['total_' . $key] = intval(array_sum(array_column($value, 'nums')));
			$stat['total'] += $stat['total_' . $key];
		}
		foreach($data as $key => $value) {
			$stat['percent_' . $key] = $stat['total'] > 0 ? round($stat['total_' . $key] / $stat['total'] * 100, 2) : 0;
		}
	}

	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		$total_0to20 = intval($data['0to20'][$i]['nums']);
		$total_20to30 = intval($data['20to30'][$i]['nums']);
		$total_30to40 = intval($data['30to40'][$i]['nums']);
		$total_40to50 = intval($data['40to50'][$i]['nums']);
		$total_50up = intval($data['50up'][$i]['nums']);
		$total = $total_0to20 + $total_20to30 + $total_30to40 + $total_40to50 + $total_50up;
		$records[$i] = array(
			'stat_day' => $i,
			'total' => $total,
			'total_0to20' => $total_0to20,
			'total_20to30' => $total_20to30,
			'total_30to40' => $total_30to40,
			'total_40to50' => $total_40to50,
			'total_50up' => $total_50up,
			'percent_0to20' => $total > 0 ? round($total_0to20 / $total * 100, 2) : 0,
			'percent_20to30' => $total > 0 ? round($total_20to30 / $total * 100, 2) : 0,
			'percent_30to40' => $total > 0 ? round($total_30to40 / $total * 100, 2) : 0,
			'percent_40to50' => $total > 0 ? round($total_40to50 / $total * 100, 2) : 0,
			'percent_50up' => $total > 0 ? round($total_50up / $total * 100, 2) : 0,
		);
		$i = date('Ymd', strtotime($i) - 86400);
	}
	if($_W['isajax']) {
		$chart = array(
			'fields' => array('total', 'total_0to20', 'total_20to30', 'total_30to40', 'total_40to50', 'total_50up'),
			'titles' => array('总订单数', "实付金额在0-20{$_W['Lang']['dollarSignCn']}", "实付金额在20-30{$_W['Lang']['dollarSignCn']}", "实付金额在30-40{$_W['Lang']['dollarSignCn']}", "实付金额在40-50{$_W['Lang']['dollarSignCn']}", "实付金额在50{$_W['Lang']['dollarSignCn']}以上"),
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
		$pieData = array(
			array(
				'name' => "实付金额在0-20{$_W['Lang']['dollarSignCn']}订单占比",
				'value' => $stat['total_0to20'],
			),
			array(
				'name' => "实付金额在20-30{$_W['Lang']['dollarSignCn']}订单占比",
				'value' => $stat['total_20to30'],
			),
			array(
				'name' => "实付金额在30-40{$_W['Lang']['dollarSignCn']}订单占比",
				'value' => $stat['total_30to40'],
			),
			array(
				'name' => "实付金额在40-50{$_W['Lang']['dollarSignCn']}订单占比",
				'value' => $stat['total_40to50'],
			),
			array(
				'name' => "实付金额在50{$_W['Lang']['dollarSignCn']}以上订单占比",
				'value' => $stat['total_50up'],
			),
		);
		$chart['pieData'] = $pieData;
		$chart['pieTitles'] = array_column($pieData, 'name');
		message(error(0, $chart), '', 'ajax');
	}
	include itemplate('statcenter/finalFee');
}

elseif($op == 'orderNum') {
	$_W['page']['title'] = '成交频次分布';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));

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
	$condition = ' where uniacid = :uniacid and status = :status and stat_day >= :starttime and stat_day <= :endtime ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':status' => 5,
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid ';
		$params[':sid'] = $sid;
	}
	$orders = pdo_fetchall('select uid, count(uid) as nums from ' . tablename('tiny_wmall_order') . " {$condition} group by uid", $params);

	$stat = array(
		'total' => 0,
		'total_1' => 0,
		'total_2' => 0,
		'total_3' => 0,
		'total_4' => 0,
		'total_5' => 0,
		'total_6' => 0,
	);
	foreach($orders as $order) {
		$stat['total']++;
		switch($order['nums']) {
			case 1:
				$stat['total_1']++;
				break;
			case 2:
				$stat['total_2']++;
				break;
			case 3:
				$stat['total_3']++;
				break;
			case 4:
				$stat['total_4']++;
				break;
			case 5:
				$stat['total_5']++;
				break;
			default:
				$stat['total_6']++;
		}
	}
	for($i = 1; $i < 7; $i++) {
		$stat['percent_' . $i] = $stat['total'] > 0 ? round($stat['total_' . $i] / $stat['total'] * 100, 2) : 0;
	}
	if($_W['isajax']) {
		$pieData = array();
		for($i = 1; $i < 7; $i++) {
			$pieData[] = array(
				'name' => $i == 6 ? '成交5单以上' : "成交{$i}单",
				'value' => $stat['percent_' . $i]
			);
		}
		$chart = array(
			'pieData' => $pieData,
			'pieTitles' => array_column($pieData, 'name')
		);
		message(error(0, $chart), '', 'ajax');
	}

	include itemplate('statcenter/orderNum');
}


