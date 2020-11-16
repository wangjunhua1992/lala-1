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
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
}
$activity = store_fetch_activity($sid);
$hot_goods = pdo_fetchall('select id,title,price,sailed,thumb from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by is_hot desc, id desc limit 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
if(!empty($hot_goods)) {
	foreach($hot_goods as &$goods) {
		$goods['thumb'] = tomedia($goods['thumb']);
	}
}
$_W['_share'] = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo']),
	'link' => ivurl('/pages/store/share', array('sid' => $sid), true),
);
$result = array(
	'store' => $store,
	'activity' => $activity,
	'hot_goods' => $hot_goods
);
imessage(error(0, $result), '', 'ajax');