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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	icheckauth();
	$_W['page']['title'] = '客服';
	if(empty($_W['member'])) {
		$openid = '1234';
		$fans = array(
			'token' => $openid,
			'nickname' => '游客',
			'avatar' => ''
		);
	} else {
		$fans = $_W['member'];
		$openid = $_W['member']['token'];
	}

	$toopenid = 'wasd';
	$cservice = array(
		'token' => $toopenid,
		'nickname' => '客服',
		'avatar' => 'http://tp1.sinaimg.cn/1571889140/180/40030060651/1',
	);
	if(empty($cservice)) {
		imessage(error(-1, '获取客服信息失败！'), '', 'error');
	}
	$ishei = pdo_fetch('select id from ' . tablename('tiny_wmall_kefu_chat') . " where uniacid = :uniacid and fansopenid = :fansopenid and ishei = 1", array(':uniacid' => $_W['uniacid'], ':fansopenid' => $openid));
	if(!empty($ishei)) {
		imessage(error(-1, '您暂时不能咨询！'), '', 'error');
	}
	if($openid == $toopenid) {
		imessage(error(-1, '不能和自己聊天！'), '', 'error');
	}
	$chat = pdo_fetch('select * from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and fansopenid = :fansopenid and kefuopenid = :kefuopenid', array(':uniacid' => $_W['uniacid'], ':fansopenid' => $openid, ':kefuopenid' => $cservice['token']));
	if(empty($chat)) {
		$chatData = array(
			'uniacid' => $_W['uniacid'],
			'fansopenid' => $openid,
			'fansavatar' => $fans['avatar'],
			'fansnickname' => $fans['nickname'],
			'kefuopenid' => $cservice['token'],
			'kefuavatar' => tomedia($cservice['avatar']),
			'kefunickname' => $cservice['nickname'],
		);

		pdo_insert('tiny_wmall_kefu_chat', $chatData);
		$chat = pdo_fetch('select * from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and fansopenid = :fansopenid and kefuopenid = :kefuopenid', array(':uniacid' => $_W['uniacid'], ':fansopenid' => $openid, ':kefuopenid' => $cservice['token']));
	}

	$page = intval($_GPC['page']);
	$pindex = max(1, $page);
	$psize = 20;
	$chat_logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_kefu_chat_log') . " where uniacid = :uniacid and chat_id = :chat_id and fansdel = 0 order by addtime asc limit " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':chat_id' => $chat['id']));
	$records = array();
	if(!empty($chat_logs)) {
		foreach($chat_logs as $log) {

		}
	}
	include itemplate('member');
}

elseif($op == 'addchat') {
	$cservice = array(
		'token' => 'wasd',
		'nickname' => '客服',
		'avatar' => 'http://tp1.sinaimg.cn/1571889140/180/40030060651/1',
	);
	//检测是否在工作时间

	$chat_id = intval($_GPC['chat_id']);
	$chat = pdo_fetch('select * from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $chat_id));
	$ishei = pdo_fetch('select id from ' . tablename('tiny_wmall_kefu_chat') . " where uniacid = :uniacid and fansopenid = :fansopenid and ishei = 1", array(':uniacid' => $_W['uniacid'], ':fansopenid' => $chat['fansopenid']));
	if(!empty($ishei)) {
		imessage(error(-1, '您暂时不能咨询！'), '', 'error');
	}
	$content = trim($_GPC['content']);
	$type = trim($_GPC['type']);
	$toopenid = trim($_GPC['toopenid']);
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'chat_id' => $chat['id'],
		'openid' => $chat['fansopenid'],
		'nickname' => $chat['fansnickname'],
		'avatar' => $chat['fansavatar'],
		'type' => $type,
		'toopenid' => trim($_GPC['toopenid']),
		'addtime' => TIMESTAMP,
		'content' => $content,
	);
	$insert_id = pdo_insert('tiny_wmall_kefu_chat_log', $insert);
	if(!empty($insert_id)) {
		$update = array(
			'kefunotread' => $chat['kefunotread'] + 1,
			'fansdel' => 0,
			'kefudel' => 0,
			'lastcontent' => $content,
			'msgtype' => $type,
			'lasttime' => TIMESTAMP
		);
		pdo_update('tiny_wmall_kefu_chat', $update, array('uniacid' => $_W['uniacid'], 'id' => $chat['id']));
	}
	$result = array();
	imessage(error(0, $result), '', 'ajax');
}

