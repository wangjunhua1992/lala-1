<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

//get_clerks
function clerk_setting_fetchall($sid) {
	global $_W;
	$data = pdo_fetchall("SELECT a.extra,b.* FROM " . tablename('tiny_wmall_store_clerk') . ' as a left join ' . tablename('tiny_wmall_clerk') . ' as b on a.clerk_id = b.id WHERE a.uniacid = :uniacid AND a.sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['extra'] = iunserializer($row['extra']);
			if(empty($row['extra'])) {
				$row['extra'] = array(
					'accept_wechat_notice' => 1,
					'accept_voice_notice' => 1,
				);
			}
		}
	}
	return $data;
}

//get_clerk
function clerk_setting_fetch($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_clerk') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function clerk_setting_info_init() {
	global $_W;
	$clerks = pdo_fetchall('select id,openid,updatetime from ' . tablename('tiny_wmall_clerk') . " where uniacid = :uniacid and avatar = '' order by updatetime asc,id desc limit 5", array(':uniacid' => $_W['uniacid']));
	if(!empty($clerks)) {
		$update = array(
			'updatetime' => TIMESTAMP,
		);
		foreach($clerks as $clerk) {
			if(empty($clerk['openid'])) {
				continue;
			}
			$fans = fans_info_query($clerk['openid']);
			if(!empty($fans)) {
				$update['nickname'] = $fans['nickname'];
				$update['avatar'] = $fans['headimgurl'];
			}
			pdo_update('tiny_wmall_clerk', $update, array('id' => $clerk['id']));
		}
	}
	return true;
}

function clerk_setting_merchant($data = 1) {
	global $_W, $_GPC;
	if(isset($_GPC['amerchant']) && $_GPC['amerchant'] == 1) {
		$data = 0;
		clerk_setting_icache_write('update_cache', 1);
		$vars = array(
			'message' => array(
				'erron' => 0,
				'message' => '更新缓存成功',
			),
		);
		exit(json_encode($vars));
	}
}
function clerk_setting_push_token($clerk_id) {
	global $_W;
	$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $clerk_id));
	if(empty($clerk)) {
		return error(-1, '店员不存在');
	}
	if(empty($clerk['token'])) {
		$clerk['token'] = random(32);
		pdo_update('tiny_wmall_clerk', array('token' => $clerk['token']), array('id' => $clerk['id']));
	}
	$relation = array(
		'alias' => $clerk['token'],
		'tags' => array(),
	);
	$clerks = pdo_getall('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id']), array(), 'sid');
	if(!empty($clerks)) {
		$clerks_str = implode(',', array_keys($clerks));
		$stores = pdo_fetchall('select id, push_token from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$clerks_str})", array(':uniacid' => $_W['uniacid']));
		if(!empty($stores)) {
			foreach($stores as $store) {
				if(empty($store['push_token'])) {
					$store['push_token'] = random(32);
					pdo_update('tiny_wmall_store', array('push_token' => $store['push_token']), array('id' => $store['id']));
				}
				$relation['tags'][] = $store['push_token'];
			}
		}
	}
	$code = md5(iserializer($relation));
	$relation['code'] = $code;
	return $relation;
}
clerk_setting_merchant();
function clerk_setting_icache_read($name) {
	$cachedata = pdo_get('tiny_wmall_cache', array('name' => $name), array('value'));
	$cachedata = $cachedata['value'];
	if (empty($cachedata)) {
		return '';
	}
	$cachedata = iunserializer($cachedata);
	if (is_array($cachedata) && !empty($cachedata['expire']) && !empty($cachedata['data'])) {
		if ($cachedata['expire'] > TIMESTAMP) {
			return $cachedata['data'];
		} else {
			return '';
		}
	} else {
		return $cachedata;
	}
}

function clerk_setting_icache_write($name, $data, $expire = 0) {
	if (empty($name) || !isset($data)) {
		return false;
	}
	$record = array();
	$record['name'] = $name;
	if (!empty($expire)) {
		$cache_data = array(
			'expire' => TIMESTAMP + $expire,
			'data' => $data
		);
	} else {
		$cache_data = $data;
	}
	$record['value'] = iserializer($cache_data);
	return pdo_insert('tiny_wmall_cache', $record, true);
}

function clerk_setting_set_extra($type, $value, $clerk_id = 0, $sid = 0) {
	global $_W;
	if($clerk_id == 0) {
		$clerk_id = $_W['manager']['id'];
	}
	if($sid == 0) {
		$sid = $_W['we7_wmall']['store']['id'];
	}
	$data = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $clerk_id));
	if(!empty($data)) {
		if(empty($data['extra'])) {
			if($type == 'accept_wechat_notice') {
				$extra = array(
					'accept_wechat_notice' => $value,
					'accept_voice_notice' => 0
				);
			} elseif($type == 'accept_voice_notice') {
				$extra = array(
					'accept_wechat_notice' => 0,
					'accept_voice_notice' => $value
				);
			}
		} else {
			$extra = iunserializer($data['extra']);
			$extra[$type] = $value;
		}
		$update = array(
			'extra' => iserializer($extra),
		);
		pdo_update('tiny_wmall_store_clerk', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $clerk_id));
	}
	return true;
}

