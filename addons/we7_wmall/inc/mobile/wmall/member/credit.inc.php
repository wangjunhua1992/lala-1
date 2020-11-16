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
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'credit';

if($ta == 'credit') {
	$credit = $_GPC['credit'] ? trim($_GPC['credit']) : 'credit1';
	if($credit == 'credit1') {
		$_W['page']['title'] = '积分明细';
	} else {
		$_W['page']['title'] = '余额明细';
	}
	$condition = " where a.uniacid = :uniacid and credittype = :credittype and a.uid = :uid";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':credittype' => $credit,
		':uid' => $_W['member']['uid']
	);

	$type = trim($_GPC['type']);
	if($type == 1) {
		$condition .= " and a.num > 0 ";
	} elseif($type == 2){
		$condition .= " and a.num < 0 ";
	}

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = trim($_GPC['min']);
	}
	$records = pdo_fetchall('select a.*, b.avatar,b.nickname,b.realname,b.mobile from' . tablename('mc_credits_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid ' . $condition . ' order by id desc LIMIT 11', $params, 'id');
	$min = 0;
	if(!empty($records)) {
		foreach($records as &$v) {
			$v['createtime'] = date('Y-m-d H:i', $v['createtime']);
		}
		$min = min(array_keys($records));
	}

	if($_W['ispost']) {
		$records = array_values($records);
		$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
		imessage($respon, '', 'ajax');
	}

}

if($ta == 'detail') {
	$id = intval($_GPC['id']);
	$detail = pdo_get('mc_credits_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
}

include itemplate('member/credit');