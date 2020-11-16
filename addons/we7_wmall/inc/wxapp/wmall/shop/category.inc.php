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
icheckauth(false);
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$sid = intval($_GPC['sid']);
	if(empty($sid)) {
		$sid = $_W['we7_wmall']['config']['mall']['default_sid'];
	}
	mload()->model('page');
	$store = store_fetch($sid);
	if($_W['is_agent']) {
		$_W['agentid'] = $store['agentid'];
	}
	if(!empty($store['data']['shopPage'])) {
		foreach($store['data']['shopPage'] as &$val) {
			$val['goodsLength'] = count($val['goods']);
			$val['thumb'] = tomedia($val['thumb']);
		}
	}
	$config_diypage = $store['data']['diypage'];
	if($config_diypage['use_diy_category'] != 1) {
		imessage(error(-1, '门店未启用自定义分类页'), '', 'ajax');
	}
	//自定义页面pid
	$pid = intval($config_diypage['shop_page']['category']);
	if($pid > 0) {
		$homepage = store_page_get($sid, $pid);
	}
	if(empty($homepage)) {
		imessage(error(-1, '门店未设置自定义分类页'), '', 'ajax');
	}
	$_W['_share'] = array(
		'title' => $store['title'],
		'desc' => $store['content'],
		'imgUrl' => tomedia($store['logo']),
		'link' => ivurl('/pages/shop/index', array('sid' => $sid), true),
	);
	$result = array(
		'homepage' => $homepage['data'],
		'cart' => cart_data_init($sid),
		'diy' => $homepage,
		'store_id' => $sid,
		'sid' => $sid,
		'config_mall' => $_config_mall,
		'store' => $store,
		'superRedpacketData' => array()
	);

	$categorys = array_values(store_fetchall_goods_category($sid, 1, false, 'all', 'available'));
	$result['category'] = $categorys;
	$cid = !empty($_GPC['cid']) ? trim($_GPC['cid']) : $categorys[0]['id'];
	$child_id = 0;
	$cindex = 0;
	if(!empty($categorys)) {
		foreach($categorys as $index => $cate) {
			if($cate['id'] == $cid) {
				$cindex = $index;
				if(!empty($cate['child']) && count($cate['child']) > 0) {
					$child_id = $cate['child'][0]['id'];
				}
				break;
			};
		}
	}
	$result['goods'] = goods_filter($sid, array('cid' => $cid, 'child_id' => $child_id));
	$result['cid'] = $cid;
	$result['child_id'] = $child_id;
	$result['cindex'] = $cindex;

	if(check_plugin_perm('superRedpacket') && $_W['we7_wmall']['config']['mall']['version'] == 2) {
		pload()->model('superRedpacket');
		$result['superRedpacketData'] = superRedpacket_grant_show();
	}
	imessage(error(0, $result), '', 'ajax');
}
