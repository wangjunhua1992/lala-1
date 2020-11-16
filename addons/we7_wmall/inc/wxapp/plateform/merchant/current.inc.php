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
	$condition = ' WHERE a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' AND b.title like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND a.trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;

	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

			$condition .= " AND a.addtime > :start AND a.addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;
		} else {
			$starttime = strtotime("-{$days} days", $todaytime);

			$condition .= ' and a.addtime >= :start';
			$params[':start'] = $starttime;
		}
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 20;
	$records = pdo_fetchall('SELECT a.*,b.title as store_title,b.logo FROM ' . tablename('tiny_wmall_store_current_log') . 'as a left join' . tablename('tiny_wmall_store') . "as b on a.sid = b.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);

	if (!empty($records)){
		$trade_types = order_trade_type();
		foreach ($records as &$val){
			$val['trade_type_cn'] = $trade_types[$val['trade_type']]['text'];
			$val['addtime_cn'] = date('Y-m-d H:i',$val['addtime']);
			$val['logo'] = tomedia($val['logo']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}
