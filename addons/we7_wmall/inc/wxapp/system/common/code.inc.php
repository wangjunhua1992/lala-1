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
mload()->model('sms');
global $_W, $_GPC;

$today = date('Ymd');
$cache_key = "we7_wmall:sms:{$_W['uniacid']}:{$today}";
$sentTimes = cache_read($cache_key);
$sentTimes = intval($sentTimes);
if($sentTimes >= 5000) {
	imessage(error(-1, '今日获取验证码次数已达最高限制'), '', 'ajax');
}

$sid = intval($_GPC['sid']);
$mobile = trim($_GPC['mobile']);
if($mobile == ''){
	imessage(error(-1, '请输入手机号'), '', 'ajax');
}

if(!is_validMobile($mobile)) {
	imessage(error(-1, '手机号格式错误'), '', 'ajax');
}
$check_captcha = isset($_GPC['check_captcha']) ? intval($_GPC['check_captcha']) : 1;
if($check_captcha == 1) {
	$captcha = trim($_GPC['captcha']);
	$hash = md5($captcha . $_W['config']['setting']['authkey']);
	if (empty($_SESSION['__code']) || $hash != $_SESSION['__code']) {
		$_SESSION['__code'] = '';
		isetcookie('__code', '');
		imessage(error(-10, '图形验证码错误, 请重新输入'), '', 'ajax');
	} else {
		$_SESSION['__code'] = '';
		isetcookie('__code', '');
	}
}


$sql = 'DELETE FROM ' . tablename('uni_verifycode') . ' WHERE `createtime`<' . (TIMESTAMP - 1800);
pdo_query($sql);

$sql = 'SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE `receiver`=:receiver AND `uniacid`=:uniacid';
$pars = array();
$pars[':receiver'] = $mobile;
$pars[':uniacid'] = $_W['uniacid'];
$row = pdo_fetch($sql, $pars);
$record = array();
if(!empty($row)) {
	if($row['total'] >= 8) {
		imessage(error(-1, '您的操作过于频繁,请稍后再试'), '', 'ajax');
	}
	$code = $row['verifycode'];
	$record['total'] = $row['total'] + 1;
} else {
	$code = random(6, true);
	$record['uniacid'] = $_W['uniacid'];
	$record['receiver'] = $mobile;
	$record['verifycode'] = $code;
	$record['total'] = 1;
	$record['createtime'] = TIMESTAMP;
}
if(!empty($row)) {
	pdo_update('uni_verifycode', $record, array('id' => $row['id']));
} else {
	pdo_insert('uni_verifycode', $record);
}
$content = array(
	'code' => $code,
	//'product' => trim($_GPC['product'])
);
$config_sms = $_W['we7_wmall']['config']['sms'];
$result = sms_send($config_sms['template']['verify_code_tpl'], $mobile, $content, $sid);
if(is_error($result)) {
	if($config_sms['set']['type'] == 'huawei') {
		slog('huaweiSms', '华为云短信通知验证码', $content, $result['message']);
	} else {
		slog('alidayuSms', '阿里大鱼短信通知验证码', $content, $result['message']);
	}
	imessage(error(-1, $result['message']), '', 'ajax');
}

$sentTimes++;
cache_write($cache_key, $sentTimes);
imessage(error(0, 'success'), '', 'ajax');