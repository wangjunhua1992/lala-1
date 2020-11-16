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

if($_W['ispost']) {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	if(empty($lat) || empty($lng)) {
		imessage(error(-1, '获取位置失败'), imurl('wmall/home/near'), 'ajax');
	}
	$stores = pdo_fetchall('select id,location_x,location_y,serve_radius from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and status = 1", array(':uniacid' => $_W['uniacid']));
	if(empty($stores)) {
		imessage(error(-1, '还没有门店哦'), ireferer(), 'ajax');
	}
	$distance = array();
	if(!empty($lat) && !empty($lng)) {
		foreach($stores as $key => &$row) {
			$row['distance'] = distanceBetween($row['location_y'], $row['location_x'], $lng, $lat);
			$row['distance'] = round($row['distance'] / 1000, 2);
			if($row['serve_radius'] > 0 && $row['distance'] > $row['serve_radius']) {
				unset($stores[$key]);
			} else {
				$distance[$row['id']] = $row['distance'];
			}
		}
	}
	$sid = 0;
	$min_distance = min($distance);
	$sid = array_search($min_distance, $distance);
	if($sid > 0) {
		$url = imurl('wmall/store/goods', array('sid' => $sid));
	} else {
		$url = imurl('wmall/home/index');
	}
	imessage(error(0, ''), $url, 'ajax');
}

include itemplate('home/near');
