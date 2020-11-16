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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'a';

//加载GatewayClient。安装GatewayClient参见本页面底部介绍
require_once MODULE_ROOT . '/library/GatewayClient/Gateway.php';
// GatewayClient 3.0.0版本开始要使用命名空间
use GatewayClient\Gateway;
// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
Gateway::$registerAddress = '127.0.0.1:1238';

icheckauth();
$fans = array(
	'role' => 'member',
	'token' => $_W['member']['token'],
	'nickname' => $_W['member']['nickname'],
	'avatar' => tomedia($_W['member']['avatar'])
);
if($op == 'member') {
	icheckauth();
	$fans = array(
		'role' => 'member',
		'token' => $_W['member']['token'],
		'nickname' => $_W['member']['nickname'],
		'avatar' => tomedia($_W['member']['avatar'])
	);
	$client_id = $_GPC['client_id'];
	Gateway::bindUid($client_id, $fans['token']);

	/*$result = array(
		'type' => 'message',
		'data' => array(
			'content' => 'dddd',
			'id' => 'lll',
			'type' => 'kefu'
		)
	);
	Gateway::sendToClient($client_id, json_encode($result));*/
	imessage(error(0, ''), '', 'ajax');
}

