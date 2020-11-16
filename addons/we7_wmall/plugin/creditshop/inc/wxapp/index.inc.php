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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index'){
	$result = array(
		'adv' => creditshop_adv_get(1),
		'goods' => creditshop_goodsall_get(),
		'category' => creditshop_category_get(1),
		'member' => $_W['member']
	);
	imessage(error(0, $result), '', 'ajax');
}

if($op == 'goods'){
	$goods = creditshop_goodsall_get();
	$result = array(
		'goods' => $goods
	);
	imessage(error(0, $result), '', 'ajax');
}

if($op == 'detail'){
	$id = $_GPC['id'];
	$good = creditshop_goods_get($id);
	//检查是否符合兑换条件
	$can = creditshop_can_exchange_goods($id);
	if($can['errno'] == -2) {
		$good['can'] = 0;
	} else {
		$good['can'] = 1;
	}
	$goods = creditshop_goodsall_get(array('page' => 1, 'psize' => 4, 'type' => $good['type']));
	$records = creditshop_record_get();
	$goods_keys = array();
	if(!empty($goods)) {
		foreach($goods as $key => $value) {
			if($value['id'] == $id) {
				unset($goods[$key]);
			}
		}
	}
	$goods = array_slice($goods, 0, 3, true);
	$member = $_W['member'];
	$member['credit1'] = intval($member['credit1']);
	$result = array(
		'good' => $good,
		'goods' => $goods,
		'member' => $member,
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}
