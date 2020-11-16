<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
$wxapp_type = trim($_GPC['type']) ? trim($_GPC['type']) : 'we7_wmall';
mload()->model('cloud');
if($op == 'index') {
	$_W['page']['title'] = '代码上传记录';
	$logs = cloud_w_wxapp_get_commit_log($wxapp_type);
	if(is_error($logs)) {
		imessage($logs, '', 'info');
	}
}
include itemplate('commitlog');
