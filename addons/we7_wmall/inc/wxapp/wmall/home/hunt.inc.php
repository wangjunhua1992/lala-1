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
icheckauth(true);
$config_mall = $_W['we7_wmall']['config']['mall'];
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$member = member_fetch();
	}
	$params = array(
		'url' => rtrim($_W['siteroot'], "/"),
	);
	$result = array(
		'hotStores' => store_fetchall_by_condition('hot'),
		'recommendStores' => store_fetchall_by_condition('recommend'),
		'searchHistorys' => $member['search_data'],
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'truncate') {
	if($_W['member']['uid'] > 0) {
		pdo_update('tiny_wmall_members', array('search_data' => ''), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
	imessage(error(0, '清除历史记录成功'), '', 'ajax');
}

if($ta == 'search') {
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$lat = trim($_GPC['lat']);
		$lng = trim($_GPC['lng']);
		$key = trim($_GPC['key']);
		$member = member_fetch();
		if(!empty($member)) {
			$num = count($member['search_data']);
			if($num >= 5) {
				array_pop($member['search_data']);
			}
			array_push($member['search_data'], $key);
			$search_data = iserializer(array_unique($member['search_data']));
			pdo_update('tiny_wmall_members', array('search_data' => $search_data), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		}
	}
	$key = trim($_GPC['key']);
	$sids = array(0);
	$sids_str = 0;
	$stores = array();
	if(!empty($key)) {
		$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and status = 1 and title like :key', array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"));
		if(!empty($goods)) {
			$store_goods = array();
			foreach($goods as $good) {
				$sids[] = $good['sid'];
				$store_goods[$good['sid']][] = $good;
			}
			$sids_str = implode(',', $sids);
			$stores = pdo_fetchall('select * from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and agentid = :agentid and status = 1 and id in ({$sids_str})", array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
		}
		$search_stores = pdo_fetchall('select id,title,logo,content,business_hours,is_rest,delivery_fee_mode,delivery_price,delivery_areas, serve_radius, not_in_serve_radius, send_price,delivery_time,delivery_mode,forward_mode,forward_url,score,label,sailed,send_price,location_x,location_y,sailed from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and agentid = :agentid and status = 1 and is_waimai = 1 and id not in ({$sids_str}) and title like :key", array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':key' => "%{$key}%"), 'id');
		$stores = array_merge($search_stores, $stores);
		$store_label = store_category_label();
		foreach($stores as $key => &$row) {
/*			if(is_ttapp()) {
				if(strexists($row['title'], '超市')) {
					unset($stores[$key]);
					continue;
				}
			}*/
			$row['delivery_areas'] = iunserializer($row['delivery_areas']);
			$row['goods'] = $store_goods[$row['id']];
			$row['logo'] = tomedia($row['logo']);
			$row['scores'] = score_format($row['score']);
			if($row['delivery_mode'] == 2 && $row['delivery_type'] != 2) {
				$row['delivery_title'] = $config_mall['delivery_title'];
			}
			$row['activity'] = store_fetch_activity($row['id']);
			if($row['delivery_free_price'] > 0) {
				$row['activity']['items']['delivery'] = array(
					'title' => "满{$row['delivery_free_price']}免配送费",
					'type' => "delivery"
				);
				$row['activity']['num'] += 1;
			}
			$row['activity']['items'] = array_values($row['activity']['items']);
			$row['activity']['is_show_all'] = 0;

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
				$price = store_order_condition($row);
				$row['delivery_price'] = $price['delivery_price'];
				$row['send_price'] = $price['send_price'];
			}
			if(!empty($lng) && !empty($lat)) {
				$row['distance'] = distanceBetween($row['location_y'], $row['location_x'], $lng, $lat);
				$row['distance'] = round($row['distance'] / 1000, 2);
				$in = is_in_store_radius($row, array($lng, $lat));
				if($config_mall['store_overradius_display'] == 2 && !$in) {
					unset($stores[$key]);
				}
				$row['distance_order'] = $row['distance'] + $row['distance'] * ($row['is_rest'] == 0 ? 1 : 100000) * ($row['is_stick'] == 1 ? 0 : 300000);
			} else {
				$row['distance'] = 0;
			}
			$row['goods_num'] = count($store_goods[$row['id']]) -1;
			$row['goods_is_show_all'] = 0;
			$row['business_hours'] = iunserializer($row['business_hours']);
			if(!$row['is_rest'] && !store_is_in_business_hours($row['business_hours'])) {
				$row['is_rest_reserve'] = 1;
				$rest_order_info = store_rest_start_delivery_time($row);
				$row['rest_reserve_cn'] = $rest_order_info['delivery_time_cn'];
			}
		}
	}
	$num = count($stores);
	if($num < 4) {
		$recommend_stores = store_fetchall_by_condition('recommend');
		foreach($recommend_stores as $k => &$v) {
			if($v['delivery_mode'] == 2 && $v['delivery_type'] != 2) {
				$v['delivery_title'] = $config_mall['delivery_title'];
			}
			if(!empty($lng) && !empty($lat)) {
				$v['distance'] = distanceBetween($v['location_y'], $v['location_x'], $lng, $lat);
				$v['distance'] = round($v['distance'] / 1000, 2);
				$in = is_in_store_radius($v, array($lng, $lat));
				if($config_mall['store_overradius_display'] == 2 && !$in) {
					unset($recommend_stores[$k]);
				}
				$v['distance_order'] = $v['distance'] + $v['distance'] * ($v['is_rest'] == 0 ? 1 : 100000) * ($v['is_stick'] == 1 ? 0 : 300000);
			} else {
				$v['distance'] = 0;
			}
		}
	}
	$result = array(
		'stores' => array_values($stores),
		'recommendStores' => $recommend_stores,
	);
	imessage(error(0, $result), '', 'ajax');
}


