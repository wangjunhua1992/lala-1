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

$sid = intval($_GPC['sid']);
store_business_hours_init($sid);
$store = store_fetch($sid);
if(empty($store)) {
	imessage(error(-1, '门店不存在或已经删除'), '', 'ajax');
}
define('ORDER_TYPE', 'takeout');

mload()->model('goods');
mload()->model('activity');

$price = store_order_condition($store);
$store['send_price'] = $price['send_price'];
$store['goods_style'] = 2;

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$result = array(
		'store' => $store,
		'cart' => cart_data_init($sid),
		'config_mall' => $_config_mall
	);
	$categorys = array_values(store_fetchall_goods_category($sid, 1, false, 'all', 'available'));
	$result['category'] = $categorys;

	$cid = trim($_GPC['cid']) ? trim($_GPC['cid']) : $categorys[0]['id'];
	$child_id = 0;
	if(!empty($categorys)) {
		foreach($categorys as $index => $cate) {
			if($cate['id'] == $cid) {
				$cindex = $index;
				if(!empty($cate['child']) && count($cate['child']) > 0) {
					$child_id = $cate['child'][0]['id'];
				}
				break;
			};
		}
	}

	$result['goods'] = goods_filter($sid, array('cid' => $cid, 'child_id' => $child_id));
	$result['cid'] = $cid;
	$result['child_id'] = $child_id;
	$result['cindex'] = $cindex;

	imessage(error(0, $result), '', 'ajax');
}