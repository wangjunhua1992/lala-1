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

if($op == 'list') {
	$key = trim($_GPC['key']);
	$data = pdo_fetchall('select a.name, b.uniacid from ' . tablename('uni_account') . ' as a left join ' . tablename('account_wechats') . ' as b on a.default_acid = b.uniacid where a.name like :akey or b.name like :key order by b.uniacid desc limit 50', array(':akey' => "%{$key}%", ':key' => "%{$key}%"), 'uniacid');
	if(!empty($data)) {
		$account = array_values($data);
	}
	message(array('errno' => 0, 'message' => $account, 'data' => $data), '', 'ajax');
}