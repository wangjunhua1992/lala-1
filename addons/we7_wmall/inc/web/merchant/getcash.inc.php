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
mload()->model('store.extra');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '商户提现记录';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND sid = :sid';
		$params[':sid'] = $sid;
	}
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$channel = trim($_GPC['channel']);
	if(!empty($channel)) {
		if($channel == 'weixin') {
			$condition .= " AND (channel = 'weixin' OR channel = 'wxapp') ";
		} else {
			$condition .= ' AND channel = :channel';
			$params[':channel'] = $channel;
		}
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
	$todaytime = strtotime(date('Y-m-d'));
	$starttime = $todaytime;
	$endtime = $starttime + 86399;
	if($days > -2) {
		if($days == -1) {
			$starttime = strtotime($_GPC['addtime']['start']);
			$endtime = strtotime($_GPC['addtime']['end']);

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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$channels = getcash_channels();
	if(!empty($records)) {
		$toaccount_status_arr = getcash_toaccount_status();
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
			if($row['channel'] == 'weixin' || $row['channel'] == 'wxapp') {
				$row['channel_cn'] = '打款到微信零钱';
			} elseif($row['channel'] == 'alipay') {
				$row['channel_cn'] = '打款到支付宝余额';
			} elseif($row['channel'] == 'bank') {
				$row['channel_cn'] = '打款到银行卡';
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$result = store_getcash_update($id, 'status', array('status' => $status));
	imessage($result, '', 'ajax');
}

elseif($op == 'transfers') {
	$id = intval($_GPC['id']);
	$transfers = store_getcash_update($id, 'transfers');
	imessage($transfers, '', 'ajax');
}

elseif($op == 'cancel') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_store_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($_W['ispost']) {
		$remark = trim($_GPC['remark']);
		$result = store_getcash_update($log, 'cancel', array('remark' => $remark));
		imessage($result, ireferer(), 'ajax');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $log['sid']), array('logo', 'title', 'telephone'));
	include itemplate('merchant/accountOp');
	die();
}

elseif($op == 'query') {
	$id = intval($_GPC['id']);
	$result = store_getcash_update($id, 'query');
	imessage($result, '', 'ajax');
}

include itemplate('merchant/getcash');