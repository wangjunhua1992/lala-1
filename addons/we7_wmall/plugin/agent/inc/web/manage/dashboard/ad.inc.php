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
if($op == 'post') {
	$_W['page']['title'] = '编辑引导页';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id, 'type' => 1));
		if(empty($slide)) {
			imessage('广告不存在或已删除', ireferer(), 'error');
		}
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
            'agentid' => $_W['agentid'],
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'type' => 1,
			'status' => intval($_GPC['status']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(!empty($slide)) {
			pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_slide', $data);
		}
		imessage(error(0, '编辑引导页成功'), iurl('dashboard/ad/list'), 'ajax');
	}
}

if($op == 'list') {
	$_W['page']['title'] = '引导页列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '编辑全屏引导页成功'), iurl('dashboard/ad/list'), 'success');
	}
	$condition = ' where uniacid = :uniacid and agentid = :agentid and type = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_slide') .  $condition, $params);
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_slide') . $condition . ' order by displayorder desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	}
	imessage(error(0, '删除引导页成功'), ireferer(), 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_slide', array('status' => $status), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('dashboard/ad');