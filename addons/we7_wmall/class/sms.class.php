<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
abstract class Sms{
	public static function create($type = '', $scene = 'code') {
		global $_W;
		if(empty($type)) {
			if($scene == 'yinsihao') {
				$config = get_plugin_config('yinsihao.basic');
			} else {
				$config = $_W['we7_wmall']['config']['sms']['set'];
			}
			$type = !empty($config['type']) ? $config['type'] : 'aliyun';
		}
		if($type == 'huawei') {
			mload()->classs('huawei.sms');
			return new HuaweiSms();
		} else {
			mload()->classs('aliyun.sms');
			return new AliyunSms();
		}
		return null;
	}
}