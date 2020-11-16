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

//删除过期绑定数据
pdo_run('delete from ' . tablename('tiny_wmall_yinsihao_bind_list') . ' where uniacid = ' . $_W['uniacid'] . ' and expiration < ' . TIMESTAMP);
