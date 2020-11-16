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
$ta = trim($_GPC['ta'])? trim($_GPC['ta']): 'list';
if($ta == 'list') {
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' AND id = :id';
		$params[':id'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (title like :keyword or area like :keyword)";
		$params[':keyword'] = "%{$keyword}%";
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_agent') . $condition . ' order by id desc limit ' . ($page - 1) * $psize.','.$psize, $params);
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}elseif($ta == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_agent', array('id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, ''), '', 'ajax');
}elseif($ta == 'status'){
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_agent', array('status' => $status), array('id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, ''), '', 'ajax');
}