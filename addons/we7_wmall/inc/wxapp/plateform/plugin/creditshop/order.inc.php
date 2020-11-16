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
global $_W,$_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'order';

if($ta == 'order') {
	$condition = ' where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$status = intval($_GPC['status']);
	if ($status > 0){
		$condition .= ' AND a.status = :status';
		$params[':status'] = $status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (a.uid = :uid or b.nickname like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('select a.*,b.avatar,b.nickname,c.title,c.thumb from ' . tablename('tiny_wmall_creditshop_order_new') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid left join " . tablename('tiny_wmall_creditshop_goods') . " as c on a.goods_id = c.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if (!empty($records)){
		foreach($records as &$val){
			$val['avatar'] = tomedia($val['avatar']);
			$val['addtime_cn'] = date('Y-m-d H:i', $val['addtime']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'detail') {
	$id = intval($_GPC['id']);
	mload()->model('plugin');
	pload()->model('creditshop');
	$order = creditshop_order_get($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$result = array(
		'order' => $order
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'handle'){
	mload()->model('plugin');
	pload()->model('creditshop');
	$id = intval($_GPC['id']);
	if(!empty($id)) {
		creditshop_order_update($id, 'handle');
	}
	imessage(error(0, ''), '', 'ajax');
}

elseif($ta == 'confirm') {
	$code = trim($_GPC['code']);
	$order = pdo_get('tiny_wmall_creditshop_order_new', array('uniacid' => $_W['uniacid'], 'code' => $code));
	mload()->model('plugin');
	pload()->model('creditshop');
	$status = creditshop_order_update($order, 'handle');
	if(is_error($status)) {
		imessage($status, '', 'ajax');
	}
	imessage(error(0, '核销成功'), '', 'ajax');
}