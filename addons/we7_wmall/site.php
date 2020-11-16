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
include(IA_ROOT . '/addons/we7_wmall/version.php');
include(IA_ROOT . '/addons/we7_wmall/defines.php');
$_W['LangType'] = 'zh-cn';
if(!empty($_GPC['lang'])) {
	$_W['LangType'] = !empty($_GPC['lang']) ? trim($_GPC['lang']) : 'zh-cn';
}
include(IA_ROOT . '/addons/we7_wmall/model.php');
class We7_wmallModuleSite extends WeModuleSite {
	private $cache = array();
	public function __construct() {}

	public function doWebWeb() {
		$this->router();
	}

	public function doMobileMobile() {
		$this->router();
	}

	public function doMobileApi() {
		global $_W, $_GPC;
		mload()->func('common');
		mload()->classs('TyAccount');
		mload()->func('api');
		mload()->model('common');
		mload()->model('member');
		mload()->model('store');
		mload()->model('order');
		$_W['we7_wmall']['global'] = get_global_config();
		if($_W['we7_wmall']['global']['development'] == 1) {
			ini_set('display_errors', '1');
			error_reporting(E_ALL ^ E_NOTICE);
		}
		$_W['is_agent'] = is_agent();
		$_W['agentid'] = 0;
		if($_W['is_agent']) {
		}
		$_W['we7_wmall']['config'] = get_system_config();
		$_W['we7_wmall']['config']['wxapp'] = get_plugin_config('wxapp');

		$routers = str_replace('//', '/', trim($_GPC['r'], '/'));
		$routers = explode('.', $routers);

		$_W['_do'] = !empty($_W['_do']) ? $_W['_do'] : trim($_GPC['do']);
		$_W['_controller'] = !empty($_W['_controller']) ? $_W['_controller'] : trim($_GPC['ctrl']);
		$_W['_action'] = trim($_GPC['ac']);
		$_W['_op'] = trim($_GPC['op']);
		$_W['_ta'] = trim($_GPC['ta']);
		$_W['_router'] = implode('/', array($_W['_controller'], $_W['_action'], $_W['_op']));
		require(WE7_WMALL_PATH . "inc/api/__init.php");
		$file_init = WE7_WMALL_PATH . "inc/api/{$_W['_controller']}/__init.php";
		$file_path = WE7_WMALL_PATH . "inc/api/{$_W['_controller']}/{$_W['_action']}/{$_W['_op']}.inc.php";
		if(is_file($file_init)) {
			require($file_init);
		}
		if(!is_file($file_path)) {
			ijson("控制器 {$_W['_controller']} 方法 {$_W['_action']}/{$_W['_op']} 未找到!", 'close', 'ajax');
		}
		require($file_path);
	}

	public function router() {
		$bootstrap = WE7_WMALL_PATH . 'inc/__init.php';
		require $bootstrap;
		exit();
	}

	public function __call($name, $arguments) {
		global $_W, $_GPC;
		$isWeb = stripos($name, 'doWeb') === 0;
		$isMobile = stripos($name, 'doMobile') === 0;
		$isApi = stripos($name, 'doApi') === 0;
		if($isWeb || $isMobile) {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			if($isWeb) {
				require $dir . 'web/__init.php';
				$do = strtolower(substr($name, 5));
				$sys = substr($do, 0, 3);
				if($sys == 'ptf') {
					$do = substr($do, 3);
					$dir .= 'web/plateform/';
				} elseif($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'web/common/';
				} else {
					$dir .= 'web/store/';
				}
				$fun = $do;
			} else {
				require $dir . 'mobile/__init.php';
				$do = strtolower(substr($name, 8));
				$sys = substr($do, 0, 3);
				if($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'mobile/common/';
				} else {
					$sys = substr($do, 0, 2);
					if($sys == 'mg') {
						$do = substr($do, 2);
						$dir .= 'mobile/manage/';
						require $dir . 'bootstrap.inc.php';
					} elseif($sys == 'dy') {
						$do = substr($do, 2);
						$dir .= 'mobile/delivery/';
						require $dir . 'bootstrap.inc.php';
					} else {
						$dir .= 'mobile/store/';
						$routers = array(
							'goods' => imurl('wmall/store/goods', array('sid' => $_GET['sid'])),
							'store' => imurl('wmall/store/index', array('sid' => $_GET['sid'])),
						);
						if(in_array($do, array_keys($routers))) {
							header("location: {$routers[$do]}");
							die;
						}
					}
				}
				$fun = $do;
			}

			$file = $dir . $fun . '.inc.php';
			if(file_exists($file)) {
				require $file;
				exit;
			}
		} else {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			require $dir . 'api/__init.php';
			$do = strtolower(substr($name, 5));
			$sys = substr($do, 0, 2);
			if($sys == 'dy') {
				$do = substr($do, 2);
				if($_GPC['v'] == 'v3') {
					$dir .= 'api/delivery_v3/';
				} else {
					$dir .= 'api/delivery/';
				}
				require $dir . 'bootstrap.inc.php';
			}
			$fun = $do;
			$file = $dir . $fun . '.inc.php';
			if(file_exists($file)) {
				require $file;
				exit;
			}
		}
		trigger_error("访问的方法 {$name} 不存在.", E_USER_WARNING);
		return null;
	}

	public function printResult($params) {
		global $_W;
		mload()->func('common');
		$_W['siteroot'] = str_replace(array('/addons/we7_wmall', '/payment/print'), array('', ''), $_W['siteroot']);
		$_W['uniacid'] = $params['uniacid'];
		$config = get_system_config();
		$_W['we7_wmall']['config'] = $config;
		if($params['result'] == 'success' && $params['from'] == 'notify') {
			mload()->model('order');
			mload()->model('store');
			order_status_update($params['order_id'], 'handle', array('role' => 'printer'));
			echo 'OK';
			$return = '';
			if($params['printer_type'] == 'lingdian') {
				$return = json_encode(array('code' => 0));
			}
			die($return);
		}
	}

	public function payResult($params) {
		global $_W;
		mload()->func('common');
		mload()->model('agent');
		$_W['siteroot'] = str_replace(array('/addons/we7_wmall', '/payment/qianfan', '/payment/majia', '/payment/alipay'), array('', '', ''), $_W['siteroot']);
		$_W['uniacid'] = $params['uniacid'];
		$record = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'order_sn' => $params['tid']));
		if(empty($record)) {
			exit();
		}
		if($record['addtime'] < strtotime('-1 month')) {
			exit();
		}
		$_W['agentid'] = $record['agentid'];
		$config = get_system_config();
		$_W['we7_wmall']['config'] = $config;
		if(($params['result'] == 'success' && $params['from'] == 'notify') || ($params['from'] == 'return' && in_array($params['type'], array('delivery', 'finishMeal', 'credit')))) {
			mload()->model('order');
			mload()->model('store');
			$record['data'] = iunserializer($record['data']);
			$params['prepay_id'] = $record['data']['prepay_id'];
			pdo_update('tiny_wmall_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('id' => $record['id']));
			//找人代付功能
			if($record['order_type'] == 'peerpay'){
				$order = pdo_get('tiny_wmall_order_peerpay_payinfo', array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
				if(!empty($order)) {
					if(!$order['is_pay']) {
						pdo_update('tiny_wmall_order_peerpay_payinfo', array('is_pay' => 1, 'paytime' => TIMESTAMP), array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
						$peerpay = pdo_get('tiny_wmall_order_peerpay', array('id' => $order['pid']));
						if(!empty($peerpay)) {
							$update = array(
								'peerpay_realprice' => round($peerpay['peerpay_realprice'] - $order['final_fee'], 2),
							);
							if($update['peerpay_realprice'] <= 0) {
								$update['status'] = 1;
							}
							pdo_update('tiny_wmall_order_peerpay', $update, array('id' => $peerpay['id']));
							if($update['status'] == 1) {
								$record = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'id' => $peerpay['plid']));
								$params = array(
									'channel' => 'wap',
									'type' => 'peerpay',
									'card_fee' => $record['fee'],
									'is_pay' => 1,
									'paytime' => TIMESTAMP,
									'out_trade_no' => '',
									'transaction_id' => '',
								);
							}
						}
					}
				}
			}
			if($record['order_type'] == 'takeout') {
				order_system_status_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'deliveryCard') {
				pload()->model('deliveryCard');
				card_setmeal_buy($record['order_id'], $params);
			} elseif($record['order_type'] == 'paybill') {
				mload()->model('order');
				mload()->model('paybill');
				paybill_order_status_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'errander') {
				pload()->model('errander');
				$_W['_plugin']['config'] = get_plugin_config('errander');
				$order = pdo_get('tiny_wmall_errander_order', array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
				if(!empty($order) && !$order['is_pay']) {
					$data = array(
						'order_channel' => $params['channel'],
						'pay_type' => $params['type'],
						'final_fee' => $params['card_fee'],
						'is_pay' => 1,
						'paytime' => TIMESTAMP,
						'out_trade_no' => $params['uniontid'],
						'transaction_id' => $params['transaction_id'],
					);
					if(!empty($params['prepay_id'])) {
						$data['data'] = iunserializer($order['data']);
						$data['data']['prepay_id'] = $params['prepay_id'];
						$data['data']['prepay_times'] = 3;
						$data['data'] = iserializer($data['data']);
					}
					pdo_update('tiny_wmall_errander_order', $data, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
					errander_order_status_update($order['id'], 'pay');
					errander_order_status_update($order['id'], 'dispatch');
				}
			} elseif($record['order_type'] == 'recharge') {
				mload()->model('member');
				member_recharge_status_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'freelunch'){
				pload()->model('freeLunch');
				freelunch_partaker_status_update($record['order_id'], 'pay');
			} elseif($record['order_type'] == 'advertise') {
				pload()->model('advertise');
				advertise_trade_update($record['order_id']);
			} elseif($record['order_type'] == 'creditshop') {
				pload()->model('creditshop');
				creditshop_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'seckill') {
				pload()->model('seckill');
				seckill_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'vip') {
				pload()->model('vip');
				vip_trade_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'recharge_vip') {
				pload()->model('vip');
				vip_recharge_status_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'mealRedpacket_plus') {
				pload()->model('mealRedpacket');
				mealRedpacket_plus_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'mealRedpacket') {
				pload()->model('mealRedpacket');
				mealRedpacket_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'gohome') {
				pload()->model('gohome');
				gohome_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'tongcheng') {
				pload()->model('tongcheng');
				tongcheng_information_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'haodian') {
				pload()->model('haodian');
				haodian_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'svip') {
				pload()->model('svip');
				svip_meal_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'plugincenter') {
				mload()->model('plugincenter');
				plugincenter_order_update($record['order_id'], 'pay', $params);
			} elseif($record['order_type'] == 'vipRedpacket') {
				pload()->model('vipRedpacket');
				vipRedpacket_order_update($record['order_id'], 'pay', $params);
			}
		}
		$routers = array(
			'wechat' => array(
				'takeout' => imurl('wmall/order/index/detail', array('id' => $record['order_id']), true),
				'deliveryCard' => imurl('deliveryCard/index', array(), true),
				'recharge' => imurl('wmall/member/mine', array(), true),
				'freelunch' => imurl('freeLunch/freeLunch/partake_success', array(), true),
				'errander' => imurl('errander/order/detail', array('id' => $record['order_id']), true),
				'peerpay' => imurl('system/paycenter/peerpay/paylist', array('payinfo_id' => $record['order_id']), true),
				'paybill' => imurl('wmall/member/mine', array(), true),
				'advertise' => imurl('manage/advertise/list', array(), true),
				'creditshop' => '',
				'mealRedpacket_plus' => imurl('mealRedpacket/plus/orderplus', array(), true),
				'plugincenter' => iurl('plugin/index/index', array(), true)
			),
			'vue' => array(
				'takeout' => ivurl('/pages/order/detail', array('id' => $record['order_id']), true),
				'errander' =>  ivurl('/pages/paotui/detail', array('id' => $record['order_id']), true),
				'deliveryCard' =>  ivurl('/package/pages/deliveryCard/index', array(), true),
				'recharge' =>  ivurl('/pages/member/mine', array(), true),
				'paybill' =>  ivurl('/pages/member/mine', array(), true),
				'creditshop' =>  ivurl('/pages/creditshop/list', array(), true),
				'freelunch' =>  ivurl('/pages/freelunch/partakeSuccess', array(), true),
				'mealRedpacket_plus' =>  ivurl('/package/pages/mealRedpacket/orderplus', array(), true),
				'mealRedpacket' =>  ivurl('/package/pages/mealRedpacket/meal', array(), true),
				'gohome' =>  ivurl('/gohome/pages/detail', array('id' => $record['order_id']), true),
				'tongcheng' =>  ivurl('/gohome/pages/tongcheng/detail', array('trade_id' => $record['order_id']), true),
				'haodian' =>  ivurl('/gohome/pages/haodian/application', array(), true),
				'svip' =>  ivurl('/pages/svip/mine', array(), true),
				'vipRedpacket' => ivurl('/plugin/pages/vipRedpacket/index', array(), true),
			),
		);
		$from = $ochannel = 'wechat';
		if($params['channel'] == 'h5app') {
			if($config['app']['customer']['webtype'] == 'vue') {
				$from = 'vue';
				$ochannel = 'h5app';
			}
		} elseif($params['channel'] == 'wap') {
			if($params['type'] == 'alipay') {
				$from = 'vue';
				$ochannel = 'owap';
			}
		}
		if($params['from'] == 'return') {
			$url = $routers[$from][$record['order_type']];
			if($record['order_type'] == 'takeout' && check_plugin_perm('wheel')) {
				mload()->model('plugin');
				pload()->model('wheel');
				$wheel_url = get_wheel_url(array('order_id' => $record['order_id'], 'ochannel' => $ochannel));
				$url = empty($wheel_url) ? $url : $wheel_url;
			}
			if(in_array($params['type'], array('credit'))) {
				imessage('下单成功', $url, 'success');
			} else {
				header('location:' . $url);
				die;
			}
		}
	}
}