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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth();
if($_config_plugin['basic']['status'] != 1) {
	imessage(error(-1, '超级会员功能未开启'), '', 'ajax');
}
if($op == 'index') {
	$nickname = $_W['member']['nickname'];
	$result = array(
		'nickname' => $nickname
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'exchange') {
	$code = trim($_GPC['code']);
	$status = svip_code_exchange($code, $_W['member']['uid']);
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, '兑换成功'), '', 'ajax');
}