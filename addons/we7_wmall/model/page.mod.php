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

function store_page_get($sid, $id = 0, $mobile = true) {
	global $_W;
	$condition = ' WHERE uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	if(empty($id)) {
		$condition .= ' and type = :type';
		$params[':type'] = 'home';
	} else {
		$condition .= ' and id = :id';
		$params[':id'] = $id;
	}
	$page = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_store_page') . $condition, $params);
	if(!empty($page)) {
		$page['is_has_location'] = $page['is_has_goodsTab'] = $page['is_has_storebasic'] = 0;
		$page['data'] = json_decode(base64_decode($page['data']), true);
		foreach($page['data']['items'] as $itemid => &$item) {
			if(in_array($item['id'], array('picture', 'banner'))) {
				foreach($item['data'] as &$val) {
					$val['imgurl'] = tomedia($val['imgurl']);
				}
				if($item['id'] == 'picture' && !isset($item['params']['picturedata'])) {
					$item['params']['picturedata'] = 0;
				}
			} elseif(in_array($item['id'], array('copyright', 'img_card'))) {
				$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
			} elseif($item['id'] == 'searchbar' && $mobile) {
				$item['params']['link'] = '/pages/store/search?sid=' . $sid;
			} elseif($item['id'] == 'store_searchbar' && $mobile) {
				$item['params']['link'] = '/pages/shop/search?sid=' . $sid;
			} elseif($item['id'] == 'richtext' && $mobile) {
				$item['params']['content'] = base64_decode($item['params']['content']);
			} elseif($item['id'] == 'info' && $mobile) {
				$store = store_fetch($sid, array('id', 'title', 'logo', 'business_hours', 'send_price', 'delivery_price', 'telephone', 'address', 'is_rest', 'location_x', 'location_y', 'consume_per_person'));
				$item['data'] = $store;
			} elseif($item['id'] == 'operation') {
				if(empty($item['params'])) {
					$item['params'] = array(
						'rownum' => 4,
						'pagenum' => 8,
						'navsdata' => 0,
						'navsnum' => 4,
						'showtype' => 0,
						'showdot' => 0,
					);
				}
				$item['params']['has_diypage'] = 0;
				if(check_plugin_perm('diypage')) {
					$item['params']['has_diypage'] = 1;
				} else {
					$item['params']['navsdata'] = 0;
					$item['params']['showtype'] = 0;
				}
				if(!isset($item['style']['dotbackground'])) {
					$item['style']['dotbackground'] = '#ff2d4b';
				}
				if($item['params']['navsdata'] == 1) {
					$categorys = store_fetchall_goods_category($sid, 1, false, 'parent', 'available');
					$categorys = array_slice($categorys, 0, $item['params']['navsnum']);
					$item['data'] = array();
					if(!empty($categorys)) {
						foreach($categorys as $cate) {
							$childid = rand(1000000000, 9999999999);
							$childid = "C{$childid}";
							$item['data'][$childid] = array(
								'text' => $cate['title'],
								'decoration' => empty($cate['description']) ? $cate['content'] : $cate['description'],
								'imgurl' => tomedia($cate['thumb']),
								'linkurl' => "/pages/store/goods?sid={$sid}&cid={$cate['id']}",
								'color' => '#333333',
								'dec_color' => '#a0a0a0'
							);
						}
					}
				} else {
					foreach($item['data'] as &$val) {
						$val['imgurl'] = tomedia($val['imgurl']);
					}
				}
				$item['data_num'] = count($item['data']);
				$item['row'] = ceil($item['params']['pagenum']/$item['params']['rownum']);
				if($mobile && $item['params']['showtype'] == 1 && $item['data_num'] > $item['params']['pagenum']) {
					$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
				}
			} elseif($item['id'] == 'coupon' && $mobile) {
				$item['sid'] = $sid;
				mload()->model('coupon');
				$coupon = coupon_collect_member_available($sid);
				if(!empty($coupon)) {
					$coupon['can_collect'] = 1;
					$coupon['endtime_cn'] = date('Y-m-d', $coupon['endtime']);
					$coupon['collect_percent'] = round($coupon['dosage'] / $coupon['amount'], 2) * 100;
				}
				$records = pdo_fetchall('select a.id,a.discount,a.condition,a.endtime,a.sid,b.title from' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b on a.couponid = b.id where a.uniacid = :uniacid and a.status = 1 and a.sid = :sid and a.uid = :uid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
				if(!empty($records)) {
					foreach($records as &$record) {
						$record['endtime_cn'] = date('Y-m-d', $record['endtime']);
					}
					$coupon['record'] = $records;
				}
				$item['data'] = $coupon;
			} elseif($item['id'] == 'onsale' && $mobile) {
				$item['sid'] = $sid;
				if($item['params']['goodsdata'] == '0') {
					if(!empty($item['data']) && is_array($item['data'])) {
						$goodsids = array();
						foreach($item['data'] as $data) {
							if(!empty($data['goods_id'])) {
								$goodsids[] = $data['goods_id'];
							}
						}
						if(!empty($goodsids)) {
							$item['data'] = array();
							$goodsids_str = implode(',', $goodsids);
							$goods = pdo_fetchall('select a.*, b.title as store_title from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') .
								" as b on a.sid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.status = 1 and a.id in ({$goodsids_str}) order by a.displayorder desc", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
						}
					}
				} elseif($item['params']['goodsdata'] == '1') {
					$item['data'] = array();
					$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status= 1';
					$params = array(
						':uniacid' => $_W['uniacid'],
						':sid' => $sid,
					);
					$limit = intval($item['params']['goodsnum']);
					$limit = $limit ? $limit : 4;
					$goods = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . " as b on a.goods_id = b.id {$condition} order by a.mall_displayorder desc limit {$limit}", $params);
					if(!empty($goods)) {
						$stores = pdo_fetchall('select distinct(a.sid),b.title as store_title,b.is_rest from ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.status = 1', array(':uniacid' => $_W['uniacid']), 'sid');
					}
				} elseif($item['params']['goodsdata'] == '2') {
					$item['data'] = array();
					$limit = intval($item['params']['goodsnum']);
					$limit = $limit ? $limit : 4;
					$goods = pdo_fetchall('select a.*, b.title as store_title from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') .
						" as b on a.sid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.status = 1 and a.is_hot = 1 order by a.displayorder desc limit {$limit}", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
				}
				if(!empty($goods)) {
					foreach($goods as $good) {
						$childid = rand(1000000000, 9999999999);
						$childid = "C{$childid}";
						$item['data'][$childid] = array(
							'goods_id' => $good['id'],
							'sid' => $good['sid'],
							'store_title' => $item['params']['goodsdata'] == '1' ? $stores[$good['sid']]['store_title'] : $good['store_title'],
							'thumb' => tomedia($good['thumb']),
							'title' => $good['title'],
							'price' => $good['price'],
							'old_price' => $good['old_price'] ? $good['old_price'] : $good['price'],
							'sailed' => $good['sailed'],
							'unitname' => empty($good['unitname']) ? '份' : $good['unitname'],
							'total' => ($good['total'] != -1 ? $good['total'] : '无限'),
							'discount' => ($good['old_price'] == 0 ? 0 : (round(($good['price'] / $good['old_price']) * 10, 1))),
							'comment_good_percent' => ($good['comment_total'] == 0 ? 0 : (round(($good['comment_good'] / $good['comment_total']) * 100, 2) . "%")),
						);
						if($item['params']['goodsdata'] == '1') {
							$item['data'][$childid]['price'] = $good['discount_price'];
							$item['data'][$childid]['old_price'] = $good['price'];
							$item['data'][$childid]['discount'] = round(($good['discount_price'] / $good['price'] * 10), 1);
						} elseif($good['svip_status'] == 1) {
							$item['data'][$childid]['svip_status'] = $good['svip_status'];
							$item['data'][$childid]['svip_price'] = $good['svip_price'];
							$item['data'][$childid]['price'] = $good['svip_price'];
							$item['data'][$childid]['discount'] = round(($good['svip_price'] / $item['data'][$childid]['old_price']) * 10, 1);
						}
					}
				}
			} elseif($item['id'] == 'evaluate' && $mobile) {
				$item['sid'] = $sid;
				$condition = " where uniacid = :uniacid and sid = :sid and status= 1 order by score desc limit 8";
				$params = array(
					':uniacid' => $_W['uniacid'],
					':sid' => $sid,
				);
				$item['data'] = array();
				$comments = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_comment') . $condition, $params);
				if(!empty($comments)) {
					foreach($comments as $comment) {
						if(!empty($comment['thumbs'])) {
							$comment['thumbs'] = iunserializer($comment['thumbs']);
							foreach($comment['thumbs'] as &$val) {
								$val = tomedia($val);
							}
						}
						$comment['data'] = iunserializer($comment['data']);
						$comment['goods_title'] = array_merge($comment['data']['good'], $comment['data']['bad']);
						$comment['avatar'] = tomedia($comment['avatar']);
						$childid = rand(1000000000, 9999999999);
						$childid = "C{$childid}";
						$item['data'][$childid] = array(
							'note' => $comment['note'],
							'thumbs' => $comment['thumbs'],
							'goods_title' => $comment['goods_title'],
							'goods_title_str' => implode(' ', $comment['goods_title']),
							'mobile' => str_replace(substr($comment['mobile'], 3, 6), '******', $comment['mobile']),
							'avatar' => $comment['avatar'],
							'reply' => $comment['reply'],
							'score_original' => $comment['score'],
							'score' => score_format($comment['score'] / 2),
							'replytime' => $comment['replytime'],
							'replytime_cn' => date('Y-m-d H:i', $comment['replytime']),
							'addtime' => $comment['addtime'],
							'addtime_cn' => date('Y-m-d H:i', $comment['addtime'])
						);
					}
				}
			} elseif($item['id'] == 'picturew' && !empty($item['data']) && $mobile) {
				foreach($item['data'] as &$v) {
					$v['imgurl'] = tomedia($v['imgurl']);
				}
				$item['data_num'] = count($item['data']);
				if(in_array($item['params']['row'], array('1','5','6'))) {
					$item['data'] = array_values($item['data']);
				} else {
					if($item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum']) {
						$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
						$item['style']['rows_num'] = ceil($item['params']['pagenum']/$item['params']['row']);
						$row_base_height = array(
							'2' => 122,
							'3' => 85,
							'4' => 65,
						);
						$item['style']['base_height'] = $row_base_height[$item['params']['row']];
					}
				}
			} elseif($item['id'] == 'gohomeActivity' && $mobile) {
				mload()->model('diy');
				$item['data'] = get_wxapp_gohome_goods($item, $mobile);
				if(empty($item['data'])) {
					unset($page['data']['items'][$itemid]);
				}
			} elseif($item['id'] == 'store_navs') {
				$item['params']['has_diypage'] = 0;
				if(check_plugin_perm('diypage')) {
					$item['params']['has_diypage'] = 1;
				} else {
					$item['params']['navsdata'] = 0;
					$item['params']['showtype'] = 0;
				}
				if(!isset($item['style']['dotbackground'])) {
					$item['style']['dotbackground'] = '#ff2d4b';
				}
				if($item['params']['navsdata'] == 1) {
					$categorys = store_fetchall_goods_category($sid, 1, false, 'parent', 'available');
					$categorys = array_slice($categorys, 0, $item['params']['navsnum']);
					$item['data'] = array();
					if(!empty($categorys)) {
						foreach($categorys as $cate) {
							$childid = rand(1000000, 9999999);
							$childid = "C{$childid}";
							$item['data'][$childid] = array(
								'text' => $cate['title'],
								'imgurl' => tomedia($cate['thumb']),
								'linkurl' => "/pages/shop/category?sid={$sid}&cid={$cate['id']}",
								'color' => '#333333',
							);
						}
					}
				} else {
					foreach($item['data'] as &$val) {
						$val['imgurl'] = tomedia($val['imgurl']);
					}
				}
				$item['data_num'] = count($item['data']);
				$item['row'] = ceil($item['params']['pagenum']/$item['params']['rownum']);
				if($mobile && $item['params']['showtype'] == 1 && $item['data_num'] > $item['params']['pagenum']) {
					$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
				}
			} elseif($item['id'] == 'store_waimai_goods' && $mobile) {
				$item['sid'] = $sid;
				$item['data'] = get_wxapp_store_waimai_goods($item, true);
				if(empty($item['data'])) {
					unset($page['data']['items'][$itemid]);
				}
			} elseif($item['id'] == 'store_goodsTab') {
				$item['sid'] = $sid;
				$item['data'] = get_wxapp_store_goodsTab($item);
				if(empty($item['data'])) {
					unset($page['data']['items'][$itemid]);
				} else {
					$page['is_has_goodsTab'] = 1;
				}
			} elseif($item['id'] == 'store_fixedsearch') {
				$page['is_has_location'] = 1;
				$item['params']['link'] = '/pages/shop/search?sid=' . $sid;
				$item['params']['linkto'] = 3;
				$page['fixedsearch'] = $item;
			} elseif($item['id'] == 'store_activity') {
				if(empty($item['params']['activitydata'])) {
					if(!empty($item['data'])) {
						foreach($item['data'] as &$val) {
							$val['imgurl'] = tomedia($val['imgurl']);
						}
						$item['data'] = array_values($item['data']);
					}
				}
			} elseif($item['id'] == 'store_notice') {
				$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
			} elseif($item['id'] == 'store_basic') {
				$page['is_has_storebasic'] = 1;
				$page['storebasic_style'] = $item['params']['style'];
			}
		}
	}
	return $page;
}

function get_wxapp_store_waimai_goods($item, $mobile = false) {
	global $_W;
	if($item['params']['goodsdata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$goodsids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['goods_id'])) {
					$goodsids[] = $data['goods_id'];
				}
			}
			if(!empty($goodsids)) {
				$item['data'] = array();
				$goodsids_str = implode(',', $goodsids);
				$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . " where uniacid = :uniacid and status = 1 and id in ({$goodsids_str}) order by FIELD(`id`, $goodsids_str)", array(':uniacid' => $_W['uniacid']));
				if(!empty($goods)) {
					$goods_categorys = pdo_fetchall('select id, is_showtime, start_time, end_time, week from ' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $item['sid']), 'id');
					mload()->model('goods');
					$config_svip_status = svip_status_is_available();
					foreach($goodsids as $goodsid) {
						foreach($goods as $good) {
							if($good['id'] == $goodsid) {
								$bargain_goods = pdo_fetch('select a.discount_price,a.max_buy_limit,b.status as bargain_status from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain'). ' as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $good['sid'], ':goods_id' => $good['id']));
								if(!empty($bargain_goods['bargain_status'])) {
									$good = array_merge($good, $bargain_goods);
								}
								$goods_category = $goods_categorys[$good['cid']];
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
								if(!$config_svip_status) {
									$good['svip_status'] = 0;
								}
								$good['config_svip_status'] = $config_svip_status;
								$good = goods_format($good);
								$good['goods_id'] = $good['id'];
								if(!$good['comment_total']) {
									$good['comment_good_percent'] = '0%';
								} else {
									$good['comment_good_percent'] = round($good['comment_good'] / $good['comment_total'] * 100, 2) . '%';
								}

								$childid =  rand(1000000, 9999999);
								$childid = "C{$childid}";
								$item['data'][$childid] = $good;
							};
						}
					}
				}
			}
		}
	} elseif($item['params']['goodsdata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		$item['data'] = array();
		$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status= 1';
		$params = array(
			':uniacid' => $_W['uniacid'],
			':sid' => $item['sid'],
		);
		$limit = intval($item['params']['goodsnum']);
		$limit = $limit ? $limit : 20;
		$goods = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . " as b on a.goods_id = b.id {$condition} order by a.mall_displayorder desc limit {$limit}", $params);

		if(!empty($goods)) {
			$goods_categorys = pdo_fetchall('select id, is_showtime, start_time, end_time, week from ' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $item['sid']), 'id');
			mload()->model('goods');
			$config_svip_status = svip_status_is_available();
			foreach($goods as &$good) {
				$goods_category = $goods_categorys[$good['cid']];
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
				if(!$config_svip_status) {
					$good['svip_status'] = 0;
				}
				$good['config_svip_status'] = $config_svip_status;
				$good = goods_format($good);
				$good['goods_id'] = $good['id'];
				if(!$good['comment_total']) {
					$good['comment_good_percent'] = '0%';
				} else {
					$good['comment_good_percent'] = round($good['comment_good'] / $good['comment_total'] * 100, 2) . '%';
				}

				$childid =  rand(1000000, 9999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = $good;
			}
		}
	}
	return $item['data'];
}


function get_wxapp_store_goodsTab($item, $mobile = false) {
	global $_W;
	if(!empty($item['data'])) {
		mload()->model('goods');
		$config_svip_status = svip_status_is_available();
		$goods_categorys = pdo_fetchall('select id, is_showtime, start_time, end_time, week from ' . tablename('tiny_wmall_goods_category') . 'where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $item['sid']), 'id');
		foreach($item['data'] as $goodsTabIndex => &$goodsTabItem) {
			$goodsTabItem['imgTitle'] = tomedia($goodsTabItem['imgTitle']);
			$tabGoods = array();
			if($goodsTabItem['goodsdata'] == '0') {
				//手动选择
				if(!empty($goodsTabItem['goods'])) {
					$goodsIds = array();
					foreach($goodsTabItem['goods'] as $goodsItem) {
						$goodsIds[] = $goodsItem['goods_id'];
					}
					$goodsIdsStr = implode(',', $goodsIds);
					$condition = " where a.uniacid = :uniacid and a.status = 1 and a.id in ({$goodsIdsStr}) order by FIELD(a.`id`, $goodsIdsStr) ";
					$params = array(
						':uniacid' => $_W['uniacid'],
					);
					$goods = pdo_fetchall('select a.*, b.id as store_id, b.agentid, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition, $params);
					if(!empty($goods)) {
						foreach($goods as $good) {
							if($good['sid'] != $good['store_id']) {
								continue;
							}
							$goods_category = $goods_categorys[$good['cid']];
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
							if(!$config_svip_status) {
								$good['svip_status'] = 0;
							}
							$good['config_svip_status'] = $config_svip_status;
							$good = goods_format($good);
							$good['goods_id'] = $good['id'];
							if(!$good['comment_total']) {
								$good['comment_good_percent'] = '0%';
							} else {
								$good['comment_good_percent'] = round($good['comment_good'] / $good['comment_total'] * 100) . '%';
							}

							$childid =  rand(1000000, 9999999);
							$childid = "C{$childid}";
							$tabGoods[$childid] = $good;
						}
					}
				}
			} else {
				//调用天天特价
				$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status= 1 ';
				$params = array(
					':uniacid' => $_W['uniacid'],
					':sid' => $item['sid']
				);
				$goods = pdo_fetchall('select a.discount_price, a.goods_id, a.discount_available_total, b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . " as b on a.goods_id = b.id {$condition} order by a.mall_displayorder desc ", $params);
				if(!empty($goods)) {
					foreach($goods as &$good) {
						$goods_category = $goods_categorys[$good['cid']];
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
						if(!$config_svip_status) {
							$good['svip_status'] = 0;
						}
						$good['config_svip_status'] = $config_svip_status;
						$good = goods_format($good);
						$good['goods_id'] = $good['id'];
						if(!$good['comment_total']) {
							$good['comment_good_percent'] = '0%';
						} else {
							$good['comment_good_percent'] = round($good['comment_good'] / $good['comment_total'] * 100) . '%';
						}

						$childid =  rand(1000000, 9999999);
						$childid = "C{$childid}";
						$tabGoods[$childid] = $good;
					}
				}

			}
			if(!empty($tabGoods)) {
				$goodsTabItem['goods'] = $tabGoods;
			} else {
				unset($item['data'][$goodsTabIndex]);
			}
		}
	}
	return $item['data'];
}

