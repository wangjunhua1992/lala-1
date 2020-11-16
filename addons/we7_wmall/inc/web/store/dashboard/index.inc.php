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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$_W['page']['title'] = '运营概况';
	$news = pdo_fetchall('select * from ' . tablename('tiny_wmall_news') . ' where uniacid = :uniacid and agentid = :agentid and is_display = 1 and is_show_home = 1 order by displayorder desc limit 5', array(':uniacid' =>  $_W['uniacid'], ':agentid' =>  $_W['agentid']));
	if(!empty($news)) {
		foreach($news as &$row) {
			$row['desc'] = cutstr($row['desc'], 20, true);
		}
		$news_one = $news[0];
		unset($news[0]);
	}
	$ads = pdo_fetchall('select * from ' . tablename('tiny_wmall_slide') . ' where uniacid = :uniacid and agentid = :agentid and type = 3 and status = 1 order by displayorder desc', array(':uniacid' =>  $_W['uniacid'], ':agentid' =>  $_W['agentid']));
}

include itemplate('store/dashboard/index');