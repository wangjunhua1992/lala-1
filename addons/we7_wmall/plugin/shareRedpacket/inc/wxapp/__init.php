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
$redPacket = shareRedpacket_get();
if(is_error($redPacket)) {
	imessage(error(-1, $redPacket['message']), '', 'ajax');
}