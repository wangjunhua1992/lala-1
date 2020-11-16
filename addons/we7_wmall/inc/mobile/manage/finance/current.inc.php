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

if($ta == 'list'){
	$_W['page']['title'] = '商户账户明细';
	$sid = intval($_GPC['__mg_sid']);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= " AND id < :id";
		$params[':id'] = $id;
	}
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_current_log') . $condition . ' ORDER BY id DESC limit 15', $params, 'id');
	$min = 0;
	if(!empty($records)){
		foreach($records as &$row){
			if($row['trade_type'] == 1) {
				$row['trade_type_cn'] = '订单入账';
			} elseif ($row['trade_type'] == 2){
				$row['trade_type_cn'] = '申请提现';
			} else {
				$row['trade_type_cn'] = '其他变动';
			}
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
		}
		$min = min(array_keys($records));
	}
	if($_W['ispost']) {
		$records = array_values($records);
		$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($ta == 'detail') {
	$_W['page']['title'] = '交心详情';
	$id = intval($_GPC['id']);
	$current = pdo_get('tiny_wmall_store_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($current)) {
		imessage('交心记录不存在', ireferer(), 'error');
	}
	if($current['trade_type'] == 2){
		$getcash_log = pdo_get('tiny_wmall_store_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $current['extra']));
		$getcash_log['account'] = iunserializer($getcash_log['account']);
	}
}

include itemplate('finance/current');

