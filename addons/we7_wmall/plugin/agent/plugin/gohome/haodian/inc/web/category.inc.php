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

if($op == 'list') {
	$_W['page']['title'] = '分类列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_haodian_category', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑分类成功'), iurl('haodian/category/list'), 'success');
		}
	}
	$all_categorys = haodian_category_fetchall();
	$categorys = $all_categorys['category'];
	$pager = $all_categorys['pager'];
}

elseif($op == 'post') {
	$_W['page']['title'] = '编辑分类';
	$id = intval($_GPC['id']);
	if($id > 0){
		$category = pdo_get('tiny_wmall_haodian_category', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	}
	if($_W['ispost']){
		$data = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'title' => trim($_GPC['title']),
			'thumb' => trim($_GPC['thumb']),
			'status' => intval($_GPC['status']),
			'link' => trim($_GPC['link']),
			'parentid' => intval($_GPC['parentid']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(empty($id)) {
			pdo_insert('tiny_wmall_haodian_category', $data);
		} else {
			pdo_update('tiny_wmall_haodian_category', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $_GPC['id']));
		}
		imessage(error(0, '编辑分类成功'), iurl('haodian/category/list'), 'ajax');
	}
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_haodian_category', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, '删除分类成功'), iurl('haodian/category/list'), 'ajax');
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_haodian_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('category');
