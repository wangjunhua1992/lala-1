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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '全部资讯';
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . ' WHERE uniacid = :uniacid and agentid = :agentid', array(':uniacid' =>  $_W['uniacid'], ':agentid' =>  $_W['agentid']));

	$condition = ' where uniacid = :uniacid and agentid = :agentid and is_display = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$cateid = intval($_GPC['cateid']);
	if(!empty($cateid)) {
		$condition .= ' and cateid = :cateid';
		$params['cateid'] = $cateid;
	}
	$news = pdo_fetchall('select * from ' . tablename('tiny_wmall_news') . $condition .' order by displayorder desc', $params);
	foreach($news as &$row) {
		$row['desc'] = cutstr($row['desc'], 10, true);
	}
}

if($ta == 'detail') {
	$_W['page']['title'] = '资讯详情';
	$id = intval($_GPC['id']);
	if(!empty($id)) {
		$news = pdo_fetch('select * from ' . tablename('tiny_wmall_news') . ' where uniacid = :uniacid and id = :id and is_display = 1', array(':uniacid' =>  $_W['uniacid'], ':id' => $id));
	}
}
include itemplate('store/shop/news');