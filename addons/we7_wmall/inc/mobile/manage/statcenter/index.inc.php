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
$_W['page']['title'] = '商户统计';

$stat = array(
	'paybill' => array(),
	'tangshi' => array(),
	'wmall' => array(),
);

$condition_paybill = ' where uniacid = :uniacid and sid = :sid and is_pay = 1 and stat_day = :stat_day';
$params_paybill = array(
	':uniacid' => $_W['uniacid'],
	':sid' => $sid,
	':stat_day' => date('Ymd'),
);
$stat['paybill']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_paybill_order') . $condition_paybill, $params_paybill));
$stat['paybill']['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee),2) from ' . tablename('tiny_wmall_paybill_order') . $condition_paybill, $params_paybill));
$stat['paybill']['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee),2) from ' . tablename('tiny_wmall_paybill_order') . $condition_paybill, $params_paybill));

$condition_tangshi = ' where uniacid = :uniacid and sid = :sid and is_pay = 1 and stat_day = :stat_day and order_type > 2';
$params_tangshi = array(
	':uniacid' => $_W['uniacid'],
	':sid' => $sid,
	':stat_day' => date('Ymd'),
);
$stat['tangshi']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition_tangshi, $params_tangshi));
$stat['tangshi']['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee - plateform_delivery_fee), 2) from ' . tablename('tiny_wmall_order') . $condition_tangshi, $params_tangshi));
$stat['tangshi']['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition_tangshi, $params_tangshi));

$condition_wmall = ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and stat_day = :stat_day and (order_type = 1 or order_type = 2)';
$params_wmall = array(
	':uniacid' => $_W['uniacid'],
	':sid' => $sid,
	':stat_day' => date('Ymd'),
);
$stat['wmall']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition_wmall, $params_wmall));
$stat['wmall']['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee - plateform_delivery_fee), 2) from ' . tablename('tiny_wmall_order') . $condition_wmall, $params_wmall));
$stat['wmall']['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition_wmall, $params_wmall));

include itemplate('statcenter/index');