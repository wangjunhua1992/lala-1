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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '基本设置';
	if($_W['ispost']) {
		pdo_update('tiny_wmall_store', array('kabao_status' => intval($_GPC['kabao_status'])), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		$basic = array(
			'give_credit1' => intval($_GPC['give_credit1']),
			'credit1_exchage_coupon' => intval($_GPC['credit1_exchage_coupon']),
			'free_delivery_fee' => intval($_GPC['free_delivery_fee']),
			'vip_goods' => intval($_GPC['vip_goods']),
			'give_coupon' => intval($_GPC['give_coupon']),
			'grant_type' => intval($_GPC['grant_type']),
			'grant_num' => $_GPC['grant_type'] == 2 ? floatval($_GPC['grant_num_2']) : floatval($_GPC['grant_num_1'])
		);
		store_set_data($sid, 'kabao.basic', $basic);
		imessage(error(0, '门店会员卡基础设置成功'), ireferer(), 'ajax');
	}
	$basic = store_get_data($sid, 'kabao.basic');
}

include itemplate('store/kabao/basic');

