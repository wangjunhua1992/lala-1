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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '跳转小程序设置';
	if($_W['ispost']){
		if(!empty($_GPC['wxapp_skip'])) {
			$wxapp_skip = array();
			foreach($_GPC['wxapp_skip']['title'] as $key => $val) {
				$val = trim($val);
				if(empty($val)) {
					continue;
				}
				$appid = $_GPC['wxapp_skip']['appid'][$key];
				if(empty($appid)) {
					continue;
				}
				$wxapp_skip[] = array(
					'title' => $val,
					'appid' => $appid
				);
			}
			set_plugin_config('wxapp.miniProgramAppIdList', $wxapp_skip);
			imessage(error(0, '编辑跳转小程序成功'), 'refresh', 'ajax');
		}
	}
	$wxapp_skip = get_plugin_config('wxapp.miniProgramAppIdList');
}

include itemplate('skip');