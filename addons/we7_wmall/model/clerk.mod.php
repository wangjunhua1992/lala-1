<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

//get_clerks
function clerk_fetchall($sid) {
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
function clerk_fetch($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_clerk') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function clerk_info_init() {
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

function clerk_push_token($clerk_id) {
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

function clerk_set_extra($type, $value, $clerk_id = 0, $sid = 0) {
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

function Jpush_clerk_send($title, $alert, $extras = array(), $audience = '', $platform = 'all') {
	global $_W;
	$config = $_W['we7_wmall']['config']['app']['manager'];
	if(empty($config['push_key']) || empty($config['push_secret'])) {
		return error(-1, 'key或secret不完善');
	}
	if(empty($config['serial_sn'])) {
		return error(-1, 'app序列号不完善');
	}
	$notify_routers = array(
		'place_order' => 'new',
		'remind' => 'remind',
		'tablecall' => 'new',
	);
	$extras['resource'] = "{$_W['siteroot']}/addons/we7_wmall/resource/mp3/{$_W['uniacid']}/{$config['phonic'][$notify_routers[$extras['notify_type']]]}";
	$sound_router = array(
		'takeout' => array(
			'place_order' => 'widget/res/sound/orderSound.wav',
			'remind' => 'widget/res/sound/remindSound.wav',
			'cancel' => 'widget/res/sound/cancelSound.wav',
			'refund' => 'widget/res/sound/refundSound.wav',
		),
		'tangshi' => array(
			'tablecall' => '',
		),
		'gohome' => array(
			'place_order' => 'widget/res/sound/gohomeOrderSound.wav',
			'cancel' => 'widget/res/sound/gohomeCancelSound.wav',
		)
	);
	$extras['order_from'] = empty($extras['order_from']) ? 'takeout' : $extras['order_from'];
	$sound = $sound_router[$extras['order_from']][$extras['notify_type']];
	if(empty($sound)) {
		$sound = 'default';
	}
	$tag = trim($config['serial_sn']);
	if(empty($audience)) {
		$audience = array(
			'tag' => array(
				$tag
			)
		);
	}
	$extras_orginal = array(
		'voice_play_type' => 2,
		'notify_type' => $notify_routers[$extras['notify_type']]
	);
	$extras = array_merge($extras, $extras_orginal);
	$jpush = array(
		'platform' => 'android',
		'audience' => $audience,
		'message' => array(
			'msg_content' => $alert,
			'title' => $title,
			'extras' => $extras
		),
	);
	load()->func('communication');
	$extra = array(
		'Authorization' => "Basic " . base64_encode("{$config['push_key']}:{$config['push_secret']}")
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('managerappJpush', '商家App极光推送(andriod)通知店员', $jpush, $return['message']);
	}
	if(empty($config['ios_build_type'])) {
		$extra = array('Authorization' => "Basic MzY4ZGVjYzc4ZDFhZTAxMDQzNmZhMTJkOjgwN2NhMmIyNjhlMTA5MTlkNGU5YTNjNw==");
	}
	$jpush_ios = array(
		'platform' => 'ios',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'ios' => array(
				'alert' => $alert,
				'sound' => $sound,
				'badge' => '+1',
				'extras' => $extras
			),
		),
		'options' => array(
			'apns_production' => 1
		),
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_ios), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('managerappJpush', '商家App极光推送(ios)通知店员', $jpush_ios, $return['message']);
	}
	return true;
}
