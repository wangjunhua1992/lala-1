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
mload()->model('plugin');
global $_W, $_GPC;
$name = $_W['_controller'];
if(defined('IN_AGENT') && !defined('IN_AGENT_PLUGIN')) {
	$name = 'agent';
}
$plugin = plugin_fetch($name);
$_W['_plugin'] = $plugin;
if(empty($plugin)) {
	imessage('插件不存在', ireferer(), 'error');
}

if(!$plugin['status']) {
	imessage('系统尚未开启该插件', ireferer(), 'error');
}

$status = plugin_account_has_perm($plugin['name']);
if(empty($status)) {
	imessage('公众号没有使用该插件的权限', ireferer(), 'error');
}
$_W['_plugin']['config'] = $_config_plugin = get_plugin_config($_W['_plugin']['name']);
pload()->model($_W['_plugin']['name']);

if(!empty($_GPC['filter'])) {
	$_GPC['filter'] = json_decode(htmlspecialchars_decode($_GPC['filter']), true);
	if(is_array($_GPC['filter'])) {
		foreach($_GPC['filter'] as $key => $val) {
			$_GPC[$key] = $val;
		}
	}
}

