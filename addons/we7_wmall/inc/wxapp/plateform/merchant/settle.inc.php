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
	$condition = ' where uniacid = :uniacid and addtype = 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$status = intval($_GPC['status']);
	if ($status > 0){
		$condition .= " AND status = :status";
		$params[':status'] = $status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' AND title like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store') . $condition . ' ORDER BY id DESC LIMIT ' . ($page - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['user'] = store_manager($val['id']);
			$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
			if ($val['status'] == 1){
				$val['status_cn'] = '审核通过';
			}elseif($val['status'] == 2){
				$val['status_cn'] = '待审核';
			}elseif($val['status'] == 3){
				$val['status_cn'] = '审核未通过';
			}elseif($val['status'] == 4){
				$val['status_cn'] = '回收站';
			}
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}elseif($ta == 'audit') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$clerk = store_manager($store['id']);
	if(empty($clerk)) {
		imessage(error(-1, '获取门店申请人失败'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_settle_notice($store['id'], 'clerk');
	imessage(error(0, ''), '', 'ajax');
}

