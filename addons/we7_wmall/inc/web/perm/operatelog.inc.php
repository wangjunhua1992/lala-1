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
	$_W['page']['title'] = '操作日志';
	$role = trim($_GPC['role']);
	$logs_all_type = mlog_types($role);
	$type = trim($_GPC['type']);

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	$_GPC['endtime'] = $endtime;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);
			$_GPC['starttime'] = $starttime;
			$_GPC['endtime'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$_GPC['starttime'] = $starttime;
		}
	}
	$data = mlog_fetch_all();
	$logs = $data['logs'];
	$pager = $data['pager'];
}
include itemplate('perm/operatelog');