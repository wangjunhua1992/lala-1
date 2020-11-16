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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'order';
mload()->model('plugin');
pload()->model('wheel');

if($ta == 'order') {
	$condition = ' where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$type = trim($_GPC['type']);
	if (!empty($type)){
		$condition .= ' AND a.type = :type';
		$params[':type'] = $type;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (a.uid = :uid or b.nickname like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('select a.*,b.nickname,b.avatar,c.title as activity_title from ' . tablename('tiny_wmall_wheel_record') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid left join " . tablename('tiny_wmall_wheel') . " as c on a.activity_id = c.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$val) {
			$val['award'] = iunserializer($val['award']);
			$val['avatar'] = tomedia($val['avatar']);
			$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
			if($val['type'] == 'noaward') {
				$val['award_value'] = $val['award']['note'];
				$val['award_type'] = award_type($val['award']['data']['takepartback']['type']);
			} else {
				$val['type'] = awards_rank($val['type'],true);
				$val['award_type'] = award_type($val['award_type']);
				if($val['award_type']['name'] != 'redpacket') {
					$val['award_value'] = $val['award']['data']['value'];
				} else {
					foreach($val['award']['data']['value'] as $redpacket) {
						$val['award_value'][] = "红包：满{$redpacket['condition']}减{$redpacket['discount']}";
					}
				}
			}
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}elseif($ta == 'status') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_wheel_record',  array('status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}