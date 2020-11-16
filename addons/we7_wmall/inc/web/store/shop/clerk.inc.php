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
mload()->model('clerk');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '店员列表';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_clerk') . 'WHERE uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$data = pdo_fetchall('SELECT *, a.id as aid, a.role as role FROM ' . tablename('tiny_wmall_store_clerk') . 'as a left join' . tablename('tiny_wmall_clerk') . 'as b on a.clerk_id = b.id WHERE a.uniacid = :uniacid and a.sid = :sid ORDER BY aid DESC LIMIT '.($pindex - 1) * $psize. ', '.$psize, array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		foreach($data as &$value) {
			$value['extra'] = iunserializer($value['extra']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

elseif($ta == 'add') {
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			imessage(error(-1, '手机号不能为空'), ireferer(), 'ajax');
		}
		$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($clerk)) {
			imessage(error(-1, '未找到该手机号对应的店员'), ireferer(), 'ajax');
		}
		$is_exist = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id'], 'sid' => $sid));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号对应的账户已经是店员, 请勿重复添加'), ireferer(), 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'clerk_id' => $clerk['id'],
			'sid' => $sid,
			'addtime' => TIMESTAMP,
			'role' => 'clerk',
			'extra' => iserializer(array(
				'accept_wechat_notice' => 1,
				'accept_voice_notice' => 1,
			)),
		);
		pdo_insert('tiny_wmall_store_clerk', $data);
		$id = pdo_insertid();
		mlog(3000, $id, '商户添加店员');
		imessage(error(0, '添加店员成功'), ireferer(), 'ajax');
	}
}

elseif($ta == 'manager') {
	$id = intval($_GPC['id']);
	$clerk = pdo_get('tiny_wmall_store_clerk', array('sid' => $sid, 'id' => $id));
	if(!empty($clerk)) {
		$is_exist = pdo_fetch('select a.*, b.title as store_title from ' . tablename('tiny_wmall_store_clerk') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.clerk_id = :clerk_id and a.role = :role and a.sid != :sid', array(':uniacid' => $_W['uniacid'], ':clerk_id' => $clerk['clerk_id'], ':role' => 'manager', ':sid' => $sid));
		if(!empty($is_exist)) {
			imessage(error(0, "该店员已经是{$is_exist['store_title']}的管理员,一个店员不能同时是多个店铺的管理员"), ireferer(), 'ajax');
		}
		pdo_update('tiny_wmall_store_clerk', array('role' => 'clerk'), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		pdo_update('tiny_wmall_store_clerk', array('role' => 'manager'), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		imessage(error(0, '设置店铺管理员成功'), ireferer(), 'ajax');
	}
	imessage(error(0, '店员信息有误'), ireferer(), 'ajax');
}

elseif($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$clerk = pdo_get('tiny_wmall_store_clerk', array('id' => $id));
		pdo_delete('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'id' => $id, 'sid' => $sid));
	}
	imessage(error(0, '删除店员成功'), ireferer(), 'ajax');
}

elseif($ta == 'cover') {
	$_W['page']['title'] = '店员入口';
	$urls = array(
		'wmerchant' => iurl('store/oauth/login', array(), true),
		'register' => imurl('manage/auth/register', array(), true),
		'login' => imurl('manage/auth/login', array(), true),
	);
}

elseif($ta == 'extra') {
	$clerk_id = intval($_GPC['id']);
	$type = trim($_GPC['type']);
	$value = intval($_GPC['value']) == 1 ? 0 : 1;
	$result = clerk_set_extra($type, $value, $clerk_id);
	if(is_error($result)) {
		imessage($result, '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

elseif($ta == 'kefu') {
	$clerk_id = intval($_GPC['id']);
	$fields = trim($_GPC['fields']);
	if($fields == 'status') {
		$value = intval($_GPC['value']) == 1 ? 2 : 1;
	} else {
		$value = intval($_GPC['value']) == 1 ? 3 : 1;
	}
	pdo_update('tiny_wmall_store_clerk', array('kefu_status' => $value), array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk_id));
	if($value == 3) {
		$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $clerk_id));
		$_W['kefu']['user'] = array(
			'token' => $clerk['token'],
			'kefu_status' => 3
		);
		pload()->model('kefu');
		kefu_offline_reply();
	}
	imessage(error(0, '修改客服状态成功'), ireferer(), 'ajax');
}

include itemplate('store/shop/clerk');







