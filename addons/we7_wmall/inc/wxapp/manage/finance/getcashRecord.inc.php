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

if ($ta == 'list') {
	$condition = ' where uniacid = :uniacid and agentid = :agentid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
		':sid' => $sid,
	);
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$record = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' order by id desc limit ' . ($page - 1) * $psize . ', ' . $psize, $params);
	if (!empty($record)) {
		foreach ($record as &$val) {
			$val['addtime'] = date('Y-m-d H:i', $val['addtime']);
			if ($val['status'] == '1') {
				$val['status_cn'] = '提现成功';
			} elseif ($val['status'] == '2') {
				$val['status_cn'] = '申请中';
			} else {
				$val['status_cn'] = '已撤销';
			}
		}
	}
	$result = array(
		'record' => $record,
	);
	imessage(error(0, $result), '', 'ajax');
}

if ($ta == 'detail') {
	$id = intval($_GPC['id']);
	$getcashDetail = pdo_get('tiny_wmall_store_getcash_log', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	if (empty($getcashDetail)) {
		imessage(error(-1, '交心记录不存在'), '', 'ajax');
	}
	if ($getcashDetail['status'] == '1') {
		$getcashDetail['status_cn'] = '提现成功';
	} elseif ($getcashDetail['status'] == '2') {
		$getcashDetail['status_cn'] = '申请中';
	} else {
		$getcashDetail['status_cn'] = '已撤销';
	}
	$getcashDetail['addtime'] = date('Y-m-d H:i', $getcashDetail['addtime']);
	$result = array(
		'getcashDetail' => $getcashDetail,
	);
	imessage(error(0, $result), '', 'ajax');
}