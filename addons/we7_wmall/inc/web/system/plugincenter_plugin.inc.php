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
mload()->model('plugincenter');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '应用列表';
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'sailed' => trim($_GPC['saileds'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_plugincenter_plugin', $data, array('id' => intval($v)));
			}
		}
		imessage(error(0, '修改成功'),iurl('system/plugincenter_plugin/list'), 'success');
	}
	$filter = $_GPC;
	$filter['uniacid'] = 0;
	$filter['psize'] = 15;
	$plugincenter = get_plugincenter_plugins($filter);
	$plugins = $plugincenter['plugins'];
	$pager = $plugincenter['pager'];
}

elseif($op == 'post') {
	$_W['page']['title'] = '编辑应用';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$plugincenter = pdo_get('tiny_wmall_plugincenter_plugin', array('id' => $id));
		if(empty($plugincenter)) {
			imessage('应用不存在或已删除', ireferer(), 'error');
		}
		$plugincenter['data'] = iunserializer($plugincenter['data']);
	}
	$plugins = pdo_fetchall('select id,title,name,thumb from' . tablename('tiny_wmall_plugin'), array(),'id');
	if($_W['ispost']) {
		$data = array(
			'uniacid' => 0,
			'displayorder' => intval($_GPC['displayorder']),
			'plugintype' => intval($_GPC['plugintype']),
			'title' => trim($_GPC['title']),
			'thumb' => trim($_GPC['thumb']),
			'sailed' => intval($_GPC['sailed']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'status' => intval($_GPC['status']),
			'addtime' => TIMESTAMP
		);
		if(!empty($_GPC['meal'])) {
			$meal = array();
			foreach ($_GPC['meal']['month'] as $key => $val) {
				$price = $_GPC['meal']['price'][$key];
				if (empty($price)) {
					continue;
				}
				$meal['meal'][] = array(
					'month' => $val,
					'price' => $price
				);
			}
			$data['data'] = iserializer($meal);
		}
		if($data['plugintype'] !== 1 && !empty($_GPC['pluginid'])) {
			$pluginid = intval($_GPC['pluginid']);
			$data['pluginid'] = $pluginid;
			$data['pluginname'] = trim($plugins[$pluginid]['name']);
			if(empty($data['thumb'])) {
				$data['thumb'] = $plugins[$pluginid]['thumb'];
			}
		}
		if($id > 0) {
			$data['addtime'] = $plugincenter['addtime'];
			pdo_update('tiny_wmall_plugincenter_plugin', $data, array('id' => $id));
		} else {
			pdo_insert('tiny_wmall_plugincenter_plugin', $data);
		}
		imessage(error(0, '编辑应用成功'), iurl('system/plugincenter_plugin/list'), 'ajax');
	}
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_plugincenter_plugin', array('status' => $status), array('id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plugincenter_plugin', array('id' => $id));
	imessage(error(0, '删除应用成功'), '', 'ajax');
}

include itemplate('system/plugincenter_plugin');