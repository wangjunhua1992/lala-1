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

function yinsihao_get_order($orderOrId, $ordersn, $orderType = 'waimai') {
	global $_W;
	if(is_array($orderOrId)) {
		$order = $orderOrId;
	} else {
		if($orderType == 'waimai') {
			$order = order_fetch($orderOrId);
		} elseif($orderType == 'errander') {
			mload()->model('plugin');
			pload()->model('errander');
			$order = errander_order_fetch($orderOrId);
		}
	}
	if($orderType == 'errander') {
		$order['ordersn'] = $order['order_sn'];
	}
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	if($order['ordersn'] != $ordersn) {
		return error(-1, '订单信息有误');
	}
	if($order['data']['yinsihao_status'] != 1) {
		return error(-1, '该订单未开启号码保护功能');
	}
	return $order;
}
/**
 * 获取隐私号及分机号
*/
function yinsihao_bind($orderOrId, $type, $ordersn, $orderType = 'waimai', $memberType = 'accept') {
	global $_W, $_GPC;
	//检测订单是否开启了隐私号功能
	$order = yinsihao_get_order($orderOrId, $ordersn, $orderType);
	if(is_error($order)) {
		return $order;
	}
	//检测平台是否开启了隐私号功能
	$basic = get_plugin_config('yinsihao.basic');
	if(empty($basic) || $basic['status'] != 1) {
		return error(-1, '平台未开启号码保护功能');
	}
	$sms_type = !empty($basic['type']) ? $basic['type'] : 'aliyun';

	//针对已完成订单或者已取消订单处理
	$status = yinsihao_order_check($orderOrId, $ordersn, $orderType);
	if(empty($status)) {
		return error(-2, '订单已无法使用隐私号联系');
	}
	$types = array('store', 'deliveryer', 'member', 'errander');
	if(!in_array($type, $types)) {
		return error(-1, '隐私号绑定类型错误');
	}
	$mobile = '';
	if($type == 'member') {
		$mobile = $order['mobile'];
	} elseif($type == 'store') {
		$mobile = pdo_fetchcolumn('select telephone from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $order['sid']));
	} elseif($type == 'deliveryer') {
		$mobile = pdo_fetchcolumn('select mobile from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $order['deliveryer_id']));
	} elseif($type == 'errander') {
		if($memberType == 'buy') {
			$mobile = $order['buy_mobile'];
		} else {
			$mobile = $order['accept_mobile'];
		}
	}
	if(empty($mobile)) {
		return error(-1, '待绑定的手机号码不存在');
	}
	//检测当前号码是否已绑定隐私号
	$checkMobile = yinsihao_checkMobileIsBind($mobile, $type, $sms_type);
	if(!empty($checkMobile)) {
		return $checkMobile;
	}
	//绑定号码
	$numbers = $basic[$type . '_number'];
	//获取有效的隐私号段
	$secret_mobile = yinsihao_get_avaiable_secret_mobile($numbers);
	if(is_error($secret_mobile)) {
		return $secret_mobile;
	}

	mload()->model('sms');
	if($sms_type == 'aliyun') {
		//获取号码池Key
		$poolKey = '';
		if(!empty($basic['poolKey'])) {
			foreach($basic['poolKey'] as $key => $value) {
				if(in_array($secret_mobile, $value)) {
					$poolKey = $key;
					break;
				}
			}
		}
		if(empty($poolKey)) {
			return error(-1, '没有有效的号码池Key');
		}
		$expiration = TIMESTAMP + 6*30*24*3600;
		if($type == 'member') {
			$expiration = TIMESTAMP + $basic['member_expiration'] * 60;
		} elseif($type == 'errander') {
			$expiration = TIMESTAMP + $basic['errander_expiration'] * 60;
		}
		$params = array(
			'Expiration' => date('Y-m-d H:i:s', $expiration),
			'PhoneNoA' => $mobile,
			'PoolKey' => $poolKey,
			'PhoneNoX' => $secret_mobile,
			'AccessKeyId' => $basic['accessKeyId'],
			'AccessSecret' => $basic['accessSecret'],
		);
	} elseif($sms_type == 'huawei') {
		//华为云 配送员与商户的绑定时间默认为720小时
		$bindExpiredTime = 720;
		if($type == 'member') {
			$bindExpiredTime = ceil($basic['member_expiration'] / 60);
		} elseif($type == 'errander') {
			$bindExpiredTime = ceil($basic['errander_expiration'] / 60);
		}
		$expiration = TIMESTAMP + $bindExpiredTime * 3600;
		$params = array(
			'appKey' => $basic['accessKeyId'],
			'appSecret' => $basic['accessSecret'],
			'virtualNum' => $secret_mobile,
			'bindNum' => $mobile,
			'bindExpiredTime' => $bindExpiredTime
		);
	}

	$data = sms_bindAxnExtension($params, $sms_type);
	if(is_error($data)) {
		return $data;
	}
	//绑定关系存入数据库
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'type' => $type,
		'sms_type' => $sms_type,
		'real_mobile' => $mobile,
		'secret_mobile' => $data['SecretNo'],
		'extension' => $data['Extension'],
		'subsid' => $data['SubsId'],
		'addtime' => TIMESTAMP,
		'expiration' => $expiration
	);
	$id = pdo_insert('tiny_wmall_yinsihao_bind_list', $insert);
	if(empty($id)) {
		return error(-1, '隐私号绑定关系保存时发生错误');
	}
	return $insert;
}

function yinsihao_unbind($bindOrId) {
	global $_W;
	if(is_array($bindOrId)) {
		$bind = $bindOrId;
	} else {
		$bind = pdo_fetch('select * from ' . tablename('tiny_wmall_yinsihao_bind_list') . " where uniacid = :uniacid and id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $bindOrId));
	}
	if(empty($bind)) {
		return error(-1, '绑定关系不存在');
	}
	$basic = get_plugin_config('yinsihao.basic');
	if($bind['sms_type'] == 'huawei') {
		$params = array(
			'appKey' => $basic['accessKeyId'],
			'appSecret' =>  $basic['accessSecret'],
			'subscriptionId' => $bind['subsid'],
		);
	} else {
		$secret_mobile = $bind['secret_mobile'];
		$poolKey = '';
		if(!empty($basic['poolKey'])) {
			foreach($basic['poolKey'] as $key => $value) {
				if(in_array($secret_mobile, $value)) {
					$poolKey = $key;
					break;
				}
			}
		}
		$params = array(
			'PoolKey' => $poolKey,
			'SecretNo' => $secret_mobile,
			'SubsId' => $bind['subsid'],
			'AccessKeyId' => $basic['accessKeyId'],
			'AccessSecret' => $basic['accessSecret'],
		);
	}
	mload()->model('sms');
	$status = sms_unbindSubscription($params, $bind['sms_type']);
	if(is_error($status)) {
		return $status;
	}
	pdo_delete('tiny_wmall_yinsihao_bind_list', array('uniacid' => $_W['uniacid'], 'id' => $bind['id']));
	return true;
}

/**
 * 检测订单是否仍能显示隐私号
*/
function yinsihao_order_check($orderOrId, $ordersn, $orderType) {
	global $_W;
	$order = yinsihao_get_order($orderOrId, $ordersn, $orderType);
	if($order['data']['yinsihao_status'] != 1) {
		return false;
	}
	if($orderType == 'waimai') {
		if($order['status'] < 5) {
			return true;
		}
	} elseif($orderType == 'errander') {
		if($order['status'] < 3) {
			return true;
		}
		$order['endtime'] = $order['delivery_success_time'];
	}
	$usefultime = 0 * 24 * 3600;
	$overtime = $order['endtime'] + $usefultime;
	if($overtime <= TIMESTAMP) {
		return false;
	}
	return true;
}

/**
 * 检测号码是否已绑定了隐私号，已绑定则返回隐私号和分机号
 * 返回false 是可以绑定
*/
function yinsihao_checkMobileIsBind($mobile, $type, $sms_type) {
	global $_W;
	$data = pdo_fetch('select * from ' . tablename('tiny_wmall_yinsihao_bind_list') . ' where uniacid = :uniacid and type = :type and sms_type = :sms_type and real_mobile = :real_mobile and expiration > :expiration', array(':uniacid' => $_W['uniacid'], ':type' => $type, ':sms_type' => $sms_type, ':real_mobile' => $mobile, ':expiration' => TIMESTAMP));
	if(empty($data)) {
		return false;
	} else {
		return $data;
	}
}


/**
 * 获取绑定数量最少的隐私号段
*/
function yinsihao_get_avaiable_secret_mobile($numberArr) {
	global $_W;
	if(empty($numberArr)) {
		return false;
	}
	$numberStr = "'" . implode("','", $numberArr) . "'";
	$data = pdo_fetchall('select  secret_mobile, count(*) as num from ' . tablename('tiny_wmall_yinsihao_bind_list') . " where uniacid = :uniacid and expiration > :expiration and secret_mobile in ({$numberStr}) group by secret_mobile order by num asc ", array(':uniacid' => $_W['uniacid'], ':expiration' => TIMESTAMP), 'secret_mobile');
	if(empty($data)) {
		//隐私号段均未绑定号码，返回第一个
		return reset($numberArr);
	}
	if(count($numberArr) != count($data)) {
		//存在未使用的隐私号段，返回未使用的隐私号段中的一个
		$used = array_keys($data);
		foreach($numberArr as $value) {
			if(!in_array($value, $used)) {
				return $value;
			}
		}
	} else {
		//隐私号段均已使用过，返回绑定次数最少的隐私号段
		$first = reset($data);
		if($first['num'] >= 200) {
			return error(-1, '没有有效的隐私号段');
		} else {
			return $first['secret_mobile'];
		}
	}

}
