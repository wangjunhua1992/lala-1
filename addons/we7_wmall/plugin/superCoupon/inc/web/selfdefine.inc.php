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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '自定义人群';
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title', 'data'));
	if(!empty($stores)) {
		foreach($stores as $key => &$value) {
			$value['data'] = iunserializer($value['data']);
			if($value['data']['superCoupon']['status'] != 1) {
				unset($stores[$key]);
			}
		}
	}
	if ($_W['ispost']) {
		$store_member_type = intval($_GPC['store_member_type']);
		if($store_member_type == 1) {
			$member_limit = array(
				'store_new_member_type' => intval($_GPC['store_new_member_type']),//0进店未下单，1下单没有订单完成
			);
		} elseif($store_member_type == 3) {
			$uids = trim($_GPC['uids']);
			if(!empty($uids)) {
				$uids = str_replace('，', ',', $uids);
				$uids = explode(',', $uids);
				$uids = array_filter($uids, trim);
				$member_limit = array(
					'uids' => $uids
				);
			}
		} else {//老顾客
			if($store_member_type == 0) {
				$member_limit = array(
					'order_stat_date_type' => intval($_GPC['order_date_type']),
					'order_weekend' => intval($_GPC['order_weekend']),
					'order_stat_time_type' => intval($_GPC['order_time_type']),
					'is_comment' => intval($_GPC['is_comment']),
					'is_favorite' => intval($_GPC['is_favorite']),
				);
			}
			if($store_member_type == 2) {//流失顾客
				$member_limit = array(
					'no_order_days' => intval($_GPC['no_order_days']),
					'before_leave_days' => intval($_GPC['before_leave_days'])
				);
				if($member_limit['no_order_days'] >= $member_limit['before_leave_days']) {
					imessage(error(-1, '统计天数不能小于未下单天数'), '', 'ajax');
				}
				if($member_limit['before_leave_days'] > 90) {
					imessage(error(-1, '统计天数不能超过90天'), '', 'ajax');
				}
			}
			$member_limit['consume_num_type'] = intval($_GPC['consume_num_type']);
			$member_limit['consume_price_type'] = intval($_GPC['consume_price_type']);
			if($member_limit['consume_num_type'] == 1) {
				$member_limit['over_avg_consume_num'] = intval($_GPC['over_avg_consume_num']);
			} elseif($member_limit['consume_num_type'] == 2) {
				$min_consume_num = intval($_GPC['min_consume_num']);
				$max_consume_num = intval($_GPC['max_consume_num']);
				if(!empty($min_consume_num) && !empty($max_consume_num) && $max_consume_num <= $min_consume_num) {
					imessage(error(-1, '最大消费次数不能小于最小消费次数'), '', 'ajax');
				}
				$member_limit['min_consume_num'] = $min_consume_num;
				$member_limit['max_consume_num'] = $max_consume_num;
			}
			if($member_limit['consume_price_type'] == 1) {
				$member_limit['over_avg_consume_price'] = intval($_GPC['over_avg_consume_price']);
			} elseif($member_limit['consume_price_type'] == 2) {
				$avg_min_consume_price = intval($_GPC['avg_min_consume_price']);
				$avg_max_consume_price = intval($_GPC['avg_max_consume_price']);
				if(!empty($avg_min_consume_price) && !empty($avg_max_consume_price) && $avg_max_consume_price <= $avg_min_consume_price) {
					imessage(error(-1, '最大客单价不能小于最小客单价'), '', 'ajax');
				}
				$member_limit['avg_min_consume_price'] = $avg_min_consume_price;
				$member_limit['avg_max_consume_price'] = $avg_max_consume_price;
			}
			if($member_limit['order_stat_time_type'] == 1) {
				$member_limit['order_stat_time'] = $_GPC['order_time'];
				if(empty($member_limit['order_stat_time'])) {
					imessage(error(-1, '下单时间段不能为空'), '', 'ajax');
				}
			}
		}
		$group_condition = array(
			'store_member_type' => intval($_GPC['store_member_type']),
			'stat_day' => intval($_GPC['date_type']),
			'member_limit' => $member_limit,
		);
		if($group_condition['stat_day'] == -1) {
			$group_condition['stat_day'] = array(
				'starttime' => trim($_GPC['stattime']['start']),
				'endtime' => trim($_GPC['stattime']['end']),
			);
			$starttime = strtotime($group_condition['stat_day']['starttime']);
			if(TIMESTAMP - $starttime > 86400 * 90) {
				imessage(error(-1, '统计日期不能超过90天之前'), '', 'ajax');
			}
			if($starttime >= TIMESTAMP) {
				imessage(error(-1, '开始时间不能大于当前时间'), '', 'ajax');
			}
		}
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'content' => trim($_GPC['content']),
			'group_condition' => iserializer($group_condition),
			'addtime' => TIMESTAMP,
		);
		$sync = intval($_GPC['sync']);
		if ($sync == 1) {
			foreach ($stores as $key => $value) {
				$insert['sid'] = $value['id'];
				pdo_insert('tiny_wmall_supercoupon_member_group', $insert);
			}
		} elseif ($sync == 2) {
			$store_ids = $_GPC['store_ids'];
			foreach ($store_ids as $key => $value) {
				$insert['sid'] = $value;
				pdo_insert('tiny_wmall_supercoupon_member_group', $insert);
			}
		}
		imessage(error(0, '自定义人群批量设置成功'), iurl('superCoupon/selfdefine'), 'ajax');
	}

}
include itemplate('selfdefine');