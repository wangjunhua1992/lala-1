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

$op = trim($_GPC['op']);

if($op == 'page') {
	if(isset($_GPC['key'])) {
		$key = trim($_GPC['key']);
		$data = pdo_fetchall('select id,name,thumb,type from ' . tablename('tiny_wmall_errander_page') . ' where uniacid = :uniacid and name like :key order by id desc limit 50', array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"), 'id');
		if(!empty($data)) {
			foreach($data as &$row) {
				$row['thumb_cn'] = tomedia($row['thumb']);
			}
			$pages = array_values($data);
		}
		message(array('errno' => 0, 'message' => $pages, 'data' => $data), '', 'ajax');
	}
	include itemplate('public/errander');
}