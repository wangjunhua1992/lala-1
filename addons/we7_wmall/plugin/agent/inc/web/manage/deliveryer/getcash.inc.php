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
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '配送员提现记录';

	$condition = ' WHERE uniacid = :uniacid AND agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);

	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}

	$channel = trim($_GPC['channel']);
	if(!empty($channel)) {
		if($channel == 'weixin') {
			$condition .= " AND (channel = 'weixin' OR channel = 'wxapp') ";
		} else {
			$condition .= ' AND channel = :channel';
			$params[':channel'] = $channel;
		}
	}

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND addtime > :start AND addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);
			$condition .= ' and addtime >= :start';
			$params[':start'] = $starttime;
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		$toaccount_status_arr = getcash_toaccount_status();
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
			if($row['channel'] == 'weixin' || $row['channel'] == 'wxapp') {
				$row['channel_cn'] = '打款到微信零钱';
			} elseif($row['channel'] == 'alipay') {
				$row['channel_cn'] = '打款到支付宝余额';
			} elseif($row['channel'] == 'bank') {
				$row['channel_cn'] = '打款到银行卡';
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
	$channels = getcash_channels();
}

/*if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	if(empty($log)) {
		imessage(error(-1, '提现记录不存在'), '', 'ajax');
	}
	$log['account'] = iunserializer($log['account']);
	if(!is_array($log['account'])) {
		$log['account'] = array();
	}
	if($log['status'] == 1) {
		imessage(error(-1, '该提现记录已处理'), '', 'ajax');
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $log['deliveryer_id']));
	if(empty($deliveryer) || empty($deliveryer['title']) || empty($deliveryer['openid'])) {
		imessage(error(-1, '配送员微信信息不完善,无法进行微信付款'), '', 'ajax');
	}
	mload()->classs('wxpay');
	$pay = new WxPay();
	$params = array(
		'partner_trade_no' => $log['trade_no'],
		'openid' => !empty($log['account']['openid']) ? $log['account']['openid'] : $deliveryer['openid'],
		'check_name' => 'FORCE_CHECK',
		're_user_name' => $deliveryer['title'],
		'amount' => $log['final_fee'] * 100,
		'desc' => "{$deliveryer['title']}" . date('Y-m-d H:i', $log['addtime']) . "配送费提现申请"
	);
	$response = $pay->mktTransfers($params);
	if(is_error($response)) {
		imessage(error(-1, $response['message']), '', 'ajax');
	}
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	deliveryer_getcash_notice($log['deliveryer_id'], $id, 'success');
	imessage(error(0, '打款成功'), '', 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, '设置提现状态成功'), '', 'ajax');
}*/
include itemplate('deliveryer/getcash');