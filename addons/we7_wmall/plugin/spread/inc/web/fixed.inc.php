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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	pdo_run("update ims_tiny_wmall_members set spreadfixed = 1 where  uniacid = :uniacid and spread1 = 0 and success_num > 0;", array(':uniacid' => $_W['uniacid']));
	imessage(error(0, '操作成功'), '', 'ajax');
}
