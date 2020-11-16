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
	$conditions = 'where uniacid = :uniacid and sid = :sid and status = :status';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$params[':status'] = $status;
	$advertises = pdo_fetchall('select * from'.tablename('tiny_wmall_advertise_trade').$conditions, $params);
	$type_cn = array(
		'recommendHome' => '为您优选首页',
		'recommendOther' => '为您优选更多页',
		'stick' => '商家置顶',
		'slideMember' => '会员中心-幻灯片',
		'slideHomeTop' => '平台首页-幻灯片',
		'slidePaycenter' => '收银台-幻灯片',
		'slideOrderDetail' => '订单详情-幻灯片',
	);
	foreach($advertises as &$advertise) {
		$advertise['type_cn'] = $type_cn[$advertise['type']];
		if($advertise['status'] == 1) {
			$advertise['until'] =  round(($advertise['endtime'] - TIMESTAMP)/86400);
		} else {
			$advertise['until'] = -1;
		}
		$advertise['starttime_cn'] = date('Y-m-d H:i', $advertise['starttime']);
		$advertise['endtime_cn'] = date('Y-m-d H:i', $advertise['endtime']);
		$advertise['addtime_cn'] = date('Y-m-d H:i', $advertise['addtime']);
	}
	$result = array(
		'advertise' => $advertises
	);
	imessage(error(0, $result), '', 'ajax');
}