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
	$_W['page']['title'] = '套餐列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$title = trim($_GPC['title'][$k]);
				if(empty($title)) {
					continue;
				}
				$data = array(
					'title' => $title,
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_svip_meal', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage(error(0, '修改成功'), iurl('svip/setmeal/list'), 'ajax');
		}
	}
	$meals = svip_meal_getall();
}

if($op == 'post') {
	$_W['page']['title'] = '编辑超级会员套餐';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'days' => intval($_GPC['days']),
			'price' => floatval($_GPC['price']),
			'oldprice' => floatval($_GPC['oldprice']),
			'description' => trim($_GPC['description']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(empty($data['title'])) {
			imessage(error(-1, '套餐名称不能为空'), '', 'ajax');
		}
		if(empty($data['days'])) {
			imessage(error(-1, '套餐时长不能为空'), '', 'ajax');
		}
		if($id > 0) {
			pdo_update('tiny_wmall_svip_meal', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_svip_meal', $data);
		}
		imessage(error(0, '编辑套餐成功'), iurl('svip/setmeal/list'), 'ajax');
	}
	if($id > 0) {
		$meal = svip_meal_get($id);
		if(empty($meal)) {
			imessage('套餐不存在或已删除', ireferer(), 'error');
		}
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_svip_meal', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除套餐成功'), '', 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_svip_meal', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '设置套餐状态成功'), '', 'ajax');
}

include itemplate('setmeal');