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

if(empty($_W['clerk']['id'])) {
	exit('uid not exist');
}

$new_id = pdo_fetchcolumn('SELECT notice_id FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid and type = :type ORDER BY notice_id DESC LIMIT 1', array(':uid' => $_W['clerk']['id'], ':type' => 'store'));
$new_id = intval($new_id);
$notices = pdo_fetchall('SELECT id FROM ' . tablename('tiny_wmall_notice') . ' WHERE status = 1 AND type = :type AND id > :id', array(':type' => 'store',':id' => $new_id));
if(!empty($notices)) {
	foreach($notices as &$notice) {
		$insert = array(
			'type' => 'store',
			'uid' => $_W['clerk']['id'],
			'notice_id' => $notice['id'],
			'is_new' => 1,
		);
		pdo_insert('tiny_wmall_notice_read_log', $insert);
	}
}
$total = 0;
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid AND is_new = 1', array(':uid' => $_W['clerk']['id']));

exit($total);
