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

function store_filter($filter = array(), $orderby = '') {
	global $_W, $_GPC;
	$condition = "  where uniacid = :uniacid and agentid = :agentid and status = 1 and is_waimai = 1";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	if(empty($filter)) {
		$filter = $_GPC;
	}
	/*if($filter['cid'] > 0) {
		$condition .= ' and cid like :cid';
		$params[':cid'] = "%|{$filter['cid']}|%";
	}*/
	if($filter['child_id'] > 0) {
		$condition .= ' and (cate_childid1 = :child_id or cate_childid2 = :child_id)';
		$params[':child_id'] = $filter['child_id'];
	} elseif($filter['cid'] > 0) {
		$condition .= ' and (cate_parentid1 = :parent_id or cate_parentid2 = :parent_id)';
		$params[':parent_id'] = $filter['cid'];
	}
	$delivery_type = intval($filter['delivery_type']);
	if($delivery_type == 2) {
		$condition .= ' and delivery_type > 1 ';
	}
	if(!empty($filter['ids'])) {
		$condition .= " and id in ({$filter['ids']})";
	}
	if(defined('IN_WXAPP') || defined('IN_VUE') ) {
		$temp = $_GPC['condition'];
		$temp = json_decode(htmlspecialchars_decode($temp), true);
	}
	if(!empty($temp)) {
		$dis = trim($temp['dis']);
		if(!empty($dis)) {
			if($dis == 'invoice_status') {
				$condition .= " and invoice_status = 1";
			} elseif($dis == 'delivery_price') {
				$condition .= " and (delivery_price = '0' or delivery_free_price > 0)";
			} else {
				$sids = pdo_getall('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'type' => $dis, 'status' => 1), array('sid'), 'sid');
				if(empty($sids)) {
					$sids = array(0);
				}
				$sids = implode(',', array_keys($sids));
				$condition .= " and id in ({$sids})";
			}
		}
		$mode = intval($temp['mode']);
		if(!empty($mode)) {
			$condition .= " and delivery_mode = ".$mode;
		}
	}
	$config_mall = $_W['we7_wmall']['config']['mall'];
	$lat = trim($_GPC['lat']) ? trim($_GPC['lat']) : '37.80081';
	$lng = trim($_GPC['lng']) ? trim($_GPC['lng']) : '112.57543';
	$order_by_base = " order by is_rest asc, is_stick desc";
	$order_by = trim($temp['order']) ? trim($temp['order']) : $config_mall['store_orderby_type'];
	if(in_array($order_by, array('sailed', 'score', 'displayorder', 'click'))) {
		$order_by_base .= ", {$order_by} desc";
	} elseif($order_by == 'displayorderAndDistance') {
		$order_by_base .= ", displayorder desc, distance asc";
	} else {
		$order_by_base .= ", {$order_by} asc";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 20;
	$limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
	$stores = pdo_fetchall('select id,agentid,cate_parentid1,cate_childid1,cate_parentid2,cate_childid2,score,title,logo,content,sailed,score,label,delivery_type,serve_radius,not_in_serve_radius,delivery_areas,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click,
 ROUND(
        6378.138 * 2 * ASIN(
            SQRT(
                POW(
                    SIN(
                        (
                            '.$lat.' * 3.141592654 / 180 - location_x * 3.141592654 / 180
                        ) / 2
                    ),
                    2
                ) + COS('.$lat.' * 3.141592654 / 180) * COS(location_x * 3.141592654 / 180) * POW(
                    SIN(
                        (
                           '.$lng.'  * 3.141592654 / 180 - location_y * 3.141592654 / 180
                        ) / 2
                    ),
                    2
                )
            )
        ) * 1000) as distance from ' . tablename('tiny_wmall_store') . " {$condition} {$order_by_base} {$limit}", $params, 'id');
	$total = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . $condition, $params));

	$pagetotal = ceil($total / $psize);
	if(!empty($stores)) {
		$store_keys = implode(',', array_keys($stores));
		$cart_nums = pdo_fetchall('select sid, num as cart_num from ' . tablename('tiny_wmall_order_cart') . " where uniacid = :uniacid and uid = :uid and sid in ({$store_keys})", array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'sid');
		$store_label = store_category_label();
		foreach($stores as $key => &$da) {
/*			if(is_ttapp()) {
				if(strexists($da['title'], '超市')) {
					unset($stores[$key]);
					continue;
				}
			}*/
			$da['cart_num'] = intval($cart_nums[$key]['cart_num']);
			$da['logo'] = tomedia($da['logo']);
			if($da['delivery_mode'] == 2 && $da['delivery_type'] != 2) {
				$da['delivery_title'] = $config_mall['delivery_title'];
			}
			$da['scores'] = score_format($da['score']);
			$da['score'] = floatval($da['score']);
			$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
			$da['hot_goods'] = array();
			$hot_goods = pdo_fetchall('select id,title,price,old_price,thumb,svip_status,svip_price from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			if(!empty($hot_goods)) {
				foreach($hot_goods as &$goods) {
					$goods['thumb'] = tomedia($goods['thumb']);
					if($goods['old_price'] > 0 && $goods['old_price'] > $goods['price']) {
						$old_price = $goods['old_price'];
						$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
					} else {
						$old_price = $goods['price'];
						$goods['old_price'] = 0;
						$goods['discount'] = 0;
					}
					if($goods['svip_status'] == 1) {
						$goods['price'] = $goods['svip_price'];
						$goods['old_price'] = $old_price;
						$goods['discount'] = round(($goods['price'] / $old_price) * 10, 1);
					}
					$da['hot_goods'][] = $goods;
				}
				$da['hot_goods_num'] = count($da['hot_goods']);
				unset($hot_goods);
			}
			if($da['label'] > 0) {
				$da['label_color'] = $store_label[$da['label']]['color'];
				$da['label_cn'] = $store_label[$da['label']]['title'];
			}
			if($da['delivery_fee_mode'] == 2) {
				$da['delivery_price'] = iunserializer($da['delivery_price']);
				$da['delivery_price'] = $da['delivery_price']['start_fee'];
			} elseif($da['delivery_fee_mode'] == 3) {
				$da['delivery_areas'] = iunserializer($da['delivery_areas']);
				if(!is_array($da['delivery_areas'])) {
					$da['delivery_areas'] = array();
				}
				$price = store_order_condition($da, array($lng, $lat));
				$da['delivery_price'] = $price['delivery_price'];
				$da['send_price'] = $price['send_price'];
				$da['delivery_free_price'] = $price['delivery_free_price'];
			}
			$da['activity'] = store_fetch_activity($da['id']);
			if($da['delivery_free_price'] > 0) {
				$da['activity']['items']['delivery'] = array(
					'title' => "满{$da['delivery_free_price']}免配送费",
					'type' => "delivery"
				);
				$da['activity']['num'] += 1;
				$da['activity']['labels'][] = array(
					'title' => '可免配送费',
					'class' => 'tag tag-success',
				);
				$da['activity']['labels_num'] += 1;
			}
			if($da['delivery_type'] >= 2) {
				$da['activity']['labels'][] = array(
					'title' => '支持自取',
					'class' => 'tag tag-success',
				);
				$da['activity']['labels_num'] += 1;
			}
			if(!empty($da['activity']['items']['zhunshibao'])) {
				$da['zhunshibao_cn'] = '准时宝';
				unset($da['activity']['items']['zhunshibao']);
			}
			$da['activity']['items'] = array_values($da['activity']['items']);
			$da['activity']['is_show_all'] = 0;

			$da['distance'] = round($da['distance']/1000, 1);
			if(!empty($lng) && !empty($lat)) {
				$in = is_in_store_radius($da, array($lng, $lat));
				if($config_mall['store_overradius_display'] == 2 && !$in) {
					unset($stores[$key]);
				}
			}
			unset($da['delivery_areas']);
			$da['business_hours'] = iunserializer($da['business_hours']);
			if(!$da['is_rest'] && !store_is_in_business_hours($da['business_hours'])) {
				$da['is_rest_reserve'] = 1;
				$rest_order_info = store_rest_start_delivery_time($da);
				$da['rest_reserve_cn'] = $rest_order_info['delivery_time_cn'];
			}
		}
	}
	$result = array(
		'stores' => array_values($stores),
		'total' => $total,
		'pagetotal' => $pagetotal,
	);
	return $result;
}