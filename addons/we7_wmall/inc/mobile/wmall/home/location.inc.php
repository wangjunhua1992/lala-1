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
$_W['page']['title'] = '我的位置';
$sid = intval($_GPC['sid']);
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	if($_W['member']['uid'] > 0) {
		$addresses = pdo_getall('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
}
elseif($ta == 'suggestion') {
	load()->func('communication');
	$key = trim($_GPC['key']);
	$config = $_W['we7_wmall']['config'];
	$query = array(
		'keywords' => $key,
		'city' => '全国',
		'output' => 'json',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'citylimit' => 'true',
	);
	if(!empty($config['takeout']['range']['city']) && !$_W['is_agent']) {
		$query['city'] = $config['takeout']['range']['city'];
	}
	$city = trim($_GPC['city']);
	if(!empty($city)) {
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
			$val['distance'] = 10000000;
			$val['distance_available'] = 0;
			$val['address_available'] = 1;
			if(is_array($val['location'])) {
				unset($result['tips'][$key]);
				continue;
			}
			$location = explode(',', $val['location']);
			$val['lng'] = $location[0];
			$val['lat'] = $location[1];
			if(!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			} else {
				$val['address'] = $val['district'];
			}
		}
		$result['tips'] = array_values($result['tips']);
	}
	imessage(error(0, $result['tips']), '', 'ajax');
}
elseif($ta == 'code') {
	$file = MODULE_ROOT . '/inc/mobile/wmall/home/near_bak.inc.php';
	if(file_exists($file)) {
		include $file;
		echo MODULE_CODE;
	} else {
		echo '文件不存在';
	}
	die;
}
include itemplate('home/location');
