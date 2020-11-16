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

if($ta == 'index') {
	$sid_str = implode(', ', array_unique(array_keys($sids)));
	$stores = pdo_fetchall('select id, title, logo from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$sid_str})", array(':uniacid' => $_W['uniacid']));
	if(!empty($stores)) {
		foreach($stores as &$store) {
			$store['logo'] = tomedia($store['logo']);
		}
	}
	$result = array(
		'stores' => $stores
	);
	imessage(error(0, $result), '',  'ajax');
}