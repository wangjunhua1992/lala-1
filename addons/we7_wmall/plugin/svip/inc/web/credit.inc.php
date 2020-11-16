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

if($op == 'list') {
	$_W['page']['title'] = '奖励金记录';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$condition .= ' and a.createtime >= :starttime and a.createtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (b.realname like '%{$keyword}%' or b.mobile like '%{$keyword}%' or b.nickname like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_member_credit_record') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition}", $params);
	$records = pdo_fetchall('select a.*,b.avatar,b.nickname,b.realname,b.mobile from ' . tablename('tiny_wmall_member_credit_record') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition} order by a.id desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}
include itemplate('credit');