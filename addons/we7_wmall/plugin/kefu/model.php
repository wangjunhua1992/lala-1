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
require_once MODULE_ROOT . '/library/GatewayClient/Gateway.php';
use GatewayClient\Gateway;
Gateway::$registerAddress = '127.0.0.1:1238';

function get_available_kefu($kefu, $fans, $relation = 'member2kefu', $extra = array()) {
	global $_W;
	if($relation == 'member2clerk') {
		if(empty($kefu['openid'])) {
			return error(-1, '对话人openid为空！');
		}
		if(empty($kefu['unionid'])) {
			return error(-1, '对话人unionid为空！');
		}
		if(!empty($extra['orderid'])) {
			$chat = pdo_get('tiny_wmall_kefu_chat', array('uniacid' => $_W['uniacid'], 'relation' => $relation, 'fansopenid' => $fans['openid'], 'kefuunionid' => $kefu['unionid'], 'orderid' => $extra['orderid']));
			if(!empty($chat)) {
				$service = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'token' => $chat['kefutoken']));
			}
			if(empty($service)) {
				$service = pdo_fetch('select a.sid, b.* from ' . tablename('tiny_wmall_store_clerk') . ' as a left join ' . tablename('tiny_wmall_clerk') . ' as b on a.clerk_id = b.id where a.uniacid = :uniacid and a.sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $kefu['unionid']));
			}
		} else {
			$service = pdo_fetch('select a.sid, b.* from ' . tablename('tiny_wmall_store_clerk') . ' as a left join ' . tablename('tiny_wmall_clerk') . ' as b on a.clerk_id = b.id where a.uniacid = :uniacid and a.sid = :sid and b.token = :token', array(':uniacid' => $_W['uniacid'], ':sid' => $kefu['unionid'], ':token' => $kefu['openid']));
		}
		if(!empty($service)) {
			$service['clerk_id'] = $service['id'];
			$service['avatar'] = tomedia($service['avatar']);
			$service['isonline'] = 1;
			$service['online_cn'] = '商家在线';
			$service['mobile_cn'] = '致电商家';
			$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $service['sid']), array('id', 'title', 'logo', 'push_token'));
			if(!empty($store)) {
				$store['logo'] = tomedia($store['logo']);
				$service['unionid'] = $store['id'];
				$service['title'] = "{$store['title']}-{$service['title']}";
			}
		}
	} elseif($relation == 'member2deliveryer') {
		$service = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'token' => $kefu['openid']));
		if(!empty($service)) {
			$service['deliveryer_id'] = $service['id'];
			$service['avatar'] = tomedia($kefu['avatar']);
			$service['isonline'] = 1;
			$service['online_cn'] = '配送员在线';
			$service['mobile_cn'] = '致电配送员';
		}
	} elseif($relation == 'member2kefu') {
		if(!empty($kefu['openid'])) {
			$service = pdo_fetch('select a.uid, a.mobile, a.nickname, a.avatar, b.token, b.username from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . ' where a.uniacid = :uniacid and b.token = :token ', array(':uniacid' => $_W['uniacid'], ':token' => $kefu['openid']));
			if(!empty($service)) {
				$service['kefu_id'] = $service['uid'];
				$service['title'] = $service['nickname'];
				$service['avatar'] = tomedia($service['avatar']);
				$service['isonline'] = 1;
				$service['online_cn'] = '客服在线';
				$service['mobile_cn'] = '致电客服';
			}
		} else {
			$service = kefu_auto_allot($fans['token']);
		}
	}
	if(empty($service)) {
		$service = error(-1, '获取客服失败');
	}
	return $service;
}

function kefu_get_available_chat($kefu, $fans, $relation = 'member2kefu', $extra = array()) {
	global $_W, $_GPC;
	$config = get_plugin_config('kefu.system');
	$chat = pdo_get('tiny_wmall_kefu_chat', array('uniacid' => $_W['uniacid'], 'relation' => $relation, 'kefuopenid' => $kefu['token'], 'fansopenid' => $fans['token']));
	$status = 0;
	$reason = '';
	$orderid = 0;

	$notmemberrole = kefu_get_touserrole($relation, 'member');
	$status_arr = array(
		'kefu' => array(
			'key' => 'kefu_status',
			'title' => '平台'
		),
		'clerk' => array(
			'key' => 'store_status',
			'title' => '门店'
		),
		'deliveryer' => array(
			'key' => 'deliveryer_status',
			'title' => '配送员'
		)
	);
	if(empty($config['status'])) {
		$status = 2;
		$reason = '平台暂未开启客服功能';
	} else {
		if(empty($config[$status_arr[$notmemberrole]['key']])) {
			$status = 2;
			$reason = "平台暂未开启{$status_arr[$notmemberrole]['title']}客服功能";
		} else {
			if($relation == 'member2deliveryer') {
				$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $kefu['deliveryer_id']), array('kefu_status', 'work_status'));
				if(!empty($deliveryer)) {
					if(empty($deliveryer['work_status'])) {
						$status = 2;
						$reason = '配送员休息中，暂时无法聊天';
					} else {
						if(!in_array($deliveryer['kefu_status'], array(1, 2))) {
							$status = 2;
							$reason = '配送员不在线，暂时无法聊天';
						} else {
							//最近订单
							$condition = ' where uniacid = :uniacid and uid = :uid and deliveryer_id = :deliveryer_id and status = :status and addtime >= :starttime and addtime < :endtime order by addtime desc';
							$params = array(
								':uniacid' => $_W['uniacid'],
								':uid' => $fans['uid'],
								':deliveryer_id' => $kefu['deliveryer_id'],
								':status' => 4,
								':starttime' => TIMESTAMP - 3 * 24 * 3600,
								':endtime' => TIMESTAMP
							);
							$orders = pdo_fetchall('select id from ' . tablename('tiny_wmall_order') . $condition, $params);
							if(empty($orders)) {
								$status = 2;
								$reason = '订单已完成, 不能继续发送消息';
							} else {
								$status = 1;
								$orderid = $orders[0]['id'];
							}
						}
					}
				} else {
					return error(-1, '未获取到配送员信息');
				}
			} elseif($relation == 'member2clerk') {
				$sid = intval($kefu['unionid']);
				$store = store_fetch($sid, array('is_rest', 'kefu_status'));
				if(!empty($store)) {
					if($store['is_rest'] == 1) {
						$status = 2;
						$reason = '门店休息中, 暂时无法聊天';
					} else {
						if(empty($store['kefu_status'])) {
							$status = 2;
							$reason = '门店已关闭客服功能, 暂时无法聊天';
						} else {
							$clerk = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'clerk_id' => $kefu['clerk_id']), array('kefu_status'));
							if(!empty($clerk)) {
								if(!in_array($clerk['kefu_status'], array(1, 2))) {
									$status = 2;
									$reason = '客服不在线, 暂时无法聊天';
								} else {
									$status = 1;
									//订单相关的判断

								}
							} else {
								return error(-1, '未获取到店员的信息');
							}
						}
					}
				} else {
					return error(-1, '未获取到门店信息');
				}
			} elseif($relation == 'member2kefu') {
				$service = pdo_fetch('select a.status, b.kefu_status from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . ' where a.uniacid = :uniacid and b.token = :token ', array(':uniacid' => $_W['uniacid'], ':token' => $kefu['token']));
				if(!empty($service)) {
					if(empty($service['status'])) {
						$status = 2;
						$reason = '客服账户已被禁用, 暂时无法聊天';
					} else {
						if(!in_array($service['kefu_status'], array(1, 2))) {
							$status = 2;
							$reason = '客服不在线，暂时无法聊天';
						} else {
							$status = 1;
						}
					}
				} else {
					return error(-1, '未获取到客服信息');
				}
			}
		}
	}
	if(!empty($chat)) {
		$chat['data'] = iunserializer($chat['data']);
		if($chat['ishei'] == 1) {
			return error(-1, '您暂时不能咨询！');
		}
		if($chat['fansopenid'] == $chat['kefuopenid']) {
			return error(-1, '不能和自己聊天！');
		}
		if($status > 0) {
			$update = array(
				'status' => $status,
				'reason' => $reason,
			);
			if($orderid > 0) {
				$update['orderid'] = $orderid;
			}
			if($_W['kefu']['user']['role'] == 'member') {
				$chat['data']['fanssystem'] = get_agent_os();
				$update['data'] = iserializer($chat['data']);
			}
			pdo_update('tiny_wmall_kefu_chat', $update, array('uniacid' => $_W['uniacid'], 'id' => $chat['id']));
			$chat = array_merge($chat, $update);
			$chat['data'] = iunserializer($chat['data']);
		}
		return $chat;
	}
	if($status > 0) {
		$chat = array(
			'uniacid' => $_W['uniacid'],
			'relation' => $relation,
			'fansopenid' => $fans['token'],
			'fansavatar' => $fans['avatar'],
			'fansnickname' => $fans['nickname'],
			'kefuunionid' => $kefu['unionid'],
			'kefuopenid' => $kefu['token'],
			'kefuavatar' => $kefu['avatar'],
			'kefunickname' => $kefu['nickname'],
			'orderid' => $extra['orderid'],
			'status' => $status,
			'reason' => $reason,
		);
		if($_W['kefu']['user']['role'] == 'member') {
			$chat['data'] = iserializer(array(
				'fanssystem' => get_agent_os(),
			));
		}
		pdo_insert('tiny_wmall_kefu_chat', $chat);
		$chat['id'] = pdo_insertid();
	}
	$chat['data'] = iunserializer($chat['data']);
	return $chat;
}

function kefu_get_chat_log($chatid, $fans) {
	global $_W, $_GPC;
	$id = intval($_GPC['min']);
	$condition = ' where uniacid = :uniacid and chatid = :chatid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':chatid' => $chatid,
	);
	if($id > 0) {
		$condition .= ' and id < :id';
		$params[':id'] = $id;
	}
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 100;
	$limit = ' order by id desc limit ' . $psize;

	$total = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_kefu_chat_log') . $condition, $params));
	$pagetotal = ceil($total / $psize);
	$logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_kefu_chat_log'). $condition . $limit, $params, 'id');
	$min = 0;
	if(!empty($logs)) {
		$min = min(array_keys($logs));
		$logs = array_reverse($logs);
		foreach($logs as $key => &$log) {
			$lastlog = $logs[$key - 1];
			$log['addtime_cn'] = '';
			if(!empty($lastlog)) {
				if($log['addtime'] - $lastlog['addtime'] > 5 * 60) {
					$log['addtime_cn'] = date('Y-m-d H:i', $log['addtime']);
				}
			}
			$log['isleft'] = 1;
			if($log['openid'] == $fans['token']) {
				$log['isleft'] = 0;
			}
			$log['avatar'] = tomedia($log['avatar']);
			if($log['type'] == 'image') {
				$log['content'] = tomedia($log['content']);
			} elseif($log['type'] == 'orderTakeout') {
				$log['orderid'] = $log['content'];
				$order = kefu_get_order($log['content']);
				if(empty($order)) {
					unset($logs[$key]);
				} else {
					$log['content'] = $order;
				}
			}
			$log['timestamp_cn'] = date('Y-m-d H:i:s', $log['addtime']);
		}
	}
	$result = array(
		'logs' => array_values($logs),
		'min' => $min,
		'total' => $total,
		'pagetotal' => $pagetotal,
	);
	return $result;
}

function kefu_get_chat($chatorid, $reverse = false) {
	global $_W;
	$chat = $chatorid;
	if(!is_array($chat)) {
		$chat = pdo_get('tiny_wmall_kefu_chat', array('uniacid' => $_W['uniacid'], 'id' => $chatorid));
	}
	if(!empty($chat)) {
		if(in_array($chat['relation'], array('member2clerk', 'member2deliveryer', 'member2kefu'))) {
			$touserole = kefu_get_touserrole($chat['relation']);
			if($_W['kefu']['user']['role'] == 'member') {
				if(empty($reverse)) {
					$chat['fans'] = $chat['fromuser'] = $_W['kefu']['user'];
					$chat['kefu'] = $chat['touser'] = kefu_get_fans($chat['kefuopenid'], $touserole, array('kefuunionid' => $chat['kefuunionid']));
					$chat['fromfans'] = 1;
					$chat['fromopenid'] = $chat['fans']['token'];
					$chat['toopenid'] = $chat['kefu']['token'];
				} else {
					$chat['kefu'] = $chat['fromuser'] =  kefu_get_fans($chat['kefuopenid'], $touserole, array('kefuunionid' => $chat['kefuunionid']));
					$chat['fans'] = $chat['touser'] = $_W['kefu']['user'];
					$chat['fromfans'] = 0;
					$chat['fromopenid'] = $chat['kefu']['token'];
					$chat['toopenid'] = $chat['fans']['token'];
				}
			} else {
				if(empty($reverse)) {
					$chat['fans'] = $chat['touser'] = kefu_get_fans($chat['fansopenid'], $touserole);
					$chat['kefu'] = $chat['fromuser'] = $_W['kefu']['user'];
					$chat['fromfans'] = 0;
					$chat['fromopenid'] = $chat['kefu']['token'];
					$chat['toopenid'] = $chat['fans']['token'];
				} else {
					$chat['kefu'] = $chat['touser'] = $_W['kefu']['user'];
					$chat['fans'] = $chat['fromuser'] = kefu_get_fans($chat['fansopenid'], $touserole);
					$chat['fromfans'] = 1;
					$chat['fromopenid'] = $chat['fans']['token'];
					$chat['toopenid'] = $chat['kefu']['token'];
				}
			}
		}
	}
	return $chat;
}

function kefu_check_chat_available($chatorid, $fansopenid) {
	global $_W;
	$chat = $chatorid;
	if(!is_array($chatorid)) {
		$chat = get_chat($chatorid);
	}
	if(empty($chat)) {
		return error(-1, '聊天记录不存在');
	}
	if($chat['relation'] == 'member2kefu') {
		if(1) {

		}
	}
	return true;
}

function kefu_get_fans1($token, $relation = 'member2clerk', $extra = array()) {
	global $_W, $_GPC;
	$type = 'member';
	if($relation == 'member2clerk') {
		if($_W['kefu']['user']['role'] == 'member') {
			$type = 'clerk';
		}
	} elseif($relation == 'member2deliveryer') {
		if($_W['kefu']['user']['role'] == 'member') {
			$type = 'deliveryer';
		}
	} elseif($relation == 'member2kefu') {
		if($_W['kefu']['user']['role'] == 'member') {
			$type = 'kefu';
		}
	} elseif($relation == 'clerk2deliveryer') {
		$type = 'clerk';
		if($_W['kefu']['user']['role'] == 'clerk') {
			$type = 'deliveryer';
		}
	}
	if($type == 'member') {
		$fans = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'token' => $token), array('uid', 'realname', 'nickname', 'avatar', 'mobile', 'token'));
		if(!empty($fans)) {
			$fans['title'] = $fans['nickname'];
			$fans['mobile_cn'] = '致电顾客';
		}
	} elseif($type == 'clerk') {
		$fans = pdo_fetch('select a.*, b.kefu_status from ' . tablename('tiny_wmall_clerk') . ' as a left join ' . tablename('tiny_wmall_store_clerk') . ' as b on a.id = b.clerk_id where a.uniacid = :uniacid and b.sid = :sid and a.token = :token', array(':uniacid' => $_W['uniacid'], ':sid' => $extra['kefuunionid'], ':token' => $token));
		if(!empty($fans)) {
			$fans['clerk_id'] = $fans['id'];
			$fans['mobile_cn'] = '致电商户';
			$fans['busy_reply'] = '';
			$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $extra['kefuunionid']), array('id', 'title', 'logo', 'data'));
			if(!empty($store)) {
				$store['logo'] = tomedia($store['logo']);
				$store['data'] = iunserializer($store['data']);
				$fans['busy_reply'] = $store['data']['kefu']['busy_reply'];
			}
		}
	} elseif($type == 'deliveryer') {
		$fans = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'token' => $token), array('id', 'title', 'nickname', 'avatar', 'mobile', 'extra', 'kefu_status'));
		if(!empty($fans)) {
			$fans['deliveryer_id'] = $fans['id'];
			$fans['mobile_cn'] = '致电配送员';
			$fans['extra'] = iunserializer($fans['extra']);
			$fans['busy_reply'] = $fans['extra']['kefu_busy_reply'];
		}
	}
	return $fans;
}

function kefu_get_fans($token, $role = 'member', $extra = array()) {
	global $_W, $_GPC;
	if($role == 'member') {
		$fans = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'token' => $token), array('uid', 'realname', 'nickname', 'avatar', 'mobile', 'token', 'success_num', 'success_last_time'));
		if(!empty($fans)) {
			$fans['title'] = $fans['nickname'];
			$fans['mobile_cn'] = '致电顾客';
			$fans['success_last_time_cn'] = date('Y-m-d H:i', $fans['success_last_time']);
		}
	} elseif($role == 'clerk') {
		$fans = pdo_fetch('select a.*, b.kefu_status from ' . tablename('tiny_wmall_clerk') . ' as a left join ' . tablename('tiny_wmall_store_clerk') . ' as b on a.id = b.clerk_id where a.uniacid = :uniacid and b.sid = :sid and a.token = :token', array(':uniacid' => $_W['uniacid'], ':sid' => $extra['kefuunionid'], ':token' => $token));
		if(!empty($fans)) {
			$fans['clerk_id'] = $fans['id'];
			$fans['mobile_cn'] = '致电商户';
			$fans['busy_reply'] = '';
			$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $extra['kefuunionid']), array('id', 'title', 'logo', 'data'));
			if(!empty($store)) {
				$store['logo'] = tomedia($store['logo']);
				$store['data'] = iunserializer($store['data']);
				$fans['busy_reply'] = $store['data']['kefu']['busy_reply'];
			}
		}
	} elseif($role == 'deliveryer') {
		$fans = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'token' => $token), array('id', 'title', 'nickname', 'avatar', 'mobile', 'extra', 'kefu_status', 'token'));
		if(!empty($fans)) {
			$fans['deliveryer_id'] = $fans['id'];
			$fans['mobile_cn'] = '致电配送员';
			$fans['extra'] = iunserializer($fans['extra']);
			$fans['busy_reply'] = $fans['extra']['kefu_busy_reply'];
		}
	} elseif($role == 'kefu') {
		$fans = pdo_fetch('select a.uid, a.mobile, a.nickname, a.avatar, b.kefu_status, b.token, b.username from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . ' where a.uniacid = :uniacid and b.token = :token ', array(':uniacid' => $_W['uniacid'], ':token' => $token));
		if(!empty($fans)) {
			$fans['kefu_id'] = $fans['uid'];
			$fans['title'] = $fans['nickname'];
			$fans['avatar'] = tomedia($fans['avatar']);
			$fans['mobile_cn'] = '致电客服';
			$config = get_plugin_config('kefu');
			$fans['busy_reply'] = $config['autoreply']['busyReply']['content'];
		}
	}
	return $fans;
}

function kefu_add_chatlog($chat) {
	global $_W, $_GPC;
	if(empty($chat)) {
		return false;
	}
	$insert = kefu_insert_chatlog($chat);

	//平台客服工作时间检测
	$flag = 1;
	if($chat['touser']['kefu_id'] > 0) {
		$status = kefu_check_currenttime_avaliable();
		if(is_error($status)) {
			$flag = 0; //不在营业时间进行非工作时间回复, 不再检测是否是忙时回复
			$log = array(
				'type' => 'text',
				'content' => $status['message'],
				'send2from' => 1
			);
			$chat_reserve = kefu_get_chat($chat, true);
			kefu_insert_chatlog($chat_reserve, $log);
		}
	}

	//忙碌自动回复
	if($flag == 1 && !empty($insert) && $chat['fromfans'] == 1) {
		if($chat['touser']['kefu_status'] == 2) {
			if(!empty($chat['touser']['busy_reply'])) {
				$log = array(
					'type' => 'text',
					'content' => $chat['touser']['busy_reply'],
					'send2from' => 1
				);
				$chat_reserve = kefu_get_chat($chat, true);
				kefu_insert_chatlog($chat_reserve, $log);
			}
		}
	}
	return $insert;
}

function kefu_insert_chatlog($chat, $log = array()) {
	global $_W, $_GPC;
	if(empty($chat)) {
		return false;
	}
	if(empty($log)) {
		$log = array(
			'content' => trim($_GPC['content']),
			'type' => trim($_GPC['type']),
		);
	}
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'chatid' => $chat['id'],
		'openid' => $chat['fromopenid'],
		'nickname' => $chat['fromuser']['nickname'],
		'avatar' => tomedia($chat['fromuser']['avatar']),
		'type' => $log['type'],
		'toopenid' => $chat['toopenid'],
		'addtime' => TIMESTAMP,
		'content' =>  $log['content'],
	);
	$insert_id = pdo_insert('tiny_wmall_kefu_chat_log', $insert);
	if(!empty($insert_id)) {
		if($chat['fromfans'] == 1) {
			$update = array(
				'kefunotread' => $chat['kefunotread'] + 1,
				'notread' => 0,
				'fansdel' => 0,
				'kefudel' => 0,
				'lastcontent' => $log['content'],
				'msgtype' => $log['type'],
				'lasttime' => TIMESTAMP
			);
		} else {
			$update = array(
				'notread' => $chat['notread'] + 1,
				'kefunotread' => 0,
				'fansdel' => 0,
				'kefudel' => 0,
				'kefulastcontent' => $log['content'],
				'kefumsgtype' => $log['type'],
				'kefulasttime' => TIMESTAMP
			);
		}
		pdo_update('tiny_wmall_kefu_chat', $update, array('uniacid' => $_W['uniacid'], 'id' => $chat['id']));
	}
	$insert['isleft'] = 0;
	if($insert['type'] == 'image') {
		$insert['content'] = tomedia($insert['content']);
	} elseif($insert['type'] == 'orderTakeout') {
		$insert['orderid'] = $insert['content'];
		$insert['content'] = kefu_get_order($insert['content']);
	}
	$insert['avatar'] = tomedia($insert['avatar']);
	$insert['timestamp_cn'] = date('Y-m-d H:i:s', $insert['addtime']);

	$send = $insert;
	$send['isleft'] = 1;
	$message = array(
		'type' => 'message',
		'data' => array(
			'chat' => $send,
		),
	);
	if($chat['fromfans'] == 1 && $chat['relation'] == 'member2kefu') {
		$user = array(
			'role' => 'kefu',
			'token' => $chat['kefuopenid'],
		);
		$chats = kefu_get_mychat($user);;
		$message['data']['chats'] = $chats['chats'];
	}

	Gateway::sendToUid($chat['toopenid'], json_encode($message));
	if($log['send2from']) {
		$message['data']['chat']['isleft'] = 0;
		Gateway::sendToUid($chat['fromopenid'], json_encode($message));
	}
	return $insert;
}

function kefu_get_fastreply($user, $relation = 'member2clerk') {
	global $_W;
	$basics = array(
		'member2clerk' => array(
			'member' => array(
				'订单出餐品了吗'
			),
			'clerk' => array(
				'找我干啥?'
			),
		),
		'member2deliveryer' => array(
			'member' => array(
				'订单什么时候能送到'
			),
			'deliveryer' => array(
				'外卖马上到'
			),
		),
		'member2kefu' => array(
			'member' => array(
				'我想咨询一下'
			),
			'kefu' => array(
				'有什么可以帮您'
			),
		)
	);
	$basic = $basics[$relation][$user['role']];
	if(!is_array($basic)) {
		$basic = array();
	}
	$reply = pdo_get('tiny_wmall_kefu_fastreply', array('uniacid' => $_W['uniacid'], 'relation' => $relation, 'useropenid' => $user['token']));
	$mine = array();
	if(!empty($reply)) {
		$mine = iunserializer($reply['content']);
	}
	$reply = array_merge($mine, $basic);
	return $reply;
}

function kefu_add_fastreply($user, $content, $relation = 'member2clerk') {
	global $_W;
	if(empty($content)) {
		return false;
	}
	$reply = pdo_get('tiny_wmall_kefu_fastreply', array('uniacid' => $_W['uniacid'], 'relation' => $relation, 'useropenid' => $user['token']));
	if(empty($reply)) {
		$reply = array(
			'uniacid' => $_W['uniacid'],
			'relation' => $relation,
			'useropenid' => $user['token'],
			'content' => iserializer(array()),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_kefu_fastreply', $reply);
		$reply['id'] = pdo_insertid();
	}
	$reply['content'] = iunserializer($reply['content']);
	array_unshift($reply['content'], $content);
	$update = array(
		'content' => iserializer($reply['content']),
	);
	pdo_update('tiny_wmall_kefu_fastreply', $update, array('id' => $reply['id']));
	return $content;
}

function kefu_get_mychat($user) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 50;
	if($user['role'] == 'member') {
		$condition .= ' and fansopenid = :fansopenid and fansdel = 0';
		$params[':fansopenid'] = $user['token'];
		$limit = ' order by notread desc, lasttime desc limit ' . ($pindex - 1) * $psize . ',' . $psize;
	} elseif($user['role'] == 'clerk') {
		$condition .= ' and kefuopenid = :kefuopenid and kefudel = 0';
		$params[':kefuopenid'] = $user['token'];
		$limit = ' order by kefunotread desc, kefulasttime desc limit ' . ($pindex - 1) * $psize . ',' . $psize;
	} elseif($user['role'] == 'deliveryer') {
		$condition .= ' and kefuopenid = :kefuopenid and kefudel = 0';
		$params[':kefuopenid'] = $user['token'];
		$limit = ' order by kefunotread desc, kefulasttime desc limit ' . ($pindex - 1) * $psize . ',' . $psize;
	} elseif($user['role'] == 'kefu') {
		$condition .= ' and ((kefuopenid = :kefuopenid and kefudel = 0) or (fansopenid = :fansopenid and fansdel = 0))';
		$params[':kefuopenid'] = $user['token'];
		$params[':fansopenid'] = $user['token'];
		$limit = ' order by kefunotread desc, kefulasttime desc limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}

	$total = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_kefu_chat') . $condition, $params));
	$pagetotal = ceil($total / $psize);
	$chats = pdo_fetchall('select * from ' . tablename('tiny_wmall_kefu_chat'). $condition . $limit, $params);
	if(!empty($chats)) {
		foreach($chats as $key => &$chat) {
			$content_key = $chat['lasttime'] > $chat['kefulasttime'] ? '' : 'kefu';
			$lasttime = $chat[$content_key . 'lasttime'];
			$content = $chat[$content_key . 'lastcontent'];
			$msgtype = $chat[$content_key . 'msgtype'];
			if($msgtype == 'image') {
				$content = '[图片消息]';
			} elseif($msgtype == 'orderTakeout') {
				$content = '[订单消息]';
			}
			$chat['content'] = $content;
			$chat['lasttime_cn'] = kefu_timestamp_cn($lasttime);
		}
	}
	$result = array(
		'chats' => $chats,
		'total' => $total,
		'pagetotal' => $pagetotal,
	);
	return $result;
}

function kefu_timestamp_cn($timestamp) {
	$cn = '';
	if(!empty($timestamp)) {
		$day = 24 * 3600;
		$hour = 3600;
		$minute = 60;
		$diff = TIMESTAMP - $timestamp;
		if($diff >= $day) {
			$cn = ceil($diff / $day) . '天前';
		} elseif($diff >= $hour && $diff < $day) {
			$cn = ceil($diff / $hour) . '小时前';
		} elseif($diff >= $minute && $diff < $hour) {
			$cn = ceil($diff / $minute) . '分钟前';
		} elseif($diff < $minute) {
			$cn = '刚刚';
		}
	}
	return $cn;
}

function kefu_get_orders($chatorid) {
	global $_W;
	$chat = $chatorid;
	if(!is_array($chat)) {
		$chat = kefu_get_chat($chat);
	}
	if(empty($chat)) {
		return false;
	}
	$condition = ' where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	if($chat['relation'] == 'member2clerk') {
		$condition .= ' and uid = :uid and sid = :sid';
		$params[':sid'] = $chat['kefuunionid'];
		if($_W['kefu']['user']['role'] == 'member') {
			$params[':uid'] = $_W['kefu']['user']['uid'];
		} elseif($_W['kefu']['user']['role'] == 'clerk') {
			$params[':uid'] = kefu_token2uid($chat['fansopenid'], 'member');
		}
	} elseif($chat['relation'] == 'member2deliveryer') {
		$condition .= ' and uid = :uid and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = kefu_token2uid($chat['kefuopenid'], 'deliveryer');
		if($_W['kefu']['user']['role'] == 'member') {
			$params[':uid'] = $_W['kefu']['user']['uid'];
		} elseif($_W['kefu']['user']['role'] == 'deliveryer') {
			$params[':uid'] = kefu_token2uid($chat['fansopenid'], 'member');
		}
	} elseif($chat['relation'] == 'member2kefu') {
		$condition .= ' and uid = :uid';
		if($_W['kefu']['user']['role'] == 'member') {
			$params[':uid'] = $_W['kefu']['user']['uid'];
		} elseif($_W['kefu']['user']['role'] == 'kefu') {
			$params[':uid'] = kefu_token2uid($chat['fansopenid'], 'member');
		}
	}
	$condition .= ' order by id desc limit 5';
	$orders = pdo_fetchall('select a.id, a.sid, a.status, a.final_fee, a.num, a.addtime, b.title, b.logo, b.delivery_mode from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition, $params);
	if(!empty($orders)) {
		foreach($orders as &$order) {
			$order['logo'] = tomedia($order['logo']);
			$order['goods'] = pdo_get('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			mload()->model('order');
			$order_status = order_status();
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['goods_title'] = "{$order['goods']['goods_title']}等{$order['num']}件商品";
			$order['link'] = ivurl('pages/order/detail', array('id' => $order['id']));
		}
	}
	return $orders;
}

function keft_set_notread_zero($chatOrId, $fromuser) {
	global $_W, $_GPC;
	if(!is_array($chatOrId)) {
		$chatid = intval($chatOrId);
		$chat = kefu_get_chat($chatid);
	} else {
		$chat = $chatOrId;
	}
	if(empty($chat)) {
		return error(-1, '获取聊天信息失败！');
	}
	if($fromuser['token'] == $chat['fansopenid']) {
		$update = array(
			'notread' => 0
		);
	} elseif($fromuser['token'] == $chat['kefuopenid']) {
		$update = array(
			'kefunotread' => 0
		);
	}
	$status = pdo_update('tiny_wmall_kefu_chat', $update, array('uniacid' => $_W['uniacid'], 'id' => $chat['id']));
	return $status;
}

function kefu_token2uid($token, $type = 'member') {
	global $_W;
	$tables = array(
		'member' => array(
			'table' => 'tiny_wmall_members',
			'field' => 'uid'
		),
		'clerk' => array(
			'table' => 'tiny_wmall_clerk',
			'field' => 'id'
		),
		'deliveryer' => array(
			'table' => 'tiny_wmall_deliveryer',
			'field' => 'id'
		),
	);
	if(!in_array($type, array_keys($tables)) || empty($token)) {
		return false;
	}
	$user = pdo_fetch("select {$tables[$type]['field']} as uid from " . tablename($tables[$type]['table']) . ' where uniacid = :uniacid and token = :token ', array(':uniacid' => $_W['uniacid'], ':token' => $token));
	$uid = !empty($user) ? $user['uid'] : 0;
	return $uid;
}

function kefu_get_touserrole($relation = 'member2clerk', $role = '') {
	global $_W;
	if(empty($role)) {
		$role = $_W['kefu']['user']['role'];
	}
	$tuserrole = str_replace($role, '', $relation);
	$tuserrole = str_replace(2, '', $tuserrole);
	return $tuserrole;
}

function kefu_auto_allot($fansopenid, $relation = 'member2kefu') {
	global $_W;
	$config = get_plugin_config('kefu');
	$rule = intval($config['system']['allotRule']);
	$flag = 1;
	if(empty($rule)) { //最近联系人优先
		$chat = pdo_fetch('select kefuopenid from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and relation = :relation and fansopenid = :fansopenid order by kefulasttime desc', array(':uniacid' => $_W['uniacid'], ':relation' => $relation, ':fansopenid' => $fansopenid));
		if(!empty($chat)) {
			$flag = 0;
			$service = pdo_fetch('select a.uid, a.mobile, a.nickname, a.avatar, b.kefu_status, b.token, b.username from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . ' where a.uniacid = :uniacid and a.status = 1 and b.token = :token ', array(':uniacid' => $_W['uniacid'], ':token' => $chat['kefuopenid']));
			if(empty($service) || $service['kefu_status'] == 3) {
				//最近客服不在线 则也随机分配
				$flag = 1;
			}
		}
	}
	if($flag == 1) { //随机分配
		$users = pdo_fetchall('select a.uid, a.mobile, a.nickname, a.avatar, a.roleid, a.perms, b.token, b.username, b.kefu_status from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid' . ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1 order by a.id desc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
		if(!empty($users)) {
			$perm_roles = pdo_getall('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'perms'), 'id');
			foreach($users as $key => $user) {
				$perms_user = explode(',', $user['perms']);
				$perms_role = explode(',', $perm_roles[$user['roleid']]['perms']);
				$perms = array_merge($perms_user, $perms_role);
				if(!in_array('kefu.kefu', $perms) || $user['kefu_status'] == 3) {
					unset($users[$key]);
				}
			}
		}
		if(empty($users)) {
			return error(-1, '暂无客服');
		}
		$key = array_rand($users);
		$service = $users[$key];
	}
	if(!empty($service)) {
		if(empty($service['token'])) {
			$service['token'] = random(20);
			pdo_update('users', array('token' => $service['token']), array('uid' => $service['uid']));
		}
		$service['kefu_id'] = $service['uid'];
		$service['title'] = $service['nickname'];
		$service['avatar'] = tomedia($service['avatar']);
		$service['isonline'] = 1;
		$service['online_cn'] = '客服在线';
		$service['mobile_cn'] = '致电客服';
	}
	return $service;
}

function kefu_check_currenttime_avaliable() {
	global $_W;
	$config = get_plugin_config('kefu');
	if($config['autoreply']['closingTime']['status'] != 1) {
		return true;
	}
	$flag = false;
	$nowtime = TIMESTAMP;
	$nowweek = date('N', $nowtime);
	$workday = $config['autoreply']['workday'];
	$worktime = $config['autoreply']['worktime'];
	if(in_array($nowweek, $workday)) {
		if(!empty($worktime)) {
			foreach($worktime as $value) {
				$start = strtotime($value['start']);
				$end = strtotime($value['end']);
				if($nowtime > $start && $nowtime < $end) {
					$flag = true;
					break;
				}
			}
		};
	}
	if($flag) {
		return true;
	} else {
		//非工作时间, 返回非工作时间提示语
		$weekday = array('周一', '周二', '周三', '周四', '周五', '周六', '周日');
		foreach($weekday as $key => $week) {
			if(!in_array($key + 1, $workday)) {
				unset($weekday[$key]);
			};
		}
		$workday_cn = implode(' ', $weekday);
		$worktime_cn = array();
		if(!empty($worktime)) {
			foreach($worktime as $value) {
				$worktime_cn[] = $value['start'] . '-' . $value['end'];
			}
		};
		$worktime_cn = implode(' ', $worktime_cn);
		$msg = "{$config['autoreply']['closingTime']['content']} 工作日: {$workday_cn} 工作时间: {$worktime_cn}";
		return error(-1, $msg);
	}
}

function kefu_chat_relations() {
	$data = array(
		'member2kefu' => array(
			'text' => '顾客和客服',
			'css' => 'label label-success'
		),
		'member2clerk' => array(
			'text' => '顾客和店员',
			'css' => 'label label-info'
		),
		'member2deliveryer' => array(
			'text' => '顾客和配送员',
			'css' => 'label label-primary'
		),
	);
	return $data;
}

function kefu_chat_status() {
	$data = array(
		'1' => array(
			'text' => '正常',
			'css' => 'label label-success'
		),
		'2' => array(
			'text' => '关闭',
			'css' => 'label label-default'
		),
	);
	return $data;
}

function kefu_delete_chat($chatId) {
	global $_W;
	$key = $_W['kefu']['user']['role'] == 'member' ? 'fansdel' : 'kefudel';
	$status = pdo_update('tiny_wmall_kefu_chat', array($key => 1), array('uniacid' => $_W['uniacid'], 'id' => $chatId));
	return $status;
}

function kefu_offline_reply() {
	global $_W;
	if($_W['kefu']['user']['kefu_status'] != 3) {
		return false;
	}
	$condition = ' where uniacid = :uniacid and kefuopenid = :kefuopenid and status = 1 and kefunotread > 0 ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':kefuopenid' => $_W['kefu']['user']['token']
	);
	$chats = pdo_fetchall('select * from ' . tablename('tiny_wmall_kefu_chat'). $condition, $params);
	if(!empty($chats)) {
		$reasons = array(
			'member2kefu' => '客服不在线，暂时无法聊天',
			'member2deliveryer' => '配送员不在线，暂时无法聊天',
			'member2clerk' => '门店客服不在线，暂时无法聊天',
		);
		foreach($chats as $chat) {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'chatid' => $chat['id'],
				'openid' => $chat['kefuopenid'],
				'nickname' => $chat['kefunickname'],
				'avatar' => tomedia($chat['kefuavatar']),
				'type' => 'system',
				'toopenid' => $chat['fansopenid'],
				'addtime' => TIMESTAMP,
				'content' => $reasons[$chat['relation']],
			);
			$insert_id = pdo_insert('tiny_wmall_kefu_chat_log', $insert);
			if(!empty($insert_id)) {
				$update = array(
					'status' => 2,
					'reason' => $reasons[$chat['relation']]
				);
				pdo_update('tiny_wmall_kefu_chat', $update, array('uniacid' => $_W['uniacid'], 'id' => $chat['id']));
			}
			$send = $insert;
			$message = array(
				'type' => 'message',
				'data' => array(
					'chat' => $send,
				),
			);
			Gateway::sendToUid($chat['fansopenid'], json_encode($message));
		}
	}
	return true;
}

function kefu_update_info($user = array()) {
	global $_W;
	if(empty($user['token'])) {
		return false;
	}
	if($user['role'] == 'member') {
		pdo_update('tiny_wmall_kefu_chat', array('fansavatar' => $user['avatar'], 'fansnickname' => $user['nickname']), array('uniacid' => $_W['uniacid'], 'fansopenid' => $user['token']));
	} else {
		pdo_update('tiny_wmall_kefu_chat', array('kefuavatar' => $user['avatar'], 'kefunickname' => $user['nickname']), array('uniacid' => $_W['uniacid'], 'kefuopenid' => $user['token']));
	}
	pdo_update('tiny_wmall_kefu_chat_log', array('avatar' => $user['avatar'], 'nickname' => $user['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $user['token']));
	return true;
}











function kefu_get_one($uid) {
	global $_W, $_GPC;
	$service = array(
		'uid' => '107',
		'avatar' => 'http://tp1.sinaimg.cn/1571889140/180/40030060651/1',
		'nickname' => '客服107'
	);
	return $service;

}

function kefu_get_chat_records($filter = array()) {
	global $_W;
	$endtime = time();
	$starttime = $endtime - 7*24*3600;
	$config = $_W['config']['kefu']['basic'];
	if($config['system']['recentMessage'] > 0) {
		$starttime = $endtime - $config['system']['recentMessage'] *24*3600;
	}
	$condition = ' where uniacid = :uniacid and ((from_uid = :from_uid and from_uid_type = :from_uid_type and to_uid = :to_uid and to_uid_type = :to_uid_type) or (from_uid = :to_uid and from_uid_type = :to_uid_type and to_uid = :from_uid and to_uid_type = :from_uid_type)) and createtime > :starttime and createtime <= :endtime ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':from_uid' => $filter['from_uid'],
		':from_uid_type' => $filter['from_uid_type'],
		':to_uid' => $filter['to_uid'],
		':to_uid_type' => $filter['to_uid_type'],
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_chat_record') . $condition . ' order by createtime asc ', $params);
	$data = array();
	if(!empty($records)) {
		$from_user = kefu_get_user_info($filter['from_uid'], $filter['from_uid_type']);
		$to_user = kefu_get_user_info($filter['to_uid'], $filter['to_uid_type']);
		foreach($records as $record) {
			$mine = $record['from_uid'] == $filter['from_uid'] ? true : false;
			$data[] = array(
				'id' => $record['from_uid'],
				'avatar' => $mine ? $from_user['avatar'] : $to_user['avatar'],
				'content' => $record['msg_type'] == 3 ? iunserializer($record['content']) : $record['content'],
				'mine' => $mine,
				'timestamp' => $record['createtime'] * 1000,
				'type' => 'kefu',
				'username' => $mine ? $from_user['nickname'] : $to_user['nickname'],
				'msg_type' => $record['msg_type'],
			);
		}
	}
	return $data;
}

function kefu_service_chat_records_fetchall($from_uid, $from_uid_type) {
	global $_W;
	$endtime = time();
	$starttime = $endtime - 7*24*3600;
	$config = $_W['config']['kefu']['basic'];
	if($config['system']['recentMessage'] > 0) {
		$starttime = $endtime - $config['system']['recentMessage'] *24*3600;
	}
	$condition = ' where uniacid = :uniacid and ((from_uid = :from_uid and from_uid_type = :from_uid_type) or (to_uid = :from_uid and to_uid_type = :from_uid_type)) and createtime > :starttime and createtime <= :endtime ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':from_uid' => $from_uid,
		':from_uid_type' => $from_uid_type,
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_chat_record') . $condition . ' order by createtime asc ', $params);
	$chatlog = array();
	if(!empty($records)) {
		$members = pdo_fetchall('select uid, nickname, avatar from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']), 'uid');
		$kefu = kefu_get_user_info($from_uid, $from_uid_type);
		foreach($records as $record) {
			$mine = $record['from_uid'] == $from_uid ? true : false;
			if($mine) {
				$key = 'kefu' . $record['to_uid'];
			} else {
				$key = 'kefu' . $record['from_uid'];
			}
			$content = $record['msg_type'] == 3 ? iunserializer($record['content']) : $record['content'];
			$chatlog[$key][] = array(
				'id' => $record['from_uid'],
				'avatar' => $mine ? $kefu['avatar'] : $members[$record['from_uid']]['avatar'],
				'content' => $content,
				'mine' => $mine,
				'timestamp' => $record['createtime'] * 1000,
				'type' => 'kefu',
				'username' => $mine ? $kefu['nickname'] : $members[$record['from_uid']]['nickname'],
				'msg_type' => $record['msg_type'],
			);
		}
	}
	$history = array();
	if(!empty($chatlog)) {
		foreach($chatlog as $key => $log) {
			foreach($log as $value) {
				if($value['id'] != $from_uid) {
					$history[$key] = array_merge($value, array('name' => $value['username'], 'historyTime' => $value['timestamp']));
					break;
				}
			}
		}

	}
	$data = array(
		'chatlog' => $chatlog,
		'history' => $history
	);
	return $data;
}

function kefu_get_user_info($uid, $type) {
	global $_W;
	$data = array();
	//1顾客 2客服 3商户 4配送员
	if($type == 1) {
		$data = pdo_fetch('select uid, nickname, avatar from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and uid = :uid ', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
	} elseif($type == 2) {
		$data = pdo_fetch('select a.*, b.username from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('users') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and a.uid = :uid ', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
		if(!empty($data)) {
			$data['avatar'] = 'http://tp1.sinaimg.cn/1571889140/180/40030060651/1';
			$data['nickname'] = '客服' . $uid;
		}
	} elseif($type == 3) {

	} elseif($type == 4) {

	}
	return $data;
}

function kefu_get_order($orderId) {
	global $_W;
	$condition = ' where a.uniacid = :uniacid and a.id = :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':id' => $orderId
	);
	$order = pdo_fetch('select a.id as aid, a.*, b.title, b.logo, b.delivery_mode from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition, $params);
	if(!empty($order)) {
		if($_W['kefu']['user']['role'] == 'member') {
			if($_W['kefu']['user']['uid'] != $order['uid']) {
				return false;
			}
		}
		$order['logo'] = tomedia($order['logo']);
		$order['goods'] = pdo_get('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'oid' => $orderId));
		$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
		mload()->model('order');
		$order_status = order_status();
		$order['status_cn'] = $order_status[$order['status']]['text'];
		$order['goods_title'] = "{$order['goods']['goods_title']}等{$order['num']}件商品";
		$order['link'] = ivurl('pages/order/detail', array('id' => $order['id']));
	}
	return $order;
}

/**
 * 检测是否要发送首句欢迎语
*/
function kefu_is_send_first_msg($filter = array())
{
	global $_W;
	$config = $_W['config']['kefu']['basic'];
	if($config['autoreply']['firstMessage']['status'] != 1) {
		return error(-1, '未开启首句欢迎语功能');
	}
	//当天客服是否给顾客发送过消息
	$starttime = strtotime(date('Y-m-d', time()));
	$endtime = time();
	$condition = ' where uniacid = :uniacid and from_uid = :from_uid and from_uid_type = :from_uid_type and to_uid = :to_uid and to_uid_type = :to_uid_type and createtime >= :starttime and createtime < :endtime ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':from_uid' => $filter['from_uid'],
		':from_uid_type' => $filter['from_uid_type'],
		':to_uid' => $filter['to_uid'],
		':to_uid_type' => $filter['to_uid_type'],
		':starttime' => $starttime,
		':endtime' => $endtime
	);
	$num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_chat_record') . $condition, $params);
	if ($num > 0) {
		return error(-1, '今日已发送过欢迎语');
	} else {
		return error(0, $config['autoreply']['firstMessage']['content']);
	}
}

/**
 * 保存聊天记录
*/

function kefu_save_record($mine, $from_uid_type, $to, $to_uid_type) {
	global $_W;
	$msg_type = intval($mine['msg_type']) ? intval($mine['msg_type']) : 1;
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'from_uid' => intval($mine['id']),
		'from_uid_type' => $from_uid_type,
		'to_uid' => intval($to['id']),
		'to_uid_type' => $to_uid_type,
		'content' => $msg_type ==3 ? iserializer($mine['content']) : trim($mine['content']),
		'msg_type' => $msg_type,
		'createtime' => time(),
		'read' => 0,
	);
	pdo_insert('tiny_wmall_chat_record', $insert);
	$insert_id = pdo_insertid();
	return $insert_id;
}



function kefu_checkuser($from_uid, $from_uid_type, $token) {
	global $_W;
	$user = kefu_get_user_info($from_uid, $from_uid_type);
	if(!empty($user) && $user['token'] == $token) {
		return true;
	} else {
		return false;
	}
}





