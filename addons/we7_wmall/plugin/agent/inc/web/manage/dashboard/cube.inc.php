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
$_W['page']['title'] = '图标魔方';

if($op == 'list') {
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $key => $id) {
				if(intval($id)) {
					$row = array(
						'title' => trim($_GPC['titles'][$key]),
						'tips' => trim($_GPC['tips'][$key]),
						'link' => trim($_GPC['links'][$key]),
						'wxapp_link' => trim($_GPC['wxapp_links'][$key]),
						'thumb' => trim($_GPC['thumbs'][$key]),
						'displayorder' =>  intval($_GPC['displayorders'][$key])
					);
					pdo_update('tiny_wmall_cube', $row, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
				}
			}
		}
		imessage(error(0, '图片魔方设置成功'), iurl('dashboard/cube/list'), 'success');
	}
	$condition = ' where uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_cube') . $condition, $params);
	$cubes = pdo_fetchall('select * from ' . tablename('tiny_wmall_cube') . $condition . ' order by displayorder desc limit '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$updata = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'title' => trim($_GPC['title']),
			'tips' => trim($_GPC['tips']),
			'link' => trim($_GPC['link']),
			'wxapp_link' => trim($_GPC['wxapp_link']),
			'thumb' => trim($_GPC['thumb']),
			'displayorder' =>  intval($_GPC['displayorder'])
		);
		if($id) {
			pdo_update('tiny_wmall_cube', $updata, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_cube', $updata);
		}
		imessage(error(0, '图片魔方设置成功'), iurl('dashboard/cube/list'), 'ajax');
	}
	if(!empty($id)) {
		$cube = pdo_get('tiny_wmall_cube', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_cube', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	}
	imessage(error(0, '删除图片魔方成功'), ireferer(), 'ajax');
}

include itemplate('dashboard/cube');