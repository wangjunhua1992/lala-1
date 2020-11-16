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

$_config = get_system_config('app');
$downurls = array(
	'manager' => array(
		'ios' => MODULE_URL . "resource/apps/{$_W['uniacid']}/ios/manager.apk",
		'android' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/manager.apk",
		'apk' => MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/android/manager.apk"
	)
);
if($op == 'app') {
	$_W['page']['title'] = '商家app设置';
	load()->func('file');
	$path = "resource/mp3/{$_W['uniacid']}/";
	mkdirs(MODULE_ROOT . '/' . $path);
	$files = array();
	if($_W['ispost']) {
		if($_GPC['form_type'] == 'setting_app') {
			foreach($_FILES as $key => $val) {
				if(!empty($val['name']) && $val['error'] == 0) {
					$pathinfo = pathinfo($val['name']);
					$ext = strtolower($pathinfo['extension']);
					if($ext != 'mp3') {
						imessage(error(-1, '仅支持mp3类型的语音文件'), ireferer(), 'ajax');
					}
					$basename =  "{$key}.{$ext}";
					if(!file_move($val['tmp_name'],  MODULE_ROOT . '/' . $path . $basename)) {
						imessage(error(-1, '保存上传文件失败'), ireferer(), 'ajax');
					}
					$files[$key] = $basename;
				}
				if(empty($files[$key])) {
					$files[$key] = $_config['manager']['phonic'][$key];
				}
			}
			$data = array(
				'serial_sn' => trim($_GPC['manager']['serial_sn']),
				'push_key' => trim($_GPC['manager']['push_key']),
				'push_secret' => trim($_GPC['manager']['push_secret']),
				'ios_build_type' => intval($_GPC['manager']['ios_build_type']),
				'version' => array(
					'ios' => trim($_GPC['manager']['version']['ios']),
					'android' => 1,
				),
				'xunfei_Android_appid' => trim($_GPC['manager']['xunfei_Android_appid']),
				'xunfei_ios_appid' => trim($_GPC['manager']['xunfei_ios_appid']),
				'ios_download_link' => trim($_GPC['manager']['ios_download_link']),
				'android_download_link' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/manager.apk",
				'phonic' => $files
			);
			set_system_config('app.manager', $data);
			imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
		} elseif($_GPC['form_type'] == 'upload_file') {
			set_time_limit(0);
			$file = upload_file($_FILES['file'], 'app', 'manager.apk', "resource/apps/{$_W['uniacid']}/android/");
			if(is_error($file)) {
				imessage(error(-1, $file['message']), '', 'ajax');
			}
			imessage(error(0, '上传APP安装包成功'), 'refresh', 'ajax');
		}
	}
	$app = get_system_config('app.manager');
}

include itemplate('app');