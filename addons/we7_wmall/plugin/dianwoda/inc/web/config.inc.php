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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$type = trim($_GPC['type']);
		if(!in_array($type, array('store', 'plateform'))) {
			imessage(error(-1, '请选择对接模式'), '', 'ajax');
		}
		$data = array(
			'status' => intval($_GPC['status']),
			'type' => $type,
			'appkey' => trim($_GPC['appkey']),
			'appsecret' => trim($_GPC['appsecret']),
			'accesstoken' => trim($_GPC['accesstoken']),
			'merchantid' => intval($_GPC['merchantid']),
			'cityCode' => intval($_GPC['cityCode']),
		);
		set_plugin_config('dianwoda', $data);
		imessage(error(0, '设置成功'), 'refresh', 'ajax');
	}
	$notify_url = WE7_WMALL_URL . 'plugin/dianwoda/notify.php';
	$dianwoda = get_plugin_config('dianwoda');
	if(empty($dianwoda['type'])) {
		$dianwoda['type'] = 'plateform';
	}

	$testUrl = imurl('dianwoda/api', array(), true);
}
include itemplate('config');