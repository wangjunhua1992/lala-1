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
mload()->model('member');
$_W['page']['title'] = '顾客概况';

$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
$day_num = ($end - $start) / 86400;
//新增人数
if($_W['isajax'] && $_W['ispost']) {
	$days = array();
	$datasets = array(
		'flow1' => array(),
	);
	for($i = 0; $i < $day_num; $i++){
		$key = date('m-d', $start + 86400 * $i);
		$days[$key] = 0;
		$datasets['flow1'][$key] = 0;
	}
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_store_members') . 'WHERE uniacid = :uniacid AND sid = :sid AND first_order_time >= :starttime and first_order_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
	foreach($data as $da) {
		$key = date('m-d', $da['addtime']);
		if(in_array($key, array_keys($days))) {
			$datasets['flow1'][$key]++;
		}
	}
	$shuju['label'] = array_keys($days);
	$shuju['datasets'] = $datasets;
	exit(json_encode($shuju));
}
$stat = member_amount_stat($sid);
include itemplate('store/member/index');