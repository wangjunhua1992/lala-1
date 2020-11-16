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
icheckauth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	imessage(error(-1, '门店不存在或已经删除'), '', 'ajax');
}

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$table_id = intval($_GPC['table_id']);
	$update = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'status' => 0,
		'table_id' => $table_id,
		'addtime' => TIMESTAMP
	);
	pdo_insert('tiny_wmall_table_call_record', $update);
	$id = pdo_insertid();
	mload()->model('table');
	$result = call_notice_clerk($id);
	if(is_error($result)) {
		imessage(error(-1, '呼叫服务员失败'), '', 'ajax');
	}
	imessage(error(0, '呼叫服务员成功，请稍等'), '', 'ajax');
}