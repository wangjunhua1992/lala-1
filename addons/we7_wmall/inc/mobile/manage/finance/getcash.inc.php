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
$_W['page']['title'] = '申请提现';
$sid = intval($_GPC['__mg_sid']);
$account = $store['account'];
$config = $_W['we7_wmall']['config']['getcash'];

if($_W['isajax']) {
	$channel = empty($_GPC['channel']) ? 'weixin' : trim($_GPC['channel']);
	if(!in_array($channel, array('weixin', 'alipay', 'bank'))) {
		imessage(error(-1, '提现渠道有误'), '', 'ajax');
	}
	$get_fee = floatval($_GPC['fee']);
	$getcash_account = array();
	if($channel == 'weixin') {
		if((empty($account['wechat']['openid']) && empty($account['wechat']['openid_wxapp'])) || empty($account['wechat']['realname'])) {
			imessage(error(-1, '提现账户不完善, 请到电脑端商户管理-财务-提现账户进行完善'), '', 'ajax');
		}
		if($_W['we7_wmall']['config']['getcash']['channel']['wechat'] == 'wxapp') {
			if(empty($account['wechat']['openid_wxapp'])) {
				imessage(error(-1, "未获取到商户账户针对小程序的openid,你可以尝试进入顾客小程序会员中心并重新设置提现账户来解决此问题"), '', 'ajax');
			}
			$channel = 'wxapp';
		} else {
			$openid = mktTransfers_get_openid($sid, $account['wechat']['openid'], $get_fee);
			if(is_error($openid)) {
				imessage($openid, '', 'ajax');
			}
			if(empty($openid)) {
				imessage(error(-1, "未获取到商户账户针对公众号的openid,你可以尝试进入平台公众号会员中心并重新设置提现账户来解决此问题"), '', 'ajax');
			}
			$account['wechat']['openid'] = $openid;
		}
		$getcash_account = $account['wechat'];
	} elseif($channel == 'alipay') {
		if($_W['we7_wmall']['config']['getcash']['type']['alipay'] != 1) {
			imessage(error(-1, '平台未开启提现到支付宝'), '', 'ajax');
		}
		if(empty($account['alipay']) || empty($account['alipay']['account']) || empty($account['alipay']['realname'])) {
			imessage(error(-1, '支付宝账户信息不完善, 请到电脑端商户管理-财务-提现账户进行完善'), '', 'ajax');
		}
		$getcash_account = $account['alipay'];
	} elseif($channel == 'bank') {
		if($_W['we7_wmall']['config']['getcash']['type']['bank'] != 1) {
			imessage(error(-1, '平台未开启提现到银行卡'), '', 'ajax');
		}
		if(empty($account['bank']) || empty($account['bank']['account']) || empty($account['bank']['realname']) || empty($account['bank']['id'])) {
			imessage(error(-1, '银行账户信息不完善, 请到电脑端商户管理-财务-提现账户进行完善'), '', 'ajax');
		}
		$getcash_account = $account['bank'];
	}

	if(!$get_fee) {
		imessage(error(-1, '提现金额有误'), '', 'ajax');
	}
	if($get_fee < $account['fee_limit']) {
		imessage(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
	}
	if($get_fee > $account['amount']) {
		imessage(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
	}

	$fee_period = $account['fee_period']*24*3600;
	if($fee_period > 0) {
		$getcash_log = pdo_fetch("select addtime from " . tablename('tiny_wmall_store_getcash_log') . " where uniacid = :uniacid and sid = :sid order by addtime desc", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
		$last_getcash = $getcash_log['addtime'];
		if($last_getcash + $fee_period > TIMESTAMP) {
			imessage(error(-1, '距上次提现时间小于提现周期'), '', 'ajax');
		}
	}

	$take_fee = round($get_fee * ($account['fee_rate'] / 100), 2);
	$take_fee = max($take_fee, $account['fee_min']);
	if($account['fee_max'] > 0) {
		$take_fee = min($take_fee, $account['fee_max']);
	}
	$final_fee = $get_fee - $take_fee;
	if($final_fee <= 0)  {
		imessage(error(-1, "实际到账金额小于0{$_W['Lang']['dollarSignCn']}"), '', 'ajax');
	}

	$data = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $_W['agentid'],
		'sid' => $sid,
		'trade_no' => date('YmdHis') . random(10, true),
		'get_fee' => $get_fee,
		'take_fee' => $take_fee,
		'final_fee' => $final_fee,
		'account' => iserializer($getcash_account),
		'status' => 2,
		'addtime' => TIMESTAMP,
		'channel' => $channel,
	);
	pdo_insert('tiny_wmall_store_getcash_log', $data);
	$getcash_id = pdo_insertid();
	store_update_account($sid, -$get_fee, 2, $getcash_id);
	//提现通知
	store_getcash_notice($sid, $getcash_id, 'apply');

	$getcashperiod = get_system_config('store.serve_fee.get_cash_period');
	if(empty($getcashperiod)) {
		imessage(error(0, '申请提现成功,等待平台管理员处理'), iurl('manage/finance/getcash/log') , 'ajax');
	} elseif ($getcashperiod == 1) {
		mload()->model('store.extra');
		$transfers = store_getcash_update($getcash_id, 'transfers');
		imessage($transfers, '', 'ajax');
	}
}
include itemplate('finance/getcash');

