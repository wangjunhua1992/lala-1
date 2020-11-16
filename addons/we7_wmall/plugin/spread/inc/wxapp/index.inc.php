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
/*if(is_ttapp()) {
	imessage(error(-1, '此功能已下架'), '', 'ajax');
}*/
icheckauth();

$member = get_spread();
if(empty($member['is_spread']) || $member['spread_status'] != 1) {
	imessage(error(-1000, ''), 'register', 'ajax');
}

$basic = $_config_plugin['basic'];
$down1 = intval(pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . 'where uniacid = :uniacid and spread1 = :spread', array(':uniacid' => $_W['uniacid'], ':spread' => $_W['member']['uid'])));
$down2 = intval(pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . 'where uniacid = :uniacid and spread2 = :spread', array(':uniacid' => $_W['uniacid'], ':spread' => $_W['member']['uid'])));

$condition = " where uniacid = :uniacid and is_pay = 1 ";
$params = array(
	':uniacid' => $_W['uniacid'],
	':spread' => $_W['member']['uid'],
);
if($basic['level'] == 2) {
	$down = $down1 + $down2;
	$condition .= " and (spread1 = :spread or spread2 = :spread)";
} elseif($basic['level'] == 1) {
	$down = $down1;
	$condition .= " and spread1 = :spread";
}
spread_group_update($_W['member']['uid']);
$member = get_spread();
$member['spread_url'] = ivurl('pages/home/index', array('code' => $member['uid']), true);
$rank = $_config_plugin['rank'];
if(empty($rank)) {
	$rank['status'] = 0;
}
$result = array(
	'member' => $member,
	'commission' => intval(pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_spread_getcash_log') .' where uniacid = :uniacid and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $_W['member']['uid']))),
	'spread' => spread_commission_stat(),
	'order' => intval(pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_order') . $condition, $params)),
	'down' => $down,
	'current' => intval(pdo_fetchcolumn('select count(*) from' .tablename('tiny_wmall_spread_current_log') . 'where uniacid = :uniacid and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $_W['member']['uid']))),
	'upgrade_explain' => get_config_text("spread:upgrade_explain"),
	'basic' => $basic,
	'rank' => $rank
);
$paotui_num = $gohome_num = 0;
if($basic['paotui_status'] == 1) {
	$paotui_num = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_errander_order') . $condition, $params);
}
if($basic['gohome_status'] == 1) {
	$paotui_num = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_gohome_order') . $condition, $params);
}
$result['order'] = $result['order'] + $paotui_num + $gohome_num;
imessage(error(0, $result), '', 'ajax');


