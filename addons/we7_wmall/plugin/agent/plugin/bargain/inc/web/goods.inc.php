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
if($op == 'index') {
	$_W['page']['title'] = '商品列表';
	$condition = ' where (a.discount_available_total = -1 or a.discount_available_total > 0) and b.status = 1 and a.uniacid = :uniacid and a.agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and a.sid = :sid';
		$params[':sid'] = $sid;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain') . ' as b on a.bargain_id = b.id' . $condition, $params);
	$bargains = pdo_fetchall('select a.*,b.order_limit,b.goods_limit from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain') . ' as b on a.bargain_id = b.id' . $condition . ' order by a.mall_displayorder desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($bargains)) {
		foreach($bargains as &$row) {
			if($row['discount_total'] == -1) {
				$row['discount_total'] = '无限';
			}
			if($row['discount_available_total'] == -1) {
				$row['discount_available_total'] = '无限';
			}
			$row['goods'] = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']), array('title', 'thumb', 'price', 'status'));
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('title'));
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_fetchall('select distinct(a.sid),b.title from ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.status = 1 order by a.sid desc', array(':uniacid' => $_W['uniacid']));
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'mall_displayorder' => intval($_GPC['mall_displayorder'][$k])
				);
				pdo_update('tiny_wmall_activity_bargain_goods', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => intval($v)));
			}
			imessage(error(0,'排序成功'), ireferer(), 'ajax');
		}
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, '商品下架成功'), ireferer(), 'ajax');
}

include itemplate('goods');