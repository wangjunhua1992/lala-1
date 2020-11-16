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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';

//加载GatewayClient。安装GatewayClient参见本页面底部介绍
require_once MODULE_ROOT . '/library/GatewayClient/Gateway.php';
// GatewayClient 3.0.0版本开始要使用命名空间
use GatewayClient\Gateway;
// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
Gateway::$registerAddress = '127.0.0.1:2345';

if($op == 'bind') {
	$_W['kefu']['user'] = $kefu = array(
		'role' => 'kefu',
		'kefu_id' => $_W['user']['uid'],
		'token' => $_W['user']['token'],
		'nickname' => $_W['user']['nickname'],
		'avatar' => tomedia($_W['user']['avatar']),
		'kefu_status' => $_W['user']['kefu_status']
	);
	$client_id = $_GPC['client_id'];
	Gateway::bindUid($client_id, $_W['kefu']['user']['token']);
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$system = array(
			'status' => intval($_GPC['system']['status']),
			'store_status' => intval($_GPC['system']['store_status']),
			'deliveryer_status' => intval($_GPC['system']['deliveryer_status']),
			'kefu_status' => intval($_GPC['system']['kefu_status']),
			/*'portal' => empty($_GPC['system']['portal']) ? array() : array_map('trim', $_GPC['system']['portal']),
			'recordSave' => intval($_GPC['system']['recordSave']),*/
			'allotRule' => intval($_GPC['system']['allotRule']),
		);

		$autoreply = array(
			/*'firstMessage' => array(
				'status' => intval($_GPC['autoreply']['firstMessage']['status']),
				'content' => trim($_GPC['autoreply']['firstMessage']['content'])
			),*/
			'closingTime' => array(
				'status' => intval($_GPC['autoreply']['closingTime']['status']),
				'content' => trim($_GPC['autoreply']['closingTime']['content'])
			),
			'busyReply' => array(
				'content' => trim($_GPC['autoreply']['busyReply']['content'])
			)
		);
		/*if($autoreply['firstMessage']['status'] == 1 && empty($autoreply['firstMessage']['content'])) {
			imessage(error(-1, '首句欢迎语的内容不能为空'), '', 'ajax');
		}*/
		if(empty($autoreply['busyReply']['content'])) {
			imessage(error(-1, '忙碌状态自动回复的内容不能为空'), '', 'ajax');
		}
		if($autoreply['closingTime']['status'] == 1) {
			if(empty($autoreply['closingTime']['content'])) {
				imessage(error(-1, '下班提醒的内容不能为空'), '', 'ajax');
			}

			$starttime = array_map('trim', $_GPC['starttime']);
			$endtime = array_map('trim', $_GPC['endtime']);
			$worktime = array();
			if(!empty($starttime)) {
				foreach($starttime as $key => $value) {
					if(!empty($value) && !empty($endtime[$key])) {
						$worktime[] = array('start' => $value, 'end' => $endtime[$key]);
					}
				}
			}
			if(count($worktime) <= 0) {
				imessage(error(-1, '请设置工作时间'), '', 'ajax');
			}
			$autoreply['worktime'] = $worktime;

			$workday = array(1, 2, 3, 4, 5, 6, 7);
			if(!empty($_GPC['workday'])) {
				$workday = array_map('intval', $_GPC['workday']);
			}
			$autoreply['workday'] = $workday;
		}

		/*$overtime = array(
			'member' => array(
				'status' => intval($_GPC['overtime']['member']['status']),
				'closetime' => floatval($_GPC['overtime']['member']['closetime']),
				'content' => trim($_GPC['overtime']['member']['content']),
				'tipstime' => floatval($_GPC['overtime']['member']['tipstime'])
			),
			'kefu' => array(
				'status' => intval($_GPC['overtime']['kefu']['status']),
				'closetime' => floatval($_GPC['overtime']['kefu']['closetime']),
				'content' => trim($_GPC['overtime']['kefu']['content']),
				'tipstime' => floatval($_GPC['overtime']['kefu']['tipstime'])
			)
		);
		if($overtime['member']['status'] == 1) {
			if($overtime['member']['closetime'] <= 0) {
				imessage(error(-1, '请设置客户未响应的超时时间'), '', 'ajax');
			}
			if(empty($overtime['member']['content'])) {
				imessage(error(-1, '请设置客户未响应的超时提醒内容'), '', 'ajax');
			}
			if($overtime['member']['tipstime'] <= 0) {
				imessage(error(-1, '请设置客户未响应的发送消息时间'), '', 'ajax');
			}
			if($overtime['member']['tipstime'] >= $overtime['member']['closetime']) {
				imessage(error(-1, '客户未响应的发送消息时间应小于超时时间'), '', 'ajax');
			}
		}
		if($overtime['kefu']['status'] == 1) {
			if($overtime['kefu']['closetime'] <= 0) {
				imessage(error(-1, '请设置客服未接待的超时时间'), '', 'ajax');
			}
			if(empty($overtime['kefu']['content'])) {
				imessage(error(-1, '请设置客服未接待的超时提醒内容'), '', 'ajax');
			}
			if($overtime['kefu']['tipstime'] <= 0) {
				imessage(error(-1, '请设置客服未接待的发送消息时间'), '', 'ajax');
			}
			if($overtime['kefu']['tipstime'] >= $overtime['kefu']['closetime']) {
				imessage(error(-1, '客服未接待的发送消息时间应小于超时时间'), '', 'ajax');
			}
		}*/
		$basic = array(
			'system' => $system,
			'autoreply' => $autoreply,
			//'overtime' => $overtime
		);
		set_plugin_config('kefu', $basic);
		imessage(error(0, '客服基础设置设置成功'), 'refresh', 'ajax');
	}
	$setting = get_plugin_config('kefu');
	include itemplate('config');
}

