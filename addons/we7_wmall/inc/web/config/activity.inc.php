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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'newmember';

if($op == 'newmember') {
	$_W['page']['title'] = '新用户判断';
	if($_W['ispost']) {
		$newmember = array(
			'newmember_condition' => intval($_GPC['newmember_condition'])
		);
		set_system_config('activity.newmember', $newmember);
		imessage(error(0, '设置新用户条件成功'), ireferer(), 'ajax');
	}
	$newmember = $_config['activity']['newmember'];
	include itemplate('config/activity-newmember');
}

elseif($op == 'notice') {
	$_W['page']['title'] = '优惠到期通知';
	if($_W['ispost']) {
		if (!empty($_GPC['time_start'])) {
			$timedata = array();
			foreach ($_GPC['time_start'] as $key => $start) {
				$start = trim($start);
				$end = trim($_GPC['time_end'][$key]);
				if (empty($start) && empty($end)) {
					continue;
				}
				$start = date("H:i",strtotime($start));
				$end = date("H:i",strtotime($end));
				$timedata[] = array('start' => $start, 'end' => $end);
			}
		}
		$data = array(
			'status' => intval($_GPC['status']),
			'timelimit' => array('status' => intval($_GPC['timelimit_status']), 'timedata' => $timedata),
			'notice_period' => intval($_GPC['notice_period']),
		);
		if($data['status'] == 1) {
			if($data['notice_period'] <= 0 || $data['notice_period'] > 5) {
				imessage(error(-1, '请设置时间间隔为1-5天'), '', 'ajax');
			}
		}
		set_system_config('activity.notice', $data);
		imessage(error(0, '设置新用户条件成功'), ireferer(), 'ajax');
	}
	$notice = $_config['activity']['notice'];
	$time_limit = $notice['timelimit'];
	include itemplate('config/activity');
}

elseif ($op == 'activityother') {
	$_W['page']['title'] = '其他 / 代金券';
	$config_activity = $_config['activity'];
	if ($_W['ispost']) {
		$data = array(
			'return_redpacket_status' => intval($_GPC['return_redpacket_status']),
			'coupon' => $_GPC['coupon']
		);
		if(!empty($config_activity)) {
			$data = array_merge($config_activity, $data);
		}
		$status = set_system_config('activity', $data);
		imessage(error(0, '设置成功'), ireferer(), 'ajax');
	}
	include itemplate('config/activity-activityother');
}



