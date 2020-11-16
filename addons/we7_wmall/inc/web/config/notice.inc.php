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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'wxtemplate';

if($op == 'wxtemplate') {
	$_W['page']['title'] = '微信模板消息';
	if($_W['ispost']) {
		$public_tpl = trim($_GPC['wechat']['public_tpl']) ? trim($_GPC['wechat']['public_tpl']) : imessage(error(-1, '订单状态变更模板不能为空'), '', 'ajax');
		$wx_template = $_GPC['wechat'];
		set_system_config('notice.wechat', $wx_template);
		imessage(error(0, '微信模板消息设置成功'), ireferer(), 'ajax');
	}
	$wechat = $_config['notice']['wechat'];
	include itemplate('config/notice-wechat');
}

if($op == 'wxtemplate_init') {
	$_W['page']['title'] = '微信模板消息';
	$templates = array(
		'OPENTM202137457' => array(
			'id' => 'OPENTM202137457',
			'title' => '订单状态更新通知',
			'name' => 'public_tpl'
		),
		'OPENTM412217161' => array(
			'id' => 'OPENTM202137457',
			'title' => '业务处理结果通知',
			'name' => 'task_tpl'
		),
		'OPENTM401619203' => array(
			'id' => 'OPENTM401619203',
			'title' => '新用户入驻申请',
			'name' => 'settle_apply_tpl'
		),
		'OPENTM417984803' => array(
			'id' => 'OPENTM417984803',
			'title' => '成功入驻通知',
			'name' => 'settle_tpl'
		),
		'TM00979' => array(
			'id' => 'TM00979',
			'title' => '提现提交',
			'name' => 'getcash_apply_tpl'
		),
		'TM00980' => array(
			'id' => 'TM00980',
			'title' => '提现成功',
			'name' => 'getcash_success_tpl'
		),
		'TM00981' => array(
			'id' => 'TM00981',
			'title' => '提现失败',
			'name' => 'getcash_fail_tpl'
		),
		'TM00004' => array(
			'id' => 'TM00004',
			'title' => '退款通知',
			'name' => 'refund_tpl'
		),
		'OPENTM415437052' => array(
			'id' => 'OPENTM415437052',
			'title' => '账户资金变动提醒',
			'name' => 'account_change_tpl'
		),
		'OPENTM405976081' => array(
			'id' => 'OPENTM405976081',
			'title' => '系统预警通知',
			'name' => 'warning_tpl'
		),
		'OPENTM202967310' => array(
			'id' => 'OPENTM202967310',
			'title' => '新会员加入通知',
			'name' => 'join_tpl'
		),
		'OPENTM416070391' => array(
			'id' => 'OPENTM416070391',
			'title' => '评价审核通知',
			'name' => 'tiezi_tpl'
		),
		'OPENTM383288748' => array(
			'id' => 'OPENTM383288748',
			'title' => '排号通知',
			'name' => 'assign_tpl'
		),
	);
	mload()->classs('wxaccount');
	$acc = new WxAccount();
	$error = array();
	$wx_template = $_config['notice']['wechat'];
	foreach($templates as $template) {
		$result = $acc->api_add_template($template['id']);
		if(is_error($result)) {
			$error[] = "{$template['title']}模板添加失败:{$result['message']}";
		} else {
			$wx_template[$template['name']] = $result;
		}
	}
	set_system_config('notice.wechat', $wx_template);
	if(!empty($error)) {
		imessage(error(-1, implode("<br>", $error)), ireferer(), 'ajax');
	} else {
		imessage(error(0, '模板消息设置成功'), ireferer(), 'ajax');
	}
	include itemplate('config/notice-wechat');
}

if($op == 'sms') {
	$_W['page']['title'] = '短信消息';
	if($_W['ispost']) {
		$data = array(
			'clerk' => array(
				'status' => intval($_GPC['clerk']['status']),
				'tts_code' => trim($_GPC['clerk']['tts_code']),
				'called_show_num' => trim($_GPC['clerk']['called_show_num']),
			),
			'deliveryer' => array(
				'status' => intval($_GPC['deliveryer']['status']),
				'tts_code' => trim($_GPC['deliveryer']['tts_code']),
				'called_show_num' => trim($_GPC['deliveryer']['called_show_num']),
			),
			'errander_deliveryer' => array(
				'status' => intval($_GPC['errander_deliveryer']['status']),
				'version' => intval($_GPC['errander_deliveryer']['version']),
				'tts_code' => trim($_GPC['errander_deliveryer']['tts_code']),
				'called_show_num' => trim($_GPC['errander_deliveryer']['called_show_num']),
			),
			'work_status_change' => array(
				'status' => intval($_GPC['work_status_change']['status']),
				'version' => intval($_GPC['work_status_change']['version']),
				'tts_code' => trim($_GPC['work_status_change']['tts_code']),
				'called_show_num' => trim($_GPC['work_status_change']['called_show_num']),
			),
		);
		set_system_config('notice.sms', $data);
		imessage(error(0, '微信模板消息设置成功'), ireferer(), 'ajax');
	}
	$sms = $_config['notice']['sms'];
	include itemplate('config/notice-sms');
}
