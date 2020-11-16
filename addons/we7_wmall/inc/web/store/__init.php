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
if($_W['we7_wmall']['sid'] > 0) {
	$_W['we7_wmall']['store'] = store_fetch($_W['we7_wmall']['sid']);
}
if($_W['_action'] == 'activity' ) {
	$op = $_W['_op'];
	$activity_types = store_all_activity();
	if(isset($activity_types['svipRedpacket'])) {
		unset($activity_types['svipRedpacket']);
	}
	$activity_types = array_keys($activity_types);
	if(in_array($op, $activity_types)) {
		$ta = trim($_GPC['ta']);
		if(($ta == 'post' || ($ta != 'del' && $_W['ispost'])) && $_W['we7_wmall']['config']['store']['activity']['perm'][$op]['status'] != 1) {
			if($_W['ispost']) {
				imessage(error(-1, '平台没有开启该活动, 详情请咨询平台管理员'), ireferer(), 'ajax');
			}
			imessage('平台没有开启该活动', ireferer(), 'info');
		}
	}
}
