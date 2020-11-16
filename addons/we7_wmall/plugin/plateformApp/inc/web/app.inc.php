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

$downurls = array(
	'plateformApp' => array(
		'android' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/plateform_1.0.apk",
		'apk' => MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/android/plateform_1.0.apk"
	),
);
$_W['page']['title'] = '平台管理app设置';
$app = $_config_plugin['app'];

if($_W['ispost']) {
	if($_GPC['form_type'] == 'setting_app') {
		$data = array(
			'serial_sn' => trim($_GPC['plateformApp']['serial_sn']),
			'ios_build_type' => intval($_GPC['plateformApp']['ios_build_type']),
			'android_build_type' => intval($_GPC['plateformApp']['android_build_type']),
			'push_key' => trim($_GPC['plateformApp']['push_key']),
			'push_secret' => trim($_GPC['plateformApp']['push_secret']),
			'ios_download_link' => trim($_GPC['plateformApp']['ios_download_link']),
			'android_download_link' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/plateform_1.0.apk",
		);
		if(!empty($app['push_tags'])) {
			$data['push_tags'] = $app['push_tags'];
		}
		set_plugin_config('plateformApp.app', $data);
		imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
	} elseif($_GPC['form_type'] == 'upload_file') {
		set_time_limit(0);
		$file = upload_file($_FILES['file'], 'app', 'plateform_1.0.apk', "resource/apps/{$_W['uniacid']}/android/");
		if(is_error($file)) {
			imessage(error(-1, $file['message']), '', 'ajax');
		}
		imessage(error(0, '上传APP安装包成功'), 'refresh', 'ajax');
	}
}

include itemplate('app');