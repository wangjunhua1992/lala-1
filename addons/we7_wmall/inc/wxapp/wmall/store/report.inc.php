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
mload()->func('tpl.app');
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('title', 'id'));
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$reasons = $_W['we7_wmall']['config']['report'];
	$result = array(
		'store' => $store,
		'reasons' => $reasons,
		'member' => $_W['member']
	);
	imessage(error(0, $result), '', 'ajax');
}

if($ta == 'post') {
	$title = !empty($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '投诉类型有误'), '', 'ajax');
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('title', 'id', 'agentid'));
	if(empty($store)) {
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $store['agentid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'title' => $title,
		'note' => trim($_GPC['note']),
		'mobile' => trim($_GPC['mobile']),
		'addtime' => TIMESTAMP,
	);
	$_GPC['thumbs'] = json_decode(htmlspecialchars_decode($_GPC['thumbs']), true);
	if(!empty($_GPC['thumbs'])) {
		$thumbs = array();
		foreach($_GPC['thumbs'] as $row) {
			if(empty($row['filename']) && empty($row['image'])) continue;
			$thumbs[] = $row['filename'] ? $row['filename'] : $row['image'];
		}
		$data['thumbs'] = iserializer($thumbs);
	}

	pdo_insert('tiny_wmall_report', $data);
	imessage(error(0, '投诉成功'), '', 'ajax');
}

