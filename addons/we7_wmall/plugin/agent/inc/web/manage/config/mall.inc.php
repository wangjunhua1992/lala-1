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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';

if ($op == 'basic'){
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$mall = array(
			'version' => 1,
			'store_orderby_type' => trim($_GPC['store_orderby_type']),
			'store_overradius_display' => intval($_GPC['store_overradius_display']),
		);
		set_agent_system_config('mall', $mall);
		$manager = $_GPC['manager'];
		set_agent_system_config('manager', $manager);
		imessage(error(0, '基础设置成功'), ireferer(), 'ajax');
	}

	$_config = get_agent_system_config();
	$config = $_config['mall'];
	$config['manager'] = $_config['manager'];
	include itemplate('config/basic');
}

elseif($op == 'close') {
	$_W['page']['title'] = '代理状态';
	if($_W['ispost']) {
		$close = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 1,
			'url' => trim($_GPC['url']),
			'tips' => trim($_GPC['tips']),
		);
		set_agent_system_config('close', $close);
		imessage(error(0, '平台状态设置成功'), ireferer(), 'ajax');
	}
	$close = get_agent_system_config('close');
	include itemplate('config/close');
}

elseif($op == 'follow') {
	$_W['page']['title'] = '分享设置';
	if($_W['ispost']) {
		$share = array(
			'title' => trim($_GPC['title']),
			'imgUrl' => trim($_GPC['imgUrl']),
			'desc' => trim($_GPC['desc']),
			'link' => trim($_GPC['link']),
		);
		set_agent_system_config('share', $share);
		imessage(error(0, '分享设置成功'), ireferer(), 'ajax');
	}
	$share = get_agent_system_config('share');
	include itemplate('config/follow');
}



