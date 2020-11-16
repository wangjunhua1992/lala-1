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

function goods_format($goods) {
	global $_W;
	$goods['is_sail_now'] = 1;
	$goods_temp = goods_is_available($goods, 'goods');
	if(!empty($goods_temp)) {
		if(!$goods_temp['return_status']) {
			$goods['is_sail_now'] = 0;
		}
		if(empty($goods['is_showtime'])) {
			$goods['is_showtime'] = $goods_temp['is_showtime'];
			$goods['start_time1'] = $goods_temp['start_time1'];
			$goods['end_time1'] = $goods_temp['end_time1'];
			$goods['week'] = $goods_temp['week'];
		}
	}
	if(isset($goods['discount_price']) && !empty($goods['discount_price'])) {
		$goods['old_price'] = $goods['old_price'] ? $goods['old_price'] : $goods['price'];
		$goods['price'] = $goods['discount_price'];
		$goods['discount'] = round($goods['price'] / $goods['old_price'], 2) * 10;
	} else {
		if($goods['config_svip_status']) {
			if($goods['svip_status'] == 1) {
				$old_price = $goods['old_price'] ? $goods['old_price'] : $goods['price'];
				$goods['discount'] = round($goods['svip_price'] / $old_price, 2) * 10;
				$goods['svip_buy_show'] = 1;
				$goods['old_price'] = $old_price;
				$goods['origin_price'] = $goods['price'];
				$goods['price'] = $goods['svip_price'];
				if($_W['member']['svip_status'] == 1) {
					$goods['svip_buy_show'] = 0;
				}
				if(ORDER_TYPE == 'tangshi') {
					$goods['svip_buy_show'] = 0;
				}
			}
		}
		if(empty($goods['svip_status']) && $goods['kabao_status'] == 1 && $goods['group_id'] > 0 && ORDER_TYPE == 'takeout') {
			if(!empty($goods['kabao_price_all'][$goods['group_id']])) {
				$kabao_price = floatval($goods['kabao_price_all'][$goods['group_id']]['kabao_price']);
				$goods['kabao_price'] = $kabao_price;
			} else {
				$goods['kabao_status'] = 0;
			}
		} else {
			$goods['kabao_status'] = 0;
		}
	}
	if($goods['price'] == $goods['old_price'] || empty($goods['old_price'])) {
		unset($goods['old_price']);
	}
	$goods['totalnum'] = 0;
	$goods['thumb'] = tomedia($goods['thumb']);
	$goods['unitname_cn'] = !empty($goods['unitname']) ? "/{$goods['unitname']}" : '';
	if($goods['unitnum'] > 1) {
		$unitname_cn = empty($goods['unitname']) ? '份' : trim($goods['unitname']);
		$goods['unitnum_multi_cn'] = "{$goods['unitnum']}{$unitname_cn}起";
	}

	$goods['options'] = array();
	if($goods['is_options'] == 1) {
		$goods['options'] = goods_option_fetch($goods['id']);
		if(empty($goods['options'])) {
			$goods['is_options'] = 0;
		}
	}
	$goods['is_attrs'] = 0;
	$goods['attrs'] = iunserializer($goods['attrs']);
	if(!empty($goods['attrs'])) {
		$goods['is_attrs'] = 1;
	}
	$goods['options_data'] = goods_build_options($goods);
	$week_cn = array();
	$time_cn = '';
	if($goods['is_showtime'] == 1) {
		if(!empty($goods['week'])) {
			$weeks = array(0, '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
			$week = explode(',', $goods['week']);
			foreach($week as $value) {
				foreach($weeks as $key1 => $value1) {
					if($value == $key1) {
						$week_cn[] = $value1;
					}
				}
			}
		} else {
			$week_cn = array('星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
		}
		if(!empty($goods['start_time1'])) {
			$time_cn = "{$goods['start_time1']}-{$goods['end_time1']} ";
		}
		if(!empty($goods['start_time2'])) {
			$time_cn .= "{$goods['start_time2']}-{$goods['end_time2']}";
		}
	}
	$goods['week_cn'] = implode(',', $week_cn);
	$goods['time_cn'] = $time_cn;
	if(empty($goods['c_status']) && empty($goods['week_cn'])) {
		$goods['week_cn'] = '商品分组已下架，暂无法购买';
	}
	return $goods;
}

function goods_avaliable_fetchall($sid, $cid = 0, $ignore_bargain = false) {
	global $_W;
	$result = array('goods' => array(), 'category' => array());
	$categorys = store_fetchall_goods_category($sid, 1, true, 'parent', 'available');
	if(empty($categorys)) {
		return $result;
	}
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 1 and huangou_type = 1';
	if(ORDER_TYPE == 'takeout') {
		$condition .= ' and (type = 1 or type = 3)';
	} elseif(ORDER_TYPE == 'tangshi') {
		$condition .= ' and (type = 2 or type = 3)';
	}
	$condition .= ' order by displayorder desc, id desc';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$goods = pdo_fetchall('select id, cid, title, price, old_price, ts_price, box_price,svip_status, svip_price, kabao_status, kabao_price, total, thumb, sailed, label, content, is_options, attrs, unitname, unitnum, comment_good, status, is_showtime, start_time1, end_time1, start_time2, end_time2, week from ' . tablename('tiny_wmall_goods') . $condition, $params, 'id');
	if(empty($goods)) {
		return $result;
	}
	$config_svip_status = svip_status_is_available();
	$group_id = ($_W['member']['kabao']['status'] == 1 && $_W['member']['kabao']['vip_goods'] == 1) ? $_W['member']['kabao']['group_id'] : 0;

	$options = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods_options') . " where uniacid = :uniacid and sid = :sid order by displayorder desc", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$goods_options = array();
	foreach($options as $option) {
		$option['discount_price'] = $option['price'];
		$option['kabao_price_all'] = iunserializer($option['kabao_price']);
		$goods_options[$option['goods_id']][$option['id']] = $option;
	}
	unset($options);
	$condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':status' => 1
	);
	if(!$ignore_bargain) {
		$bargains = pdo_fetchall('select id, title, content, order_limit, goods_limit from ' . tablename('tiny_wmall_activity_bargain') . $condition, $params, 'id');
		if(!empty($bargains)) {
			$bargain_ids = implode(',', array_keys($bargains));
			$params = array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':stat_day' => date('Ymd'),
				':uid' => $_W['member']['uid']
			);
			$where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in ({$bargain_ids}) group by bargain_id";
			$bargain_order = pdo_fetchall('select count(distinct(oid)) as num, bargain_id from ' . tablename('tiny_wmall_order_stat') . $where, $params, 'bargain_id');
			foreach($bargains as &$bargain) {
				$bargain['avaliable_order_limit'] = $bargain['order_limit'];
				if(!empty($bargain_order)) {
					$bargain['avaliable_order_limit'] = $bargain['order_limit'] - intval($bargain_order[$bargain['id']]['num']);
				}
				$bargain['hasgoods'] = array();
				array_unshift($categorys, array('id' => "bargain_{$bargain['id']}", 'title' => $bargain['title'], 'bargain_id' => $bargain['id']));
			}
			$where = " where uniacid = :uniacid and sid = :sid and (discount_available_total = -1 or discount_available_total > 0) and bargain_id in ({$bargain_ids})";
			$params = array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
			);
			$bargain_goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_bargain_goods') . $where, $params, 'goods_id');
		};
	}
	$cart = order_fetch_member_cart($sid);
	$cart_goodsids = array();
	if(!empty($cart)) {
		$cart_goodsids = array_keys($cart['data']);
	}
	$cate_goods = array();
	foreach($goods as &$good) {
		$goods_category = $categorys[$good['cid']];
		$good['c_status'] = $goods_category['status'];
		$good['c_is_showtime'] = $goods_category['is_showtime'];
		$good['c_start_time'] = $goods_category['start_time'];
		$good['c_end_time'] = $goods_category['end_time'];
		$good['c_week'] = $goods_category['week'];
		if(empty($good['is_showtime']) && !empty($good['c_is_showtime'])) {
			$good['is_showtime'] = $good['c_is_showtime'];
			$good['start_time1'] = $good['c_start_time'];
			$good['end_time1'] = $good['c_end_time'];
			$good['week'] = $good['c_week'];
		}
		$good['c_status'] = $goods_category['status'];
		$good['c_status'] = $goods_category['status'];
		$goods['is_showtime'] = $goods['c_is_showtime'];
		$goods['start_time1'] = $goods['c_start_time'];
		$goods['end_time1'] = $goods['c_end_time'];
		$goods['week'] = $goods['c_week'];
		$good['is_sail_now'] = 1;
		if(!goods_is_available($good)) {
			$good['is_sail_now'] = 0;
		}
		$good['totalnum'] = 0;
		$good['thumb'] = tomedia($good['thumb']);
		$good['unitname_cn'] = !empty($good['unitname']) ? "/{$good['unitname']}" : '';
		if($good['unitnum'] > 1) {
			$unitname_cn = empty($good['unitname']) ? '份' : trim($good['unitname']);
			$good['unitnum_multi_cn'] = "{$good['unitnum']}{$unitname_cn}起";
		}

		$good['options'] = array();
		if($good['is_options'] == 1) {
			$good['options'] = $goods_options[$good['id']];
			if(empty($good['options'])) {
				$good['is_options'] = 0;
			}
		}
		$good['is_attrs'] = 0;
		$good['attrs'] = iunserializer($good['attrs']);
		if(!empty($good['attrs'])) {
			$good['is_attrs'] = 1;
		}
		$good['config_svip_status'] = $config_svip_status;
		$good['kabao_price_all'] = iunserializer($good['kabao_price']);
		$good['group_id'] = $group_id;
		$good['from'] = 'goods';
		$good['options_data'] = goods_build_options($good);
		if(defined('IN_VUE')) {
			$good['options'] = array_values($goods_options[$good['id']]);
		}
		$good['show'] = 0;
		if(!empty($cid) && $good['cid'] == $cid) {
			$good['show'] = 1;
		}
		if($good['is_showtime'] == 1) {
			if(!empty($good['week'])) {
				$week = explode(',', $good['week']);
				$weeks = array(0, '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
				$week_cn = array();
				foreach($week as $val){
					foreach($weeks as $k => $v) {
						if($val == $k){
							$week_cn[] = $v;
						}
					}
				}
				$good['week_cn'] = implode('，', $week_cn);
			} else {
				$good['week_cn'] = '星期一，星期二，星期三，星期四，星期五，星期六，星期日';
			}
			$time_cn = '';
			if(!empty($good['start_time1'])) {
				$time_cn = "{$good['start_time1']}-{$good['end_time1']} ";
			}
			if(!empty($good['start_time2'])) {
				$time_cn .= "{$good['start_time2']}-{$good['end_time2']}";
			}
			if(empty($good['start_time1']) && empty($good['start_time2'])) {
				$time_cn = '00:00-23:59';
			}
			$good['time_cn'] = $time_cn;
		}
		if(empty($good['c_status'])) {
			$good['week_cn'] = '商品分组已下架，暂无法购买';
		}
		if($config_svip_status) {
			if($good['svip_status'] == 1) {
				$old_price = $good['old_price'] ? $good['old_price'] : $good['price'];
				$good['discount'] = round($good['svip_price'] / $old_price, 2) * 10;
				$good['old_price'] = $old_price;
				$good['svip_buy_show'] = 1;
				$good['origin_price'] = $good['price'];
				$good['price'] = $good['svip_price'];
				if($_W['member']['svip_status'] == 1) {
					$good['svip_buy_show'] = 0;
				}
			}
		} else {
			$good['svip_status'] = 0;
		}
		//svip级别高于kabao
		if(empty($good['svip_status']) && $good['group_id'] > 0 && ORDER_TYPE == 'takeout') {
			if($good['kabao_status'] == 1) {
				$kabao_price = $good['price'];
				if(!empty($good['kabao_price_all'][$group_id])) {
					$kabao_price = floatval($good['kabao_price_all'][$group_id]['kabao_price']);
				}
				$good['kabao_price'] = $kabao_price;
			}
		} else {
			$good['kabao_status'] = 0;
		}

		if(ORDER_TYPE == 'tangshi') {
			$good['price'] = $good['ts_price'];
			$good['old_price'] = $good['ts_price'];
			$good['svip_buy_show'] = 0;
		}
		if(in_array($good['id'], $cart_goodsids)) {
			foreach($cart['data'][$good['id']] as $key => $cart_option) {
				$good['options_data'][$key]['num'] = $cart_option['num'];
				$good['totalnum'] += $cart_option['num'];
			}
		}
		$good['bargain_id'] = 0;
		if(!empty($bargain_goods) && in_array($good['id'], array_keys($bargain_goods)) && ($good['total'] == -1 || $good['total'] > 0)) {
			$discount_goods = $bargain_goods[$good['id']];
			$good['bargain_id'] = $discount_goods['bargain_id'];
			$good['old_price'] = $good['old_price'] ? $good['old_price'] : $good['price'];
			$good['discount'] = round($discount_goods['discount_price'] / $good['old_price'], 2) * 10;
			$good['discount_price'] = $discount_goods['discount_price'];
			$good['discount_total'] = $discount_goods['discount_total'];
			$good['max_buy_limit'] = $discount_goods['max_buy_limit'];
			$good['poi_user_type'] = $discount_goods['poi_user_type'];
			$good['svip_buy_show'] = 0;
			if(defined('IN_VUE') || defined('IN_WXAPP')) {
				$good['price'] = $good['discount_price'];
				if($good['price'] == $good['old_price'] || empty($good['old_price'])) {
					unset($good['old_price']);
				}
				$cate_goods["bargain_{$discount_goods['bargain_id']}"][] = $good;
				$cate_goods[$good['cid']][] = $good;
			} else {
				$cate_goods["bargain_{$discount_goods['bargain_id']}"][] = $good;
			}
		} else {
			if($good['price'] == $good['old_price'] || empty($good['old_price'])) {
				unset($good['old_price']);
			}
			$good['discount_price'] = $good['price'];
			$cate_goods[$good['cid']][] = $good;
		}
	}

	if(!is_array($bargains)) {
		$bargains = array();
	}
	$result = array('goods' => $goods, 'cate_goods' => $cate_goods, 'category' => $categorys, 'bargains' => $bargains);
	if(defined('IN_VUE') || defined('IN_WXAPP')) {
		$cate_has_goods = array();
		if(!empty($categorys)) {
			foreach($categorys as $key => $value) {
				if(array_key_exists($value['id'], $cate_goods)) {
					$cate_has_goods[] = array(
						'id' => $value['id'],
						'title' => $value['title'],
						'thumb' => $value['thumb'],
						'category_min_fee' => $value['min_fee'],
						'total' => $value['total'],
						'goods' => $cate_goods[$value['id']]
					);
				}
			}
		}
		$result['cate_has_goods'] = $cate_has_goods;
	}
	return $result;
}