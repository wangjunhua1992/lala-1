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
	imessage(error(-1, '暂未开放此功能'), '', 'ajax');
}
$config_bargain = get_plugin_config('bargain');
if($config_bargain['status'] == 0) {
	imessage(error(-1, '暂未开放此功能'), '', 'ajax');
}
$config_bargain['thumb'] = tomedia($config_bargain['thumb']);

$_W['page']['title'] = '天天特价';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1 and b.status = 1 and c.status = 1 ';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);
	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']);
	$bargains = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.title,b.thumb,b.price,b.sid,b.sailed,c.title as store_title,c.is_in_business from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id left join' . tablename('tiny_wmall_store') . ' as c on b.sid = c.id' . $condition  . ' order by c.is_in_business desc, a.mall_displayorder desc, a.id desc limit ' . ($page - 1) * $psize . " , {$psize}", $params);
	if(!empty($bargains)) {
		foreach($bargains as &$row) {
			if($row['discount_available_total'] == -1) {
				$row['discount_available_total'] = '无限';
			}
			$row['thumb'] = tomedia($row['thumb']);
			$row['discount'] = round(($row['discount_price'] / $row['price'] * 10), 1);
		}
	}
	$config_bargain['lazyload_goods'] = tomedia($_W['we7_wmall']['config']['mall']['lazyload_goods']);
	$respon = array(
		'bargains' => $bargains,
		'config' => $config_bargain
	);
	$_W['_share'] = array(
		'title' => $config_bargain['share']['title'],
		'desc' => $config_bargain['share']['desc'],
		'imgUrl' => tomedia($config_bargain['share']['imgUrl']),
		'link' => !empty($config_bargain['share']['link']) ? $config_bargain['share']['link'] : ivurl('/plugin/pages/bargain/index', array(), true)
	);
	imessage(error(0, $respon), '', 'ajax');
}