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

function statistics_store() {
	global $_W;
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	if($_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $_W['agentid'];
	}
	$total_num = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . $condition . ' and (status = 1 or status = 0)', $params));
	$total_amount = floatval(pdo_fetchcolumn('select round(sum(amount), 2) from ' . tablename('tiny_wmall_store_account') . $condition, $params));
	$total_getcash = floatval(pdo_fetchcolumn('select round(sum(get_fee), 2) from ' . tablename('tiny_wmall_store_getcash_log') . $condition.  ' and status = 2', $params));
	return array(
		'total_num' => $total_num,
		'total_amount' => $total_amount,
		'total_getcash' => $total_getcash
	);
}

function statistics_deliveryer() {
	global $_W;
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	if($_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $_W['agentid'];
	}
	$total_num = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . $condition . ' and status = 1 ', $params));
	$total_credit2 = floatval(pdo_fetchcolumn('select round(sum(credit2), 2) from ' . tablename('tiny_wmall_deliveryer') . $condition . ' and status = 1 ', $params));
	$total_getcash = floatval(pdo_fetchcolumn('select round(sum(get_fee), 2) from ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition . ' and status = 2', $params));
	return array(
		'total_num' => $total_num,
		'total_credit2' => $total_credit2,
		'total_getcash' => $total_getcash
	);
}

function statistics_member() {
	global $_W;
	$total_num = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . 'where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	$total_credit1 = floatval(pdo_fetchcolumn('select round(sum(credit1), 2) from ' . tablename('tiny_wmall_members') . 'where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	$total_credit2 = floatval(pdo_fetchcolumn('select round(sum(credit2), 2) from ' . tablename('tiny_wmall_members') . 'where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	return array(
		'total_num' => $total_num,
		'total_credit1' => $total_credit1,
		'total_credit2' => $total_credit2
	);
}

