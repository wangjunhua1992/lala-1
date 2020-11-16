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
$ta = trim($_GPC['ta'])? trim($_GPC['ta']): 'list';
icheckauth();
if($ta == 'list') {
	$time = TIMESTAMP - 7776000; //90天
	pdo_query('delete from ' . tablename('tiny_wmall_member_footmark') . ' where uniacid = :uniacid and addtime < :time', array(':uniacid' => $_W['uniacid'], ':time' => $time));

	$stores = pdo_fetchall('select id,score,title,logo,sailed,score,label,is_rest,business_hours,is_in_business,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and agentid = :agentid", array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	if(!empty($stores)) {
		$store_label = store_category_label();
		foreach($stores as $key => &$row) {
			$row['logo'] = tomedia($row['logo']);
			//$row['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			$row['activity'] = store_fetch_activity($row['id']);
			$row['activity']['items'] = array_values($row['activity']['items']);
			$row['score'] = round($row['score'], 2);
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			if($row['label'] > 0) {
				$row['label_color'] = $store_label[$row['label']]['color'];
				$row['label_cn'] = $store_label[$row['label']]['title'];
			}
			if($row['delivery_fee_mode'] == 2) {
				$row['delivery_price'] = iunserializer($row['delivery_price']);
				$row['delivery_price'] = $row['delivery_price']['start_fee'];
			} elseif($row['delivery_fee_mode'] == 3) {
				$row['delivery_areas'] = iunserializer($row['delivery_areas']);
				if(!is_array($row['delivery_areas'])) {
					$row['delivery_areas'] = array();
				}
				$price = store_order_condition($row['id'], array($lng, $lat));
				$row['delivery_price'] = $price['delivery_price'];
				$row['send_price'] = $price['send_price'];
			}
			$row['delivery_title'] = $_W['we7_wmall']['config']['mall']['delivery_title'];
		}
	}
	$footmarks = pdo_fetchall('select * from ' . tablename('tiny_wmall_member_footmark') . ' where uniacid = :uniacid and uid = :uid group by stat_day order by stat_day desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($footmarks)) {
		foreach($footmarks as &$val) {
			$val['date'] = date('m-d', $val['addtime']);
			if($val['stat_day'] == date('Ymd')) {
				$val['date'] = '今天';
			} elseif ($val['stat_day'] == date('Ymd') - 1) {
				$val['date'] = '昨天';
			}
			$val['marks'] = pdo_getall('tiny_wmall_member_footmark', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'stat_day' => $val['stat_day']), array('id','sid'));
			$val['stores'] = array();
			foreach($val['marks'] as $marks) {
				if (!empty($stores[$marks['sid']])) {
					$val['stores'][] = $stores[$marks['sid']];
				}
			}	
		}
	}
	$result = array(
		'footmarks' => $footmarks
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'del') {
	$ids = $_GPC['ids'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_member_footmark', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除足迹成功'), '', 'ajax');
}

include itemplate('home/footmark');


