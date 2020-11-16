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
icheckauth();
$_W['kefu']['user'] = $fans = array(
	'role' => 'member',
	'uid' => $_W['member']['uid'],
	'token' => $_W['member']['token'],
	'nickname' => $_W['member']['nickname'],
	'avatar' => tomedia($_W['member']['avatar'])
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'chat';

if($op == 'chat') {
	$relation = trim($_GPC['relation']) ? trim($_GPC['relation']) : 'member2clerk';
	if(empty($relation)) {
		imessage(error(-1, '请选择咨询对象0！'), '', 'ajax');
	}
	$kefuopenid = trim($_GPC['kefuopenid']);
	if(empty($kefuopenid) && $relation != 'member2kefu') {
		imessage(error(-1, '请选择咨询对象1！'), '', 'ajax');
	}
	$kefuunionid = trim($_GPC['kefuunionid']);
	if($relation == 'member2clerk' && empty($kefuunionid)) {
		imessage(error(-1, '请选择咨询对象2！'), '', 'ajax');
	}
	$kefu = array(
		'openid' => $kefuopenid,
		'unionid' => $kefuunionid,
	);
	$orderid = intval($_GPC['orderid']);
	$kefu = get_available_kefu($kefu, $fans, $relation, array('orderid' => $orderid));
	if(is_error($kefu)) {
		imessage($kefu, '', 'ajax');
	}
	$chat = kefu_get_available_chat($kefu, $fans, $relation, array('orderid' => $orderid));
	if(is_error($chat)) {
		imessage($chat, '', 'ajax');
	}
	$chatlog = kefu_get_chat_log($chat['id'], $fans);
	keft_set_notread_zero($chat, $fans);
	$result = array(
		'chatlog' => $chatlog,
		'chat' => $chat,
		'kefu' => $kefu,
		'fans' => $fans,
		'reply' => kefu_get_fastreply($fans, $relation),
		'order' => kefu_get_order($orderid),
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'addchat') {
	$chatid = intval($_GPC['chatid']);
	$chat = kefu_get_chat($chatid);
	if(empty($chat)) {
		imessage(error(-1, '获取聊天信息失败！'), '', 'ajax');
	}
	$iscanchat = kefu_check_chat_available($chat, $fansopenid);
	if(is_error($iscanchat)) {
		imessage($iscanchat, '', 'ajax');
	}
	$log = kefu_add_chatlog($chat);

	$result = array(
		'log' => $log
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'addreply') {
	$relation = trim($_GPC['relation']) ? trim($_GPC['relation']) : 'member2clerk';
	if(empty($relation)) {
		imessage(error(-1, '请选择咨询对象0！'), '', 'ajax');
	}
	$content = trim($_GPC['content']);
	if(empty($content)) {
		imessage(error(-1, '回复内容不能为空'), '', 'ajax');
	}
	$status = kefu_add_fastreply($fans, $content, $relation);
	$result = array(
		'reply' => kefu_get_fastreply($fans, $relation)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'index') {
	kefu_update_info($_W['kefu']['user']);
	$chats = kefu_get_mychat($fans);
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
		imessage(error(-1, '获取聊天信息失败！'), '', 'ajax');
	}
	$chatlog = kefu_get_chat_log($chat['id'], $fans);
	$result = array(
		'chatlog' => $chatlog,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'zero') {
	$chatid = intval($_GPC['chatid']);
	$status = keft_set_notread_zero($chatid, $fans);
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, '未读消息数已设置为0'), '', 'ajax');
}

elseif($op == 'delete') {
	$chatid = intval($_GPC['chatid']);
	$status = kefu_delete_chat($chatid);
	if(empty($status)) {
		imessage(error(0, '会话移除失败'), '', 'ajax');
	}
	imessage(error(0, '会话移除成功'), '', 'ajax');
}


