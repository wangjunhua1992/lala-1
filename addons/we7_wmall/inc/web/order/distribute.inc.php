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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '订单分布';
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$agentid = intval($_GPC['agentid']);
	if ($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$stat_type = $_GPC['stat_type'] ? trim($_GPC['stat_type']) : 'today';
	if($stat_type == 'today') {
		$condition .= ' and stat_day = :stat_day';
		$params[':stat_day'] = date('Ymd');
	} elseif($stat_type == 'month') {
		$condition .= ' and stat_month = :stat_month';
		$params[':stat_month'] = date('Ym');
	} elseif($stat_type == 'last_month') {
		$condition .= ' and stat_month = :stat_month';
		$params[':stat_month'] = date('Ym', strtotime('-1 month'));
	}
	$orders = pdo_fetchall('select location_x, location_y from ' . tablename('tiny_wmall_order') . $condition, $params);
}
include itemplate('order/distribute');

