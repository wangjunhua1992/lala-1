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
$_W['page']['title'] = '配送会员卡';

if($op == 'index') {
	$payment = get_available_payment('deliveryCard');
	$pay_types = order_pay_types();
	$endtime = strtotime(date('Y-m-d'));
	if($_W['member']['setmeal_endtime'] > 0) {
		$setmeal_endtime = $_W['member']['setmeal_endtime'];
		if($setmeal_endtime > $endtime) {
			$endtime = $setmeal_endtime;
		}
	}
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid and status = 1 order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']));
	if(empty($cards)) {
		imessage('平台未设置配送会员卡套餐', ireferer(), 'error');
	}
	foreach($cards as &$row) {
		$row['endtime'] = date('Y-m-d', strtotime("{$row['days']}days", $endtime));
	}
}

if($op == 'pay') {
	$id = intval($_GPC['setmeal_id']);
	$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($card)) {
		imessage(error(-1, '会员卡套餐不存在'), '', 'ajax');
	}
	$pay_type = trim($_GPC['pay_type']);
	if(!in_array($pay_type, array('alipay', 'wechat', 'credit'))) {
		imessage(error(-1, '支付方式错误'), '', 'ajax');
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'ordersn' => date('YmdHis') . random(6, true),
		'card_id' => $card['id'],
		'final_fee' => $card['price'],
		'pay_type' => $pay_type,
		'is_pay' => 0,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_delivery_cards_order', $order);
	$id = pdo_insertid();
	imessage(error(0, $id), '', 'ajax');
}

include itemplate('apply');

