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
	$condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':deliveryer_id' => $_deliveryer['id']
	);
	$comment_type = trim($_GPC['comment_type']) ? trim($_GPC['comment_type']) : 'all';
	if($comment_type == 'good') {
		$condition .= " and delivery_service >= 3";
	} elseif($comment_type == 'bad') {
		$condition .= " and delivery_service < 3";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']);
	$totalComment = floatval(pdo_fetchcolumn('select round(avg(delivery_service), 1) from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $_deliveryer['id'])));
	$records = pdo_fetchall('SELECT id, delivery_service, deliveryer_tag, note, addtime FROM ' . tablename('tiny_wmall_order_comment') . $condition . ' ORDER BY id DESC LIMIT ' .($pindex - 1) * $psize.','. $psize, $params);
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['delivery_service'] = intval($val['delivery_service']);
			$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
			$val['delivery_service_cn'] = ($val['delivery_service'] >= 3) ? '满意' : '不满意';
			if(!empty($val['deliveryer_tag'])) {
				$val['deliveryer_tag'] = explode(',', $val['deliveryer_tag']);
			} else {
				$val['deliveryer_tag'] = array();
			}
		}
	}

	$result = array(
		'records' => $records,
		'totalComment' => $totalComment
	);
	imessage(error(0, $result), '', 'ajax');
}