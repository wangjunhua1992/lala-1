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
$_W['page']['title'] = '我的配送';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index'){
	if($_W['deliveryer']['perm_errander'] == 1 || in_array($_W['deliveryer']['perm_takeout'], array(1, 3))) {
		$pft_stat = deliveryer_plateform_order_stat($_deliveryer['id']);
	}
	if(in_array($_W['deliveryer']['perm_takeout'], array(3))) {
		$stores = pdo_fetchall('select id,title,logo from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$_W['deliveryer']['sids_sn']})", array(':uniacid' => $_W['uniacid']));
		$stat = pdo_fetchall('select count(*) as num, sid from ' . tablename('tiny_wmall_order') . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and sid in ({$_W['deliveryer']['sids_sn']}) group by sid", array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $_deliveryer['id']), 'sid');
	}
}

if($ta == 'work_status'){
	$status = intval($_GPC['work_status']);
	$result = deliveryer_work_status_set($_deliveryer['id'], $status);
	if(is_error($result)) {
		imessage($result, imurl('delivery/member/mine'), 'ajax');
	}
	imessage(error(0, '设置工作状态成功'), imurl('delivery/member/mine'), 'ajax');
}

if($ta == 'changes') {
	$type = trim($_GPC['type']);
	$value = intval(!$_W['deliveryer']['extra'][$type]);
	deliveryer_set_extra($type, $value, $_W['deliveryer']['id']);
	imessage(error(0, '状态修改成功'), imurl('delivery/member/mine'), 'ajax');
}
include itemplate('member/mine');