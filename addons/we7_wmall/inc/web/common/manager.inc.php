<?php
/**
 * 啦啦外卖 - 做好用的外卖系统!
 * =========================================================
 * Ctay right 2015-2038 太原多讯网络科技有限公司, 保留所有权利。
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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'link';

if($ta == 'link') {
	$type = empty($_GPC['type']) ? 'manager' :  trim($_GPC['type']);
	$urls = wxapp_urls($type);
	include itemplate('public/plateformLink');
}