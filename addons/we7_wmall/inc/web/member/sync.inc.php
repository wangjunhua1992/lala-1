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
mload()->model('member');
$_W['page']['title'] = '同步会员数据';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	if($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$member = pdo_get('tiny_wmall_members', array('uid' => $uid));
		if(!empty($member)) {
			$data = array();
			if(strexists($member['avatar'], "/132132")) {
				$data['avatar'] = str_replace('/132132', '/132', $member['avatar']);
			}
			if($member['sex'] == '1' || $member['sex'] == '2') {
				$data['sex'] = ($member['sex'] == '1' ? '男' : '女');
			}
			pdo_update('tiny_wmall_members', $data, array('uid' => $uid));
		}
		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}
	$uids = pdo_getall('tiny_wmall_members', array('uniacid' => $_W['uniacid']), array('uid'), 'uid');
	$uids = array_keys($uids);
}
include itemplate('member/sync');