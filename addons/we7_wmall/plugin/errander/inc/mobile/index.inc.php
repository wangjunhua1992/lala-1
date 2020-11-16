<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$_W['page']['title'] = '随意购';

if($op == 'index') {
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_errander_category') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	$delivery_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 and is_errander = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
}

if($op == 'deliveryer') {
	mload()->model('deliveryer');
	$deliveryer = deliveryer_fetchall(0, array('work_status' => 1, 'order_type' => 'is_errander'));
	imessage(error(0, $deliveryer), '', 'ajax');
}

include itemplate('index');
