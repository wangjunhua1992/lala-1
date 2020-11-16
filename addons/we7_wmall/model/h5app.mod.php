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

function h5app_push($alias, $title, $msg = '', $url = '') {
	global $_W;
	$config = $_W['we7_wmall']['config']['app']['customer'];
	if($config['build_type'] == 'ydb') {
		if(empty($config['ydb_appid']) || empty($config['ydb_key'])) {
			return error(-1, '云打包appid或key不完善');
		}
	} else {
		if(empty($config['jpush_key']) || empty($config['jpush_secret'])) {
			return error(-1, '极光推送key或secret不完善');
		}
	}
	if(empty($config['serial_sn'])) {
		return error(-1, 'app序列号不完善');
	}
	load()->func('communication');
	if($config['build_type'] == 'ydb') {
		$array = array(
			'appid' => $config['appid'],
			'key' => $config['key'],
			'title' => $title,
			'users' => $alias,
			'msg' => $msg,
			'url' => !empty($url) ? urlencode($url) : '',
		);
		$query = http_build_query($array);
		$url = "http://pushmsg.ydbimg.com/rest/weblsq/1.0/PushMsg.aspx?{$query}";
		$response = ihttp_get($url);
		if(is_error($response)) {
			return $response;
		}
		$result = @json_decode($response['content'], true);
		if($result['status'] != 1) {
			return error(-1, "错误代码: {$result['status']}, 错误信息: {$result['msg']}");
		}
	} else {
		$audience = array(
			'alias' => array($alias)
		);
		$data = Jpush_member_send($msg, $title, array('url' => $url), $audience);
		return $data;
	}
	return true;
}
