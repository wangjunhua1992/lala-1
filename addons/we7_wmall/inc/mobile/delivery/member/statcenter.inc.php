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
$_W['page']['title'] = '配送统计';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'store';

if($ta == 'store') {
	$sids = $_W['deliveryer']['sids'];
	if(empty($sids)) {
		imessage(error(0, '您不是店内配送员'), imurl('delivery/member/mine', array()), 'error');
	}
	$condition = " where uniacid = :uniacid and status = 5 and delivery_type = 1 and order_type <= 2 and deliveryer_id = :deliveryer_id and stat_day = :stat_day";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':deliveryer_id' => $_W['deliveryer']['id'],
		'stat_day' => date('Ymd')
	);
	$condition .= " and sid in ({$_W['deliveryer']['sids_sn']})";
	$orders = pdo_fetchall('select sid, count(*) as order_num, sum(num) as goods_num from ' . tablename('tiny_wmall_order') . " {$condition} group by sid", $params, 'sid');
	$stores = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$_W['deliveryer']['sids_sn']})", array(':uniacid' => $_W['uniacid']), 'id');
	$records = array();
	if(!empty($stores)) {
		foreach($stores as $store) {
			if(array_key_exists($store['id'], $orders)) {
				$records[] = array(
					'id' => $store['id'],
					'title' => $store['title'],
					'order_num' => intval($orders[$store['id']]['order_num']),
					'goods_num' => intval($orders[$store['id']]['goods_num']),
				);
			} else {
				$records[] = array(
					'id' => $store['id'],
					'title' => $store['title'],
					'order_num' => 0,
					'goods_num' => 0,
				);
			}
		}
	}
}


include itemplate('member/statcenter');