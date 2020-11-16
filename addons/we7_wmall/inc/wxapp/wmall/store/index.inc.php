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
icheckauth(false);
$config_mall = $_W['we7_wmall']['config']['mall'];
$id = $sid = intval($_GPC['sid']);
$store = store_fetch($id);
if(empty($store)) {
	imessage(error(-1, '门店不存在或已经删除'), '', 'ajax');
}
if(empty($store['notice'])) {
	$store['notice'] = '本店暂无公告';
}

$activity = store_fetch_activity($sid);
$store['qualification']['business']['thumb'] = tomedia($store['qualification']['business']['thumb']);
$store['qualification']['service']['thumb'] = tomedia($store['qualification']['service']['thumb']);
$store['qualification']['more1']['thumb'] = tomedia($store['qualification']['more1']['thumb']);
$store['qualification']['more2']['thumb'] = tomedia($store['qualification']['more2']['thumb']);
$store['is_favorite'] = is_favorite_store($sid, $_W['member']['uid']);
$store['activity'] = $activity;
$result = array(
	'store' => $store,
	'activity' => $activity
);
imessage(error(0, $result), '', 'ajax');