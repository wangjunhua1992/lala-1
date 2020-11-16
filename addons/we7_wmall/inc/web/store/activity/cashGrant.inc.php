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
mload()->model('activity');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '返现优惠';
	if($_W['ispost']) {
		$starttime = trim($_GPC['starttime']);
		if(empty($starttime)) {
			imessage(error(-1, '活动开始时间不能为空'), '', 'ajax');
		}
		$endtime = trim($_GPC['endtime']);
		if(empty($endtime)) {
			imessage(error(-1, '活动结束时间不能为空'), '', 'ajax');
		}
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);
		if($starttime >= $endtime) {
			imessage(error(-1, '活动开始时间不能大于结束时间'), '', 'ajax');
		}
		$data = array();
		$title = array();
		if(!empty($_GPC['condition'])) {
			foreach ($_GPC['condition'] as $key => $value) {
				$condition = floatval($value);
				$back = floatval($_GPC['back'][$key]);
				if($condition && $back) {
					$data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
						'plateform_charge' => 0,
						'store_charge' => $back,
					);

					if(!empty($_W['ismanager'])) {
						$data[$condition]['agent_charge'] = trim($_GPC['agent_charge'][$key]);
						$data[$condition]['plateform_charge'] = trim($_GPC['plateform_charge'][$key]);
						if($data[$condition]['agent_charge'] > $back) {
							$data[$condition]['agent_charge'] = $back;
							$data[$condition]['plateform_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} elseif($data[$condition]['plateform_charge'] > $back) {
							$data[$condition]['plateform_charge'] = $back;
							$data[$condition]['agent_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} elseif($data[$condition]['plateform_charge'] + $data[$condition]['agent_charge'] > $back) {
							$data[$condition]['plateform_charge'] = $back - $data[$condition]['agent_charge'];
							$data[$condition]['store_charge'] = 0;
						} else {
							$data[$condition]['store_charge'] = round($back - $data[$condition]['agent_charge'] - $data[$condition]['plateform_charge'], 2);
						}
						if($data[$condition]['store_charge'] < 0) {
							$data[$condition]['store_charge'] = 0;
						}
					} elseif(!empty($_W['isagenter'])) {
						$data[$condition]['agent_charge'] = trim($_GPC['agent_charge'][$key]);
						if($data[$condition]['agent_charge'] > $back) {
							$data[$condition]['agent_charge'] = $back;
							$data[$condition]['plateform_charge'] = 0;
							$data[$condition]['store_charge'] = 0;
						} else {
							$data[$condition]['store_charge'] = round($back - $data[$condition]['agent_charge'], 2);
						}
						if($data[$condition]['store_charge'] < 0) {
							$data[$condition]['store_charge'] = 0;
						}
					}
					$title_item = "满{$condition}{$_W['Lang']['dollarSignCn']}返{$back}{$_W['Lang']['dollarSignCn']}";
					if(check_plugin_perm('iglobal') && $_W['we7_wmall']['config']['iglobal']['lang'] == 'zhcn2uy') {
						$title_item = "满{$condition}{$_W['Lang']['dollarSignCn']}返يۋانعا تولسا  {$back}{$_W['Lang']['dollarSignCn']}يۋانى قايتارلادى";
					}
					$title[] = $title_item;
				}
			}
		}
		if(empty($data)) {
			imessage(error(-1, '返现活动不能为空'), '', 'ajax');
		}

		$title = implode(',', $title);
		$activity = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'type' => 'cashGrant',
			'status' => 1,
			'data' => iserializer($data),
		);
		$status = activity_set($sid, $activity);
		if(is_error($status)) {
			imessage($status, '', 'ajax');
		}
		imessage(error(0, '设置返现优惠优惠成功'), 'refresh', 'ajax');
	}
	$activity = activity_get($sid, 'cashGrant');
	if(!empty($activity)) {
		foreach($activity['data'] as &$row) {
			if(!is_array($row)) {
				continue;
			}
			$data[] = $row;
		}
		$activity['data'] = $data;
	}
	$count = count($activity['data']);
	for($i = 0; $i < 4 - $count; $i++) {
		$activity['data'][] = array(
			'condition' => '',
			'back' => '',
		);
	}
}

if($ta == 'del') {
	$status = activity_del($sid, 'cashGrant');
	if(is_error($status)) {
		imessage($status, ireferer(), 'ajax');
	}
	imessage(error(0, '撤销活动成功'), ireferer(), 'ajax');
}
include itemplate('store/activity/cashGrant');