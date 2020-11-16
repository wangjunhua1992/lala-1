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
	$_W['page']['title'] = '问题列表';
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage('保存修改成功', iurl('config/help/list'), 'success');
		}
	}

	$condition = ' WHERE uniacid = :uniacid order by displayorder desc, id asc';
	$params[':uniacid'] = $_W['uniacid'];
	$helps = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_help') . $condition ,$params);
}

if($op == 'post') {
	$_W['page']['title'] = '编辑问题';
	$id = $_GPC['id'];
	$item = pdo_get('tiny_wmall_help', array('id' => $id));
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' =>trim($_GPC['title']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if($id) {
			pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_help', $data);
		}
		imessage(error(0, '编辑问题成功'), iurl('config/help/list'), 'ajax');
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_help', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除内容成功'), ireferer(), 'ajax');
}

include itemplate('config/help');