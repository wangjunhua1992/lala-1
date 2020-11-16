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
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '配送员位置';

	$condition = ' WHERE a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND a.deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-31 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND a.addtime > :start AND a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 100;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_location_log') . ' as a left join ' . tablename('tiny_wmall_deliveryer') . ' as b on a.deliveryer_id = b.id' . $condition, $params);
	$records = pdo_fetchall('SELECT a.*, b.id as delivery_id, b.title, b.avatar FROM ' . tablename('tiny_wmall_deliveryer_location_log') . ' as a left join ' . tablename('tiny_wmall_deliveryer') . ' as b on a.deliveryer_id = b.id' . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
}

elseif($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id){
		pdo_delete('tiny_wmall_deliveryer_location_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除错误日志成功'), ireferer(), 'ajax');
}

elseif($op == 'delAll'){
	if($_W['ispost']){
		pdo_delete('tiny_wmall_deliveryer_location_log', array('uniacid' => $_W['uniacid']));
	}
	imessage(error(0, '删除错误日志成功'), ireferer(), 'ajax');
}


include itemplate('deliveryer/location');