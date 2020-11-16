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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'group';
if($op == 'group') {
	$_W['page']['title'] = '顾客设置';
	$config_member = get_system_config('member');
	if($_W['ispost']) {
		$config_member['group_update_mode'] = trim($_GPC['group_update_mode']);
		if(empty($config_member['group_update_mode'])) {
			imessage(error(-1, '请选择顾客等级升级依据'));
		}
		$config_member['force_bind_mobile'] = intval($_GPC['force_bind_mobile']);
		set_system_config('member', $config_member);
		imessage(error(0, ''), ireferer(), 'ajax');
	}
	$group_update_mode = $config_member['group_update_mode'];
	$force_bind_mobile = $config_member['force_bind_mobile'];
}

elseif($op == 'address') {
	$_W['page']['title'] = '顾客收货地址设置';
	if($_W['ispost']) {
		$use_weixin_address = intval($_GPC['use_weixin_address']);
		set_system_config('member.use_weixin_address', $use_weixin_address);
		imessage(error(0, '设置顾客收货地址模式'), ireferer(), 'ajax');
	}
	$use_weixin_address = get_system_config('member.use_weixin_address');
}

include itemplate('config/member');