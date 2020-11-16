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
mload()->model('member');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '推广员提现记录';
	$condition = " where a.uniacid = :uniacid";
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$status = $_GPC['status'] ? intval($_GPC['status']) : 0;
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = intval($_GPC['status']);
	}
	$channel = trim($_GPC['channel']);
	if(in_array($channel, array('credit', 'bank', 'alipay'))) {
		$condition .= ' and a.channel = :channel';
		$params[':channel'] = $channel;
	} elseif($channel == 'wechat') {
		$condition .= " and (a.channel = 'weixin' or a.channel = 'wxapp')";
	}
	$keywords = trim($_GPC['keywords']);
	if(!empty($keywords)) {
		$condition .= " and (b.realname like '%{$keywords}%' or mobile like '%{$keywords}%')";
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND a.addtime > :start AND a.addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$condition .= ' and a.addtime >= :start';
			$params[':start'] = $starttime;
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_spread_getcash_log') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.spreadid = b.uid ' . $condition, $params);
	$records = pdo_fetchall('SELECT a.*,b.realname,b.avatar,b.nickname FROM ' . tablename('tiny_wmall_spread_getcash_log') . ' as a left join '. tablename('tiny_wmall_members') . ' as b on a.spreadid = b.uid' . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		$toaccount_status_arr = getcash_toaccount_status();
		foreach($records as &$val) {
			$val['account'] = iunserializer($val['account']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$update = array(
		'status' => $status,
		'endtime' => TIMESTAMP
	);
	if($status == 1) {
		$update['toaccount_status'] = 2;
	}
	pdo_update('tiny_wmall_spread_getcash_log', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	$member = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	//需要有微信模板消息通知
	sys_notice_spread_getcash($id, 'success');
	imessage(error(0, '设置提现状态成功'), '', 'ajax');
}

if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功, 无法打款'), ireferer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), ireferer(), 'ajax');
	}
	$log['account'] = iunserializer($log['account']);
	$channel = $log['channel'];
	if(in_array($channel, array('weixin', 'wxapp'))) {
		if(empty($log['account']['realname'])) {
			imessage(error(-1, '推广员微信信息不完善,无法进行微信付款'), '', 'ajax');
		}
		$params = array(
			'partner_trade_no' => $log['trade_no'],
			'openid' => $log['account']['openid'],
			'check_name' => 'FORCE_CHECK',
			're_user_name' => $log['account']['realname'],
			'amount' => $log['final_fee'] * 100,
			'desc' => "{$log['account']['realname']}" . date('Y-m-d H:i', $log['addtime']) . "推广佣金提现申请"
		);
		mload()->classs('wxpay');
		if($log['channel'] == 'wxapp') {
			$params['openid'] = $log['account']['openid_wxapp'];
			if(empty($params['openid'])) {
				imessage(error(-1, '模块版本为小程序版。未获取到推广员针对小程序的openid'), '', 'ajax');
			}
			$pay = new Wxpay('wxapp');
		} else {
			if(empty($params['openid'])) {
				imessage(error(-1, '模块版本为公众号版。未获取到推广员针对公众号的openid'), '', 'ajax');
			}
			$pay = new Wxpay();
		}
		$response = $pay->mktTransfers($params);
	} elseif($channel == 'bank') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$params = array(
			'partner_trade_no' => $log['trade_no'],
			'enc_bank_no' => $log['account']['account'],
			'enc_true_name' => $log['account']['realname'],
			'bank_code' => $log['account']['id'],
			'amount' => $log['final_fee'] * 100,
			'desc' => "{$log['account']['realname']}" . date('Y-m-d H:i', $log['addtime']) . "推广佣金提现申请"
		);
		$response = $pay->mktPayBank($params);
	} elseif($channel == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay();
		$params = array(
			'out_biz_no' => $log['trade_no'],
			'payee_account' =>  $log['account']['account'],
			'amount' => $log['final_fee'],
			'payee_real_name' => $log['account']['realname'],
			'remark' => "{$log['account']['realname']}" . date('Y-m-d H:i', $log['addtime']) . "推广佣金提现申请"
		);
		$response = $pay->transfer($params);
	}
	if(is_error($response)) {
		imessage(error(-1, $response['message']), '', 'ajax');
	}
	$update = array(
		'status' => 1,
		'endtime' => TIMESTAMP,
		'toaccount_status' => 1
	);
	if(in_array($log['channel'], array('weixin', 'wxapp', 'alipay'))) {
		$update['toaccount_status'] = 2;
	}
	pdo_update('tiny_wmall_spread_getcash_log', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
	sys_notice_spread_getcash($id, 'success');
	imessage(error(0, '打款成功'), '', 'ajax');
}

if($op == 'balance') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$uid = $log['spreadid'];
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), ireferer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), ireferer(), 'ajax');
	}
	pdo_update('tiny_wmall_spread_getcash_log', array('status' => 1, 'toaccount_status' => 2, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	member_credit_update($uid, 'credit2', $log['final_fee']);
	imessage(error(0, '打款成功'), '', 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 1) {
		imessage(error(-1, '本次提现已成功,无法撤销'), ireferer(), 'ajax');
	} elseif($log['status'] == 3) {
		imessage(error(-1, '本次提现已撤销'), ireferer(), 'ajax');
	}
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $log['spreadid']));
	if($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		$extra = array(
			'trade_type' => 3,
			'extra' => '',
			'remark' => $remark,
		);
		spread_update_credit2($log['spreadid'], $log['get_fee'], $extra);
		pdo_update('tiny_wmall_spread_getcash_log', array('status' => 3, 'toaccount_status' => 3, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
		sys_notice_spread_getcash($id, 'cancel', $remark);
		imessage(error(0, '提现撤销成功'), ireferer(), 'ajax');
	}
	include itemplate('getcashOp');
	die();
}

elseif($op == 'query') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_spread_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($log['status'] == 2) {
		return error(-1, '该提现正在申请中，请等待管理员审核');
	}
	if($log['status'] == 3) {
		return error(-1, '该提现申请已撤销');
	}
	if($log['channel'] != 'bank' || $log['toaccount_status'] == 2) {
		return error(0, '该提现已成功到账');
	}
	if($log['toaccount_status'] == 3) {
		return error(-1, '该提现已失败，请联系管理员处理');
	}
	$params = array(
		'partner_trade_no' => $log['trade_no']
	);
	mload()->classs('wxpay');
	$pay = new WxPay();
	$response = $pay->mktQueryBank($params);
	if(is_error($response)) {
		imessage($response, '', 'ajax');
	}
	$result = $response['message'];
	if(in_array($result['status'], array('SUCCESS', 'FAILED', 'BANK_FAIL'))) {
		pdo_update('tiny_wmall_spread_getcash_log', array('toaccount_status' => $result['toaccount_status']), array('uniacid' => $_W['uniacid'], 'id' => $log['id']));
	}
	imessage(error($result['errno'], $result['msg']), '', 'ajax');
}

include itemplate('getcash');