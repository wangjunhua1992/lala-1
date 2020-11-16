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
$_W['page']['title'] = '顾客列表';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$condition = ' where a.uniacid = :uniacid and a.sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$key = trim($_GPC['key']);
	if(!empty($key)) {
		$time = strtotime('-30 days');
		if($key == 'success_30') {
			$condition .= ' and a.success_last_time >= :time';
		} elseif($key == 'noorder_30') {
			$condition .= ' and a.success_last_time < :time';
		} elseif($key == 'cancel_30') {
			$condition .= ' and a.cancel_last_time >= :time';
		}
		$params[':time'] = $time;
	}
	$sort = trim($_GPC['sort']);
	$sort_val = intval($_GPC['sort_val']);
	if(!empty($sort)) {
		if($sort_val == 1) {
			$condition .= " ORDER BY a.{$sort} DESC";
		} else {
			$condition .= " ORDER BY a.{$sort} ASC";
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid'  . $condition, $params);
	$data = pdo_fetchall('select a.*,b.nickname from ' . tablename('tiny_wmall_store_members') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($data as &$row) {
		if($row['success_num'] > 0) {
			$row['aveage'] = round($row['success_price'] / $row['success_num'], 2);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

include itemplate('store/activity/member');

