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
if(!check_plugin_perm('bargain')) {
	imessage('暂未开放此功能', '', 'error');
}
$config_bargain = get_plugin_config('bargain');
if($config_bargain['status'] == 0) {
	imessage('暂未开放此功能', '', 'error');
}

$_W['page']['title'] = '天天特价';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);
	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= ' and a.goods_id < :id';
		$params[':id'] = $id;
	}
	$bargains = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.title,b.thumb,b.price,b.sid,b.sailed,c.title as store_title,c.is_in_business from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id left join ' . tablename('tiny_wmall_store') . ' as c on b.sid = c.id' . $condition  . ' order by c.is_in_business desc, a.mall_displayorder desc limit 100', $params, 'goods_id');
	$min = 0;
	if(!empty($bargains)) {
		foreach($bargains as &$row) {
			if($row['discount_available_total'] == -1) {
				$row['discount_available_total'] = '无限';
			}
			$row['thumb'] = tomedia($row['thumb']);
			$row['discount'] = round(($row['discount_price'] / $row['price'] * 10), 1);
		}
		$min = min(array_keys($bargains));
	}
	if($_W['ispost']) {
		$bargains = array_values($bargains);
		$respon = array('errno' => 0, 'message' => $bargains, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	mload()->model('goods');
	$goods = goods_fetch($id);
	if(is_error($goods)) {
		imessage(error(-1, '商品不存在或已删除'), '', 'ajax');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $goods['sid']), array('is_in_business'));
	$goods['is_in_business'] = $store['is_in_business'];
	imessage(error(0, $goods), '', 'ajax');
}

$_share = array(
	'title' => $config_bargain['share']['title'],
	'desc' => $config_bargain['share']['desc'],
	'link' => !empty($config_bargain['share']['link']) ? $config_bargain['share']['link'] : imurl('bargain/index', array(), true),
	'imgUrl' => tomedia($config_bargain['share']['imgUrl'])
);
include itemplate('index');
