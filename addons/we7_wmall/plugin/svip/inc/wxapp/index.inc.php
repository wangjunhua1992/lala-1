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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'meal';
icheckauth();
if($_config_plugin['basic']['status'] != 1) {
	imessage(error(-1, '超级会员功能未开启'), '', 'ajax');
}
if($op == 'index') {
	$member = $_W['member'];
	if($member['svip_status'] == 1) {
		imessage(error(-2, ''), '', 'ajax');
	}
	$filter = array(
		'status' => 1,
		'psize' => 10
	);
	$redpackets = svip_redpacket_fetchall($filter);
	$tasks = svip_task_getall(array('status' => 1, 'psize' => 3));
	$result = array(
		'redpackets' => $redpackets['redpackets'],
		'tasks' => $tasks['tasks'],
		'agreement' => get_config_text('agreement_svip'),
		'config' => array(
			'exchange_max' => intval($_config_plugin['basic']['exchange_max'])
		)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'meal') {
	$member = $_W['member'];
	$member['svip_endtime_cn'] = date('Y-m-d', $member['svip_endtime']);
	$result = array(
		'meals' => svip_meal_getall(array('status' => 1)),
		'member' => $member,
		'agreement' => get_config_text('agreement_svip')
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'buy') {
	$id = intval($_GPC['id']);
	$meal = svip_meal_get($id);
	if(empty($meal)) {
		imessage(error(-1, '套餐不存在'), '', 'ajax');
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'ordersn' => date('YmdHis') . random(6, true),
		'meal_id' => $meal['id'],
		'final_fee' => $meal['price'],
		'is_pay' => 0,
		'order_channel' => in_array($_W['ochannel'], array('wxapp', 'ttapp')) ? $_W['ochannel'] : 'wechat',
		'addtime' => TIMESTAMP,
		'data' => iserializer(array(
			'days' => $meal['days']
		))
	);
	pdo_insert('tiny_wmall_svip_meal_order', $order);
	$id = pdo_insertid();
	imessage(error(0, array('id'=>$id)), '', 'ajax');
}