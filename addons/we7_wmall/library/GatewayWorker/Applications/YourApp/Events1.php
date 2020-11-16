<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);
use \GatewayWorker\Lib\Gateway;
use Workerman\Lib\Timer;
global $_W, $_GPC;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
	/**
	 * 当客户端连接时触发
	 * 如果业务不需此回调可以删除onConnect
	 *
	 * @param int $client_id 连接id
	 */
	public static function onConnect($client_id)
	{
		// 向当前client_id发送数据
		$data = array(
			'type' => 'connect',
			'data' => array(
				'clientId' => $client_id
			)
		);
		Gateway::sendToClient($client_id, json_encode($data));
		// 向所有人发送
		//Gateway::sendToAll("$client_id login\r\n");
	}

	/**
	 * 当客户端发来消息时触发
	 * @param int $client_id 连接id
	 * @param mixed $message 具体消息
	 */
	public static function onMessage($client_id, $message)
	{
		global $_W, $_GPC;
		$message = json_decode($message, true);
		require_once __DIR__ . '/bootstrap.inc.php';
		$_W['uniacid'] = $message['uniacid'];
		$_W['siteroot'] = $message['siteroot'];
		$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'];
		$_W['config']['kefu'] = get_plugin_config('kefu');
		require_once __DIR__ . '/../../../../plugin/kefu/model.php';

		$getData = $message['data'];
		//flog('$_W', $_W);
		$result = array(
			'type' => $message['type'],
			'data' => array()
		);
		switch ($message['type'])
		{
			case 'init':
				//某个客户端上线
				//client_id与uid绑定 uid与client_id是一对多的关系，系统允许一个uid下有多个client_id
				$uid = $getData['uid'];
				Gateway::bindUid($client_id, $getData['type'] . $uid);

				switch ($getData['type'])
				{
					//顾客登录
					case 'member':
						$member = pdo_fetch('select * from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and uid = :uid ', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
						$result['data']['member'] = $member;
						//分配客服
						$service = kefu_get_one($uid);
						$result['data']['service'] = $service;
						//聊天记录
						$records = kefu_get_chat_records(array('from_uid' => $uid, 'from_uid_type' => 1, 'to_uid' => $service['uid'], 'to_uid_type' => 2));
						$result['data']['records'] = $records;

						$orderId = intval($getData['orderId']);
						if($orderId > 0) {
							$result['data']['order'] = kefu_get_order($orderId, $uid);
						}

						//向客户端发送消息
						Gateway::sendToClient($client_id, json_encode($result));

						//首句欢迎语
						$status = kefu_is_send_first_msg(array('from_uid' => $service['uid'], 'from_uid_type' => 2, 'to_uid' => $uid, 'to_uid_type' => 1));
						if(!is_error($status)) {
							$mine = array(
								'avatar' => $service['avatar'],
								'content' => $status['message'],
								'id' => $service['uid'],
								'mine' => true,
								'username' => $service['nickname']
							);
							$to = array(
								'avatar' => $member['avatar'],
								'content' => '',
								'id' => $uid,
								'mine' => false,
								'name' => $member['nickname'],
								'timestamp' => time() * 1000,
								'type' => 'kefu',
								'username' => $member['nickname']
							);
							$insert_id = kefu_save_record($mine, 2, $to, 1);
							if($insert_id > 0) {
								$firstMsg = array(
									'type' => 'message',
									'data' => array(
										'mine' => $mine,
										'to' => $to
									)
								);
								Gateway::sendToUid('member' . $uid, json_encode($firstMsg));
							}
						}
						break;

					//客服登录
					case 'service':
						$service = kefu_get_user_info($uid, 2);
						$result['data']['service'] = $service;
						//客服聊天记录
						$records = kefu_service_chat_records_fetchall($uid, 2);
						$result['data']['records'] = $records;
						Gateway::sendToClient($client_id, json_encode($result));
						break;
					default:

				}
				break;
			case 'message':
				//客户端发来消息
				$mine = $getData['mine'];
				$to = $getData['to'];
				$from_uid_type = intval($getData['from_uid_type']);
				$to_uid_type = intval($getData['to_uid_type']);

				//顾客发来消息，判断是否是工作时间
				if($from_uid_type == 1 && $to_uid_type == 2) {
					$avaliable = kefu_check_currenttime_avaliable();
					if(is_error($avaliable)) {
						$mine1 = array(
							'avatar' => $to['avatar'],
							'content' => $avaliable['message'],
							'id' => $to['id'],
							'mine' => true,
							'username' => $to['name']
						);
						$to1 = array(
							'avatar' => $mine['avatar'],
							'content' => '',
							'id' => $mine['id'],
							'mine' => false,
							'name' => $mine['username'],
							'timestamp' => time() * 1000,
							'type' => 'kefu',
							'username' => $mine['username']
						);
						$result['data']['mine'] = $mine1;
						$result['data']['to'] = $to1;
						Gateway::sendToUid('member' . $mine['id'], json_encode($result));
						break;
					}
				}

				//清除定时器
				$allSession = self::getSessionByUid($mine['id'], $from_uid_type);
				if(!empty($allSession)) {
					foreach($allSession as $value) {
						if(!empty($value['tipsTimerId'])) {
							Timer::del($value['tipsTimerId']);
						}
						if($value['closeTimerId']) {
							Timer::del($value['closeTimerId']);
						}
					}
				}

				//保存聊天记录
				$insert_id = kefu_save_record($mine, $from_uid_type, $to, $to_uid_type);
				if($insert_id > 0) {
					//给收信息人发消息
					$types = array('1' => 'member', '2' => 'service', '3' => 'store', '4' => 'deliveryer');
					$trueToUid = $types[$to_uid_type] . $to['id'];
					$toArr = Gateway::getClientIdByUid($trueToUid);
					$trueFromUid = $types[$from_uid_type] . $mine['id'];
					$fromArr = Gateway::getClientIdByUid($trueFromUid);

					if(empty($toArr)) {
						//收消息人均已下线
						$result['type'] = 'none';
						$contentText = array('1' => '顾客已下线', '2' => '客服已下线', '3' => '商户已下线', '4' => '配送员已下线');
						$result['data'] = array(
							'content' => $contentText[$to_uid_type],
							'id' => $to['id'],
							'type' => 'kefu'
						);
						Gateway::sendToUid($types[$from_uid_type] . $mine['id'], json_encode($result));
					} else {
						$result['data']['mine'] = $mine;
						$result['data']['to'] = $to;
						//向uid绑定的所有在线client_id发送数据
						Gateway::sendToUid($trueToUid, json_encode($result));

						//发送信息后开启2个定时器, 10s后关闭会话的定时器，关闭会话的前5s发送关闭会话提示信息的定时器
						$config = $_W['config']['kefu']['basic'];
						$openTimer = false;
						$closeTimerIntval = 0;
						$tipsTimerInterval = 0;
						$content = '';
						if($to_uid_type == 1 && $config['overtime']['member']['status'] == 1) {
							$openTimer = true;
							$closeTimerIntval = $config['overtime']['member']['closetime'] * 60;
							$tipsTimerInterval = ($config['overtime']['member']['closetime'] - $config['overtime']['member']['tipstime']) * 60;
							$content = $config['overtime']['member']['content'];
						} elseif($to_uid_type == 2 && $config['overtime']['kefu']['status'] == 1) {
							$openTimer = true;
							$closeTimerIntval = $config['overtime']['kefu']['closetime'] * 60;
							$tipsTimerInterval = ($config['overtime']['kefu']['closetime'] - $config['overtime']['kefu']['tipstime']) * 60;
							$content = $config['overtime']['kefu']['content'];
						}

						if($openTimer) {
							$tipsTimerId = Timer::add($tipsTimerInterval, function($trueToUid, $getData, $result, $content) {
								if($getData['to_uid_type'] == 1) {
									//给顾客发消息
									$result['data']['mine']['content'] = $content;
									Gateway::sendToUid($trueToUid, json_encode($result));
								} elseif($getData['to_uid_type'] == 2) {
									//客服端提示
									$result = array(
										'type' => 'close',
										'data' => array(
											'content' => $content,
											'id' => $getData['mine']['id'],
											'type' => 'kefu'
										)
									);
									Gateway::sendToUid($trueToUid, json_encode($result));
								}
							}, array($trueToUid, $getData, $result, $content), false);
							self::setSessionByUid($to['id'], $to_uid_type, 'tipsTimerId', $tipsTimerId);

							$closeTimerId = Timer::add($closeTimerIntval, function($trueToUid, $trueFromUid, $getData, $fromArr, $toArr) {
								$result = array(
									'type' => 'close',
									'data' => array(
										'content' => '会话已关闭',
										'id' => $getData['mine']['id'],
										'type' => 'kefu'
									)
								);
								Gateway::sendToUid($trueToUid, json_encode($result));
								if($getData['to_uid_type'] == 1) {
									//发给顾客，顾客未回复关闭顾客端会话
									foreach($toArr as $toItem) {
										Gateway::closeClient($toItem);
									}
								} elseif($getData['to_uid_type'] == 2) {
									//发给客服，客服未回复也要关闭顾客端会话
									$result = array(
										'type' => 'close',
										'data' => array(
											'content' => '会话已关闭',
											'id' => $getData['to']['id'],
											'type' => 'kefu'
										)
									);
									Gateway::sendToUid($trueFromUid, json_encode($result));
									if(!empty($fromArr)) {
										foreach($fromArr as $fromItem) {
											Gateway::closeClient($fromItem);
										}
									}
								}
							}, array($trueToUid, $trueFromUid, $getData, $fromArr, $toArr), false);
							self::setSessionByUid($to['id'], $to_uid_type, 'closeTimerId', $closeTimerId);
						}
					}
				}
				break;

			default:

		}

	}

	/**
	 * 当用户断开连接时触发
	 * @param int $client_id 连接id
	 */
	public static function onClose($client_id)
	{
		// 向所有人发送
		GateWay::sendToAll("$client_id logout\r\n");
	}

	public static function setSessionByUid($uid, $type, $key, $value) {
		//设置某个用户的所有客户端的缓存
		$types = array('1' => 'member', '2' => 'service', '3' => 'store', '4' => 'deliveryer');
		$trueUid = $types[$type] . $uid;
		$clientIds = Gateway::getClientIdByUid($trueUid);
		if(!empty($clientIds)) {
			foreach($clientIds as $client_id) {
				$session = Gateway::getSession($client_id);
				if(empty($session)) {
					$session = array();
				}
				$session[$key] = $value;
				Gateway::setSession($client_id, $session);
			}
		}
		return true;
	}

	public static function getSessionByUid($uid, $type) {
		//获取某个用户所有客户端的缓存
		$types = array('1' => 'member', '2' => 'service', '3' => 'store', '4' => 'deliveryer');
		$trueUid = $types[$type] . $uid;
		$clientIds = Gateway::getClientIdByUid($trueUid);
		$allSession = array();
		if(!empty($clientIds)) {
			foreach($clientIds as $client_id) {
				$session = Gateway::getSession($client_id);
				if(empty($session)) {
					$session = array();
				}
				$allSession[$client_id] = $session;
			}
		}
		return $allSession;
	}
}
