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
	$condition = ' where a.uniacid = :uniacid and (a.status = 1 or a.status = 0)';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$keywords = trim($_GPC['keywords']);
	if(!empty($keywords)) {
		$condition .= " and a.title like '%{$keywords}%' or a.id = '{$keywords}'";
	}
	$is_rest = intval($_GPC['is_rest']);
	if($is_rest > -1) {
		$condition .= ' AND is_rest = :is_rest';
		$params[':is_rest'] = $is_rest;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$store = pdo_fetchall('select a.id, a.title, a.logo, a.label, a.telephone, a.is_rest, a.sailed, a.click, a.displayorder, a.is_in_business, a.is_recommend, a.is_stick, b.amount, b.deposit from ' . tablename('tiny_wmall_store') . ' as a left join '. tablename('tiny_wmall_store_account') .' as b on a.id = b.sid ' . $condition . ' order by displayorder desc,id desc limit '.($pindex - 1) * $psize.','.$psize, $params);
	$store_label = store_category_label();
	if(!empty($store)) {
		foreach($store as &$value) {
			$value['label_cn'] = $store_label[$value['label']]['title'];
			$value['logo'] = tomedia($value['logo']);
		}
	}
	$result = array(
		'records' => $store
	);
	imessage(error(0, $result),'', 'ajax');
}

elseif($ta == 'change') {
	$sid = intval($_GPC['id']);
	$name = trim($_GPC['name']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_store', array($name => $value), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	imessage(error(0, '设置成功'), '', 'ajax');

}