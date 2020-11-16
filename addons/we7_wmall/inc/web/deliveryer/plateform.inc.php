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
mload()->model('deliveryer');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '平台配送员';
	$condition = ' WHERE uniacid = :uniacid and status = 1';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$work_status = isset($_GPC['work_status']) ? intval($_GPC['work_status']) : -1;
	if($work_status > -1) {
		$condition .= ' and work_status = :work_status';
		$params[':work_status'] = $work_status;
	}
	$is_takeout = isset($_GPC['is_takeout']) ? intval($_GPC['is_takeout']) : -1;
	if($is_takeout > -1) {
		$condition .= ' and is_takeout = :is_takeout';
		$params[':is_takeout'] = $is_takeout;
	}
	$is_errander = isset($_GPC['is_errander']) ? intval($_GPC['is_errander']) : -1;
	if($is_errander > -1) {
		$condition .= ' and is_errander = :is_errander';
		$params[':is_errander'] = $is_errander;
	}
	$takeout_num = intval($_GPC['takeout_num']);
	if($takeout_num == 1) {
		$condition .= ' and collect_max_takeout > 0 and order_takeout_num >= collect_max_takeout';
	} elseif($takeout_num == 2) {
		$condition .= ' and (collect_max_takeout = 0 or (collect_max_takeout > 0 and order_takeout_num < collect_max_takeout))';
	}
	$errander_num = intval($_GPC['errander_num']);
	if($errander_num == 1) {
		$condition .= ' and collect_max_errander > 0 and order_errander_num >= collect_max_errander';
	} elseif($errander_num == 2) {
		$condition .= ' and (collect_max_errander = 0 or (collect_max_errander > 0 and order_errander_num < collect_max_errander))';
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (title like '%{$keyword}%' or nickname like '%{$keyword}%' or mobile like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer') . $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['auth_info'] = iunserializer($row['auth_info']);
			$row['extra'] = iunserializer($row['extra']);
			$row['perm_transfer'] = iunserializer($row['perm_transfer']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('deliveryer/plateform');
}

elseif($op == 'turncate') {
	if(!$_W['isajax']) {
		return false;
	}
	if(empty($_GPC['ids'])) {
		imessage(error(-1, '请选择要操作的账户'), '', 'ajax');
	}
	$remark = trim($_GPC['remark']);
	foreach($_GPC['ids'] as $id) {
		$id = intval($id);
		if(!$id) continue;
		$account = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($account) || empty($account['credit2']) || $account['credit2'] == 0) {
			continue;
		}
		deliveryer_update_credit2($id, -$account['credit2'], 3, '', $remark);
	}
	imessage(error(0, ''), '', 'ajax');
	include itemplate('deliveryer/plateform');

}

elseif($op == 'changes') {
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id), array('avatar', 'nickname', 'mobile', 'credit2', 'status'));
	if(empty($deliveryer)) {
		imessage(error(-1, '配送员不存在'), '', 'ajax');
	}
	if($deliveryer['status'] != 1) {
		imessage(error(-1, '配送员已被删除'), '', 'ajax');
	}
	if($_W['ispost']) {
		$change_type = intval($_GPC['change_type']);
		$credit2 = floatval($_GPC['credit2']);
		$remark= trim($_GPC['remark']);
		if($change_type == 1) {
			$fee = '+' . $credit2;
		} elseif($change_type == 2) {
			$fee = '-' . $credit2;
		} else {
			$fee =  $credit2 - $deliveryer['credit2'] ;
		}
		deliveryer_update_credit2($id, $fee, 3, '', $remark);
		imessage(error(0, '更改账户余额成功'), ireferer(),'ajax');
	}
	include itemplate('deliveryer/plateformOp');
	die();
}

elseif($op == 'stat') {
	$_W['page']['title'] = '配送统计';
	$id = intval($_GPC['id']);
	$deliveryer = deliveryer_fetch($id);
	if(empty($deliveryer)) {
		imessage('配送员不存在', ireferer(), 'error');
	}
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;

	if($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array(),
		);
		for($i = 0; $i < $day_num; $i++){
			$key = date('m-d', $start + 86400 * $i);
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
		}
		if($_GPC['errander'] == '1') {
			$data = pdo_fetchall("SELECT addtime FROM " . tablename('tiny_wmall_errander_order') . 'WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id and status = 3 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id, ':starttime' => $start, ':endtime' => $end));
			foreach($data as $da) {
				$key = date('m-d', $da['addtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow1'][$key]++;
				}
			}
			$shuju['label'] = array_keys($days);
			$shuju['datasets'] = $datasets;
		} else {
			$data = pdo_fetchall("SELECT addtime FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id AND delivery_type = 2 and status = 5 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id, ':starttime' => $start, ':endtime' => $end));
			foreach($data as $da) {
				$key = date('m-d', $da['addtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow1'][$key]++;
				}
			}
			$shuju['label'] = array_keys($days);
			$shuju['datasets'] = $datasets;
		}
		exit(json_encode($shuju));
	}
	$stat = deliveryer_plateform_order_stat($id);
	include itemplate('deliveryer/plateform');

}

elseif($op == 'group'){
	if($_W['ispost'] && $_GPC['set'] == 1) {
		$deliveryerId = explode(',', $_GPC['ids']);
		if(empty($deliveryerId)){
			imessage(error(-1, '请选择配送员'), '', 'ajax');
		}
		mload()->model('plugin');
		if(check_plugin_perm('errander')) {
			mload()->model('plugin');
			pload()->model('errander');
		}
		foreach($deliveryerId as $id){
			pdo_update('tiny_wmall_deliveryer', array('is_takeout' => intval($_GPC['is_takeout']), 'is_errander' => intval($_GPC['is_errander'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
			if(check_plugin_perm('errander')) {
				errander_category_deliveryer_reset($id);
			}
		}
		imessage(error(0, '批量修改成功'), iurl('deliveryer/plateform/list'), 'ajax');
	}
	$ids = implode(',', $_GPC['id']);
	include itemplate('deliveryer/plateformOp');
	die();
}

elseif($op == 'post') {
	$_W['page']['title'] = '配送员信息';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']);
		if(!is_validMobile($mobile)) {
			imessage(error(-1, '手机号格式错误'), '', 'ajax');
		}
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号已绑定其他配送员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['wechat']['openid']);
		if(!empty($openid)) {
			$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
			if(!empty($is_exist)) {
				imessage(error(-1, '该微信信息已绑定其他配送员, 请更换微信信息'), '', 'ajax');
			}
		}
		$openid_wxapp = trim($_GPC['wechat']['openid_wxapp']);
		if(!empty($openid_wxapp)) {
			$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid_wxapp = :openid_wxapp and id != :id', array(':uniacid' => $_W['uniacid'], ':openid_wxapp' => $openid_wxapp, ':id' => $id));
			if(!empty($is_exist)) {
				imessage(error(-1, '该微信信息已绑定其他配送员, 请更换微信信息'), '', 'ajax');
			}
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'mobile' => $mobile,
			'title' => trim($_GPC['title']),
			'openid' => $openid,
			'openid_wxapp' => trim($_GPC['wechat']['openid_wxapp']),
			'nickname' => trim($_GPC['wechat']['nickname']),
			'avatar' => trim($_GPC['wechat']['avatar']),
			'sex' => trim($_GPC['sex']),
			'age' => intval($_GPC['age']),
			'is_errander' => intval($_GPC['is_errander']),
			'is_takeout' => intval($_GPC['is_takeout']),
			'collect_max_takeout' => intval($_GPC['collect_max_takeout']),
			'collect_max_errander' => intval($_GPC['collect_max_errander']),
			'perm_cancel' => iserializer($_GPC['perm_cancel']),
			'perm_transfer' => iserializer($_GPC['perm_transfer']),
			'fee_getcash' => iserializer($_GPC['fee_getcash']),
			'fee_include_tips' => intval($_GPC['fee_include_tips'])
		);

		$deliveryer_takeout_fee_type = intval($_GPC['deliveryer_takeout_fee_type']);
		$deliveryer_takeout_fee = 0;
		if($deliveryer_takeout_fee_type == 1) {
			$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_1']);
		} elseif($deliveryer_takeout_fee_type == 2) {
			$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_2']);
		} elseif($deliveryer_takeout_fee_type == 3) {
			$deliveryer_takeout_fee = array(
				'start_fee' => floatval($_GPC['deliveryer_takeout_fee_3']['start_fee']),
				'start_km' => floatval($_GPC['deliveryer_takeout_fee_3']['start_km']),
				'pre_km' => floatval($_GPC['deliveryer_takeout_fee_3']['pre_km']),
				'max_fee' => floatval($_GPC['deliveryer_takeout_fee_3']['max_fee'])
			);
		} elseif($deliveryer_takeout_fee_type == 4) {
			$deliveryer_takeout_fee = floatval($_GPC['deliveryer_takeout_fee_4']);
		}
		$deliveryer_errander_fee_type = intval($_GPC['deliveryer_errander_fee_type']);
		$deliveryer_errander_fee = 0;
		if($deliveryer_errander_fee_type == 1) {
			$deliveryer_errander_fee = floatval($_GPC['deliveryer_errander_fee_1']);
		} elseif($deliveryer_errander_fee_type == 2) {
			$deliveryer_errander_fee = floatval($_GPC['deliveryer_errander_fee_2']);
		} elseif($deliveryer_errander_fee_type == 3) {
			$deliveryer_errander_fee = array(
				'start_fee' => floatval($_GPC['deliveryer_errander_fee_3']['start_fee']),
				'start_km' => floatval($_GPC['deliveryer_errander_fee_3']['start_km']),
				'pre_km' => floatval($_GPC['deliveryer_errander_fee_3']['pre_km']),
				'max_fee' => floatval($_GPC['deliveryer_errander_fee_3']['max_fee'])
			);
		}
		$delivery_fee =  array(
			'takeout' => array(
				'deliveryer_fee_type' => $deliveryer_takeout_fee_type,
				'deliveryer_fee' => $deliveryer_takeout_fee
			),
			'errander' => array(
				'deliveryer_fee_type' => $deliveryer_errander_fee_type,
				'deliveryer_fee' => $deliveryer_errander_fee
			)
		);
		$data['fee_delivery'] = iserializer($delivery_fee);

		if($_W['is_agent']) {
			$data['agentid'] = intval($_GPC['agent_id']);
		}
		if(!$id) {
			$data['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '登录密码不能为空'), '', 'ajax');
			$length = strlen($data['password']);
			if($length < 8 || $length > 20) {
				imessage(error(-1, '请输入8-20密码'), ireferer(), 'ajax');
			}
			if(!preg_match(IREGULAR_PASSWORD, $data['password'])) {
				imessage(error(-1, '密码必须由数字和字母组合'), ireferer(), 'ajax');
			}
			if($data['password'] != trim($_GPC['repassword'])) {
				imessage(error(-1, '两次密码输入不一致'), ireferer(), 'ajax');
			}
			$data['extra'] = iserializer(array(
					'accept_wechat_notice' => 1,
					'accept_voice_notice' => 1
				));
			$data['salt'] = random(6);
			$data['token'] = random(32);
			$data['password'] = md5(md5($data['salt'] . $data['password']) . $data['salt']);
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_deliveryer', $data);
			$id = pdo_insertid();
			deliveryer_all(true);
			mlog(4000, $id, '平台添加配送员');
			imessage(error(0, '添加配送员成功'), iurl('deliveryer/plateform/post', array('id' => $id)), 'ajax');
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$length = strlen($password);
				if($length < 8 || $length > 20) {
					imessage(error(-1, '请输入8-20密码'), ireferer(), 'ajax');
				}
				if(!preg_match(IREGULAR_PASSWORD, $password)) {
					imessage(error(-1, '密码必须由数字和字母组合'), ireferer(), 'ajax');
				}
				if($password != trim($_GPC['repassword'])) {
					imessage(error(-1, '两次密码输入不一致'), ireferer(), 'ajax');
				}
				$data['salt'] = random(6);
				$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
			}
			pdo_update('tiny_wmall_deliveryer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			if($data['is_errander'] == 0 && check_plugin_perm('errander')) {
				mload()->model('plugin');
				pload()->model('errander');
				errander_category_deliveryer_reset($id);
			}
			deliveryer_all(true);
			mlog(4001, $id);
			imessage(error(0, '编辑配送员成功'), iurl('deliveryer/plateform/post', array('id' => $id)), 'ajax');
		}
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(!empty($deliveryer)) {
		$deliveryer['perm_cancel'] = iunserializer($deliveryer['perm_cancel']);
		$deliveryer['perm_transfer'] = iunserializer($deliveryer['perm_transfer']);
		$deliveryer['fee_getcash'] = iunserializer($deliveryer['fee_getcash']);
		$deliveryer['fee_delivery'] = iunserializer($deliveryer['fee_delivery']);
	}
	mload()->model('agent');
	$agents = get_agents();
	include itemplate('deliveryer/plateform');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_deliveryer', array('status' => 2, 'deltime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	mlog(4007, $id, '配送员加入回收站');
	deliveryer_all(true);
	imessage(error(0, '删除配送员成功'), '', 'ajax');
	include itemplate('deliveryer/plateform');
}

elseif($op == 'perm') {
	$deliveryerId = intval($_GPC['id']);
	$fields = trim($_GPC['fields']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_deliveryer', array($fields => $value), array('uniacid' => $_W['uniacid'], 'id' => $deliveryerId));
	if($fields == 'is_errander' && check_plugin_perm('errander') && $value == 0){
		mload()->model('plugin');
		pload()->model('errander');
		errander_category_deliveryer_reset($deliveryerId);
	}
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'agent'){
	if($_W['ispost'] && $_GPC['set'] == 1) {
		$deliveryerId = explode(',', $_GPC['ids']);
		if(empty($deliveryerId)){
			imessage(error(-1, '请选择配送员'), '', 'ajax');
		}
		$agentid = intval($_GPC['agentid']);
		mload()->model('agent');
		foreach($deliveryerId as $id){
			update_deliveryer_agent($id, $agentid);
		}
		cache_clean("we7_wmall:deliveryers:{$_W['uniacid']}");
		imessage(error(0, '批量修改成功'), iurl('deliveryer/plateform/list'), 'ajax');
	}
	$agents = get_agents();
	$ids = implode(',', $_GPC['id']);
	include itemplate('deliveryer/plateformOp');
	die();
}

elseif($op == 'work_status'){
	$deliveryerId = intval($_GPC['id']);
	$status = intval($_GPC['value']);
	$result = deliveryer_work_status_set($deliveryerId, $status, true, true);
	if(is_error($result)) {
		imessage($result, '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'extra'){
	$deliveryerId = intval($_GPC['id']);
	$type = trim($_GPC['type']);
	$value = intval($_GPC['value']);
	$result = deliveryer_set_extra($type, $value, $deliveryerId);
	if(is_error($result)) {
		imessage($result, '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'kefu') {
	$deliveryerId = intval($_GPC['id']);
	$fields = trim($_GPC['fields']);
	if($fields == 'status') {
		$value = intval($_GPC['value']) == 1 ? 1 : 2;
	} else {
		$value = intval($_GPC['value']) == 1 ? 1 : 3;
	}
	pdo_update('tiny_wmall_deliveryer', array('kefu_status' => $value), array('uniacid' => $_W['uniacid'], 'id' => $deliveryerId));
	if($value == 3) {
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryerId));
		$_W['kefu']['user'] = array(
			'token' => $deliveryer['token'],
			'kefu_status' => 3
		);
		pload()->model('kefu');
		kefu_offline_reply();
	}
	imessage(error(0, '修改客服状态成功'), ireferer(), 'ajax');
}
