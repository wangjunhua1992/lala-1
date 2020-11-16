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
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$sid = intval($_GPC['store_id']);
	if($sid > 0) {
		$condition .= ' and a.sid = :sid';
		$params[':sid'] = $sid;
	}
	if(isset($_GPC['is_options'])) {
		$is_options = intval($_GPC['is_options']);
		$condition .= ' and a.is_options = :is_options';
		$params[':is_options'] = $is_options;
	}
	$key = trim($_GPC['key']);
	if(!empty($key)) {
		$condition .= ' and a.title like :key';
		$params[':key'] = "%{$key}%";
	}
	$condition .= ' and b.agentid = :agentid';
	$params[':agentid'] = $_W['agentid'];
	$data = pdo_fetchall('select a.id, a.sid, a.title, a.thumb, a.price, a.total, b.status as store_status from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id' . $condition, $params, 'id');
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['thumb'] = tomedia($row['thumb']);
			if($row['total'] == -1) {
				$row['total'] = '无限';
			}
		}
		$goods = array_values($data);
	}
	message(array('errno' => 0, 'message' => $goods, 'data' => $data), '', 'ajax');
}