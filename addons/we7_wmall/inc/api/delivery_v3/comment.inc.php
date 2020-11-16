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

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'takeout';
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];

if($op == 'takeout') {
	$stat_type = trim($_GPC['stat_type']) ? trim($_GPC['stat_type']) : 'month';
	if($stat_type == 'month') {
		$starttime =  mktime(0, 0, 0, date('m'), 1, date('Y'));
		$endtime = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
	} elseif($stat_type == 'last_month') {
		$starttime =  mktime(0, 0, 0, date('m')-1, 1, date('Y'));
		$endtime = mktime(23, 59, 59, date('m'), 0, date('Y'));
	}
	//审核状态
	$condition = " where uniacid = :uniacid and agentid = :agentid and deliveryer_id = :deliveryer_id and addtime >= :starttime and addtime <= :endtime";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
		':deliveryer_id' => $deliveryer['id'],
		':starttime' => $starttime,
		':endtime' => $endtime,
	);
	//评论没有区分商家评论，
	/*$comment_role = trim($_GPC['comment_role']) ? trim($_GPC['comment_role']) : 'custom';
	$condition .= ' and role = :role';
	$params[':role'] = $comment_role;*/
	$load_type = trim($_GPC['load_type']) ? trim($_GPC['load_type']) : 'load';
	$id = intval($_GPC['id']);
	if($load_type == 'load') {
		if($id > 0) {
			$condition .= " and id < :id";
			$params[':id'] = $id;
		}
	} else {
		$condition .= " and id > :id";
		$params[':id'] = $id;
	}
	$order_type = trim($_GPC['order_type']) ? trim($_GPC['order_type']) : 'takeout';
	$routers = array(
		'takeout' => array(
			'table' => 'tiny_wmall_order_comment',
			'fields_str' => 'id, delivery_service, deliveryer_tag, note'
		),
		/*'errander' => array(
			'table' => '',
			'fields_str' => ''
		),*/
	);
	$router = $routers[$order_type];
	$total_num = intval(pdo_fetchcolumn('select count(*) from ' . tablename($router['table']) . $condition, $params));
	$condition_good = "{$condition} and delivery_service >= 3";
	$good_num = intval(pdo_fetchcolumn('select count(*) from ' . tablename($router['table']) . $condition_good, $params));

	$comment_type = trim($_GPC['comment_type']) ? trim($_GPC['comment_type']) : 'all';
	if($comment_type == 'good') {
		$condition .= " and delivery_service >= 3";
	} elseif($comment_type == 'bad') {
		$condition .= " and delivery_service < 3";
	}
	$condition .= ' order by id desc limit 15';
	$min_id = intval(pdo_fetchcolumn('select min(id) as min_id FROM ' . tablename($router['table']) . $condition , $params));
	$comments = pdo_fetchall("select {$router['fields_str']} from " . tablename($router['table']) . $condition, $params, 'id');

	$min = $max = 0;
	if(!empty($comments)) {
		foreach($comments as &$item) {
			$item['addtime_cn'] = date('Y-m-d H:i', $item['addtime']);
			$item['delivery_service_cn'] = ($item['delivery_service'] >= 3) ? '满意' : '不满意';
			if(!empty($item['deliveryer_tag'])) {
				$item['deliveryer_tag'] = explode(',', $item['deliveryer_tag']);
			} else {
				$item['deliveryer_tag'] = array();
			}
		}
		$more = 1;
		$min = min(array_keys($comments));
		$max = max(array_keys($comments));
		if($min <= $min_id) {
			$more = 0;
		}
	}
	$result = array(
		'list' => array_values($comments),
		'total_num' => $total_num,
		'good_num' => $good_num,
		'bad_num' => $total_num - $good_num,
		'max_id' => $max,
		'min_id' => $min,
		'more' => $more,
	);
	message(ierror(0, '', $result), '', 'ajax');
}



