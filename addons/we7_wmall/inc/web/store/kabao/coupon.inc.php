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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '门店会员优惠券列表';
	pload()->model('kabao');

	$type = trim($_GPC['type']);
	$filter = array(
		'sid' => $sid
	);
	$data = kabao_fetchall_coupon();
	$coupons = $data['data'];
	$pager = $data['pager'];

	$types = kabao_coupon_types();
}

elseif($ta == 'post') {
	$_W['page']['title'] = '编辑门店会员优惠券';
	$id = intval($_GPC['id']);
	$coupon = pdo_get('tiny_wmall_kabao_coupon', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($_W['ispost']) {
		$insert = array(
			'sid' => $sid,
			'type' => trim($_GPC['type']),
			'title' => trim($_GPC['title']),
			'discount' => intval($_GPC['discount']),
			'credit1' => intval($_GPC['credit1']),
			'condition' => intval($_GPC['condition']),
			'use_days_limit' => intval($_GPC['use_days_limit']),
		);
		if(empty($insert['title'])) {
			$insert['title'] = '店铺通用满减券';
		}
		if($insert['discount'] <= 0) {
			imessage(error(-1, '优惠券金额必须是正整数'), '', 'ajax');
		}
		if($insert['condition'] <= 0) {
			imessage(error(-1, '满多少元可用必须是正整数'), '', 'ajax');
		}
		if($insert['discount'] >= $insert['condition']) {
			imessage(error(-1, '优惠券金额不能大于等于使用条件'), '', 'ajax');
		}
		if($insert['use_days_limit'] <= 0) {
			imessage(error(-1, '领取后几天内有效必须是正整数'), '', 'ajax');
		}
		if($insert['type'] == 'exchange' && $insert['credit1'] <= 0) {
			imessage(error(-1, '兑换所需积分必须是正整数'), '', 'ajax');
		}
		if($insert['type'] == 'bind') {
			$inserr['credit1'] = 0;
			if(empty($coupon) || $coupon['type'] == 'exchange') {
				$is_exist = pdo_get('tiny_wmall_kabao_coupon', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'bind'));
				if(!empty($is_exist)) {
					imessage(error(-1, '绑卡赠券类型的优惠券已存在，无法再次创建'), '', 'ajax');
				}
			}
		}
		if($id > 0) {
			pdo_update('tiny_wmall_kabao_coupon', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			$insert['uniacid'] = $_W['uniacid'];
			$insert['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_kabao_coupon', $insert);
		}
		imessage(error(0, '门店会员优惠券设置成功'), iurl('store/kabao/coupon/list'), 'ajax');
	}
}

elseif($ta == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_kabao_coupon', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '优惠券删除成功'), iurl('store/kabao/coupon/list'), 'ajax');
}

include itemplate('store/kabao/coupon');

