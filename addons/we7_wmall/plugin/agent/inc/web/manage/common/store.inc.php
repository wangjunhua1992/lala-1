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
	if(isset($_GPC['key'])) {
		$key = trim($_GPC['key']);
		$data = pdo_fetchall('select id, title, logo, delivery_free_price, content, score,sailed,send_price,delivery_price,delivery_time, `data` from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and is_waimai = 1 and title like :key order by id desc limit 50', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':key' => "%{$key}%"), 'id');
		if(!empty($data)) {
			foreach($data as &$row) {
				$row['data'] = iunserializer($row['data']);
				$row['shopSign'] = $row['data']['shopSign'];
				$row['logo'] = tomedia($row['logo']);
				$row['hot_goods'] = array();
				$row['hot_goods'] = pdo_fetchall('select id,title,price,old_price,thumb from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
				if(!empty($row['hot_goods'])) {
					foreach($row['hot_goods'] as &$goods) {
						if($goods['old_price'] != 0) {
							$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
						} else {
							$goods['discount'] = 0;
						}
					}
				}
				$row['activity'] = store_fetch_activity($row['id']);
				if(!empty($row['activity'])) {
					$row['activity']['num'] = $row['activity']['num'];
					$row['activity']['items'] = array_values($row['activity']['items']);
				}
				$row['price'] = store_order_condition($row['id']);
				$row['send_price'] = $row['price']['send_price'];
				$row['delivery_price'] = $row['price']['delivery_price'];
				$row['score_cn'] = round($row['score'] / 5, 2) * 100;
				if(empty($row['addtime'])) {
					$row['addtime'] = '未知';
				} else {
					$row['addtime'] = date('Y-m-d H:i');
				}
			}
			$stores = array_values($data);
		}
		message(array('errno' => 0, 'message' => $stores, 'data' => $data), '', 'ajax');
	}
	include itemplate('public/store');
}

if($op == 'category') {
	if(isset($_GPC['key'])) {
		$key = trim($_GPC['key']);
		$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_category') . ' where uniacid = :uniacid and agentid = :agentid and title like :key order by id desc limit 50', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':key' => "%{$key}%"), 'id');
		if(!empty($data)) {
			foreach($data as &$row) {
				$row['thumb_cn'] = tomedia($row['thumb']);
			}
			$categorys = array_values($data);
		}
		message(array('errno' => 0, 'message' => $categorys, 'data' => $data), '', 'ajax');
	}
	include itemplate('public/store');
}