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

function google_batch_calculate_distance($origins, $destination, $key='AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw') {
	global $_W;
	$query = array(
		'key' => $key,
		'origins' => $origins,
		'destination' => implode(',', $destination),
	);
	$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?';
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get($url . $query);
	if(is_error($result)) {
		return $result;
	}
	$result = @json_decode($result['content'], true);
}

function google_geocode_geo($address, $key = 'AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw') {
	global $_W;
	if(empty($address)) {
		return error(-1, '要获取经纬度的地址不存在');
	}
	$query = array(
		'key' => $key,
		'address' => $address,
	);
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?';
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get($url . $query);
	if(is_error($result)) {
		return $result;
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 'OK') {
		return error(-1, $result['error_message']);
	}
	$data = $result['results'][0];
	if(!empty($data) && !empty($data['geometry']['location'])) {
		$data['location'] = array($data['geometry']['location']['lng'], $data['geometry']['location']['lat']);
	}
	return $data;
}