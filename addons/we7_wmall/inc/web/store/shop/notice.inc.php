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
	$_W['page']['title'] = '系统消息';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	if(empty($_W['clerk'])) {
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_notice') .  ' where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['we7_wmall']['store']['agentid'], ':type' => 'store'));
		$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_notice') . ' where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1 order by displayorder desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['we7_wmall']['store']['agentid'], ':type' => 'store'));
	} else {
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . ' as b on a.id = b.notice_id where b.uid = :uid and a.uniacid = :uniacid and a.agentid = :agentid and a.type = :type and a.status = 1 ', array(':uid' => $_W['clerk']['id'], ':uniacid' => $_W['uniacid'], ':agentid' => $_W['we7_wmall']['store']['agentid'], ':type' => 'store'));
		$data = pdo_fetchall('select a.*,b.uid,b.is_new from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . ' as b on a.id = b.notice_id where b.uid = :uid and a.uniacid = :uniacid and a.agentid = :agentid and a.type = :type and a.status = 1 order by id desc, displayorder desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':uid' => $_W['clerk']['id'],':uniacid' => $_W['uniacid'], ':agentid' => $_W['we7_wmall']['store']['agentid'], ':type' => 'store'));
	}
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'detail') {
	$_W['page']['title'] = '消息详情';
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id, 'status' => 1, 'type' => 'store'));
	if(empty($item)) {
		imessage('该消息不存在或已删除', iurl('store/shop/notice/list'), 'error');
	}
	pdo_update('tiny_wmall_notice_read_log', array('is_new' => 0), array('notice_id' => $id, 'uid' => $_W['clerk']['id']));
}
include itemplate('store/shop/notice');
