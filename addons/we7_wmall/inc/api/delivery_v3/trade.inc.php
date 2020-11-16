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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'inout';
$config_cash = $deliveryer['fee_getcash'];

if($op == 'getcash_config') {
	$data = array(
		'delivery_type' => 1,
		'credit2' => $deliveryer['credit2'],
		'rule' => $config_cash,
	);
	message(ierror(0, '', $data), '', 'ajax');
}

elseif($op == 'getcash_submit') {
	if((empty($deliveryer['openid']) && empty($deliveryer['openid_wxapp'])) || empty($deliveryer['title'])) {
		message(ierror(-1, '配送员账户不完善, 无法提现'), '', 'ajax');
	}
	$get_fee = floatval($_GPC['fee']);
	$channel = 'weixin';
	if(MODULE_FAMILY == 'wxapp') {
		$channel = 'wxapp';
		if(empty($deliveryer['openid_wxapp'])) {
			message(ierror(-1, "未获取到配送员针对小程序的openid, 你可以尝试进入顾客小程序会员中心来解决此问题"), '', 'ajax');
		}
	} else {
		$openid = mktTransfers_get_openid($deliveryer['id'], $deliveryer['openid'], $get_fee, 'deliveryer');
		if(empty($openid)) {
			message(ierror(-1, "未获取到配送员针对公众号的openid, 你可以尝试进入平台公众号员中心来解决此问题"), '', 'ajax');
		}
	}
	if(!$get_fee || $get_fee <= 0) {
		message(ierror(-1, '提现金额有误'), '', 'ajax');
	}
	if($get_fee < $config_cash['get_cash_fee_limit']) {
		message(ierror(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
	}
	if($get_fee > $deliveryer['credit2']) {
		message(ierror(-1, '提现金额大于账户可用余额'), '', 'ajax');
	}
	$take_fee = round($get_fee * ($config_cash['get_cash_fee_rate'] / 100), 2);
	$take_fee = max($take_fee, $config_cash['get_cash_fee_min']);
	if($config_cash['get_cash_fee_max'] > 0) {
		$take_fee = min($take_fee, $config_cash['get_cash_fee_max']);
	}
	$final_fee = $get_fee - $take_fee;
	if($final_fee < 0)  {
		message(ierror(-1, "提现金额{$get_fee}{$_W['Lang']['dollarSignCn']}, 需要收取手续费{$take_fee}{$_W['Lang']['dollarSignCn']}, 实际到账金额{$final_fee}, 无法体现"), '', 'ajax');
	}

	$data = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $_W['agentid'],
		'deliveryer_id' => $deliveryer['id'],
		'trade_no' => date('YmdHis') . random(10, true),
		'get_fee' => $get_fee,
		'take_fee' => $take_fee,
		'final_fee' => $final_fee,
		'account' => iserializer(
			array(
				'nickname' => $deliveryer['nickname'],
				'openid' => $openid,
				'openid_wxapp' => $deliveryer['openid_wxapp'],
				'avatar' => $deliveryer['avatar'],
				'realname' => $deliveryer['title'],
			)
		),
		'status' => 2,
		'channel' => $channel,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_deliveryer_getcash_log', $data);
	$getcash_id = pdo_insertid();
	$remark = date('Y-m-d H:i:s') . "申请提现,提现金额{$get_fee}{$_W['Lang']['dollarSignCn']}, 手续费{$take_fee}{$_W['Lang']['dollarSignCn']}, 实际到账{$final_fee}{$_W['Lang']['dollarSignCn']}";
	deliveryer_update_credit2($deliveryer['id'], -$get_fee, 2, $getcash_id, $remark);
	//提现通知
	deliveryer_getcash_notice($deliveryer['id'], $getcash_id, 'apply');
	$current = pdo_get('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'trade_type' => '2', 'extra' => $getcash_id));

	$getcashperiod = $config_cash['get_cash_period'];
	if(empty($getcashperiod)) {
		message(ierror(0, '申请提现成功,等待平台管理员审核', array('getcash_id' => $getcash_id, 'current_id' => $current['id'])), '', 'ajax');
	} elseif($getcashperiod == 1) {
		$transfers = deliveryer_getcash_update($getcash_id, 'transfers');
		message(ierror(0, $transfers['message'], array('getcash_id' => $getcash_id, 'current_id' => $current['id'])), '', 'ajax');
	}
}

elseif($op == 'getcash_detail') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id, 'deliveryer_id' => $deliveryer['id']));
	if(empty($log)) {
		message(ierror(-1, '提现记录不存在或已经删除'), '', 'ajax');
	}
	$log['title'] = $deliveryer['title'];
	$log['nickname'] = $deliveryer['nickname'];
	$log['openid'] = $deliveryer['openid'];
	$log['addtime_cn'] = date('Y-m-d H:i', $log['addtime']);
	$log['endtime_cn'] = date('Y-m-d H:i', $log['endtime']);
	$status = array(
		'1' => '提现成功',
		'2' => '申请中',
	);
	$log['status_cn'] = $status[$log['status']];
	message(ierror(0, '申请提现成功', $log), '', 'ajax');
}

elseif($op == 'inout') {
	$condition = ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':deliveryer_id' => $deliveryer['id'],
	);
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'load';
	$id = intval($_GPC['id']);
	if($type == 'load') {
		if($id > 0) {
			$condition .= " and id < :id";
			$params[':id'] = $id;
		}
	} else {
		$condition .= " and id > :id";
		$params[':id'] = $id;
	}

	$min_id = intval(pdo_fetchcolumn('SELECT min(id) as min_id FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition , $params));
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT 20', $params, 'id');
	$min = $max = 0;
	if(!empty($records)) {
		$trade_types = array(
			'1' => '配送费入账',
			'2' => '申请提现',
			'3' => '其他变动',
		);
		foreach($records as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['trade_type_cn'] = $trade_types[$row['trade_type']];
		}
		$more = 1;
		$min = min(array_keys($orders));
		$max = max(array_keys($orders));
		if($min <= $min_id) {
			$more = 0;
		}
	}
	$records = array_values($records);
	$data = array(
		'list' => $records,
		'max_id' => $max,
		'min_id' => $min,
		'more' => $more
	);
	$respon = array('resultCode' => 0, 'resultMessage' => '调用成功', 'data' => $data);
	message($respon, '', 'ajax');
}

elseif($op == 'inout_detail') {
	$id = intval($_GPC['id']);
	$current = pdo_get('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($current)) {
		message(ierror(-1, '交心记录不存在'), '', 'ajax');
	}
	$current['getcash_log'] = (object)array();
	if($current['trade_type'] == 2){
		$getcash_log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $current['extra']));
		if(!empty($getcash_log)) {
			$getcash_log['account'] = iunserializer($getcash_log['account']);
			$getcash_log['addtime_cn'] = date('Y-m-d H:i', $getcash_log['addtime']);
			$getcash_log['endtime_cn'] = date('Y-m-d H:i', $getcash_log['endtime']);
			$status = array(
				'1' => '提现成功',
				'2' => '申请中',
			);
			$getcash_log['status_cn'] = $status[$getcash_log['status']];
			$current['getcash_log'] = $getcash_log;
		}
	}
	$trade_types = array(
		'1' => '配送费入账',
		'2' => '申请提现',
		'3' => '其他变动',
	);
	$current['addtime_cn'] = date('Y-m-d H:i', $current['addtime']);
	$current['trade_type_cn'] = $trade_types[$current['trade_type']];
	message(ierror(0, '', $current), '', 'ajax');
}
