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
	$_W['page']['title'] = '天天特价设置';
	if($_W['ispost']) {
		$share = array(
			'title' => trim($_GPC['title']),
			'imgUrl' => trim($_GPC['imgUrl']),
			'desc' => trim($_GPC['desc']),
			'link' => trim($_GPC['link']),
		);
		$bargain = array(
			'status' => intval($_GPC['status']),
			'is_home_display' => intval($_GPC['is_home_display']),
			'template' => intval($_GPC['template']),
			'thumb' => trim($_GPC['thumb']),
			'agreement' => htmlspecialchars_decode($_GPC['agreement']),
			'share' => $share,
		);
		set_agent_plugin_config('bargain', $bargain);
		imessage(error(0, '设置天天特价活动成功'), 'refresh', 'ajax');
	}
	$config_bargain = get_agent_plugin_config('bargain');
}

include itemplate('index');