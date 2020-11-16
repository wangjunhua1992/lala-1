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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'favorite') {
	$id = intval($_GPC['id']);
	$type = trim($_GPC['type']);
	if($type == 'star') {
		$store = store_fetch($id, array('id', 'title'));
		if(empty($store)) {
			imessage(error(-1, '门店不存在'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
			'sid' => $id,
			'addtime' => TIMESTAMP,
		);
		$is_exist = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
		if(empty($is_exist)) {
			pdo_insert('tiny_wmall_store_favorite', $data);
		}
	} else {
		pdo_delete('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
	}
	imessage(error(0, ''), '', 'ajax');
}