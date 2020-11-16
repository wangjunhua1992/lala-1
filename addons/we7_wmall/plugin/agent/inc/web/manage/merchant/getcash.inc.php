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
	$_W['page']['title'] = '商户提现记录';

	$condition = ' WHERE uniacid = :uniacid AND agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND sid = :sid';
		$params[':sid'] = $sid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$channel = trim($_GPC['channel']);
	if(!empty($channel)) {
		if($channel == 'weixin') {
			$condition .= " AND (channel = 'weixin' OR channel = 'wxapp') ";
		} else {
			$condition .= ' AND channel = :channel';
			$params[':channel'] = $channel;
		}
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND addtime > :start AND addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);

			$condition .= ' and addtime >= :start';
			$params[':start'] = $starttime;
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		$toaccount_status_arr = getcash_toaccount_status();
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid']), array('id', 'title', 'logo'), 'id');
	$channels = getcash_channels();
}
include itemplate('merchant/getcash');