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
$credit = $_GPC['credit'] ? trim($_GPC['credit']) : 'credit1';
if($credit == 'credit1') {
	$_W['page']['title'] = '积分明细';
} else {
	$_W['page']['title'] = '余额明细';
}
$condition = " where a.uniacid = :uniacid and credittype = :credittype";
$params = array(
	':uniacid' => $_W['uniacid'],
	':credittype' => $credit
);
$type = trim($_GPC['type']);
if($type == 'add') {
	$condition .= " and a.num > 0 ";
} elseif($type == 'minus'){
	$condition .= " and a.num < 0 ";
}
$keywords = trim($_GPC['keyword']);
if(!empty($keywords)) {
	$condition .= " and (b.nickname like '%{$keywords}%' or b.realname like '%{$keywords}%' or b.mobile like '%{$keywords}%')";
}
if(!empty($_GPC['createtime']['start']) && !empty($_GPC['createtime']['end'])) {
	$starttime = strtotime($_GPC['createtime']['start']);
	$endtime = strtotime($_GPC['createtime']['end']);
	$condition .= ' and a.createtime >= :starttime and a.createtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
}
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$total = pdo_fetchcolumn('select count(*) from' . tablename('mc_credits_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid ' . $condition, $params);
$records = pdo_fetchall('select a.*, b.avatar,b.nickname,b.realname,b.mobile from' . tablename('mc_credits_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid ' . $condition . ' order by createtime desc LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
$pager = pagination($total, $pindex, $psize);

include itemplate('member/credit');