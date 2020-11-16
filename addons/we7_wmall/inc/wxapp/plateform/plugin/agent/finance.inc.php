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
$ta = trim($_GPC['ta'])? trim($_GPC['ta']): 'index';

if($ta == 'index') {
	$agent = $_W['agent'];
	$result = array(
		'agent' => $agent
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'getcash') {
	$agent = $_W['agent'];
	if((empty($agent['account']['wechat']['openid']) && empty($agent['account']['wechat']['openid_wxapp'])) || empty($agent['account']['wechat']['realname'])) {
		imessage(error(-1, '提现前请先完善提现账户'), '', 'ajax');
	}
	$get_fee = floatval($_GPC['get_fee']);
	if($_W['we7_wmall']['config']['getcash']['channel']['wechat'] == 'wxapp') {
		if(empty($agent['account']['wechat']['openid_wxapp'])) {
			imessage(error(-1, "未获取到代理商针对小程序的openid,你可以尝试进入顾客小程序会员中心并重新设置提现账户来解决此问题"), '', 'ajax');
		}
	} else {
		$openid = mktTransfers_get_openid($agent['id'], $agent['account']['wechat']['openid'], $get_fee, 'agent');
		if(is_error($openid)) {
			imessage($openid, '', 'ajax');
		}
		if(empty($openid)) {
			imessage(error(-1, "未获取到代理商针对公众号的openid,你可以尝试进入平台公众号会员中心并重新设置提现账户来解决此问题"), '', 'ajax');
		}
		$agent['account']['wechat']['openid'] = $openid;
	}

	$fee_period = $agent['fee']['fee_period']*24*3600;
	if($fee_period > 0) {
		$getcash_log = pdo_fetch("select addtime from " . tablename('tiny_wmall_agent_getcash_log') . " where uniacid = :uniacid and agentid = :agentid order by addtime desc", array(':uniacid' => $_W['uniacid'], ':agentid' => $agent['id']));
		$last_getcash = $getcash_log['addtime'];
		if($last_getcash + $fee_period > TIMESTAMP) {
			imessage(error(-1, '距上次提现时间小于提现周期'), '', 'ajax');
		}
	}

	$data = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $agent['id'],
		'trade_no' => date('YmdHis') . random(10, true),
		'get_fee' => $get_fee,
		'take_fee' => 0,
		'final_fee' => $get_fee,
		'account' => iserializer($agent['account']['wechat']),
		'status' => 2,
		'addtime' => TIMESTAMP,
		'channel' => MODULE_FAMILY == 'wxapp' ? 'wxapp' : 'weixin'
	);
	pdo_insert('tiny_wmall_agent_getcash_log', $data);
	$getcash_id = pdo_insertid();
	agent_update_account($agent['id'], -$get_fee, 2, '');
	//提现通知
	sys_notice_agent_getcash($agent['id'], $getcash_id, 'apply');
	imessage(error(0, '申请提现成功,等待平台管理员审核'), '' , 'ajax');
}