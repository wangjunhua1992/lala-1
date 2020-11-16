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
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';

if($op == 'list') {
	$_W['page']['title'] = '会员收货地址';
	$condition = ' where uniacid = :uniacid and type = 1';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = intval($_GPC['status']);
	if($status > 0) {
		if($status == 1) {
			$condition .= ' and location_x != :location_x and location_y != :location_y and location_x != :location_x1 and location_y != :location_y1 and location_x != :location_x2 and location_y != :location_y2';
		} elseif($status == 2) {
			$condition .= ' and (location_x = :location_x or location_y = :location_y or location_x = :location_x1 or location_y = :location_y1 or location_x = :location_x2 or location_y = :location_y2)';
		}
		$params[':location_x'] = '';
		$params[':location_y'] = '';
		$params[':location_x1'] = 'undefined';
		$params[':location_y1'] = 'undefined';
		$params[':location_x2'] = 0;
		$params[':location_y2'] = 0;
	}
	$uid = intval($_GPC['uid']);
	if($uid > 0) {
		$condition .= ' and uid = :uid';
		$params[':uid'] = $uid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (realname like '%{$keyword}%' or mobile like '%{$keyword}%' or uid = '{$keyword}')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_address') . $condition, $params);

	$no_location = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_address') . " where uniacid = :uniacid and type = 1 and (location_x = '' or location_y = '' or location_x = 'undefined' or location_y = 'undefined' or location_x = 0 or location_y = 0)", array(':uniacid' => $_W['uniacid'])));

	$addresses = pdo_fetchall('select * from ' . tablename('tiny_wmall_address') . $condition . ' order by id desc limit '.($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑会员收货地址';
	$id = intval($_GPC['id']);
	$address = pdo_get('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if ($_W['ispost']) {
		$data = array(
			'realname' => trim($_GPC['realname']),
			'sex' => trim($_GPC['sex']),
			'mobile' => trim($_GPC['mobile']),
			'address' => trim($_GPC['address']),
			'number' => trim($_GPC['number']),
			'location_x' => trim($_GPC['map']['lat']),
			'location_y' => trim($_GPC['map']['lng'])
		);
		if ($id) {
			pdo_update('tiny_wmall_address', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			imessage(error(0, '编辑收货地址成功'), iurl('member/address'), 'ajax');
		}
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除收货地址成功'), '', 'ajax');
}

if($op == 'del_no_location') {
	$res = pdo_query("delete from " . tablename('tiny_wmall_address') . " where uniacid = :uniacid and type = 1 and (location_x = :location_x or location_y = :location_y or location_x = 'undefined' or location_y = 'undefined' or location_x = 0 or location_y = 0)", array(':uniacid' => $_W['uniacid'], ':location_x' => '', ':location_y' => ''));
	imessage(error(0, '删除无经纬度的地址成功'), ireferer(), 'ajax');
}
include itemplate('member/address');

