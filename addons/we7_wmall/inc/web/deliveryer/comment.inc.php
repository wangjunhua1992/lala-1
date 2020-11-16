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
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '配送评价';

	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}

	$delivery_service = intval($_GPC['delivery_service']);
	if($delivery_service > 0) {
		$condition .= ' AND delivery_service = :delivery_service';
		$params[':delivery_service'] = $delivery_service;
	}

	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']) + 86399;

			$condition .= " AND addtime > :start AND addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);

			$condition .= ' and addtime >= :start';
			$params[':start'] = $starttime;
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order_comment') .  $condition, $params);
	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_comment') . $condition . ' order by addtime desc, id desc limit '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['data'] = iunserializer($val['data']);
			$val['goods_cn'] = implode('，', $val['data']['good']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
}

include itemplate('deliveryer/comment');