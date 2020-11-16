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
	$_W['page']['title'] = '门店会员减免配送费';

	if($_W['ispost']) {
		if(!empty($_GPC['condition'])) {
			foreach($_GPC['condition'] as $key => $value) {
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
				}
			}
		}
		if(empty($data)) {
			imessage(error(-1, '满减配送费活动不能为空'), '', 'ajax');
		}
		store_set_data($sid, 'kabao.deliveryFee', $data);
		imessage(error(0, '设置门店会员满减配送费活动成功'), 'refresh', 'ajax');
	}


	$activity = store_get_data($sid, 'kabao.deliveryFee');
	$count = count($activity);
	for($i = 0; $i < 4 - $count; $i++) {
		$activity[] = array(
			'condition' => '',
			'back' => '',
		);
	}
}

elseif($ta == 'del') {
	store_set_data($sid, 'kabao.deliveryFee', array());
	imessage(error(0, '撤销门店会员满减配送费活动成功'), ireferer(), 'ajax');
}

include itemplate('store/kabao/deliveryFee');

