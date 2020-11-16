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

function is_in_two_point($point1, $point2, $point3, $quadrant = false) {
	//判断点3在点1点2构成的经纬度坐标系矩形或象限内 $quadrant=true不限定在矩形内；
	//$point2原点，$point3判断点，（y，x）格式
	$diff_lng = $point1[0] - $point2[0];
	$diff_lat = $point1[1] - $point2[1];
	if($quadrant) {
		if($diff_lng * ($point3[0] - $point2[0]) > 0 && $diff_lat * ($point3[1] - $point2[1]) > 0) {
			return true;
		}
	}
	$lng_in = $lat_in = false;
	if($diff_lng > 0 && $point3[0] >= $point2[0] && $point3[0] <= $point1[0]) {
		$lng_in = true;
	} elseif($diff_lng < 0 && $point3[0] >= $point1[0] && $point3[0] <= $point2[0]) {
		$lng_in = true;
	}
	if($diff_lat > 0 && $point3[1] >= $point2[1] && $point3[1] <= $point1[1]) {
		$lat_in = true;
	} elseif($diff_lat < 0 && $point3[1] >= $point1[1] && $point3[1] <= $point2[1]) {
		$lat_in = true;
	}
	if(($lat_in && $lng_in) || ($diff_lat == 0 && $lng_in) || ($diff_lng == 0 && $lat_in)) {
		return true;
	}
	return false;
}

function is_points_in_identical_side($point1, $point2, $point3, $point4, $vector = false) {
	//$point2原点，$point3判断点， $point4判断点起点， （y，x）格式, 同一象限内的点进行判断
	//$vector是否判断矢量
	$slope = ($point1[0] - $point2[0]) / ($point1[1] - $point2[1]);
	$same_direction = true;
	if($vector) {
		$same_direction = ($point1[0] - $point2[0]) * ($point3[0] - $point4[0]) >= 0;
	}
	if(($slope * $point3[1] - $point3[0]) * ($slope * $point4[1] - $point4[0]) > 0 && $same_direction) {
		return true;
	} else {
		return false;
	}
}

function is_in_identical_direction($reference, $judged) {
	//$point2, $point4原点; $point1, $point3终点； $point1, $point2为参照点；（y，x）格式； 同一象限内的点进行判断；
	$in_quadrant_accept = is_in_two_point($reference['destination'], $reference['origin'],  $judged['destination'], true);
	$in_quadrant_origin = is_in_two_point($reference['destination'], $reference['origin'], $judged['origin']);
	if($in_quadrant_accept && $in_quadrant_origin) {
		$in_identical_direction = is_points_in_identical_side($reference['destination'], $reference['origin'], $judged['destination'], $judged['origin'], true);
		if($in_identical_direction) {
			return true;
		}
	}
	return false;
}
