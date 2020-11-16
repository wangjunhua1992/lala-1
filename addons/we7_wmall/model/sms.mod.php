<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
mload()->classs('sms');

function sms_send($type, $mobile, $content, $sid = 0) {
	$sms = Sms::create();
	$result = $sms->sendCode($type, $mobile, $content, $sid = 0);
	return $result;
}

function sms_singlecall($called_num, $content, $type = 'clerk') {
	$sms = Sms::create();
	$result = $sms->singleCall($called_num, $content, $type);
	return $result;
}

function sms_bindAxnExtension($params, $sms_type = 'aliyun') {
	$sms = Sms::create($sms_type);
	$result = $sms->yinsihaoBind($params);
	return $result;
}

function sms_unbindSubscription($params, $sms_type = 'aliyun') {
	$sms = Sms::create($sms_type);
	$result = $sms->yinsihaoUnbind($params);
	return $result;
}
