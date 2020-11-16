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
	$condition = ' WHERE a.uniacid = :uniacid and a.is_pay = 1';
	$params[':uniacid'] = $_W['uniacid'];
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)){
		$condition .= ' and b.title like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$status = intval($_GPC['status']);
	if ($status > -1){
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT a.*,b.title as store_title,b.logo FROM ' . tablename('tiny_wmall_advertise_trade') . ' as a left join ' . tablename('tiny_wmall_store') . " as b on a.sid = b.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if (!empty($records)){
		foreach($records as &$val){
			$val['logo'] = tomedia($val['logo']);
			$val['addtime_cn'] = date('Y-m-d', $val['addtime']);
			$val['endtime_cn'] = date('Y-m-d', $val['endtime']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}