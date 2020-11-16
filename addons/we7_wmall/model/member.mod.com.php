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

function icheckauth($force = true) {
	global $_W, $_GPC;
	load()->model('mc');
	if($_GPC['from'] == 'vue' && $_GPC['u'] == 'weixin' && empty($_W['openid'])) {
		$result = array(
			'errno' => 41000,
			'message' => '需要获取openid',
			'sessionid' => $_W['session_id'],
			'oauthurl' => imurl('system/common/vuesession', array('state' => "we7sid-{$_W['session_id']}", 'from' => 'vue'), true),
		);
		imessage($result, '', 'ajax');
	}

	$_W['member'] = array();
	if(is_weixin() && !defined('IN_WXAPP') && !defined('IN_TTAPP') && !defined('IN_VUE')) {
		if(!empty($_W['openid'])) {
			if(empty($force)) {
				$member = get_member($_W['openid']);
			} else {
				$fansInfo = mc_oauth_userinfo();
				if(!empty($fansInfo['unionid'])) {
					pdo_update('tiny_wmall_members', array('openid' => $fansInfo['openid']), array('unionId' => $fansInfo['unionid']));
					pdo_update('tiny_wmall_members', array('unionId' => $fansInfo['unionid']), array('openid' => $fansInfo['openid']));
					member_union($fansInfo['unionid']);
					$member = get_member($fansInfo['unionid'], 'unionId');
				} else {
					$member = get_member($fansInfo['openid']);
				}
				$avatar = rtrim(rtrim($fansInfo['headimgurl'], '0'), 132) . 132;
				if(empty($member)) {
					$mc = pdo_fetch('select a.fanid,b.credit1,b.credit2,b.uid,b.realname,b.mobile,b.gender from' . tablename('mc_mapping_fans') . ' as a left join ' . tablename('mc_members') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and b.uniacid = :buniacid and a.acid = :acid and a.openid = :openid', array(':uniacid' => $_W['uniacid'], ':buniacid' => $_W['uniacid'], ':acid' => $_W['acid'], ':openid' => $_W['openid']));
					if(empty($mc['uid'])) {
						$member = array(
							'uniacid' => $_W['uniacid'],
							'uid' => date('His') . random(3, true),
							'openid' => $fansInfo['openid'],
							'unionId' => $fansInfo['unionid'],
							'nickname' => $fansInfo['nickname'],
							'realname' => $fansInfo['nickname'],
							'sex' => ($fansInfo['sex'] == 1 ? '男' : '女'),
							'avatar' => $avatar,
							'is_sys' => 2, //模拟用户
							'status' => 1,
							'token' => random(32),
							'addtime' => TIMESTAMP,
						);
						pdo_insert('tiny_wmall_members', $member);
						$member['credit1'] = 0;
						$member['credit2'] = 0;
					} else {
						$member = array(
							'uniacid' => $_W['uniacid'],
							'uid' => $mc['uid'],
							'openid' => $_W['openid'],
							'unionId' => $fansInfo['unionid'],
							'nickname' => $fansInfo['nickname'],
							'realname' => $mc['realname'],
							'mobile' => $mc['mobile'],
							'sex' => ($fansInfo['sex'] == 1 ? '男' : '女'),
							'avatar' => $avatar,
							'is_sys' => 1,
							'status' => 1,
							'token' => random(32),
							'addtime' => TIMESTAMP,
						);
						pdo_insert('tiny_wmall_members', $member);
						$member['credit1'] = $mc['credit1'];
						$member['credit2'] = $mc['credit2'];
					}
				} else {
					if(($member['nickname'] != $fansInfo['nickname']) || ($member['avatar'] != $avatar)) {
						$update = array(
							'nickname' => $fansInfo['nickname'],
							'avatar' => $avatar
						);
						pdo_update('tiny_wmall_members', $update, array('id' => $member['id']));
					}
				}
			}
			$_W['member'] = $member;
		}
	} elseif(defined('IN_WXAPP')) {
		if(!empty($_W['openid'])) {
			//这里统一用户
			if(!empty($_W['unionid'])) {
				pdo_update('tiny_wmall_members', array('openid_wxapp' => $_W['openid']), array('uniacid' => $_W['uniacid'], 'unionId' => $_W['unionid']));
				pdo_update('tiny_wmall_members', array('unionId' => $_W['unionid']), array('uniacid' => $_W['uniacid'], 'openid_wxapp' => $_W['openid']));
				member_union($_W['unionid']);
				$member = get_member($_W['unionid'], 'unionId');
			}
			if(empty($member)) {
				$member = get_member($_W['openid'], 'openid_wxapp');
			}
			if(!empty($member)) {
				$_W['member'] = $member;
				$update = array();
				if(empty($member['openid_wxapp'])) {
					$update['openid_wxapp'] = $_W['openid'];
					$_W['member']['openid_wxapp'] = $_W['openid'];
				}
				if(empty($member['unionId']) && !empty($_W['unionid'])) {
					$update['unionId'] = $_W['unionid'];
				}
				if(!empty($update)) {
					pdo_update('tiny_wmall_members', $update, array('id' => $_W['member']['id']));
				}
			}
		}
	} elseif(defined('IN_TTAPP')) {
		if(!empty($_W['openid'])) {
			$member = get_member($_W['openid'], 'openid_ttapp');
			if(!empty($member)) {
				$_W['member'] = $member;
			}
		}
	} elseif(defined('IN_VUE')) {
		if(!empty($_W['openid']) || !empty($_W['itoken'])) {
			//这里统一用户
			if(!empty($_W['unionid'])) {
				member_union($_W['unionid']);
				$member = get_member($_W['unionid'], 'unionId');
			}
			if(!empty($_W['openid']) && empty($member)) {
				$member = get_member($_W['openid'], 'openid');
			}
			if(!empty($_W['itoken']) && empty($member)) {
				$member = get_member($_W['itoken'], 'token');
			}
			if(!is_weixin() && !check_plugin_exist('customerApp')) {
				$member = array();
			}
			if(!empty($member)) {
				$_W['member'] = $member;
				$update = array();
				if(empty($member['unionId']) && !empty($_W['unionid'])) {
					$update['unionId'] = $_W['unionid'];
				}
				if(empty($member['openid']) && !empty($_W['openid'])) {
					$update['openid'] = $_W['openid'];
					$_W['member']['openid'] = $_W['openid'];
				}
				if(!empty($update)) {
					pdo_update('tiny_wmall_members', $update, array('id' => $_W['member']['id']));
				}
			}
		}
	} else {
		$key = "we7_wmall_member_session_{$_W['uniacid']}";
		if(isset($_GPC[$key])) {
			$session = json_decode(base64_decode($_GPC[$key]), true);
			if(is_array($session)) {
				$member = get_member($session['uid']);
				if(is_array($member) && ($session['hash'] == $member['password'])) {
					$_W['member'] = $member;
				} else {
					isetcookie($key, false, -100);
				}
			} else {
				isetcookie($key, false, -100);
			}
		}
	}
	//$_W['member'] = get_member('10158', 'uid');
	if($_W['member']['uid'] > 0) {
		record_member_scan();
		$_W['openid'] = $_W['openid_wechat'] = $_W['member']['openid'];
		$_W['openid_wxapp'] = $_W['member']['openid_wxapp'];
		if(defined('IN_WXAPP')) {
			$_W['openid'] = $_W['member']['openid'] = $_W['openid_wxapp'];
		}
		//顾客端极光推送
		$relation = member_push_token($_W['member']);
		if(!is_error($relation)) {
			$_W['wxapp']['jpush_relation'] = $relation;
		}
		member_group_update();
		$_W['member']['is_store_newmember'] = 1;
		$_W['member']['is_mall_newmember'] = 1;
		$config_newmember_condition = 0;
		if(!empty($_W['we7_wmall']['config']['activity'])) {
			$config_newmember_condition = $_W['we7_wmall']['config']['activity']['newmember']['newmember_condition'];
		}
		if($_GPC['sid'] > 0) {
			$is_store_newmember = is_store_newmember($_GPC['sid']);
			if(!$is_store_newmember) {
				$_W['member']['is_store_newmember'] = 0;
				$_W['member']['is_mall_newmember'] = 0;
			}
		}
		if($_W['member']['is_mall_newmember'] == 1) {
			if($config_newmember_condition == 1) {
				$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status != 6', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
			} else {
				$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), array('id'));
			}
			if(!empty($is_exist)) {
				$_W['member']['is_mall_newmember'] = 0;
			}
		}
		if(!$_W['member']['status'] && $force) {
			$tips = !empty($_W['we7_wmall']['config']['close']['tips']) ? $_W['we7_wmall']['config']['close']['tips'] : '亲，亲,平台休息中。。。';
			imessage(error(-3000, $tips), 'close', 'ajax');
		}
		$sid = intval($_GPC['sid']);
		if($sid > 0 && check_plugin_perm('kabao')) {
			pload()->model('kabao');
			$_W['member']['kabao'] = kabao_get_member_store_vip($sid, $_W['member']['uid'], true);
		}
		return true;
	}
	if($force) {
		$forward = base64_encode($_SERVER['QUERY_STRING']);
		if(defined('IN_WXAPP') || defined('IN_TTAPP')) {
			imessage(error(41009, '请先登录'), '', 'ajax');
		} elseif(defined('IN_VUE')) {
			$result = array(
				'errno' => 41009,
				'message' => '请先登录',
				'sessionid' => $_W['session_id'],
				'justweixin' => !check_plugin_exist('customerApp'),
				'oauthurl' => imurl('system/common/vuesession/userinfo', array('state' => "we7sid-{$_W['session_id']}", 'from' => 'vue'), true),
			);
			imessage($result, '', 'ajax');
		} elseif(is_qianfan()) {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			include itemplate('auth/qianfan');
			exit();
		} elseif(is_majia()) {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			include itemplate('auth/majia');
			exit();
		} else {
			if($_W['ispost']) {
				imessage(error(-1, '请先登录'), imurl('wmall/auth/login', array('forward' => $forward)), 'ajax');
			}
			header("location: " . imurl('wmall/auth/login', array('forward' => $forward)), true);
		}
		exit;
	}
}

function member_union($unionId, $field = 'unionId') {
	global $_W;
	if(empty($unionId)) {
		return false;
	}
	$fields = array(
		'id', 'uid', 'groupid', 'groupid_updatetime', 'uid_majia', 'uid_qianfan', 'openid',
		'openid_wxapp', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname',
		'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_deliveryfee_free_limit',
		'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime',
		'spread1', 'spread2', 'spreadcredit2', 'spreadfixed',
		'svip_status', 'svip_credit1', 'svip_starttime', 'svip_endtime',
	);
	$fields_str = implode(',', $fields);
	$members = pdo_fetchall("select {$fields_str} from " . tablename('tiny_wmall_members') . " where uniacid = :uniacid and {$field} = :unionId order by id asc" , array(':uniacid' => $_W['uniacid'], ':unionId' => $unionId));
	if(empty($members) || count($members) == 1) {
		return false;
	}
	$update = array();
	$uids = $ids = array();
	$setmeals = array();
	foreach($members as $member) {
		if($field == 'mobile') {
			if(!empty($member['avatar'])) {
				$update['avatar'] = $member['avatar'];
			}
			if(!empty($member['nickname'])) {
				$update['nickname'] = $member['nickname'];
			}
			if(!empty($member['realname'])) {
				$update['realname'] = $member['realname'];
			}
			if(!empty($member['openid'])) {
				$update['openid'] = $member['openid'];
			}
			if(!empty($member['unionId'])) {
				$update['unionId'] = $member['unionId'];
			}
			if(!empty($member['openid_wxapp'])) {
				$update['openid_wxapp'] = $member['openid_wxapp'];
			}
			if(!empty($member['openid_wx'])) {
				$update['openid_wx'] = $member['openid_wx'];
			}
		}
		$ids[] = $member['id'];
		$uids[] = $member['uid'];
		if($member['is_sys'] == 2) {
			$update['credit1'] += $member['credit1'];
			$update['credit2'] += $member['credit2'];
		}
		$update['spreadcredit2'] += $member['spreadcredit2'];
		if($member['setmeal_endtime'] > 0) {
			$setmeals[$member['uid']] = $member['setmeal_endtime'];
		}
		if(empty($update['is_spread']) && $member['is_spread'] == 1) {
			$update['is_spread'] = 1;
		}
		if(empty($update['svip_status']) && $member['svip_status'] == 1) {
			$update['svip_status'] = 1;
		}
		$update['svip_credit1'] += $member['svip_credit1'];
		if(!empty($member['svip_starttime'])) {
			if(empty($update['svip_starttime']) || $member['svip_starttime'] < $update['svip_starttime']) {
				$update['svip_starttime'] = $member['svip_starttime'];
			}
		}
		if(!empty($member['svip_endtime'])) {
			if(empty($update['svip_endtime']) || $member['svip_endtime'] > $update['svip_endtime']) {
				$update['svip_endtime'] = $member['svip_endtime'];
			}
		}
	}
	$max_setmeal = max($setmeals);
	$max_setmeal_index = array_search($max_setmeal, $setmeals);
	$setmeal = $setmeals[$max_setmeal_index];
	if(!empty($setmeal)) {
		$update['setmeal_id'] = $setmeal['setmeal_id'];
		$update['setmeal_day_free_limit'] = $setmeal['setmeal_day_free_limit'];
		$update['setmeal_deliveryfee_free_limit'] = $setmeal['setmeal_deliveryfee_free_limit'];
		$update['setmeal_starttime'] = $setmeal['setmeal_starttime'];
		$update['setmeal_endtime'] = $setmeal['setmeal_endtime'];
	}

	$update['is_sys'] = 2;
	$update['uid'] = $uids[0];
	unset($uids[0]);
	unset($ids[0]);
	if(!empty($ids)) {
		foreach($ids as $id) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'fromuid' => $id,
				'touid' => $update['uid'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_member_union_log', $log);
		}
	}
	$ids_str = implode(',', $ids);
	pdo_run("delete from ims_tiny_wmall_members where uniacid = '{$_W['uniacid']}' and id in ({$ids_str})");
	pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $update['uid']));

	$tables = array(
		'ims_tiny_wmall_activity_redpacket_record',
		'ims_tiny_wmall_activity_coupon_record',
		'ims_tiny_wmall_address',
		'ims_tiny_wmall_order',
		'ims_tiny_wmall_order_comment',
		'ims_mc_credits_record',
	);
	$uids_str = implode(',', $uids);
	foreach($tables as $table) {
		pdo_run("update {$table} set uid = '{$update['uid']}' where uniacid = '{$_W['uniacid']}' and uid in ({$uids_str})");
	}
	//跑腿订单，生活圈订单，积分商城没有处理
	$checktables = array(
		'tiny_wmall_svip_meal_order',
		'tiny_wmall_superredpacket_meal_order'
	);
	foreach($checktables as $table) {
		if(pdo_tableexists($table)) {
			pdo_run("update ims_{$table} set uid = '{$update['uid']}' where uniacid = '{$_W['uniacid']}' and uid in ({$uids_str})");
		}
	}
	if($update['is_spread'] == 1) {
		pdo_run("update ims_tiny_wmall_members set 	spread1 = '{$update['uid']}' where spread1 in ({$uids_str})");
		pdo_run("update ims_tiny_wmall_members set 	spread2 = '{$update['uid']}' where spread2 in ({$uids_str})");
	}
	return true;
}

function get_member($openid, $field = 'openid') {
	global $_W;
	$uid = intval($openid);
	$fields = array('id', 'uid', 'groupid', 'groupid_updatetime', 'uid_majia', 'uid_qianfan', 'openid', 'openid_wxapp', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname', 'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_deliveryfee_free_limit', 'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime', 'spread1', 'spread2', 'spreadcredit2', 'spreadfixed', 'svip_status', 'svip_starttime', 'svip_endtime', 'svip_credit1', 'account');
	if($uid == 0) {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], $field => $openid), $fields);
		if(empty($info)) {
			if(strexists($openid, 'sns_qq_')) {
				$openid = str_replace('sns_qq_', '', $openid);
				$condition = ' openid_qq = :openid';
			} elseif (strexists($openid, 'sns_wx_')) {
				$openid = str_replace('sns_wx_', '', $openid);
				$condition = ' openid_wx = :openid';
			}
			if(!empty($condition)) {
				$info = pdo_fetch('select * from ' . tablename('tiny_wmall_members') . " where uniacid=:uniacid and {$condition}", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
		}
	} else {
		$info = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $openid), $fields);
	}
	if(!empty($info)) {
		$groups = member_groups();
		$info['avatar'] = tomedia($info['avatar']);
		$info['groupname'] = $groups[$info['groupid']]['title'];
		$info['svip_credit1'] = floatval($info['svip_credit1']);
		$info['account'] = iunserializer($info['account']);
		$update = array();
		if(empty($info['token'])) {
			$info['token'] = $update['token'] = random(32);
		}
		if($info['svip_status'] == 1 && $info['svip_endtime'] <= TIMESTAMP) {
			$info['svip_status'] = $update['svip_status'] = 2;
		}
		if(!empty($update)) {
			pdo_update('tiny_wmall_members', $update, array('id' => $info['id']));
		}
		$openid = $info['openid'];
		if($info['is_sys'] == 2) {
			if(!empty($openid)) {
				$fans = pdo_fetch('select a.fanid,b.credit1,b.credit2,b.uid from' . tablename('mc_mapping_fans') . ' as a left join ' . tablename('mc_members') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and a.openid = :openid and b.uniacid = :buniacid order by fanid asc', array(':uniacid' => $_W['uniacid'], ':buniacid' => $_W['uniacid'], ':openid' => $openid));
				if(!empty($fans['uid'])) {
					$upgrade = array('uid' => $fans['uid'], 'is_sys' => 1);
					load()->model('mc');
					if($info['credit1'] > 0) {
						mc_credit_update($fans['uid'], 'credit1', $info['credit1']);
						$upgrade['credit1'] = 0;
					}
					if($info['credit2'] > 0) {
						mc_credit_update($fans['uid'], 'credit2', $info['credit2']);
						$upgrade['credit2'] = 0;
					}
					pdo_update('tiny_wmall_members', $upgrade, array('id' => $info['id']));
					if($info['uid'] != $fans['uid']) {
						$tables = array(
							'tiny_wmall_activity_coupon_record',
							'tiny_wmall_address',
							'tiny_wmall_order',
							'tiny_wmall_order_comment',
							'tiny_wmall_member_recharge',
						);
						foreach($tables as $table) {
							pdo_update($table, array('uid' => $fans['uid']), array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']));
						}
					}
					$info['uid'] = $fans['uid'];
					$info['is_sys'] = 1;
					$member = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']), array('credit1', 'credit2'));
					$info['credit1'] = $member['credit1'];
					$info['credit2'] = $member['credit2'];
				}
			}
		} else {
			$member = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $info['uid']), array('credit1', 'credit2'));
			if(empty($member)) {
				pdo_update('tiny_wmall_members', array('is_sys' => 2), array('id' => $info['id']));
			} else {
				$info['credit1'] = $member['credit1'];
				$info['credit2'] = $member['credit2'];
			}
		}
	}
	return $info;
}

function member_register($params) {
	global $_W;
	if(empty($params['openid'])) {
		return error(-1, '微信信息错误');
	}
	$fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $params['openid']));
	if(!empty($fans['uid'])) {
		$mc = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $fans['uid']), array('uid', 'realname', 'mobile', 'gender', 'credit1', 'credit2'));
	}
	if(empty($mc)) {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => date('His') . random(3, true),
			'openid' => $params['openid'],
			'mobile' => $params['mobile'],
			'nickname' => $params['nickname'],
			'realname' => $params['nickname'],
			'sex' => ($params['sex'] == 1 ? '男' : '女'),
			'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132,
			'is_sys' => 2, //模拟用户
			'status' => 1,
			'token' => random(32),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_members', $member);
	} else {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $mc['uid'],
			'openid' => $params['openid'],
			'nickname' => $params['nickname'],
			'realname' => $mc['realname'],
			'mobile' => $params['mobile'] ? $params['mobile'] : $mc['mobile'],
			'sex' => ($params['sex'] == 1 ? '男' : '女'),
			'avatar' => rtrim(rtrim($params['headimgurl'], '0'), 132) . 132,
			'is_sys' => 1,
			'status' => 1,
			'token' => random(32),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_members', $member);
	}
	return $member;
}

function member_uid2token($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$token = pdo_fetchcolumn('select token from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $token;
}

function member_uid2openid($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$openid = pdo_fetchcolumn('select openid from ' . tablename('tiny_wmall_members') . ' where uid = :uid', array(':uid' => $uid));
	return $openid;
}

function member_credit_update($uid, $credittype, $creditval = 0, $log = array(), $wxtpl_notice = true) {
	global $_W;
	$fields = array('id', 'uid', 'groupid', 'groupid_updatetime', 'uid_majia', 'uid_qianfan', 'openid', 'token', 'credit1', 'credit2', 'avatar', 'nickname', 'sex', 'realname', 'mobile', 'password', 'mobile_audit', 'setmeal_id', 'setmeal_day_free_limit', 'setmeal_deliveryfee_free_limit', 'setmeal_starttime', 'setmeal_endtime', 'is_sys', 'status', 'addtime', 'spread1', 'spread2');
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), $fields);
	if(empty($member)) {
		return error(-1, '会员不存在');
	}
	if(!in_array($credittype, array('credit1', 'credit2'))) {
		return error('-1', "积分类型有误");
	}
	$credittype = trim($credittype);
	$creditval = floatval($creditval);
	if(empty($creditval)) {
		return true;
	}
	load()->model('mc');
	if($member['is_sys'] == 1) {
		$result = mc_credit_update($uid, $credittype, $creditval, $log);
	} else {
		$value = $member[$credittype];
		if($creditval > 0 || ($value + $creditval >= 0)) {
			pdo_update('tiny_wmall_members', array($credittype => $value + $creditval), array('uid' => $uid));
			$result = true;
			//日志
			//记录日志(如果没有$log参数或参数不合法,将视为用户自己修改积分);
			if (empty($log) || !is_array($log)) {
				$log = array($uid, '未记录', 0, 0);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $uid,
				'credittype' => $credittype,
				'num' => $creditval,
				'createtime' => TIMESTAMP,
				'operator' => intval($log[0]),
				'module' => 'we7_wmall',
				'clerk_id' => 0,
				'store_id' => 0,
				'remark' => $log[1],
			);
			pdo_insert('mc_credits_record', $data);
		} else {
			return error('-1', "积分类型为“{$credittype}”的积分不够，无法操作。");
		}
	}

	if(!empty($wxtpl_notice) && !is_error($result)) {
		load()->func('communication');
		$openid = member_uid2openid($uid);
		if(empty($openid)) {
			return true;
		}
		$member = get_member($uid);
		$config = $_W['we7_wmall']['config'];
		if($credittype == 'credit1') {
			$params = array(
				'first' => "您在{$config['mall']['title']}的账户积分有新的变动",
				'keyword1' => '积分变动',
				'keyword2' => "{$creditval}积分",
				'keyword3' => date('Y-m-d H:i', TIMESTAMP),
				'keyword4' => $creditval > 0 ? '积分充值' : '积分消费',
				'remark' => implode("\n", array(
					"积分余额:{$member['credit1']}",
					"备注: {$log[1]}"
				))
			);
		} else {
			$params = array(
				'first' => "您在{$config['mall']['title']}的账户余额有新的变动",
				'keyword1' => "余额变动",
				'keyword2' => "{$creditval}{$_W['Lang']['dollarSignCn']}",
				'keyword3' => date('Y-m-d H:i', TIMESTAMP),
				'keyword4' => $creditval > 0 ? '余额充值' : '余额消费',
				'remark' => implode("\n", array(
					"账户余额:{$member['credit2']}" ,
					"备注: {$log[1]}"
				))
			);
		}
		$send = sys_wechat_tpl_format($params);
		$acc = TyAccount::create($_W['acid']);
		$url = ivurl('pages/member/mine', array(), true);
		$miniprogram = '';
		if(MODULE_FAMILY == 'wxapp') {
			$miniprogram = array(
				'appid' => $_W['we7_wmall']['config']['wxapp']['basic']['key'],
				'pagepath'=> "pages/member/mine",
			);
		}
		if(!is_error($acc)) {
			$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send, $url, $miniprogram);
			if(is_error($status)) {
				slog('wxtplNotice', '平台账户变动微信通知会员', $send, $status['message']);
			}
		}
	}
	return $result;
}

function member_oauth_info($url, $account, $openid = '') {
	global $_W, $_GPC;
	if(empty($openid))  {
		$openid = $_W['openid'];
	}
	$oauth = pdo_get('tiny_wmall_oauth_fans', array('appid' => $account['appid'], 'openid' => $openid));
	if(!empty($oauth)) {
		$oauth = array(
			'appid' => $account['appid'],
			'openid' => $oauth['oauth_openid']
		);
		return $oauth;
	}
	mload()->classs('wxaccount');
	$acc = new WxAccount($account);
	if(is_error($acc)) {
		return $acc;
	}
	$code = trim($_GPC['code']);
	if(empty($code)) {
		$state = 'we7sid-'.$_W['session_id'];
		$data = $acc->getOauthCodeUrl($url, $state);
		if(is_error($data)) {
			return $data;
		}
		if(!defined('IN_VUE')) {
			header('Location: ' . $data);
			die;
		} else {
			$result = array(
				'errno' => 41001,
				'message' => '需要获取openid',
				'sessionid' => $_W['session_id'],
				'oauthurl' => $data,
			);
			imessage($result, '', 'ajax');
		}
	} else {
		$data = $acc->getOauthInfo($code);
		if(!is_error($data)) {
			$oauth_openid = $data['openid'];
			$is_exist = pdo_get('tiny_wmall_oauth_fans', array('appid' => $account['appid'], 'openid' => $openid, 'oauth_openid' => $oauth_openid));
			if(empty($is_exist)) {
				$insert = array(
					'appid' => $account['appid'],
					'openid' => $openid,
					'oauth_openid' => $oauth_openid,
				);
				pdo_insert('tiny_wmall_oauth_fans', $insert);
			}
		}
		if(defined('IN_VUE')) {
			$url = irurl(ivurl('pages/public/pay', array('order_id' => $_SESSION['pay_params']['id'], 'order_type' => $_SESSION['pay_params']['order_type'], 'autoPay' => $_SESSION['pay_params']['pay_type']), true));
			header('location:' . $url);
			die;
		}
		return $data;
	}
}

function member_recharge_status_update($order_id, $type, $params) {
	global $_W;
	$order = pdo_get('tiny_wmall_member_recharge', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '充值订单不存在');
	}
	if($type == 'pay') {
		$update = array(
			'is_pay' => 1,
			'pay_type' => $params['type'],
			'paytime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_member_recharge', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
		$tag = iunserializer($order['tag']);
		if($tag['credit2'] > 0) {
			$log = array(
				$order['uid'],
				"用户充值{$tag['credit2']}{$_W['Lang']['dollarSignCn']}"
			);
			member_credit_update($order['uid'], 'credit2', $tag['credit2'], $log);
		}
		if(!empty($tag['grant'])) {
			$array = array(
				'credit1' => '积分',
				'credit2' => $_W['Lang']['dollarSignCn'],
			);
			$log = array(
				$order['uid'],
				"用户充值{$tag['credit2']}{$_W['Lang']['dollarSignCn']}赠送{$tag['grant']['back']}{$array[$tag['grant']['type']]}"
			);
			member_credit_update($order['uid'], $tag['grant']['type'], $tag['grant']['back'], $log);
		}
		if(check_plugin_perm('svip')) {
			mload()->model('plugin');
			pload()->model('svip');
			svip_task_finish_check($order['uid'], 'oneChargeFee', $order);
		}
		return true;
	}
	return true;
}

//member_fetchall_address
function member_fetchall_address($filter = array()) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 1 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach($data as &$li) {
			if(!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if(!empty($filter['serve_radius']) && $dist > ($filter['serve_radius'] * 1000)) {
					$li['available'] = 0;
					$dis_available[$li['id']] = $li;
				} else {
					$li['available'] = 1;
					$available[$li['id']] = $li;
				}
			} else {
				$li['available'] = 0;
				$dis_available[$li['id']] = $li;
			}
		}
		if($filter['nokey'] == 1) {
			$available = array_values($available);
			$dis_available = array_values($dis_available);
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return $data;
}

//member_fetch_address
function member_fetch_address($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_fetch_available_address($sidOrStore) {
	global $_W, $_GPC;
	if(is_array($sidOrStore)) {
		$store = $sidOrStore;
		$sid = $store['id'];
	} else {
		$sid = intval($sidOrStore);
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode','delivery_price', 'delivery_areas', 'delivery_areas1', 'serve_radius', 'not_in_serve_radius'));
	}
	$address = array();
	if(!$_GPC['r']) {
		$is_ok = 0;
		if($_GPC['__aid'] > 0) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => intval($_GPC['__aid'])));
			if(!empty($temp)) {
				$is_ok = is_in_store_area($store, $temp);
				if(!empty($is_ok)) {
					$is_ok = is_in_plateform_radius(array($temp['location_y'], $temp['location_x']));
				}
			}
		}
		if(empty($is_ok)) {
			$temp = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'type' => 1, 'is_default' => 1));
			if(!empty($temp)) {//$temp为空，商户设置在配送半径之外是否允许下单 is_in_store_radius返回true,先在函数外处理
				$is_ok = is_in_store_area($store, $temp, $_GPC['_guess_area']);
				if(!empty($is_ok)) {
					$is_ok = is_in_plateform_radius(array($temp['location_y'], $temp['location_x']));
				}
			}
		}
		if(empty($is_ok)) {
			$addresses = member_fetchall_address();
			foreach($addresses as $li) {
				$is_ok = is_in_store_area($store, $li, $_GPC['_guess_area']);
				if(!empty($is_ok)) {
					$is_ok = is_in_plateform_radius(array($li['location_y'], $li['location_x']));
				}
				if(!empty($is_ok)) {
					$address = $li;
					break;
				}
			}
		} else {
			$address = $temp;
		}
	} else {
		$address_id = intval($_GPC['address_id']);
		$temp = member_fetch_address($address_id);
		$is_ok = is_in_store_area($store, $temp);
		if(!empty($is_ok)) {
			$is_ok = is_in_plateform_radius(array($temp['location_y'], $temp['location_x']));
		}
		if($is_ok) {
			$address = $temp;
		}
	}
	if(empty($store['address_type']) && !empty($address) && $store['delivery_fee_mode'] == 2) {
		$distance_type = $store['delivery_price_extra']['distance_type'];
		$dist = calculate_distance(array($store['location_y'], $store['location_x']), array($address['location_y'], $address['location_x']), $distance_type);
		$check_drag_member_address = get_system_config('takeout.order.check_member_drag_address');
		if($check_drag_member_address == 1) {
			if(!empty($_GPC['__lng']) && !empty($_GPC['__lat'])) {
				$dist1 = calculate_distance(array($store['location_y'], $store['location_x']), array($_GPC['__lng'], $_GPC['__lat']), $distance_type);
				$dist = max($dist, $dist1);
			}
		}
		if($store['delivery_price_extra']['calculate_distance_type'] == 1) {
			$dist = ceil($dist);
		} elseif($store['delivery_price_extra']['calculate_distance_type'] == 2) {
			$dist = floor($dist);
		}
		$address['distance'] = $dist;
		if(is_error($address['distance'])) {
			return error(-1, "获取配送距离失败:{$address['distance']['message']}");
		}
	}
	return $address;
}

function member_fetchall_address_bystore($sidOrStore) {
	global $_W;
	if(is_array($sidOrStore)) {
		$store = $sidOrStore;
		$sid = $store['id'];
	} else {
		$sid = intval($sidOrStore);
		$store = store_fetch($sid, array('location_y', 'location_x', 'delivery_fee_mode', 'delivery_price', 'delivery_areas', 'delivery_areas1', 'serve_radius', 'not_in_serve_radius'));
	}
	if(empty($store) || !isset($store['order_address_limit'])) {
		return false;
	}
	$addresses = array(
		'available' => array(),
		'not_available' => array(),
	);
	$addresstemps = member_fetchall_address();
	foreach($addresstemps as &$li) {
		if($store['address_type'] == 1) {
			$is_ok = is_in_store_radius($store, array('area_id' => $li['area_id']));
			if(empty($is_ok)) {
				$li['available'] = 0;
				$addresses['available'][] = $li;
			} else {
				$li['available'] = 1;
				$addresses['not_available'][] = $li;
			}
		} else {
			if($store['order_address_limit'] > 1) {
				$li['available'] = 0;
				if(!empty($li['location_x']) && !empty($li['location_y'])) {
					$li['is_ok'] = is_in_store_radius($store, array($li['location_y'], $li['location_x']));
					if($li['is_ok'] == 1) {
						$status = is_in_plateform_radius(array($li['location_y'], $li['location_x']));
						if(!empty($status)) {
							$li['available'] = 1;
							$addresses['available'][] = $li;
						} else {
							$li['available'] = 0;
							$addresses['not_available'][] = $li;
						}
					} else {
						$li['available'] = 0;
						$addresses['not_available'][] = $li;
					}
				} else {
					$addresses['not_available'][] = $li;
				}
			} else {
				$li['available'] = 1;
			}
		}
	}
	if($store['address_type'] == 1 || $store['order_address_limit'] > 1) {
		$addresstemps = array_merge($addresses['available'], $addresses['not_available']);
	}
	return $addresstemps;
}

function member_takeout_address_check($sidOrStore, $idOrAddress) {
	global $_GPC;
	if(is_array($sidOrStore)) {
		$store = $sidOrStore;
	} else {
		$store = store_fetch($sidOrStore);
	}
	$address = $idOrAddress;
	if(!is_array($address)) {
		$address = member_fetch_address($idOrAddress);
	}
	if(empty($address)) {
		return error(-1, '地址不存在');
	}
	if($store['order_address_limit'] > 1 || !empty($store['address_type'])) {
		$is_ok = is_in_store_area($store, $address);
		if(!$is_ok) {
			return error(-1, '该收货地址超过门店配送范围,请选择其他收货地址');
		}
		$status = is_in_plateform_radius(array($address['location_y'], $address['location_x']));
		if(!$status) {
			return error(-1, '该收货地址超过平台配送范围,请选择其他收货地址');
		}
	}
	if(empty($store['address_type']) && $store['delivery_fee_mode'] == 2) {
		$distance_type = $store['delivery_price_extra']['distance_type'];
		$dist = calculate_distance(array($store['location_y'], $store['location_x']), array($address['location_y'], $address['location_x']), $distance_type);
		$check_drag_member_address = get_system_config('takeout.order.check_member_drag_address');
		if($check_drag_member_address == 1) {
			if(!empty($_GPC['__lng']) && !empty($_GPC['__lat'])) {
				$dist1 = calculate_distance(array($store['location_y'], $store['location_x']), array($_GPC['__lng'], $_GPC['__lat']), $distance_type);
				$dist = max($dist, $dist1);
			}
		}
		if($store['delivery_price_extra']['calculate_distance_type'] == 1) {
			$dist = ceil($dist);
		} elseif($store['delivery_price_extra']['calculate_distance_type'] == 2) {
			$dist = floor($dist);
		}
		$address['distance'] = $dist;
		if(is_error($address['distance'])) {
			return error(-1, "获取配送距离失败:{$address['distance']['message']}");
		}
	}
	return $address;
}

function member_amount_stat($sid) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime and success_first_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and success_first_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	return $stat;
}

function member_fetch($uid = 0) {
	global $_W;
	if(!$uid) {
		$uid = $_W['member']['uid'];
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	if(!empty($member)) {
		$member['search_data'] = iunserializer($member['search_data']);
		if(!is_array($member['search_data'])) {
			$member['search_data'] = array();
		}
	}
	return $member;
}

function member_fetchall_serve_address($filter = array()) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 2 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($filter['serve_radius']) && !empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach($data as $li) {
			if(!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if($dist > ($filter['serve_radius'] * 1000)) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return array('available' => $data);
}

function member_fetch_serve_address($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 2', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

function member_plateform_amount_stat() {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	return $stat;
}

function tosetmeal($setmeal_id, $or = false) {
	global $_W;
	$data = pdo_fetch('select id, uniacid, title from' . tablename('tiny_wmall_delivery_cards') . ' where id = :id and uniacid = :uniacid', array(':id' => $setmeal_id, ':uniacid' => $_W['uniacid']));
	if($or) {
		return $data;
	} else {
		return $data['title'];
	}
}

function member_groups() {
	global $_W;
	$config_member = $_W['we7_wmall']['config']['member'];
	return $config_member['group'];
}

function member_group_update($wx_tpl = false) {
	global $_W;
	if($_W['member']['groupid_updatetime'] > TIMESTAMP - 600) {
		return true;
	}
	$condition = ' where uniacid = :uniacid and is_pay = 1 and uid = :uid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':uid' => $_W['member']['uid'],
	);
	$config_member = $_W['we7_wmall']['config']['member'];
	if($config_member['group_update_mode'] == 'order_money') {
		//外卖订单和店内订单消费总额满
		$condition .= " and status = 5";
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config_member['group_update_mode'] == 'order_count') {
		//外卖订单和店内订单消费次数满
		$condition .= " and status = 5";
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_order') . $condition, $params);
		$result = intval($result);
	} elseif($config_member['group_update_mode'] == 'delivery_money') {
		//跑腿订单消费总额满
		$condition .= " and status = 3";
		$result = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_errander_order') . $condition, $params);
		$result = round($result, 2);
	} elseif($config_member['group_update_mode'] == 'delivery_count') {
		//跑腿订单消费次数满
		$condition .= " and status = 3";
		$result = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_errander_order') . $condition, $params);
		$result = intval($result);
	} elseif($config_member['group_update_mode'] == 'count_money') {
		//外卖订单、店内订单和跑腿订单消费总额满
		$order = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_order') . $condition . " and status = 5", $params);
		$errander = pdo_fetchcolumn('select sum(final_fee) from' . tablename('tiny_wmall_errander_order') . $condition . " and status = 3", $params);
		$result = $order + $errander;
		$result = round($result, 2);
	}
	$old_group_id = $_W['member']['groupid'];
	$groups = member_groups();
	foreach($groups as $group) {
		if(($result >= $group['group_condition']) && ($group['group_condition'] > $groups[$old_group_id]['group_condition'])) {
			$group_id = $group['id'];
		}
	}
	pdo_update('tiny_wmall_members', array('groupid' => $group_id, 'groupid_updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	if($wx_tpl) {
		//微信模板消息
	}
	$_W['member']['groupid'] = $group_id;
	$_W['member']['groupname'] = $groups[$group_id]['title'];
	return true;
}

function member_invoices() {
	global $_W;
	$invoice = pdo_fetchall('select * from' . tablename('tiny_wmall_member_invoice') . 'where uniacid = :uniacid and uid = :uid order by addtime asc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	return $invoice;
}

function member_invoice($id) {
	global $_W;
	$invoice = pdo_get('tiny_wmall_member_invoice', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => $id));
	return $invoice;
}

function member_recharge_type($channel, $key = 'all') {
	$data = array(
		'wechat' => array(
			'css' => 'label label-success',
			'text' => '微信',
		),
		'alipay' => array(
			'css' => 'label label-info',
			'text' => '支付宝',
		),
	);
	if($key == 'all') {
		return $data[$channel];
	} elseif($key == 'text') {
		return $data[$channel]['text'];
	} elseif($key == 'css') {
		return $data[$channel]['css'];
	}
}

function member_errander_address_add($address, $mode = 'favorite') {
	global $_W;
	if(empty($address['address']) || empty($address['name']) || empty($address['location_x']) || empty($address['location_y'])) {
		return false;
	}
	$is_exist = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'location_x' => $address['location_x'], 'location_y' => $address['location_y'], 'mode' => $mode));
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'name' => $address['address'],
		'address' => $address['name'],
		'number' => $address['number'],
		'location_x' => $address['location_x'],
		'location_y' => $address['location_y'],
		'type' => 3,
		'mode' => $mode
	);
	if(empty($is_exist)) {
		pdo_insert('tiny_wmall_address', $insert);
		$id = pdo_insertid();
	} else {
		$id = $is_exist['id'];
		pdo_update('tiny_wmall_address', $insert, array('id' => $id));
	}
	return $id;
}

function member_errander_address_del($id) {
	global $_W;
	pdo_delete('tiny_wmall_address', array('id' => $id));
	return true;
}

function member_openid2wxapp($openid = '') {
	global $_W;
	if(empty($openid)) {
		$openid = $_W['openid'];
	}
	$openid_wxapp = pdo_fetchcolumn('select openid_wxapp from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
	return $openid_wxapp;
}

if(!function_exists('member_wxapp2openid')) {
	function member_wxapp2openid($openid_wxapp = '') {
		global $_W;
		if(empty($openid_wxapp)) {
			$openid_wxapp = $_W['openid_wxapp'];
		}
		$openid = pdo_fetchcolumn('select openid from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and openid_wxapp = :openid_wxapp', array(':uniacid' => $_W['uniacid'], ':openid_wxapp' => $openid_wxapp));
		return $openid;
	}
}

function is_store_newmember($sid) {
	global $_W;
	$config_newmember_condition = 0;
	if(!empty($_W['we7_wmall']['config']['activity'])) {
		$config_newmember_condition = $_W['we7_wmall']['config']['activity']['newmember']['newmember_condition'];
	}
	if($config_newmember_condition == 1) {
		$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and status != 6', array(':uniacid' => $_W['uniacid'], ':sid' => intval($sid), ':uid' => $_W['member']['uid']));
	} else {
		$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => intval($sid), 'uid' => $_W['member']['uid']), array('id'));
	}
	if($is_exist) {
		return false;
	}
	return true;
}

function member_is_in_black($plugin, $uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$is_black = pdo_get('tiny_wmall_member_black', array('uniacid' => $_W['uniacid'], 'uid' => intval($uid), 'plugin' => trim($plugin)), array('id'));
	if($is_black) {
		return true;
	}
	return false;
}

function member_plateform_order_num($orderType, $uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	if($orderType == 'waimai') {
		$orderNumber = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status < 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
	} elseif($orderType == 'errander') {
		$orderNumber = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status < 4', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
	}
	return $orderNumber;
}

function member_store_order_num($sid, $uid = 0) {
	global $_W;
	if(empty($sid)) {
		return false;
	}
	if(empty($uid)) {
		$uid = $_W['member']['uid'];
	}
	$store_orderNumber = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and sid = :sid and is_pay = 1 and status < 6', array(':uniacid' => $_W['uniacid'], 'sid' => $sid, ':uid' => $uid));
	return $store_orderNumber;
}

function member_push_token($memberOrUid) {
	global $_W;
	if(!is_array($memberOrUid)) {
		$uid = intval($memberOrUid);
		$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('uid', 'token'));
	} else {
		$member = $memberOrUid;
	}
	if(empty($member)) {
		return error(-1, '顾客不存在');
	}
	if(empty($member['token'])) {
		$member['token'] = random(32);
		pdo_update('tiny_wmall_members', array('token' => $member['token']), array('uid' => $member['uid']));
	}
	$config = $_W['we7_wmall']['config']['app']['customer'];
	$relation = array(
		'alias' => $member['token'],
		'tags' => array(
			$config['serial_sn'],
		),
	);
	$code = md5(iserializer($relation));
	$relation['code'] = $code;
	return $relation;
}

function Jpush_member_send($title, $alert, $extras = array(), $audience = '') {
	global $_W;
	$config = $_W['we7_wmall']['config']['app']['customer'];
	if(empty($config['jpush_key']) || empty($config['jpush_secret'])) {
		return error(-1, 'key或secret不完善');
	}
	$tag = trim($config['serial_sn']);
	if(empty($audience)) {
		$audience = array(
			'tag' => array(
				$tag
			)
		);
	}
	$jpush = array(
		'platform' => 'android',
		'audience' => $audience,
		/*		'message' => array(
					'msg_content' => $alert,
					'title' => $title,
					'extras' => $extras
				),*/
		'notification' => array(
			'alert' => $alert,
			'alert' => $alert,
			"title'=>'JPush test",
			'extras' => $extras
		),
	);
	load()->func('communication');
	$extra = array(
		'Authorization' => "Basic " . base64_encode("{$config['jpush_key']}:{$config['jpush_secret']}")
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush), $extra);
	$return = Jpush_response_parse($response);
	if(is_error($return)) {
		slog('customerappJpush', '顾客App极光推送(andriod)通知顾客', $jpush, $return['message']);
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
				'sound' => '',
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
		slog('customerappJpush', '顾客App极光推送(ios)通知顾客', $jpush_ios, $return['message']);
	}
	return true;
}