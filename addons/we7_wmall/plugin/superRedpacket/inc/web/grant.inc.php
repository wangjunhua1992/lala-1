<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op'])? trim($_GPC['op']): 'list';

if($op == 'list') {
	$_W['page']['title'] = '超级红包列表';
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':type' => 'superRedpacket'
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) FROM ' . tablename('tiny_wmall_superredpacket') .  $condition, $params);
	$superRedpackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_superredpacket') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($superRedpackets as &$row) {
		$row['grant_object'] = iunserializer($row['grant_object']);
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('grantList');
}

elseif($op == 'post') {
	$_W['page']['title'] = '超级红包设置';
	$id = intval($_GPC['id']);
	$from = trim($_GPC['from']);
	if($_W['ispost']) {
		$data = $_GPC['data'];
		if($data['customer']['type'] == 1) {
			$data['customer']['uid'] = str_replace('，', ',', $data['customer']['uid']);
			$uid = array_filter(explode(',', $data['customer']['uid']));
		} else {
			$condition = ' where uniacid = :uniacid';
			$params = array(
				':uniacid' => $_W['uniacid']
			);
			if($data['customer']['type'] > 1) {
				$time = strtotime('-30 days');
				if($data['customer']['type'] == 2) {
					$condition .= ' and success_last_time >= :time';
				} elseif($data['customer']['type'] == 3) {
					$condition .= ' and success_last_time < :time';
				} elseif($data['customer']['type'] == 4) {
					$condition .= ' and cancel_last_time >= :time';
				}
				$params[':time'] = $time;
			}
			$uid = pdo_fetchall('select uid from ' . tablename('tiny_wmall_members') . $condition, $params, 'uid');
			$uid = array_keys($uid);
		}
		if(empty($uid)) {
			imessage(error(-1, '发放对象为空'), ireferer(), 'ajax');
		}
		$grant_object = array(
			'total' => count($uid),
			'grant_success' => 0,
			//'grant_uid' => $uid,
			'unissued_uid' => $uid
		);
		$menudata = array(
			'uniacid' => $_W['uniacid'],
			'name' => $data['name'],
			'type' => 'superRedpacket',
			'data' => base64_encode(json_encode($data)),
			'grant_object' => iserializer($grant_object)
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_superredpacket', $menudata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$menudata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_superredpacket', $menudata);
			$id = pdo_insertid();
		}
		imessage(error(0, '超级红包设置成功'), iurl('superRedpacket/grant/send', array('id' => $id)), 'ajax');
	}
	if(!empty($id)) {
		$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if(!empty($superRedpacket)) {
			$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
			$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
		}
	}
	include itemplate('grantPost');
}

elseif($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除成功'), ireferer(), 'ajax');
}

elseif($op == 'send') {
	$_W['page']['title'] = '发送超级红包';
	$id = intval($_GPC['id']);
	$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(empty($superRedpacket)) {
		imessage('该红包不存在或已经删除', ireferer(), 'error');
	}
	$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
	if($_W['ispost']) {
		mload()->model('redPacket');
		mload()->model('member');
		$total = $superRedpacket['grant_object']['total'];
		$superRedpacket['grant_object']['unissued_uid'] = array_values($superRedpacket['grant_object']['unissued_uid']);
		$uids = array_slice($superRedpacket['grant_object']['unissued_uid'], 0, 20);
		$more = 1;
		if(count($uids) < 20 || $total <= 20) {
			$more = 0;
		}
		foreach($uids as $key => $uid) {
			$discount = 0;
			$num = 0;
			foreach($superRedpacket['data']['redpackets'] as $redpacket) {
				if(empty($redpacket['times'])) {
					$redpacket['times'] = array();
				}
				$params = array(
					'title' => $redpacket['name'],
					'agentid' => $superRedpacket['agentid'],
					'activity_id' => $superRedpacket['id'],
					'uid' => $uid,
					'channel' => 'superRedpacket',
					'type' => 'grant',
					'discount' => $redpacket['discount'],
					'condition' => $redpacket['condition'],
					'grant_days_effect' => $redpacket['grant_days_effect'],
					'days_limit' =>  $redpacket['use_days_limit'],
					'is_show' => 0,
					'scene' => $redpacket['scene'],
					'order_type_limit' => $redpacket['order_type_limit'],
				);
				$times_limit = array();
				if(!empty($redpacket['times'])) {
					foreach($redpacket['times'] as $time) {
						if($time['start_hour'] && $time['end_hour']) {
							$times_limit[] = $time;
						}
					}
				}
				if(!empty($times_limit)) {
					$params['times_limit'] = iserializer($times_limit);
				}
				$category_limit = array();
				if(!empty($redpacket['categorys'])) {
					foreach($redpacket['categorys'] as $category) {
						$category_limit[] = $category['id'];
					}
				}
				$params['category_limit'] = implode('|', $category_limit);
				redPacket_grant($params, false);
				$discount += $params['discount'];
				$num++;
			}
			if($superRedpacket['data']['customer']['template_notice'] == 1) {
				$openid = member_uid2openid($uid);
				if(!empty($openid)) {
					$config = $_W['we7_wmall']['config'];
					$params = array(
						'first' => "您在{$config['mall']['title']}的账户有{$num}个代金券到账",
						'keyword1' => "账户代金券",
						'keyword2' => "{$discount}{$_W['Lang']['dollarSignCn']}",
						'keyword3' => date('Y-m-d H:i', TIMESTAMP),
						'keyword4' => "账户有新的代金券到账",
						'remark' => implode("\n", array(
							"感谢您对{$config['mall']['title']}平台的支持与厚爱。点击查看红包>>"
						))
					);
					$send = sys_wechat_tpl_format($params);
					load()->func('communication');
					$acc = WeAccount::create($_W['acid']);
					$url = ivurl('pages/home/index', array(), true);
					$status = $acc->sendTplNotice($openid, $_W['we7_wmall']['config']['notice']['wechat']['account_change_tpl'], $send, $url);
					if(is_error($status)) {
						slog('wxtplNotice', '发放平台红包微信通知顾客', $send, $status['message']);
					}
				}
			}
			unset($superRedpacket['grant_object']['unissued_uid'][$key]);
			$superRedpacket['grant_object']['grant_success']++;
		}
		$superRedpacket['grant_object']['unissued_uid'] = array_values($superRedpacket['grant_object']['unissued_uid']);
		pdo_update('tiny_wmall_superredpacket', array('grant_object' => iserializer($superRedpacket['grant_object'])), array('id' => $id, 'uniacid' => $_W['uniacid']));
		imessage(error(0, $more), '', 'ajax');
	}
	include itemplate('grantSend');
}

elseif($op == 'copy') {
	$id = intval($_GPC['id']);
	$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(empty($superRedpacket)) {
		imessage('该红包不存在或已经删除', ireferer(), 'error');
	}
	$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
	$data = $superRedpacket['data'];
	if($data['customer']['type'] == 1) {
		$data['customer']['uid'] = str_replace('，', ',', $data['customer']['uid']);
		$uid = array_filter(explode(',', $data['customer']['uid']));
	} else {
		$condition = ' where uniacid = :uniacid';
		$params = array(
			':uniacid' => $_W['uniacid']
		);
		if($data['customer']['type'] > 1) {
			$time = strtotime('-30 days');
			if($data['customer']['type'] == 2) {
				$condition .= ' and success_last_time >= :time';
			} elseif($data['customer']['type'] == 3) {
				$condition .= ' and success_last_time < :time';
			} elseif($data['customer']['type'] == 4) {
				$condition .= ' and cancel_last_time >= :time';
			}
			$params[':time'] = $time;
		}
		$uid = pdo_fetchall('select uid from ' . tablename('tiny_wmall_members') . $condition, $params, 'uid');
		$uid = array_keys($uid);
	}
	if(empty($uid)) {
		imessage(error(-1, '发放对象为空'), ireferer(), 'ajax');
	}
	$grant_object = array(
		'total' => count($uid),
		'grant_success' => 0,
		'unissued_uid' => $uid
	);
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'name' => $data['name'],
		'type' => 'superRedpacket',
		'data' => base64_encode(json_encode($data)),
		'grant_object' => iserializer($grant_object),
		'addtime' => TIMESTAMP
	);
	pdo_insert('tiny_wmall_superredpacket', $insert);
	$id = pdo_insertid();
	imessage(error(0, '复制成功'), iurl('superRedpacket/grant/post', array('id' => $id, 'from' => 'copy')), 'ajax');
}