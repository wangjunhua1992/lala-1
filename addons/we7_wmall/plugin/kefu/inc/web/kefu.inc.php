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

$_W['kefu']['user'] = $kefu = array(
	'role' => 'kefu',
	'kefu_id' => $_W['user']['uid'],
	'token' => $_W['user']['token'],
	'nickname' => $_W['user']['nickname'],
	'avatar' => tomedia($_W['user']['avatar']),
	'kefu_status' => $_W['user']['kefu_status']
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'chat';

if($op == 'chat') {
	$_W['page']['title'] = '客服接待';

	$chats = kefu_get_mychat($kefu);
	$first = array();
	if(!empty($chats['chats'])) {
		$first = reset($chats['chats']);
	}
	$fansopenid = trim($_GPC['fansopenid']);
	$relation = trim($_GPC['relation']);
	if(empty($fansopenid) && empty($relation)) {
		$relation = $first['relation'];
		$fansopenid = $first['fansopenid'];
	}

	$fans = array(
		'token' => $fansopenid
	);
	$touserole = kefu_get_touserrole($relation);
	$fans = !empty($fans['token']) ? kefu_get_fans($fans['token'], $touserole) : array();
	$orderid = intval($_GPC['orderid']);
	$chat = !empty($fans) ? kefu_get_available_chat($kefu, $fans, $relation, array('orderid' => $orderid)) : $first;
	$chatlog = array(
		'logs' => array(),
		'min' => 0
	);
	if(!empty($chat)) {
		$chatlog = kefu_get_chat_log($chat['id'], $kefu);
		keft_set_notread_zero($chat, $kefu);
	}

	$result = array(
		'relation' => $relation,
		'chatlog' => $chatlog,
		'chat' => $chat,
		'kefu' => $kefu,
		'fans' => $fans,
		'chats' => $chats,
		'reply' => kefu_get_fastreply($kefu, $relation),
		'order' => kefu_get_order($orderid),
	);
	if($_W['ispost']) {
		imessage(error(0, $result), '', 'ajax');
	}
	kefu_update_info($_W['kefu']['user']);
	include itemplate('kefu');
}

elseif($op == 'addchat') {
	$chatid = intval($_GPC['chatid']);
	$chat = kefu_get_chat($chatid);
	if(empty($chat)) {
		imessage(error(-1, '获取聊天信息失败！'), '', 'error');
	}
	$iscanchat = kefu_check_chat_available($chat, $fansopenid);
	if(is_error($iscanchat)) {
		imessage($iscanchat, '', 'error');
	}
	$log = kefu_add_chatlog($chat);

	$result = array(
		'log' => $log
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'addreply') {
	$relation = trim($_GPC['relation']) ? trim($_GPC['relation']) : 'member2kefu';
	if(empty($relation)) {
		imessage(error(-1, '请选择咨询对象0！'), '', 'error');
	}
	$content = trim($_GPC['content']);
	if(empty($content)) {
		imessage(error(-1, '回复内容不能为空'), '', 'error');
	}
	$status = kefu_add_fastreply($kefu, $content, $relation);
	$result = array(
		'reply' => kefu_get_fastreply($kefu, $relation)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'index') {
	$chats = kefu_get_mychat($kefu);
	$result = array(
		'chats' => $chats
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'order') {
	$chatid = intval($_GPC['chatid']);
	$orders = kefu_get_orders($chatid);
	$result = array(
		'orders' => $orders
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'more') {
	$chatid = intval($_GPC['chatid']);
	$chat = kefu_get_chat($chatid);
	if(empty($chat)) {
		imessage(error(-1, '获取聊天信息失败！'), '', 'error');
	}
	$chatlog = kefu_get_chat_log($chat['id'], $kefu);
	$result = array(
		'chatlog' => $chatlog,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'zero') {
	$chatid = intval($_GPC['chatid']);
	$status = keft_set_notread_zero($chatid, $kefu);
	if(is_error($status)) {
		imessage($status, '', 'error');
	}
	imessage(error(0, '未读消息数已设置为0'), '', 'ajax');
}

elseif($op == 'kefu_status') {
	$update = array(
		'kefu_status' => intval($_GPC['kefu_status'])
	);
	pdo_update('users', $update, array('token' => $kefu['token']));
	$_W['kefu']['user']['kefu_status'] = $update['kefu_status'];
	kefu_offline_reply();
	imessage(error(0, '客服状态切换成功'), '', 'ajax');
}


