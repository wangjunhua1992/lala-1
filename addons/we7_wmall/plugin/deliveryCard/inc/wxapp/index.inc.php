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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$agreement_card = get_config_text('agreement_card');
$config = get_plugin_config('deliveryCard');

if($op == 'index') {
	//删除半小时以前的订单
	pdo_query('delete from ' . tablename('tiny_wmall_delivery_cards_order') . ' where uniacid = :uniacid and is_pay = 0 and addtime < :addtime', array(':uniacid' => $_W['uniacid'], ':addtime' => TIMESTAMP - 3600));

	$deliveryCard_status = check_plugin_perm('deliveryCard') && $config['card_apply_status'];
	$deliveryCard_setmeal_ok = 0;
	if($deliveryCard_status && $_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] > TIMESTAMP) {
		$deliveryCard_setmeal_ok = 1;
	}
	$_W['member']['setmeal_starttime'] = date('Y-m-d', $_W['member']['setmeal_starttime']);
	$_W['member']['setmeal_endtime'] = date('Y-m-d', $_W['member']['setmeal_endtime']);
	$diypower = array();
	if(!empty($config['diypower'])) {
		foreach($config['diypower'] as &$power) {
			$power['img'] = tomedia($power['img']);
		}
		$diypower = $config['diypower'];
	}
	$result = array(
		'deliveryCard_setmeal_ok' => $deliveryCard_setmeal_ok,
		'agreement' => $agreement_card,
		'member' => $_W['member'],
		'diypower' => $diypower
	);
	imessage(error(0, $result), '', 'ajax');
}

if($op == 'power') {
	$diytips = !empty($config['diytips']) ? array_values($config['diytips']) : array();
	$result = array(
		'member' => $_W['member'],
		'diytips' => $diytips
	);
	imessage(error(0, $result), '', 'ajax');
}


