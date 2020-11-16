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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '商户配送策略';
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $agentid;
	}

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' and status = :status ';
		$params[':status'] = $status;
	}

	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-7 day', $today);
		$endtime = $today + 86399;
	}

	$condition .= ' and addtime >= :starttime and addtime < :endtime ';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;

	$keywords = trim($_GPC['keywords']);
	if(!empty($keywords)) {
		$condition .= " and title like '%{$keywords}%' ";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_delivery_policy') . $condition, $params);
	$policys = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_delivery_policy'). " {$condition} order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if(!empty($policys)) {
		foreach($policys as &$value) {
			$value['addtime_cn'] = date('Y-m-d H:i', $value['addtime']);
			$value['starttime_cn'] = $value['starttime'] > 0 ? date('Y-m-d H:i', $value['starttime']) : '未开始';
			$value['endtime_cn'] = $value['endtime'] > 0 ? date('Y-m-d H:i', $value['endtime']) : '未结束';
			$value['policy'] = iunserializer($value['policy']);
			$value['sign_cn'] = $value['policy']['change_type'] == 'plus' ? '增加' : '减少';
		}
	}

	$status_arr = store_policy_status();
}

elseif($op == 'post') {
	$_W['page']['title'] = '添加商户配送策略';
	
	$is_exist = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_delivery_policy') . ' where uniacid = :uniacid and agentid = :agentid and status < 3', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'])));
	if($is_exist > 0) {
		imessage(error(-1, '存在已创建未执行或者正在执行中的策略，删除未执行的策略或者结束执行中的策略后才可以创建新的策略'), iurl('merchant/policy/list'), 'error');
	}

	$stores = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid and delivery_mode = 2 and status != 4', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	if($_W['ispost']) {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'title' => trim($_GPC['title']),
			'addtime' => TIMESTAMP,
			'status' => 1,
		);
		$policy = array(
			'change_type' => trim($_GPC['change_type']),
			'send_price' => floatval($_GPC['send_price']),
			'delivery_price' => floatval($_GPC['delivery_price']),
			'pre_km_fee' => floatval($_GPC['pre_km_fee']),
		);
		if(!in_array($policy['change_type'], array('plus', 'minus'))) {
			imessage(error(-1, '请选择变化方向'), '', 'ajax');
		}
		if($policy['send_price'] < 0) {
			imessage(error(-1, '起送价变化值不能小于零'), '', 'ajax');
		}
		if($policy['delivery_price'] < 0) {
			imessage(error(-1, '配送费变化值不能小于零'), '', 'ajax');
		}
		if($policy['pre_km_fee'] < 0) {
			imessage(error(-1, '每增加1公里变化值不能小于零'), '', 'ajax');
		}
		$insert['policy'] = iserializer($policy);
		$sync = intval($_GPC['sync']);
		if($sync == 1) {
			$store_ids = array_keys($stores);
		} elseif($sync == 2) {
			$store_ids = array_map('intval', $_GPC['store_ids']);
		}
		if(empty($store_ids)) {
			imessage(error(-1, '请选择要同步的商家'), '', 'ajax');
		}
		$insert['store'] = ',' . implode($store_ids, ',') . ',';
		pdo_insert('tiny_wmall_store_delivery_policy', $insert);
		imessage(error(0, '商户配送策略添加成功'), iurl('merchant/policy/list'), 'ajax');
	}
}

elseif($op == 'start') {
	$id = intval($_GPC['id']);
	$policy = pdo_get('tiny_wmall_store_delivery_policy', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($policy)) {
		imessage(error(-1, '商户配送策略不存在或已删除'), '', 'ajax');
	}
	if($policy['status'] == 2) {
		imessage(error(-1, '商户配送策略已开始执行'), '', 'ajax');
	}
	if($policy['status'] == 3) {
		imessage(error(-1, '商户配送策略已执行结束'), '', 'ajax');
	}
	$policy['policy'] = iunserializer($policy['policy']);
	$sign = $policy['policy']['change_type'] == 'minus' ? '-' : '+';
	$send_price = $sign . floatval($policy['policy']['send_price']);
	$delivery_price = $sign . floatval($policy['policy']['delivery_price']);
	$pre_km_fee = $sign . floatval($policy['policy']['pre_km_fee']);

	$store_ids = trim($policy['store'], ',');
	$store_ids = explode(',', $store_ids);
	if(!empty($store_ids)) {
		foreach($store_ids as $sid) {
			$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('id', 'delivery_mode', 'delivery_fee_mode', 'delivery_areas', 'delivery_price', 'send_price'));
			if(empty($store) || $store['delivery_mode'] != 2) {
				continue;
			}
			$original = array();
			$changed = array();
			if($store['delivery_fee_mode'] == 2) {
				$original['send_price'] = $store['send_price'];
				$original['delivery_price'] = $store['delivery_price'];
				
				$changed['send_price'] = floatval($store['send_price']) + $send_price;
				$changed['delivery_price'] = iunserializer($store['delivery_price']);
				$changed['delivery_price']['start_fee'] = floatval($changed['delivery_price']['start_fee']) + $delivery_price;
				$changed['delivery_price']['pre_km_fee'] = floatval($changed['delivery_price']['pre_km_fee']) + $pre_km_fee;
				$changed['delivery_price'] = iserializer($changed['delivery_price']);
			} elseif($store['delivery_fee_mode'] == 3) {
				$original['delivery_areas'] = $store['delivery_areas'];

				$changed['delivery_areas'] = iunserializer($store['delivery_areas']);
				if(!empty($changed['delivery_areas'])) {
					foreach($changed['delivery_areas'] as &$delivery_area) {
						$delivery_area['send_price'] = floatval($delivery_area['send_price']) + $send_price;
						$delivery_area['delivery_price'] = floatval($delivery_area['delivery_price']) + $delivery_price;
					}
				}
				$changed['delivery_areas'] = iserializer($changed['delivery_areas']);
			} else {
				$original['send_price'] = $store['send_price'];
				$original['delivery_price'] = $store['delivery_price'];

				$changed['send_price'] = floatval($store['send_price']) + $send_price;
				$changed['delivery_price'] = floatval($store['delivery_price']) + $delivery_price;
			}
			pdo_update('tiny_wmall_store', $changed, array('uniacid' => $_W['uniacid'], 'id' => $store['id']));
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'policyid' => $policy['id'],
				'sid' => $store['id'],
				'original' => iserializer($original),
				'changed' => iserializer($changed),
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_store_delivery_policy_log', $insert);
		}
	}
	$update = array(
		'status' => 2,
		'starttime' => TIMESTAMP
	);
	pdo_update('tiny_wmall_store_delivery_policy', $update, array('uniacid' => $_W['uniacid'], 'id' => $policy['id']));
	imessage(error(0, '商户配送策略执行成功'), iurl('merchant/policy/list'), 'ajax');
}

elseif($op == 'end') {
	$id = intval($_GPC['id']);
	$policy = pdo_get('tiny_wmall_store_delivery_policy', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($policy)) {
		imessage(error(-1, '商户配送策略不存在或已删除'), '', 'ajax');
	}
	if($policy['status'] == 1) {
		imessage(error(-1, '商户配送策略还未执行'), '', 'ajax');
	}
	if($policy['status'] == 3) {
		imessage(error(-1, '商户配送策略已执行结束'), '', 'ajax');
	}
	$logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_delivery_policy_log') . ' where uniacid = :uniacid and policyid = :policyid', array(':uniacid' => $_W['uniacid'], ':policyid' => $policy['id']));
	if(!empty($logs)) {
		foreach($logs as $log) {
			$log['original'] = iunserializer($log['original']);
			$update = array();
			if(!empty($log['original'])) {
				foreach($log['original'] as $key => $value) {
					$update[$key] = $value;
				}
			}
			if(!empty($update)) {
				pdo_update('tiny_wmall_store', $update, array('uniacid' => $_W['uniacid'], 'id' => $log['sid']));
			}
		}
	}
	$update = array(
		'status' => 3,
		'endtime' => TIMESTAMP
	);
	pdo_update('tiny_wmall_store_delivery_policy', $update, array('uniacid' => $_W['uniacid'], 'id' => $policy['id']));
	imessage(error(0, '商户配送策略结束成功'), iurl('merchant/policy/list'), 'ajax');
}

elseif($op == 'delete') {
	$id = intval($_GPC['id']);
	$policy = pdo_get('tiny_wmall_store_delivery_policy', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($policy)) {
		imessage(error(-1, '商户配送策略不存在或已删除'), '', 'ajax');
	}
	if($policy['status'] == 2) {
		imessage(error(-1, '商户配送策略执行中不可删除'), '', 'ajax');
	}
	pdo_delete('tiny_wmall_store_delivery_policy', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '商户配送策略删除成功'), iurl('merchant/policy/list'), 'ajax');
}

include itemplate('merchant/policy');
