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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'log';

if($ta == 'log') {
	$_W['page']['title'] = '交心记录';

	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$status = intval($_GPC['status']);
	if($status > 0) {
		if($status == 7) {
			$condition .= ' and (status != 5 and status != 6)';
		} else {
			$condition .= ' and status = :status';
			$params[':status'] = $status;
		}
	}
	$stat_day = intval($_GPC['stat_day']);
	if($stat_day > 0) {
		$condition .= ' and stat_day = :stat_day';
		$params[':stat_day'] = $stat_day;
		$starttime = $endtime = strtotime($stat_day);
	} else {
		if(!empty($_GPC['addtime'])) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);
		} else {
			$today = strtotime(date('Y-m-d'));
			$starttime = strtotime('-15 day', $today);
			$endtime = $today + 86399;
		}
		$condition .= ' and addtime >= :starttime and addtime <= :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}
	$condition .= ' order by id desc';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$order_status = order_status();
	$order_type = order_types();
}
include itemplate('store/finance/order');

