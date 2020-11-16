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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '活动列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'name' => trim($_GPC['name'][$k]),
					'oldprice' => trim($_GPC['oldprice'][$k]),
					'total' => trim($_GPC['total'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_pintuan_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑活动商品成功'), iurl('pintuan/activity/list'), 'ajax');
		}
	}

	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and name like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$sid = intval($_GPC['sid']);
	if(!empty($sid)) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$cateid = intval($_GPC['cateid']);
	if(!empty($cateid)) {
		$condition .= ' and cateid = :cateid';
		$params[':cateid'] = $cateid;
	}
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : '-1';
	if($status > -1) {
		$condition .= " and status = :status";
		$params[':status'] = $status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_pintuan_goods') . $condition, $params);
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_pintuan_goods') . $condition . ' order by displayorder desc,id asc LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($goods)) {
		foreach($goods as &$da) {
			$da['thumb'] = tomedia($da['thumb']);
		}
	}
	$goods_status = gohome_goods_status();
	$stores = store_fetchall(array('id','title'));
	$categorys = pdo_fetchall('select id,title from ' . tablename('tiny_wmall_pintuan_category') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']), 'id');
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	$result = gohome_del_goods($id, 'pintuan');
	imessage($result, '',  'ajax');
}

include itemplate('activity');