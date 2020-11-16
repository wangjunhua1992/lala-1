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
mload()->model('cloud');
global $_W, $_GPC;
$_W['page']['title'] = '应用中心';

$_W['plugin_types'] = plugin_types();
$plugins = plugin_fetchall();
$perms = get_agent_perm('plugins');
$_W['plugins'] = array();
foreach($plugins as $row) {
	if(!empty($perms) && !in_array($row['name'], $perms)) {
		continue;
	}
	if(!check_perm($row['name'])) {
		continue;
	}
	$_W['plugins'][$row['type']][] = $row;
	$i++;
}

if(!$i) {
	imessage('没有可用的插件,请联系平台管理员开通', '', 'info');
}

include itemplate('plugin/index');