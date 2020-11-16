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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';


if($op == 'index') {
	$_W['page']['title'] = '参与记录';
	$condition = ' as record left join ' . tablename('tiny_wmall_wheel') . ' as wheel on record.activity_id = wheel.id left join ' . tablename('tiny_wmall_members') . ' as member on record.uid = member.uid where record.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$wid = intval($_GPC['wid']);
	if (!empty($wid)) {
		$condition .= ' and record.activity_id = :wid';
		$params[':wid'] = $wid;
	}
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$condition .= ' and record.type = :type';
		$params[':type'] = $type;
	}
	$uid = intval($_GPC['uid']);
	if(!empty($uid)) {
		$condition .= ' and record.uid = :uid';
		$params[':uid'] = $uid;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$total = pdo_fetchcolumn('select count(*) FROM ' . tablename('tiny_wmall_wheel_record') .  $condition, $params);
	$records = pdo_fetchall('select record.*,record.status as record_status,record.id as record_id,record.addtime as record_addtime, wheel.*, member.nickname, member.avatar from ' . tablename('tiny_wmall_wheel_record') . $condition . ' order by record_id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$wheels = pdo_fetchall('select * from ' . tablename('tiny_wmall_wheel'));
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['award'] = iunserializer($val['award']);
			if($val['type'] == 'noaward') {
				$val['award_value'] = $val['award']['note'];
				$val['award_type'] = award_type($val['award']['data']['takepartback']['type']);
			} else {
				$val['type'] = awards_rank($val['type'],true);
				$val['award_type'] = award_type($val['award_type']);
				if($val['award_type']['name'] != 'redpacket') {
					$val['award_value'] = $val['award']['data']['value'];
				} else {
					foreach($val['award']['data']['value'] as $redpacket) {
						$val['award_value'][] = "红包：满{$redpacket['condition']}减{$redpacket['discount']}";
					}
				}
			}
		}
	}
	include itemplate('record');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_wheel_record',  array('status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '奖品已发出'), iurl('wheel/record/index'), 'ajax');
}