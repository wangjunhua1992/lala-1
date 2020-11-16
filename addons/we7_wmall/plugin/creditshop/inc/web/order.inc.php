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
global $_W,$_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '积分商城兑换记录';
	$condition = ' where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}

	$goods_type = trim($_GPC['goods_type']);
	if(!empty($goods_type)){
		$condition .= ' and a.goods_type = :goods_type';
		$params[':goods_type'] = $goods_type;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= ' and a.addtime >= :starttime and a.addtime <= :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (a.username like '%{$keyword}%' or a.mobile like '%{$keyword}%' or b.nickname like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_creditshop_order_new') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid left join ' . tablename('tiny_wmall_creditshop_goods') . " as c on a.goods_id = c.id {$condition}", $params);
	$orders = pdo_fetchall('select a.*,b.avatar,b.nickname,c.title,c.thumb from ' . tablename('tiny_wmall_creditshop_order_new') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid left join " . tablename('tiny_wmall_creditshop_goods') . " as c on a.goods_id = c.id {$condition} order by a.id desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pay_types = order_pay_types();
	$pager = pagination($total, $pindex, $psize);
	include itemplate('order');
}

if($op == 'handle') {
	$ids = $_GPC['id'];
	if(!empty($ids)) {
		foreach($ids as $value) {
			creditshop_order_update($value, 'handle');
		}
	}
	imessage(error(0, '确认订单状态成功'), iurl('creditshop/order/list'), 'ajax');
}
