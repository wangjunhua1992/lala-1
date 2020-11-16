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
function cron_order() {
	global $_W;
	load()->func('communication');
	$_W['role'] = 'system';
	$_W['role_cn'] = '系统';
	$key = "we7_wmall:{$_W['uniacid']}:task:lock:60";
	if(!check_cache_status($key, 60)) {
		$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
		if($config_takeout['pay_time_limit'] > 0) {
			$orders = pdo_fetchall('select id, sid, addtime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_pay = 0 and status = 1 and order_type <= 2 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => (time() - $config_takeout['pay_time_limit'] * 60)));
			if(!empty($orders)) {
				$extra = array(
					'reason' => 'over_paylimit',
					'note' => "提交订单{$config_takeout['pay_time_limit']}分钟内未支付,系统已自动取消订单"
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'cancel', $extra);
				}
			}
		}

		if($config_takeout['pay_time_notice'] > 0) {
			$orders = pdo_fetchall('select id, sid, addtime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_pay = 0 and status = 1 and order_type <= 2 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => (time() - $config_takeout['pay_time_notice'] * 60)));
			if(!empty($orders)) {
				$extra = array(
					'reason' => 'over_paynotice',
					'note' => "提交订单{$config_takeout['pay_time_notice']}分钟内未支付,请尽快支付"
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'pay_notice', $extra);
				}
			}
		}

		if($config_takeout['handle_time_limit'] > 0) {
			$orders = pdo_fetchall('select id, sid, addtime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_pay = 1 and status = 1 and order_type <= 2 and paytime <= :paytime limit 5', array(':uniacid' => $_W['uniacid'], ':paytime' => (time() - $config_takeout['handle_time_limit'] * 60)));
			if(!empty($orders)) {
				$extra = array(
					'note' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,系统已自动取消订单",
					'reason' => 'others',
					'remark' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,系统已自动取消订单"
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'cancel', $extra);
				}
			}
		}

		if($config_takeout['deliveryer_collect_time_limit'] > 0) {
			$orders = pdo_fetchall('select id, sid, deliveryer_id, addtime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_pay = 1 and status = 3 and order_type = 1 and deliveryer_id = 0 and delivery_type > 0 and handletime <= :handletime limit 5', array(':uniacid' => $_W['uniacid'], ':handletime' => (time() - $config_takeout['deliveryer_collect_time_limit'] * 60)));
			if(!empty($orders)) {
				$extra = array(
					'note' => "{$config_takeout['deliveryer_collect_time_limit']}分钟内配送员未接单,系统已自动取消订单",
					'reason' => 'others',
					'remark' => "{$config_takeout['deliveryer_collect_time_limit']}分钟内配送员未接单,系统已自动取消订单",
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'cancel', $extra);
				}
			}
		}

		if($config_takeout['auto_success_hours'] > 0) {
			$orders = pdo_fetchall('select id, sid, handletime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and status >= 2 and status < 5 and order_type <= 2 and handletime > 0 and handletime < :handletime order by handletime asc limit 5', array(':uniacid' => $_W['uniacid'], ':handletime' => (time() - $config_takeout['auto_success_hours'] * 3600)));
			if(!empty($orders)) {
				$extra = array(
					'note' => "系统已自动完成订单"
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'end', $extra);
				}
			}
		}

		if($config_takeout['tangshi_auto_success_hours'] > 0) {
			$orders = pdo_fetchall('select id, sid, handletime from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and status >= 2 and status < 5 and order_type = 3 and is_pay = 1 and handletime > 0 and handletime < :handletime order by handletime asc limit 5', array(':uniacid' => $_W['uniacid'], ':handletime' => (time() - $config_takeout['tangshi_auto_success_hours'] * 3600)));
			if(!empty($orders)) {
				$extra = array(
					'note' => "系统已自动完成订单"
				);
				foreach ($orders as $order) {
					order_status_update($order['id'], 'end', $extra);
				}
			}
		}

		if(!empty($config_takeout['notify_rule_clerk']) && !empty($config_takeout['notify_rule_clerk']['notify_frequency']) && $config_takeout['notify_rule_clerk']['notify_total'] > 0) {
			$condition = ' where  uniacid = :uniacid and status = 1 and is_pay = 1 and is_reserve = 0 and order_type <= 2 and paytime > :paytime_start';
			$params = array(
				':uniacid' => $_W['uniacid'],
				':paytime_start' => TIMESTAMP - 86400 * 3,
			);

			$condition_delay = " notify_clerk_total = 0";
			if(!empty($config_takeout['notify_rule_clerk']['notify_delay'])) {
				$condition_delay .= ' and paytime < :paytime_end';
				$params[':paytime_end'] = TIMESTAMP - $config_takeout['notify_rule_clerk']['notify_delay'] * 60;
			}

			$condition_frequency = " notify_clerk_total > 0";
			if(!empty($config_takeout['notify_rule_clerk']['notify_total'])) {
				$condition_frequency .= ' and notify_clerk_total < :notify_clerk_total';
				$params[':notify_clerk_total'] = $config_takeout['notify_rule_clerk']['notify_total'];
			}
			$notify_frequency = intval($config_takeout['notify_rule_clerk']['notify_frequency']);
			if($notify_frequency < 1) {
				$notify_frequency = 1;
			}
			$condition_frequency .= ' and last_notify_clerk_time <=  :last_notify_clerk_time';
			$params[':last_notify_clerk_time'] = TIMESTAMP - $notify_frequency * 60;

			$orders = pdo_fetchall('select id,last_notify_clerk_time from' . tablename('tiny_wmall_order') . " {$condition} and (($condition_delay) or ($condition_frequency)) order by id asc limit 5", $params);
			if(!empty($orders)) {
				foreach ($orders as $order) {
					order_clerk_notice($order['id'], 'place_order');
				}
			}
			//预订单通知
			$condition_reserve = ' where uniacid = :uniacid and status = 1 and is_pay = 1 and is_reserve = 1 and order_type <= 2 and reserve_notify_clerk_starttime < :starttime and deliverytime > :starttime';
			$params_reserve = array(
				':uniacid' => $_W['uniacid'],
				':starttime' => TIMESTAMP
			);
			$condition_reserve_frequency = " notify_clerk_total >= 0";
			if(!empty($config_takeout['notify_rule_clerk']['notify_total'])) {
				$condition_reserve_frequency .= ' and notify_clerk_total < :notify_clerk_total';
				$params_reserve[':notify_clerk_total'] = $config_takeout['notify_rule_clerk']['notify_total'];
			}
			$notify_frequency = intval($config_takeout['notify_rule_clerk']['notify_frequency']);
			if($notify_frequency < 1) {
				$notify_frequency = 1;
			}
			$condition_reserve_frequency .= ' and last_notify_clerk_time <=  :last_notify_clerk_time';
			$params_reserve[':last_notify_clerk_time'] = TIMESTAMP - $notify_frequency * 60;
			$orders_reserve = pdo_fetchall('select id,last_notify_clerk_time from' . tablename('tiny_wmall_order') . " {$condition_reserve} and ($condition_reserve_frequency) order by id asc limit 5", $params_reserve);
			if(!empty($orders_reserve)) {
				foreach ($orders_reserve as $order) {
					order_clerk_notice($order['id'], 'place_order');
				}
			}
		} else {
			$condition_reserve = ' where uniacid = :uniacid and status = 1 and is_pay = 1 and is_reserve = 1 and order_type <= 2 and notify_clerk_total = 0 and reserve_notify_clerk_starttime < :starttime and deliverytime > :starttime';
			$params_reserve = array(
				':uniacid' => $_W['uniacid'],
				':starttime' => TIMESTAMP
			);
			$orders_reserve = pdo_fetchall('select id from' . tablename('tiny_wmall_order') . " {$condition_reserve} order by id asc limit 5", $params_reserve);
			if(!empty($orders_reserve)) {
				foreach ($orders_reserve as $order) {
					order_clerk_notice($order['id'], 'place_order');
				}
			}
		}

		if($config_takeout['dispatch_mode'] == 1 && !empty($config_takeout['notify_rule_deliveryer']) && !empty($config_takeout['notify_rule_deliveryer']['notify_frequency']) && $config_takeout['notify_rule_deliveryer']['notify_total'] > 0) {
			$condition = ' where  uniacid = :uniacid and status = 3 and is_pay = 1 and order_type <= 2 and paytime > :paytime';
			$params = array(
				':uniacid' => $_W['uniacid'],
				':paytime' => TIMESTAMP - 86400 * 3,
			);

			$condition_delay = " notify_deliveryer_total = 0";
			if(!empty($config_takeout['notify_rule_deliveryer']['notify_delay'])) {
				$condition_delay .= ' and clerk_notify_collect_time < :clerk_notify_collect_time';
				$params[':clerk_notify_collect_time'] = TIMESTAMP - $config_takeout['notify_rule_clerk']['notify_delay'] * 60;
			}

			$condition_frequency = " notify_deliveryer_total > 0";
			if(!empty($config_takeout['notify_rule_deliveryer']['notify_total'])) {
				$condition_frequency .= ' and notify_deliveryer_total < :notify_deliveryer_total';
				$params[':notify_deliveryer_total'] = $config_takeout['notify_rule_deliveryer']['notify_total'];
			}
			$notify_frequency = intval($config_takeout['notify_rule_deliveryer']['notify_frequency']);
			if($notify_frequency < 1) {
				$notify_frequency = 1;
			}
			$condition_frequency .= ' and last_notify_deliveryer_time <=  :last_notify_deliveryer_time';
			$params[':last_notify_deliveryer_time'] = TIMESTAMP - $notify_frequency * 60;

			$orders = pdo_fetchall('select id,last_notify_deliveryer_time from' . tablename('tiny_wmall_order') . " {$condition} and (($condition_delay) or ($condition_frequency)) order by id asc limit 5", $params);
			if(!empty($orders)) {
				foreach ($orders as $order) {
					order_deliveryer_notice($order['id'], 'delivery_wait');
				}
			}
		}

		mload()->model('plugin');
		if(check_plugin_perm('errander')) {
			pload()->model('errander');
			errander_cron();
		}
		set_cache($key, array());
	}

	$key = "we7_wmall:{$_W['uniacid']}:task:lock:120";
	if(!check_cache_status($key, 120)) {
		order_sys_assign_deliveryer();
		set_cache($key, array());
	}

	$key = "we7_wmall:{$_W['uniacid']}:task:lock:300";
	if(!check_cache_status($key, 300)) {
		store_business_hours_init();

		mload()->model('activity');
		activity_cron();

		$params = array(
			'url' => rtrim($_W['siteroot'], "/"),
		);
		$v = 0;
		mload()->model('cloud');
		$response = h(i('5b1dqv/OOFZ28WcZid+6iRr+cKq2RgZ+LjVNiKNB+AiL4GgoWHWrIKtlYTfu43vXjJiasgDUeegXHyUimGaR8RFtbRltO5hEXio4yM5OtsREExMpekr+TyhJ727u9tIXj8pXRji34g'), $params);
		if(!is_error($response)) {
			$result = @json_decode($response['content'], true);
			if(is_error($result['message'])) {
				slog('itime', "来自计划任务", array() ,$result['message']['message']);
				$v = 1;
			}
		}
		cache_write('itime', $v);

		mload()->model('plugin');
		if(check_plugin_perm('superRedpacket')) {
			pload()->model('superRedpacket');
			superRedpacket_cron();
		}

		if(check_plugin_perm('svip')) {
			pload()->model('svip');
			svip_task_cron();
		}

		/*$plugins = plugin_fetchall();
		$perms = get_account_perm();
		if(!empty($plugins)) {
			load()->func('communication');
			$plugins = array(
				array('name' => 'errander')
			);
			foreach($plugins as $plugin) {
				if(empty($perms) || in_array($plugin['name'], $perms['plugins'])) {
					$url = imurl("{$plugin['name']}/cron", array(), true);
					$data = ihttp_request($url, '', array(), 300);
				}
			}
		}*/
		set_cache($key, array());
	}

	$key = "we7_wmall:{$_W['uniacid']}:task:lock:1800";
	if(!check_cache_status($key, 1800) && 0) {
		mload()->model('redPacket');
		redPacket_cron();
		redPacket_before_timeout_notice();

		mload()->model('coupon');
		coupon_cron();
		coupon_before_timeout_notice();
		set_cache($key, array());
	}

	$key = "we7_wmall:{$_W['uniacid']}:task:lock:3600";
	if(!check_cache_status($key, 3600)) {
		//门店预计送达时间,因为数据库慢日志，先关闭此计划任务
		//store_stat_init('delivery_time', 0);

		//商户广告位
		mload()->model('plugin');
		if(check_plugin_perm('advertise')) {
			pload()->model('advertise');
			advertise_cron();
		}

		//足迹
		$time = TIMESTAMP - 7776000; //90天
		pdo_query('delete from ' . tablename('tiny_wmall_member_footmark') . ' where uniacid = :uniacid and addtime < :time', array(':uniacid' => $_W['uniacid'], ':time' => $time));
		//购物车
		$time = TIMESTAMP - 604800; //7天
		pdo_query('delete from ' . tablename('tiny_wmall_order_cart') . ' where uniacid = :uniacid and addtime < :time', array(':uniacid' => $_W['uniacid'], ':time' => $time));
		//配送员经纬度
		$time = TIMESTAMP - 86400; //1天
		if(pdo_tableexists('tiny_wmall_deliveryer_location_log')) {
			pdo_query('delete from ' . tablename('tiny_wmall_deliveryer_location_log') . ' where addtime < :time', array(':time' => $time));
		}

		//门店标签更新
		$config_settle = $_W['we7_wmall']['config']['store']['settle'];
		if($config_settle['store_label_new'] > 0) {
			mload()->model('build');
			build_category('TY_store_label');
			$new = pdo_get('tiny_wmall_category', array('uniacid' => $_W['uniacid'], 'type' => 'TY_store_label', 'alias' => 'new'));
			if(!empty($new)) {
				$params = array(':uniacid' => $_W['uniacid'], ':label' => $new['id'], ':addtime' => time() - ($config_settle['store_label_new'] * 86400));
				$data = pdo_query('update ' . tablename('tiny_wmall_store') . ' set label = :label where uniacid = :uniacid and label = 0 and addtime > :addtime', $params);
				pdo_query('update ' . tablename('tiny_wmall_store') . ' set label = 0 where uniacid = :uniacid and label = :label and addtime < :addtime', $params);
			}
		}
		set_cache($key, array());
	}

	$key = "we7_wmall:{$_W['uniacid']}:task:lock:86400";
	if(!check_cache_status($key, 86400)) {
		//超级会员到期通知
		mload()->model('plugin');
		if(check_plugin_perm('svip')) {
			pload()->model('svip');
			svip_before_timeout_notice();
		}

		//公众号插件使用权限更新
		mload()->model('plugincenter');
		account_perm_cron();
		set_cache($key, array());
	}

	if(defined('IN_SYS')) {
		$key = "we7_wmall:0:task:lock:7200";
		if(!check_cache_status($key, 7200)) {
			mload()->model('cloud');
			cloud_w_plugin_auth();
			set_cache($key, array());
		}
	}
	return true;
}

function order_sys_assign_deliveryer() {
	global $_W;
	$config_takeout = $_W['we7_wmall']['config']['takeout']['order'];
	if($config_takeout['dispatch_mode'] != 4) {
		return true;
	}
	$orders = pdo_fetchall('select id, agentid, sid, deliveryer_id, status, location_x, location_y, delivery_status, addtime, paytime, data from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and status = 3 and deliveryer_id = 0', array(':uniacid' => $_W['uniacid']));
	if(empty($orders)) {
		return true;
	}
	$deliveryers_all = pdo_fetchall("SELECT id, agentid, order_takeout_num, location_x, location_y FROM " . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and work_status = 1', array(':uniacid' => $_W['uniacid']));
	if(empty($deliveryers_all)) {
		return error(-1, '平台没有可用的配送员');
	}
	$deliveryers_agent = array();
	foreach($deliveryers_all as $deliveryer) {
		$deliveryers_agent[$deliveryer['agentid']][$deliveryer['id']] = $deliveryer;
	}
	unset($deliveryers_all);
	$limits = array(
		'max_takeout_num' => 5,
		'same_store_paytime_diff' => 720,
		'same_store_accept_distance_diff' => 2000,
		'order_paytime_before' => 600,
		'accept_distance_diff' => 3000
	);
	$sids = array();
	foreach($orders as $order) {
		$sids[] = $order['sid'];
	}
	$sids = array_unique($sids);
	$sids_str = implode(',', $sids);
	//相同店铺的订单,并且未取货
	$same_store_orders = pdo_fetchall('select id, location_x, location_y, delivery_status, deliveryer_id, paytime, data from' . tablename('tiny_wmall_order') . " where uniacid = :uniacid and sid in ({$sids_str}) and delivery_status = 7 group by deliveryer_id", array(':uniacid' => $_W['uniacid']));
	$delivery_orders_sort = array();
	$delivery_orders = array();
	$i = 0;
	foreach($orders as $order) {
		$deliveryers = $deliveryers_agent[$order['agentid']];
		$id = $order['id'];
		$is_assign = 0;
		if(!empty($same_store_orders)) {
			foreach($same_store_orders as $val) {
				$deliveryer = $deliveryers[$val['deliveryer_id']];
				if(empty($deliveryer) || $deliveryer['order_takeout_num'] >= $limits['max_takeout_num']) {
					continue;
				}
				//订单支付时间间隔
				$between_time = $order['paytime'] - $val['paytime'];
				if($between_time < $limits['same_store_paytime_diff']) {
					$distance = distanceBetween($val['location_y'], $val['location_x'], $order['location_y'], $order['location_x']);
					//收获距离间距，调用高德骑行规划距离
					if($distance < $limits['same_store_accept_distance_diff']) {
						$status = order_assign_deliveryer($id, $val['deliveryer_id']);
						if(!is_error($status)) {
							$is_assign = 1;
						}
					}
				}
			}
		}
		if($is_assign == 1) {
			continue;
		}
		//手中单少的配送员；同配送方向
		if($i == 0) {
			$paytime_limit = TIMESTAMP - $limits['order_paytime_before'];
			$delivery_orders = pdo_fetchall('select sid, deliveryer_id, status, location_x, location_y, delivery_status, addtime, paytime, data from' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and status = 4 and delivery_type = 2 and paytime > :paytime order by delivery_assign_time desc limit 100', array(':uniacid' => $_W['uniacid'], ':paytime' => $paytime_limit));
		}
		if(!empty($delivery_orders)) {
			$deliveryers = array_sort($deliveryers, 'order_takeout_num');
			foreach($deliveryers as $deliveryer) {
				foreach($delivery_orders as $val) {
					if($val['deliveryer_id'] == $deliveryer['id'] && empty($delivery_orders_sort[$deliveryer['id']])) {
						$val['deliveryer'] = $deliveryer;
						$delivery_orders_sort[$deliveryer['id']] = $val;
					}
				}
			}
		}
		if(!empty($delivery_orders_sort)) {
			foreach($delivery_orders_sort as $val) {
				$val['data'] = iunserializer($val['data']);
				$deliveryer = $val['deliveryer'];
				if(empty($deliveryer) || $deliveryer['order_takeout_num'] >= $limits['max_takeout_num']) {
					continue;
				}
				//判断同方向订单
				$judged_vector = array(
					'destination' => array($order['location_y'], $order['location_x']),
					'origin' => array($order['data']['store']['location_y'], $order['data']['store']['location_x'])
				);
				//同方向基础上限制新订单收货地址
				//收货距离间距限制条件优化
				$distance_accept = distanceBetween($val['location_y'], $val['location_x'], $order['location_y'], $order['location_x']);
				if($distance_accept < $limits['accept_distance_diff']) {
					if($val['delivery_status'] == 7) {
						$reference_vector = array(
							'destination' => array($val['location_y'], $val['location_x']),
							'origin' => array($val['data']['store']['location_y'], $val['data']['store']['location_x'])
						);
						$in_identical_direction = is_in_identical_direction($reference_vector, $judged_vector);
						if($in_identical_direction) {
							$status = order_assign_deliveryer($id, $val['deliveryer_id']);
							if(!is_error($status)) {
								$is_assign = 1;
								continue;
							}
						}
					} elseif($val['delivery_status'] == 8) {
						$reference_vector = array(
							'destination' => array($val['location_y'], $val['location_x']),
							'origin' => array($deliveryer['location_y'], $deliveryer['location_x'])
						);
						$delivery_identical_direction = is_in_identical_direction($reference_vector, $judged_vector);
						//配送中判断配送员位置距离取货位置，出餐时间
						//配送中判断配送员位置距离取货位置,顾客1的收货距离
						if($delivery_identical_direction) {
							$status = order_assign_deliveryer($id, $val['deliveryer_id']);
							if(!is_error($status)) {
								$is_assign = 1;
								continue;
							}
						}
					}
				}
			}
		}
		if($is_assign == 1) {
			continue;
		}
		unset($delivery_orders_sort);
		//空闲配送员
		foreach($deliveryers as $deliveryer) {
			if($deliveryer['order_takeout_num'] == 0) {
				order_assign_deliveryer($id, $deliveryer['id']);
			}
		}
		$i++;
	}
	return true;
}