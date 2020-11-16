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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'app';
/*mload()->model('member');
$audience = array(
	'alias' => array('pP6VNsXqSOOKl5xVsnf7S4osVZiu9vsE')
);
$data = Jpush_member_send('eee', '89', array('url' => 'http://www.baidu.com'), $audience);
var_dump($data);die;*/

$_config = get_system_config('app');
$downurls = array(
	'customer' => array(
		'ios' => MODULE_URL . "resource/apps/{$_W['uniacid']}/ios/customer.apk",
		'android' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/customer.apk",
		'apk' => MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/android/customer.apk",
	)
);
if($op == 'app') {
	$_W['page']['title'] = '顾客app设置';
	if($_W['ispost']) {
		if($_GPC['form_type'] == 'setting_app') {
			$login = array(
				'qq' => intval($_GPC['qq']),
				'wx' => intval($_GPC['wx']),
			);
			$data = array(
				'serial_sn' => trim($_GPC['serial_sn']),
				'webtype' => trim($_GPC['webtype']),
				'iosstatus' => intval($_GPC['iosstatus']),
				'iosurl' => trim($_GPC['iosurl']),
				'iosmenu' => intval($_GPC['iosmenu']),
				'build_type' => trim($_GPC['build_type']),
				'ydb_appid' => trim($_GPC['ydb_appid']),
				'ydb_key' => trim($_GPC['ydb_key']),
				'jpush_key' => trim($_GPC['jpush_key']),
				'jpush_secret' => trim($_GPC['jpush_secret']),
				'login' => $login,
				'ios_download_link' => trim($_GPC['customer']['ios_download_link']),
				'android_download_link' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/customer.apk",
			);
			set_system_config('app.customer', $data);
			imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
		} elseif($_GPC['form_type'] == 'upload_file') {
			set_time_limit(0);
			$file = upload_file($_FILES['file'], 'app', 'customer.apk', "resource/apps/{$_W['uniacid']}/android/");
			if(is_error($file)) {
				imessage(error(-1, $file['message']), '', 'ajax');
			}
			imessage(error(0, '上传APP安装包成功'), 'refresh', 'ajax');
		}
	}
	$app = get_system_config('app.customer');
	$menus = pdo_getall('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'version' => 2), array('id', 'name'));
}

include itemplate('app');