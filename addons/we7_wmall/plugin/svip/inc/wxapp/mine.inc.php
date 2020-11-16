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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth();
if($_config_plugin['basic']['status'] != 1) {
	imessage(error(-1, '超级会员功能未开启'), '', 'ajax');
}
if($op == 'index') {
	$member = $_W['member'];
	if($member['svip_status'] != 1) {
		imessage(error(-2, '您还未开通会员'), '', 'ajax');
	}
	$member['svip_endtime_cn'] = date('Y-m-d', $member['svip_endtime']);
	$config = get_plugin_config('svip.basic');
	$member['exchange_max'] = intval($config['exchange_max']);
	$num_taked = svip_member_exchange_redpacket_num();
	$member['num_taked'] = $num_taked;
	$member['total_discount'] = svip_member_redpacket_total();

	$filter = array(
		'status' => 1,
		'psize' => 10
	);
	$redpackets = svip_redpacket_fetchall($filter);
	$goods = svip_goods_getall($filter);
	$tasks = svip_task_getall(array('status' => 1, 'psize' => 3));
	$result = array(
		'member' => $member,
		'redpackets' => $redpackets['redpackets'],
		'goods' => $goods['goods'],
		'tasks' => $tasks['tasks'],
		'agreement' => get_config_text('agreement_svip')
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'exchange') {
	$id = intval($_GPC['id']);
	$exchange_cost = intval($_GPC['exchange_cost']);
	$redpacket = svip_redpacket_fetch($id);
	$status = svip_redpacket_exchage($redpacket, $exchange_cost);
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	$num_taked = svip_member_exchange_redpacket_num();
	$total_discount = svip_member_redpacket_total();
	$result = array(
		'num_taked' => svip_member_exchange_redpacket_num(),
		'total_discount' => svip_member_redpacket_total()
	);
	imessage(error(0, $result), '', 'ajax');
}
