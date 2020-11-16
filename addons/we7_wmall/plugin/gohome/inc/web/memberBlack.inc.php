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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('member.extra');

if($op == 'list') {
	$_W['page']['title'] = '黑名单列表';
	$filter = array(
		'keyword' => $_GPC['keyword']
	);
	$member_black = member_get_black($filter);
	$member_black = $member_black['member_black'];
	$limit_visit = array(
		'gohome' => '砍价页面',
		'tongcheng' => '同城页面'
	);
}

elseif($op == 'del') {
	$uid = intval($_GPC['uid']);
	$type = trim($_GPC['type']);
	$status = member_del_black($uid, $type);
	if($status) {
		imessage(error(0, '用户已经移出黑名单'), iurl('gohome/memberBlack/list'), 'ajax');
	} else {
		imessage(error(-1, '移出黑名单失败'), iurl('gohome/memberBlack/list'), 'ajax');
	}
}

include itemplate('memberBlack');
