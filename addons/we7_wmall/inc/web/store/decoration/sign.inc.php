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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index'){
	$_W['page']['title'] = '编辑门店招牌';
	$shopSign = store_get_data($sid, 'shopSign');
	if($_W['ispost']){
		$thumb = trim($_GPC['thumb']);
		store_set_data($sid, 'shopSign', $thumb);
		imessage(error(0, '设置门店招牌成功'), iurl('store/decoration/sign/index'), 'ajax');
	}
}

include itemplate('store/decoration/sign');