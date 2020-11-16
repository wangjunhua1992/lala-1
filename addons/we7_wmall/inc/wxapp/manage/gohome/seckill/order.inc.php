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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$pay_types = order_pay_types();

	$condition = " where a.uniacid = :uniacid and a.sid = :sid and a.is_pay = 1";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;

	$orders =  pdo_fetchall('select a.*, b.title as goods_title, b.use_limit_day from ' . tablename('tiny_wmall_seckill_order') . ' as a left join ' . tablename('tiny_wmall_seckill_goods') . ' as b on a.goods_id = b.id ' . $condition . ' order by a.id desc limit ' . ($page - 1) * $psize . ', ' . $psize, $params);
	if(!empty($orders)) {
		foreach($orders as &$value) {
			$value['addtime_cn'] = date('m-d H:i:s', $value['addtime']);
			if($value['status'] == 1) {
				$value['status_cn'] = '待核销';
				$value['status_color'] = 'color-default';
			} elseif ($value['status'] == 2) {
				$value['status_cn'] = '已核销';
				$value['status_color'] = 'color-success';

			} elseif ($value['status'] == 3) {
				$value['status_cn'] = '已取消';
				$value['status_color'] = 'color-danger';
			}
			$value['pay_type_cn'] = $value['pay_type'] ? $pay_types[$value['pay_type']]['text'] : '未支付';
		}
	}
	$result = array(
		'orders' => $orders
	);
	imessage(error(0, $result), '', 'ajax');
}


elseif($ta == 'status') {
	$id = intval($_GPC['id']);
	mload()->model('plugin');
	pload()->model('seckill');
	$order = seckill_order_get($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] != 1) {
		imessage(error(-1, '该订单已核销或已取消'), '', 'ajax');
	}

	$type = trim($_GPC['type']);
	if($type == 'status') {
		$code = intval($_GPC['code']);
		if($code != $order['code']) {
			imessage(error(-1, '兑换码不正确'), '', 'ajax');
		}
		seckill_order_update($order, 'status');
		imessage(error(0, '核销成功'), '', 'ajax');
	} elseif($type == 'cancel') {
		$res = seckill_order_update($order, 'cancel');
		imessage(error(0, '取消成功'), '', 'ajax');
	}
}

