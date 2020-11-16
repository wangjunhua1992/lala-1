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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list'){
	$_W['page']['title'] = '公告列表';
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'link' => trim($_GPC['links'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage('编辑成功', iurl('deliveryer/notice/list'), 'success');
	}
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':type' => 'delivery'
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_notice') .  $condition, $params);
	$notices = pdo_fetchall('select * from' . tablename('tiny_wmall_notice') . $condition . ' order by displayorder desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post'){
	$_W['page']['title'] = '编辑公告';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$notice = pdo_get('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'type' => 'delivery', 'id' => $id));
	}
	if(empty($notice)) {
		$notice = array(
			'status' => 1,
		);
	}
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'link' => trim($_GPC['link']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'addtime' => TIMESTAMP,
			'type' => 'delivery',
		);
		if(!empty($notice['id'])) {
			pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_notice', $data);
		}
		imessage(error(0, '更新公告成功'), iurl('deliveryer/notice/list'), 'ajax');
	}
}

if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	pdo_delete('tiny_wmall_notice_read_log', array('notice_id' => $id));
	imessage(error(0, '删除公告成功'), iurl('deliveryer/notice/list'), 'ajax');
}

if($op == 'status'){
	$id = intval($_GPC['id']);
	$data = array(
		'status' => intval($_GPC['status']),
	);
	pdo_update('tiny_wmall_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('deliveryer/notice');