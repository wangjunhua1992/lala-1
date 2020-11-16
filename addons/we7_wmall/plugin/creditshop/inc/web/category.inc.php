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
	$_W['page']['title'] = '分类列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])){
			foreach($_GPC['ids'] as $k =>$v) {
				$data = array(
					'name' => trim($_GPC['name'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_creditshop_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '编辑商品分类成功'), iurl('creditshop/category/list'), 'ajax');
	}
	$categorys = pdo_fetchall('select * from' . tablename('tiny_wmall_creditshop_category') . ' where uniacid = :uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']));
}

if($op == 'post'){
	$_W['page']['title'] = '编辑分类';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$category = pdo_get('tiny_wmall_creditshop_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($category)) {
			imessage('幻灯片不存在或已删除', ireferer(), 'error');
		}
	}
	if($_W['ispost']){
		$name = trim($_GPC['name']) ? trim($_GPC['name']) : imessage(error(-1, '分类名称不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $name,
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'isrecommand' => intval($_GPC['isrecommand'])
		);
		if(!empty($category)){
			pdo_update('tiny_wmall_creditshop_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_creditshop_category', $data);
		}
		imessage(error(0, '编辑商品分类成功'), iurl('creditshop/category/list'), 'ajax');
	}
}

if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_creditshop_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除成功'), '', 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_creditshop_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('category');