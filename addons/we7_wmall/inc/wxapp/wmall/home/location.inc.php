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
icheckauth(false);
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$addresses = array();
	if($_W['member']['uid'] > 0) {
		$addresses = pdo_getall('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
	imessage(error(0, $addresses), '', 'ajax');
}

elseif($ta == 'suggestion') {
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
