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

$op = trim($_GPC['op']);
if($op == 'list') {
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$sid = intval($_GPC['store_id']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$is_options = intval($_GPC['is_options']);
	$condition .= ' and is_options = :is_options';
	$params[':is_options'] = $is_options;
	if(isset($_GPC['svip_status'])) {
		$svip_status = intval($_GPC['svip_status']);
		$condition .= ' and svip_status = :svip_status';
		$params[':svip_status'] = $svip_status;
	}
	if(isset($_GPC['kabao_status'])) {
		$kabao_status = intval($_GPC['kabao_status']);
		$condition .= ' and kabao_status = :kabao_status';
		$params[':kabao_status'] = $kabao_status;
	}
	$key = trim($_GPC['key']);
	if(!empty($key)) {
		$condition .= ' and title like :key';
		$params[':key'] = "%{$key}%";
	}
	$data = pdo_fetchall('select id, sid, title, thumb, price, old_price, sailed, comment_good, comment_total,total, content from ' . tablename('tiny_wmall_goods') . $condition, $params, 'id');
	if(!empty($data)) {
		if($_GPC['from'] == 'huangou' || $_GPC['from'] == 'bargain') {
			$goods_bargain = pdo_getall('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid), array('goods_id'), 'goods_id');
		}
		foreach($data as &$row) {
			if(!empty($goods_bargain) && !empty($goods_bargain[$row['id']])) {
				unset($data[$row['id']]);
				continue;
			}
			$row['thumb'] = tomedia($row['thumb']);
			$row['store'] = pdo_fetch('select id, title, logo, send_price, delivery_price, delivery_time from ' .tablename('tiny_wmall_store') . ' where id = :id ', array(':id' => $row['sid']));
			if($row['store']) {
				$row['store']['price'] = store_order_condition($row['store']['id']);
				$row['store']['delivery_price'] = $row['store']['price']['delivery_price'];
				$row['store_title'] = $row['store']['title'];
			}
			$row['old_price'] = $row['old_price'];
			if($row['old_price'] != 0) {
				$row['discount'] = round(($row['price'] / $row['old_price']) * 10, 1);
			} else {
				$row['discount'] = 0;
			}
			if($row['comment_total'] != 0) {
				$row['comment_good_percent'] = round(($row['comment_good'] / $row['comment_total']) * 100, 2) . "%";
			} else {
				$row['comment_good_percent'] = "0%";
			}
			
			if($row['total'] == -1) {
				$row['total'] = '无限';
			}
		}
		$goods = array_values($data);
	}
	message(array('errno' => 0, 'message' => $goods, 'data' => $data), '', 'ajax');
}