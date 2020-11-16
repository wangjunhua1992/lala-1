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
	$_W['page']['title'] = '编辑幻灯片';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_plugincenter_slide', array('id' => $id));
		if(empty($slide)) {
			imessage('幻灯片不存在或已删除', ireferer(), 'error');
		}
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => 0,
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'link' => trim($_GPC['link']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_plugincenter_slide', $data, array('id' => $id));
		} else {
			pdo_insert('tiny_wmall_plugincenter_slide', $data);
		}
		imessage(error(0, '编辑幻灯片成功'), iurl('system/plugincenter_slide/list'), 'ajax');
	}
	include itemplate('system/plugincenter_slide');
}

elseif($op == 'list') {
	$_W['page']['title'] = '幻灯片列表';
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_plugincenter_slide', $data, array('id' => intval($v)));
			}
		}
		imessage('编辑幻灯片成功', iurl('system/plugincenter_slide/list'), 'success');
	}
	$condition = ' where 1';
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_plugincenter_slide') . $condition . ' order by displayorder desc', $params);
	include itemplate('system/plugincenter_slide');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plugincenter_slide', array('id' => $id));
	imessage(error(0, '删除幻灯片成功'), '', 'ajax');
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_plugincenter_slide', array('status' => $status), array('id' => $id));
	imessage(error(0, ''), '', 'ajax');
}
