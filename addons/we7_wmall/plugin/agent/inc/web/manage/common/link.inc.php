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
$callback = $_GPC['callback'];
$discounts = store_discounts();
$data = array();
$data['takeout']['sys'] = array(
	'title' => '平台链接',
	'items' => array(
		array(
			'title' => '平台首页',
			'url' => imurl('wmall/home/index')
		),
		array(
			'title' => '搜索商家',
			'url' => imurl('wmall/home/hunt')
		),
		array(
			'title' => '附近商家',
			'url' => imurl('wmall/home/search')
		),
		array(
			'title' => '会员中心',
			'url' => imurl('wmall/member/mine')
		),
		array(
			'title' => '我的订单',
			'url' => imurl('wmall/order/index')
		),
		array(
			'title' => '我的代金券',
			'url' => imurl('wmall/member/coupon')
		),
		array(
			'title' => '我的收货地址',
			'url' => imurl('wmall/member/address')
		),
		array(
			'title' => '我的收藏',
			'url' => imurl('wmall/member/favorite')
		),
		array(
			'title' => '我的评价',
			'url' => imurl('wmall/member/comment')
		),
		array(
			'title' => '配送会员卡',
			'url' => imurl('deliveryCard/index')
		),
		array(
			'title' => '领券中心',
			'url' => imurl('wmall/channel/coupon')
		),
		array(
			'title' => '余额充值',
			'url' => imurl('wmall/member/recharge')
		),
	)
);

$data['takeout']['dis'] = array(
	'title' => '优惠活动',
	'items' => array()
);
foreach($discounts as $row) {
	$data['takeout']['dis']['items'][] = array(
		'title' => $row['title'],
		'url' => imurl('wmall/home/search', array('dis' => $row['key']))
	);
}
if(check_plugin_perm('errander')) {
	$data['errander'] = array(
		array(
			'title' => '平台链接',
			'items' => array(
				array(
					'title' => '跑腿首页',
					'url' => imurl('errander/index')
				),
				array(
					'title' => '跑腿订单',
					'url' => imurl('errander/order/list')
				),
			)
		),
	);
	$data['errander']['business'] = array(
		'title' => '业务链接',
		'items' => array()
	);
	$categorys = pdo_getall('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'status' => 1), array('id', 'title'));
	if(!empty($categorys)) {
		foreach($categorys as $category) {
			$data['errander']['business']['items'][] = array(
				'title' => $category['title'],
				'url' => imurl('errander/category', array('id' => $category['id']))
			);
		}
	}
}
if(check_plugin_perm('ordergrant')) {
	$data['ordergrant'] = array(
		array(
			'title' => '平台链接',
			'items' => array(
				array(
					'title' => '下单有礼入口',
					'url' => imurl('ordergrant/index')
				),
				array(
					'title' => '奖励记录',
					'url' => imurl('ordergrant/record')
				),
				array(
					'title' => '订单分享入口',
					'url' => imurl('ordergrant/share')
				),
			)
		),
	);
}

include itemplate('public/link');
