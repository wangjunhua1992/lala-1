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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$exchange_max = intval($_GPC['exchange_max']);
		if($exchange_max <= 0) {
			imessage(error(-1, '会员每月最多领取红包个数必须大于零'), '', 'ajax');
		}
		$store_redpacket_min = floatval($_GPC['store_redpacket_min']);
		if($store_redpacket_min < 0) {
			imessage(error(-1, '商家红包最低金额不能小于零'), '', 'ajax');
		}
		$svip = array(
			'status' => intval($_GPC['status']),
			'exchange_max' => $exchange_max,
			'store_redpacket_min' => $store_redpacket_min,
			'notice_before_overtime' => intval($_GPC['notice_before_overtime'])
		);
		set_plugin_config('svip.basic', $svip);
		set_config_text('超级会员权益说明', 'agreement_svip', htmlspecialchars_decode($_GPC['agreement_svip']));
		set_config_text('超级会员任务说明', 'agreement_mission_svip', htmlspecialchars_decode($_GPC['agreement_mission_svip']));
		imessage(error(0, '保存成功'), 'refresh', 'ajax');
	}
	$config = get_plugin_config('svip.basic');
	$agreement_svip = get_config_text('agreement_svip');
	$agreement_mission_svip = get_config_text('agreement_mission_svip');
}
include itemplate('config');