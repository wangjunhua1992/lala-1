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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$stat = deliveryer_stat_order();
	if(is_error($stat)) {
		imessage($stat, '', 'ajax');
	}
	imessage(error(0, $stat), '', 'ajax');
}

elseif($ta == 'rank') {
	$params = array(
		'type' => trim($_GPC['type']),
		'deliveryer_id' => $_W['deliveryer']['id'],
		'sort_type' => trim($_GPC['sort_type']),
	);
	$rank = deliveryer_takeout_rank($params);
	if(is_error($rank)) {
		imessage($rank, '', 'ajax');
	}
	imessage(error(0, $rank), '', 'ajax');
}

elseif($ta == 'rank_errander') {
	$params = array(
		'type' => trim($_GPC['type']),
		'deliveryer_id' => $_W['deliveryer']['id'],
		'sort_type' => trim($_GPC['sort_type']),
	);
	$rank = deliveryer_errander_rank($params);
	if(is_error($rank)) {
		imessage($rank, '', 'ajax');
	}
	imessage(error(0, $rank), '', 'ajax');
}

elseif($ta == 'stat_vue') {
	$type = trim($_GPC['type']);
	$stat = deliveryer_stat_order1($type);
	$result = array(
		'stat' => $stat
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'detail') {
	$type = trim($_GPC['type']);
	$result = array(
		'records' => deliveryer_stat_detail($type)
	);
	imessage(error(0, $result), '', 'ajax');
}
