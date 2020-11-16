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
	$_W['page']['title'] = '专享红包发放记录';
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':type' => 'person'
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
		$row['data'] = json_decode(base64_decode($row['data']), true);
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('personList');
}

elseif($op == 'post') {
	$_W['page']['title'] = '专享红包';
	$id = intval($_GPC['id']);
	$from = trim($_GPC['from']);
	if($_W['ispost']) {
		if(!empty($id) && $from != 'copy') {
			imessage(error(-1, '已创建的专享红包活动不能再编辑'), iurl('superRedpacket/person/send'), 'ajax');
		}
		$redpacket = array(
			'scene' => trim($_GPC['scene']),
			'order_type_limit' => intval($_GPC['order_type_limit']),
			'name' => trim($_GPC['name']),
			'discount' => floatval($_GPC['discount']),
			'condition' => floatval($_GPC['condition']),
			'grant_days_effect' => intval($_GPC['grant_days_effect']),
			'use_days_limit' => intval($_GPC['use_days_limit']),
			'categorys' => array(),
			'times' => array()
		);
		if(empty($redpacket['name'])) {
			imessage(error(-1, '红包名称不能为空'), '', 'ajax');
		}
		if($redpacket['discount'] < 0) {
			imessage(error(-1, '红包金额不能小于零'), '', 'ajax');
		}
		if($redpacket['condition'] < 0) {
			imessage(error(-1, '红包的使用条件不能小于零'), '', 'ajax');
		}
		if($redpacket['grant_days_effect'] < 0) {
			imessage(error(-1, '红包的领取后几天内有效的值不能小于零'), '', 'ajax');
		}
		if($redpacket['use_days_limit'] < 0) {
			imessage(error(-1, '红包的有效期不能小于零'), '', 'ajax');
		}
		if(!empty($_GPC['category']['id'])) {
			foreach($_GPC['category']['id'] as $ckey => $cid) {
				$cid = intval($cid);
				if($cid > 0) {
					$redpacket['categorys'][] = array(
						'id' => $cid,
						'title' => trim($_GPC['category']['title'][$ckey]),
						'src' => trim($_GPC['category']['src'][$ckey])
					);
				}
			}
		}
		if(!empty($_GPC['start_hour'])) {
			foreach($_GPC['start_hour'] as $skey => $sval) {
				if(!empty($sval) && !empty($_GPC['end_hour'][$skey])) {
					$redpacket['times'][] = array(
						'start_hour' => $sval,
						'end_hour' => $_GPC['end_hour'][$skey]
					);
				}
			}
		}

		$num = intval($_GPC['num']);
		if($num <= 0) {
			imessage(error(-1, '每人发放数量应大于零'), ireferer(), 'ajax');
		}

		$type = trim($_GPC['type']);
		if($type == 1) {
			$uids = str_replace('，', ',', $_GPC['uid']);
			$uids = array_filter(explode(',', $uids));
		}
		if(empty($uids)) {
			imessage(error(-1, '发放对象为空'), ireferer(), 'ajax');
		}

		$unissued_uid = array();
		foreach($uids as $key => $value) {
			$unissued_uid[] = array(
				'uid' => $value,
				'unissued' => $num
			);
		}
		$grant_object = array(
			'total' => count($uids), //发放多少人
			'num' => $num, //每人发放数量
			'uids' => $uids, //发放给哪些人
			'grant_success' => 0, //成功发放几人
			'unissued_uid' => $unissued_uid //未发放的人及未发放次数
		);
		$data = array(
			'redpacket' => $redpacket,
			'template_notice' => intval($_GPC['template_notice'])
		);
		if(!empty($id) && $from == 'copy') {
			$update = array(
				'name' => $redpacket['name'],
				'data' => base64_encode(json_encode($data)),
				'grant_object' => iserializer($grant_object),
			);
			pdo_update('tiny_wmall_superredpacket', $update, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'name' => $redpacket['name'],
				'type' => 'person',
				'data' => base64_encode(json_encode($data)),
				'grant_object' => iserializer($grant_object),
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_superredpacket', $insert);
			$id = pdo_insertid();
		}
		imessage(error(0, '专享红包设置成功'), iurl('superRedpacket/person/send', array('id' => $id)), 'ajax');
	}
	if(!empty($id)) {
		$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if(!empty($superRedpacket)) {
			$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
			$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
			$superRedpacket['grant_object']['uids'] = implode(',', $superRedpacket['grant_object']['uids']);
		}
	}
	include itemplate('personPost');
}

elseif($op == 'send') {
	$_W['page']['title'] = '发送专享红包';
	$id = intval($_GPC['id']);
	$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(empty($superRedpacket)) {
		imessage('该红包不存在或已经删除', ireferer(), 'error');
	}
	$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);
	$redpacket = $superRedpacket['data']['redpacket'];
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
		foreach($uids as $key => &$uid) {
			$unissued = intval($uid['unissued']);
			if($unissued <= 0) {
				continue;
			}
			for($i = 0; $i < $unissued; $i++) {
				if(empty($redpacket['times'])) {
					$redpacket['times'] = array();
				}
				$params = array(
					'title' => $redpacket['name'],
					'agentid' => $superRedpacket['agentid'],
					'activity_id' => $superRedpacket['id'],
					'uid' => $uid['uid'],
					'channel' => 'superRedpacket',
					'type' => 'person',
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
				$uid['unissued']--;
			}
			if($superRedpacket['data']['template_notice'] == 1) {
				$openid = member_uid2openid($uid['uid']);
				if(!empty($openid)) {
					$config = $_W['we7_wmall']['config'];
					$discount = $redpacket['discount'] * $superRedpacket['grant_object']['num'];
					$params = array(
						'first' => "您在{$config['mall']['title']}的账户有{$superRedpacket['grant_object']['num']}个代金券到账",
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
	include itemplate('personSend');
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

elseif($op == 'copy') {
	$id = intval($_GPC['id']);
	$superRedpacket = pdo_fetch('select * from ' . tablename('tiny_wmall_superredpacket') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if(empty($superRedpacket)) {
		imessage('该红包不存在或已经删除', ireferer(), 'error');
	}
	$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	$superRedpacket['grant_object'] = iunserializer($superRedpacket['grant_object']);

	$unissued_uid = array();
	foreach($superRedpacket['grant_object']['uids'] as $key => $value) {
		$unissued_uid[] = array(
			'uid' => $value,
			'unissued' => $superRedpacket['grant_object']['num']
		);
	}

	$grant_object = array(
		'total' => $superRedpacket['grant_object']['total'],
		'num' => $superRedpacket['grant_object']['num'],
		'uids' =>  $superRedpacket['grant_object']['uids'],
		'grant_success' => 0,
		'unissued_uid' => $unissued_uid
	);

	$insert = array(
		'uniacid' => $_W['uniacid'],
		'name' => $superRedpacket['name'],
		'type' => 'person',
		'data' => base64_encode(json_encode($superRedpacket['data'])),
		'grant_object' => iserializer($grant_object),
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_superredpacket', $insert);
	$id = pdo_insertid();
	imessage(error(0, '复制成功'), iurl('superRedpacket/person/post', array('id' => $id, 'from' => 'copy')), 'ajax');
}