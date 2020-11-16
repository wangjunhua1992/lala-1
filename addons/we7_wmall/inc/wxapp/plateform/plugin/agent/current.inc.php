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
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' AND a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND a.trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)){
		$condition .= ' AND (b.title like :keyword or b.area like :keyword)';
		$params[':keyword'] = "%{$keyword}%";
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT a.*,b.title,b.area FROM ' . tablename('tiny_wmall_agent_current_log') . ' as a left join ' . tablename('tiny_wmall_agent') . " as b on a.agentid = b.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if (!empty($records)){
		foreach($records as &$val){
			$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}