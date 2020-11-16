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
icheckauth();
global $_W;
$params = @json_decode(base64_decode($_GPC['params']), true);
if(empty($params)) {
	$params = $_SESSION['pay_params'];
}
if(empty($params) || $params['module'] != 'we7_wmall') {
	imessage('支付参数错误.', ireferer(), 'error');
}
$payment = get_available_payment($params['order_type'], $params['sid'], false, $params['orderType']);
if(empty($payment)) {
	imessage('没有有效的支付方式, 请联系网站管理员.', '', 'error');
}
$pay_type = trim($_GPC['ta']);
if(empty($pay_type) || !in_array($pay_type, $payment)) {
	imessage('支付方式错误,请联系商家', ireferer(), 'error');
}
$routers = array(
	'takeout' => array(
		'table' => 'tiny_wmall_order',
		'cancel_status' => 6,
		'order_sn' => 'ordersn'
	),
	'deliveryCard' => array(
		'table' => 'tiny_wmall_delivery_cards_order',
		'order_sn' => 'ordersn'
	),
	'errander' => array(
		'table' => 'tiny_wmall_errander_order',
		'cancel_status' => 4,
		'order_sn' => 'order_sn'
	),
	'recharge' => array(
		'table' => 'tiny_wmall_member_recharge',
		'order_sn' => 'order_sn'
	),
	'freelunch' => array(
		'table' => 'tiny_wmall_freelunch_partaker',
		'order_sn' => 'order_sn'
	),
	'peerpay' => array(
		'table' => 'tiny_wmall_order_peerpay_payinfo',
		'order_sn' => 'order_sn'
	),
	'paybill' => array(
		'table' => 'tiny_wmall_paybill_order',
		'order_sn' => 'order_sn'
	),
	'mealRedpacket_plus' => array(
		'table' => 'tiny_wmall_superredpacket_meal_order',
		'order_sn' => 'order_sn'
	),
);
$router = $routers[$params['order_type']];
$order = pdo_get($router['table'], array('uniacid' => $_W['uniacid'], $router['order_sn'] => $params['tid']));
if(isset($router['cancel_status']) && $order['status'] == $router['cancel_status']) {
	imessage('订单已取消，不能发起支付', '', 'error');
}
$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
$pars  = array(
	':uniacid' => $_W['uniacid'],
	':module' => $params['module'],
	':tid' => $params['tid'],
);
$log = pdo_fetch($sql, $pars);
if(empty($log)) {
	imessage('系统支付错误, 请稍后重试.', '', 'error');
}
if(($pay_type != 'credit' && !empty($_GPC['notify'])) && $log['status'] != '0') {
	imessage('这个订单已经支付成功, 不需要重复支付.', '', 'error');
}

$moduleid = pdo_fetchcolumn("SELECT mid FROM ".tablename('modules')." WHERE name = :name", array(':name' => $params['module']));
$moduleid = empty($moduleid) ? '000000' : sprintf("%06d", $moduleid);

$record = array();
$record['type'] = $log['type'] = $pay_type;
if(empty($log['uniontid'])) {
	$record['uniontid'] = $log['uniontid'] = date('YmdHis').$moduleid.random(8,1);
}
pdo_update('core_paylog', $record, array('plid' => $log['plid']));

$ps = array();
$ps['tid'] = $log['plid'];
$ps['uniontid'] = $log['uniontid'];
$ps['user'] = $_W['fans']['from_user'];
$ps['fee'] = $log['card_fee'];
$ps['global_fee'] = round($log['card_fee'] * $_W['dollarrate'], 2);
$ps['title'] = $params['title'];
$ps['url_detail'] = $params['url_detail'];
$ps['openid'] = $log['openid'];

mload()->model('payment');

if($pay_type == 'alipay') {
	$ret = alipay_build($ps, $_W['we7_wmall']['config']['payment']['alipay']);
	if(is_error($ret)) {
		imessage('支付宝支付参数有错', ireferer(), 'error');
	}
	if($ret['url']) {
		echo '<script type="text/javascript" src="../payment/alipay/ap.js"></script><script type="text/javascript">_AP.pay("'.$ret['url'].'")</script>';
		exit();
	}
}
elseif($pay_type == 'yimafu') {
	$wechat = $_W['we7_wmall']['config']['payment']['yimafu'];
	$ret = yimafu_build($ps, $wechat);
	if(is_error($ret)) {
		imessage('一码付参数有错', ireferer(), 'error');
	}
	header("location:{$ret}");
	exit();
}
elseif($pay_type == 'wechat') {
	if(is_weixin()) {
		$config_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		if(in_array($config_wechat['type'], array('borrow', 'borrow_partner'))) {
			$_SESSION['pay_params'] = $params;
			$url = imurl("system/paycenter/cash/{$pay_type}", array(), true);
			$oauth = member_oauth_info($url, $config_wechat[$config_wechat['type']], $_W['openid']);
			if(is_error($oauth)) {
				imessage("获取粉丝身份出错,请重新发起支付。具体原因:{$oauth['message']}", ireferer(), 'error');
			}
			$_W['oauth_openid'] = $oauth['openid'];
		}
		unset($_SESSION['pay_params']);
		$tag = array(
			'acid' => $_W['acid'],
			'uid' => $_W['member']['uid'],
		);
		$openid = !empty($_W['oauth_openid']) ? $_W['oauth_openid'] : $_W['openid'];
		pdo_update('core_paylog', array('openid' => $openid, 'tag' => iserializer($tag)), array('plid' => $log['plid']));
		$ps['title'] = urlencode($params['title']);
		$ps['openid'] = $openid;
		$sl = base64_encode(json_encode($ps));
		$auth = sha1($sl . $_W['uniacid'] . $_W['config']['setting']['authkey']);
		$url = imurl('system/paycenter/wxpay', array('auth' => $auth, 'ps' => $sl));
		header("Location:{$url}");
		exit();
	} else {
		$wechat = $_W['we7_wmall']['config']['payment']['h5_wechat'];
		$wechat['trade_type'] = 'MWEB';
		$wechat['channel'] = 'H5';
		$wOpt = wechat_build($ps, $wechat);
		if (is_error($wOpt)) {
			imessage($wOpt['message'], ireferer(), 'error');
		} else {
			$wOpt['mweb_url'] = "{$wOpt['mweb_url']}&redirect_url=" . urlencode($params['url_detail']);
			header("location:{$wOpt['mweb_url']}");
			die;
		}
	}
}
elseif($pay_type == 'credit_old') {
	if($log['module'] == 'recharge') {
		imessage('不能使用余额支付', ireferer(), 'error');
	}
	if(empty($_GPC['notify'])) {
		if(!empty($log) && $log['status'] == '0') {
			if($_W['member']['credit2'] < $ps['fee']) {
				imessage("余额不足以支付, 需要 {$ps['fee']}, 当前 {$_W['member']['credit2']} {$_W['Lang']['dollarSignCn']}", ireferer(), 'error');
			}
			$fee = floatval($ps['fee']);
			$result = member_credit_update($_W['member']['uid'], 'credit2', -$fee, array($_W['member']['uid'], "消费余额:{$fee}{$_W['Lang']['dollarSignCn']}"));
			if(is_error($result)) {
				imessage($result['message'], '', 'error');
			}
			if(!empty($_W['openid'])) {
				mc_notice_credit2($_W['openid'], $_W['member']['uid'], $fee, 0, '线上消费');
			}
			pdo_update('core_paylog', array('status' => '1', 'type' => 'credit'), array('plid' => $log['plid']));
			$site = WeUtility::createModuleSite($log['module']);
			if(!is_error($site)) {
				$site->weid = $_W['weid'];
				$site->uniacid = $_W['uniacid'];
				$site->inMobile = true;
				$method = 'payResult';
				if(method_exists($site, $method)) {
					$ret = array();
					$ret['result'] = 'success';
					$ret['type'] = $log['type'];
					$ret['channel'] = is_h5app() ? 'h5app' : 'wap';
					$ret['from'] = 'return';
					$ret['tid'] = $log['tid'];
					$ret['uniontid'] = $log['uniontid'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					$ret['weid'] = $log['weid'];
					$ret['uniacid'] = $log['uniacid'];
					$ret['acid'] = $log['acid'];
					$ret['is_usecard'] = $log['is_usecard'];
					$ret['card_type'] = $log['card_type'];
					$ret['card_fee'] = $log['card_fee'];
					$ret['card_id'] = $log['card_id'];
					echo '<iframe style="display:none;" src="'.imurl('system/paycenter/cash/credit', array('notify' => 'yes', 'params' => $_GPC['params']), true).'"></iframe>';
					$site->$method($ret);
				}
			}
		}
	} else {
		$site = WeUtility::createModuleSite($log['module']);
		if(!is_error($site)) {
			$site->weid = $_W['weid'];
			$site->uniacid = $_W['uniacid'];
			$site->inMobile = true;
			$method = 'payResult';
			if (method_exists($site, $method)) {
				$ret = array();
				$ret['result'] = 'success';
				$ret['type'] = $log['type'];
				$ret['channel'] = is_h5app() ? 'h5app' : 'wap';
				$ret['from'] = 'notify';
				$ret['tid'] = $log['tid'];
				$ret['user'] = $log['openid'];
				$ret['fee'] = $log['fee'];
				$ret['weid'] = $log['weid'];
				$ret['uniacid'] = $log['uniacid'];
				$ret['acid'] = $log['acid'];
				$ret['is_usecard'] = $log['is_usecard'];
				$ret['card_type'] = $log['card_type'];
				$ret['card_fee'] = $log['card_fee'];
				$ret['card_id'] = $log['card_id'];
				$site->$method($ret);
			}
		}
	}
}
elseif($pay_type == 'credit') {
	if(!empty($log) && $log['status'] == '0') {
		if($_W['member']['credit2'] < $ps['fee']) {
			imessage("余额不足以支付, 需要 {$ps['fee']}, 当前 {$_W['member']['credit2']} {$_W['Lang']['dollarSignCn']}", ireferer(), 'error');
		}
		$fee = floatval($ps['fee']);
		$result = member_credit_update($_W['member']['uid'], 'credit2', -$fee, array($_W['member']['uid'], "消费余额:{$fee}{$_W['Lang']['dollarSignCn']}"));
		if(is_error($result)) {
			imessage($result['message'], '', 'error');
		}
		if(!empty($_W['openid'])) {
			mc_notice_credit2($_W['openid'], $_W['member']['uid'], $fee, 0, '线上消费');
		}
		pdo_update('core_paylog', array('status' => '1', 'type' => 'credit'), array('plid' => $log['plid']));
		$site = WeUtility::createModuleSite($log['module']);
		if(!is_error($site)) {
			$site->weid = $_W['weid'];
			$site->uniacid = $_W['uniacid'];
			$site->inMobile = true;
			$method = 'payResult';
			if(method_exists($site, $method)) {
				$ret = array();
				$ret['result'] = 'success';
				$ret['type'] = 'credit';
				$ret['channel'] = is_h5app() ? 'h5app' : 'wap';
				$ret['from'] = 'return';
				$ret['tid'] = $log['tid'];
				$ret['uniontid'] = $log['uniontid'];
				$ret['user'] = $log['openid'];
				$ret['fee'] = $log['fee'];
				$ret['weid'] = $log['weid'];
				$ret['uniacid'] = $log['uniacid'];
				$ret['acid'] = $log['acid'];
				$ret['is_usecard'] = $log['is_usecard'];
				$ret['card_type'] = $log['card_type'];
				$ret['card_fee'] = $log['card_fee'];
				$ret['card_id'] = $log['card_id'];
				$site->$method($ret);
			}
		}
	}
}
elseif($pay_type == 'delivery' || $pay_type == 'finishMeal') {
	if(!empty($log) && $log['status'] == '0') {
		$site = WeUtility::createModuleSite($log['module']);
		if(!is_error($site)) {
			$site->weid = $_W['weid'];
			$site->uniacid = $_W['uniacid'];
			$site->inMobile = true;
			$method = 'payResult';
			if (method_exists($site, $method)) {
				$ret = array();
				$ret['result'] = 'failed';
				$ret['type'] = $log['type'];
				$ret['channel'] = is_h5app() ? 'h5app' : 'wap';
				$ret['from'] = 'return';
				$ret['tid'] = $log['tid'];
				$ret['user'] = $log['openid'];
				$ret['fee'] = $log['fee'];
				$ret['weid'] = $log['weid'];
				$ret['uniacid'] = $log['uniacid'];
				$ret['is_usecard'] = $log['is_usecard'];
				$ret['card_type'] = $log['card_type'];
				$ret['card_fee'] = $log['card_fee'];
				$ret['card_id'] = $log['card_id'];
				exit($site->$method($ret));
			}
		}
	}
}