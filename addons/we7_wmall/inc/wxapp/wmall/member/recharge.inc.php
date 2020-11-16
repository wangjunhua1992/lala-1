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
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$config_recharge = $_W['we7_wmall']['config']['recharge'];
	if(empty($config_recharge['diy_status'])) {
		$config_recharge['diy_status'] = 1;
	}
	$config_recharge['diy_min'] = floatval($config_recharge['diy_min']);
	if($config_recharge['status'] != 1) {
		imessage(error(-1, '平台暂未开启充值功能'), '', 'ajax');
	}
/*	if(is_ttapp()) {
		imessage(error(-1, '平台已下架充值功能'), '', 'ajax');
	}*/
	pdo_query('delete from ' . tablename('tiny_wmall_member_recharge') . ' where is_pay = 0 and addtime < :time', array(':time' => TIMESTAMP - 86400));
	$result = array(
		'recharge' => $config_recharge,
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'submit') {
	$config_recharge = $_W['we7_wmall']['config']['recharge'];
	if(empty($config_recharge['diy_status'])) {
		$config_recharge['diy_status'] = 1;
	}
	$config_recharge['diy_min'] = floatval($config_recharge['diy_min']);
	if($config_recharge['status'] != 1) {
		imessage(error(-1, '平台暂未开启充值功能'), '', 'ajax');
	}
	$price = floatval($_GPC['price']);
	if(!$price || $price < 0) {
		imessage(error(-1, '充值金额必须大于0'), '', 'ajax');
	}
	if($config_recharge['diy_status'] == 1 && $price < $config_recharge['diy_min']) {
		imessage(error(-1, "最低充值{$config_recharge['diy_min']}{$_W['Lang']['dollarSignCn']}"), '', 'ajax');
	}
	$tag = array(
		'credit2' => $price,
	);
	if(!empty($config_recharge['items'])) {
		foreach($config_recharge['items'] as $item) {
			if($item['charge'] == $price) {
				if(!empty($item['back']) && !empty($item['type'])) {
					$tag['grant'] = array(
						'type' => $item['type'],
						'back' => $item['back'],
					);
				}
				break;
			}
		}
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'order_sn' => date('YmdHis') . random(6, true),
		'type' => 'credit2',
		'fee' => $price,
		'final_fee' => $price,
		'pay_type' => '',
		'is_pay' => 0,
		'tag' => iserializer($tag),
		'addtime' => TIMESTAMP,
	);

	pdo_insert('tiny_wmall_member_recharge', $data);
	$id = pdo_insertid();
	imessage(error(0, $id), '', 'ajax');
}


