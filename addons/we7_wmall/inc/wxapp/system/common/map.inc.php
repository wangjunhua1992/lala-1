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
load()->func('communication');
global $_W, $_GPC;
$ta  = trim($_GPC['ta']);

if($ta == 'regeo_qq') {
	$query = array(
		'output' => 'json',
		'get_poi' => 1,
		'key' => '2ECBZ-DXGLS-26MOI-6XMGC-QLTA6-SYFYL',
		'location' => "{$_GPC['latitude']},{$_GPC['longitude']}"
	);
	$query = http_build_query($query);
	$result = ihttp_get('http://apis.map.qq.com/ws/geocoder/v1/?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 0) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	$result = $result['result'];
	$data = array(
		'address' => $result['formatted_addresses']['recommend'],
		'location_x' => $result['location']['lat'],
		'location_y' => $result['location']['lng'],
		'latitude' => $result['location']['lat'],
		'longitude' => $result['location']['lng'],
		'locations' => "{$result['location']['lng']}, {$result['location']['lat']}",
	);
	if(empty($data['address'])) {
		$data['address'] = $result['address'];
	}
	foreach($result['pois'] as &$item) {
		$item['location_y'] = $item['location']['lng'];
		$item['location_x'] = $item['location']['lat'];
		$item['name'] = $item['title'];
		$item['address'] = $item['address'];
	}
	$data['pois'] = $result['pois'];
	imessage(error(0, $data), '', 'ajax');
}
elseif($ta == 'regeo') {
	$latitude = trim($_GPC['latitude']);
	$longitude = trim($_GPC['longitude']);
	$convert = intval($_GPC['convert']);
	if($convert)  {
		//转换坐标系
		$result = ihttp_post('http://restapi.amap.com/v3/assistant/coordinate/convert?parameters',  array('locations' => "{$longitude},{$latitude}", 'coordsys' => 'gps', 'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b'));
		if(is_error($result)) {
			imessage(error(-1, "{$result['message']}"), '', 'ajax');
		}
		$respon = @json_decode($result['content'], true);
		$locations = $respon['locations'];
	} else {
		$locations = "{$longitude},{$latitude}";
	}
	//地址转换
	$query = array(
		'output' => 'json',
		'extensions' => 'all',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'location' => $locations,
	);
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/geocode/regeo?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if(!empty($result['regeocode']['addressComponent']['neighborhood']['name'])) {
		$address = $result['regeocode']['addressComponent']['neighborhood']['name'];
	} elseif(!empty($result['regeocode']['aois'][0])) {
		$address = $result['regeocode']['aois'][0]['name'];
	} else {
		$address = str_replace(array($result['regeocode']['addressComponent']['province'], $result['regeocode']['addressComponent']['district'], $result['regeocode']['addressComponent']['city'], $result['regeocode']['addressComponent']['township']), '', $result['regeocode']['formatted_address']);
	}
	foreach($result['regeocode']['pois'] as &$item) {
		$itemold = $item;

		$location = explode(',', $item['location']);
		$item['location_y'] = $location[0];
		$item['location_x'] = $location[1];
		$item['name'] = $itemold['address'];
		$item['address'] = $itemold['name'];
	}

	$result['address'] = $address;
	$result['pois'] = $result['regeocode']['pois'];
	$result['aois'] = $result['regeocode']['aois'];
	$result['locations'] = $locations;
	$loc = explode(',', $locations);
	$result['location_y'] = $result['longitude'] = $loc[0];
	$result['location_x'] = $result['latitude'] = $loc[1];
	imessage(error(0, $result), '', 'ajax');
}
elseif($ta == 'place_around') {
	$latitude = trim($_GPC['latitude']);
	$longitude = trim($_GPC['longitude']);
	$query = array(
		'output' => 'json',
		'extensions' => 'all',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'location' => "{$longitude},{$latitude}",
		'keywords' => $_GPC['keywords'],
	);
	if(!empty($_GPC['city'])) {
		$query['city'] = $_GPC['city'];
	}
	if(!empty($_GPC['radius'])) {
		$query['radius'] = $_GPC['radius'];
	}
	if(!empty($_GPC['sortrule'])) {
		$query['sortrule'] = $_GPC['sortrule'];
	}
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/place/around?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if(!empty($result['pois'])) {
		foreach($result['pois'] as &$item) {
			$itemold = $item;
			$location = explode(',', $item['location']);
			$item['location_y'] = $location[0];
			$item['location_x'] = $location[1];
			$item['name'] = $itemold['address'];
			$item['address'] = $itemold['name'];
		}
	}
	imessage(error(0, $result['pois']), '', 'ajax');
}

elseif($ta == 'suggestion_qq') {
	$key = trim($_GPC['key']);
	$query = array(
		'key' => '2ECBZ-DXGLS-26MOI-6XMGC-QLTA6-SYFYL',
		'keyword' => $key,
		'region' => '全国',
		'region_fix' => 1,
		'output' => 'json',
	);
	$city = trim($_GPC['city']);
	if(!empty($city)) {
		$query['region'] = $city;
	} else {
		$plugin = trim($_GPC['plugin']) ? trim($_GPC['plugin']) : 'takeout';
		$config = $_W['we7_wmall']['config'];
		if($plugin == 'takeout') {
			$city = $config['takeout']['range']['city'];
		} elseif($plugin == 'errander') {
			$city = get_plugin_config('errander.city');
		}
		$query['region'] = $city;
	}

	$query = http_build_query($query);
	$result = ihttp_get('http://apis.map.qq.com/ws/place/v1/suggestion?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 0) {
		imessage(error(-1, $result['message']), '', 'ajax');
	}
	if(!empty($result['data'])) {
		foreach($result['data'] as $key => &$val) {
			$val['name'] = $val['title'];
			$val['address'] = $val['address'];
			$val['lng'] = $val['location_y'] = $val['location']['lng'];
			$val['lat'] = $val['location_x'] = $val['location']['lat'];
		}
	}
	imessage(error(0, $result['data']), '', 'ajax');
}

elseif($ta == 'suggestion') {
	$key = trim($_GPC['key']);
	$query = array(
		'keywords' => $key,
		'city' => '全国',
		'output' => 'json',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'citylimit' => 'true',
		'type' => '商务住宅|科教文化服务|地名地址信息|医疗保健服务|政府机构及社会团体|住宿服务|公司企业|道路附属设施|购物服务|生活服务|体育休闲服务|交通设施服务',
	);

	$city = trim($_GPC['city']);
	if(!empty($city)) {
		$query['city'] = $city;
	} else {
		$plugin = trim($_GPC['plugin']) ? trim($_GPC['plugin']) : 'takeout';
		$config = $_W['we7_wmall']['config'];
		if($plugin == 'takeout') {
			$city = $config['takeout']['range']['city'];
		} elseif($plugin == 'errander') {
			$city = get_plugin_config('errander.city');
		}
		$query['city'] = $city;
	}
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/assistant/inputtips?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if(!empty($result['tips'])) {
		$distance_sort = 0;
		foreach($result['tips'] as $key => &$val) {
			$valold = $val;
			$val['name'] = $valold['address'];
			$val['address'] = $valold['name'];
			if(is_array($val['location'])) {
				unset($result['tips'][$key]);
				continue;
			}
			$location = explode(',', $val['location']);
			$val['lng'] = $val['location_y'] = $location[0];
			$val['lat'] = $val['location_x'] = $location[1];
			if(!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			} else {
				$val['address'] = $val['district'];
			}
			if(is_array($val['name'])) {
				$val['name'] = $val['address'];
			}
		}
		$result['tips'] = array_values($result['tips']);
	}
	imessage(error(0, $result['tips']), '', 'ajax');
}

/**
 * google Place API：https://developers.google.cn/places/web-service/search
*/

elseif($ta == 'suggestion_google') {
	$key = trim($_GPC['key']);
	$query = array(
		'key' => 'AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw',
		'query' => $key,
		//'region' => 'CN' //区域代码ccTLD
	);
	$query = http_build_query($query);
	$result = ihttp_get('https://maps.googleapis.com/maps/api/place/textsearch/json?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 'OK') {
		imessage(error(-1, $result['error_message']), '', 'ajax');
	}
	$address = $result['results'];
	$place_search = array();
	if(!empty($address)) {
		foreach($address as $value) {
			$place_search[] = array(
				'name' => $value['name'],
				'address' => $value['formatted_address'],
				'lat' => $value['geometry']['location']['lat'],
				'lng' => $value['geometry']['location']['lng'],
				'location_x' => $value['geometry']['location']['lat'],
				'location_y' => $value['geometry']['location']['lng'],
			);
		}
	}
	imessage(error(0, $place_search), '', 'ajax');
}

elseif($ta == 'place_around_google') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	if(empty($lat) || empty($lng)) {
		imessage(error(-1, '经纬度不存在'), '', 'ajax');
	}
	$location = array(
		'lat' => $lat,
		'lng' => $lng
	);
	$radius = intval($_GPC['radius']) > 0 ? intval($_GPC['radius']) : 2000;
	$query = array(
		'key' => 'AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw',
		'location' => implode(',', $location),
		'radius' => $radius,
	);
	$query = http_build_query($query);
	$result = ihttp_get('https://maps.googleapis.com/maps/api/place/nearbysearch/json?' . $query);
	if(is_error($result)) {
		imessage(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 'OK') {
		$error_messages = array(
			'ZERO_RESULTS' => '未能搜索到附近的位置',
			'OVER_QUERY_LIMIT ' => '请求已超过配额',
			'REQUEST_DENIED' => '请求被拒绝，通常是因为缺少key参数',
			'INVALID_REQUEST' => '缺少必需的查询参数location或者radius',
			'UNKNOWN_ERROR' => '服务器端错误',
		);
		$msg = $result['error_message'] ? $result['error_message'] : $error_messages[$result['status']];
		imessage(error(-1, $msg), '', 'ajax');
	}
	$address = $result['results'];
	$place_around = array();
	if(!empty($address)) {
		foreach($address as $value) {
			$place_around[] = array(
				'name' => $value['name'],
				'address' => $value['vicinity'],
				'lat' => $value['geometry']['location']['lat'],
				'lng' => $value['geometry']['location']['lng'],
				'location_x' => $value['geometry']['location']['lat'],
				'location_y' => $value['geometry']['location']['lng'],
			);
		}
	}
	$result = array(
		'place_around' => $place_around
	);
	imessage(error(0, $result), '', 'ajax');
}
