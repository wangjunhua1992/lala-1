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
$_W['page']['title'] = '跑腿';
$id = 3;
/*$diypage = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_errander_diypage') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
$diypage['data'] = json_decode(base64_decode($diypage['data']), true);*/
include itemplate('index1');