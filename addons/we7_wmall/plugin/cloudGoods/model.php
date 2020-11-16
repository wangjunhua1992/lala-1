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

function cloudgoods_getall_menus($filter = array()) {
	global $_W, $_GPC;
	$params = array();

	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		if(isset($condition)) {
			$condition .= ' and agentid = :agentid';
		} else {
			$condition = ' where agentid = :agentid';
		}
		$params[':agentid'] = $agentid;
	}

	if(!empty($filter)) {
		if(!empty($filter['keywords'])) {
			$keywords = trim($filter['keywords']);
			if(!empty($keywords)) {
				if(isset($condition)) {
					$condition .= " and title like '%{$keywords}%'";
				} else {
					$condition = " where title like '%{$keywords}%'";
				}
			}
		}
	}

	$condition .= ' order by displayorder desc';
	$menus = pdo_fetchall('select * from ' . tablename('tiny_wmall_cloudgoods_menu_category') . $condition, $params, 'id');
	if(!empty($menus)) {
		foreach($menus as &$menu) {
			$menu['total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_cloudgoods_goods') . ' where uniacid = :uniacid and menu_id = :menu_id', array(':uniacid' => $_W['uniacid'], ':menu_id' => $menu['id'])));
		}
	}
	return $menus;
}

function cloudgoods_option_fetch($id) {
	global $_W;
	return  pdo_fetchall('select * from ' . tablename('tiny_wmall_cloudgoods_goods_options') . ' where goods_id = :goods_id order by displayorder desc, id asc', array(':goods_id' => $id));
}

function cloudgoods_getall_goods($filter = array()) {
	global $_GPC;
	$params = array();

	if(!empty($filter)) {
		if($filter['goods_categoryid'] > 0) {
			if(isset($condition)) {
				$condition .= ' and category_id = :category_id';
			} else {
				$condition = 'where category_id = :category_id';
			}
			$params = array(
				':category_id' => $filter['goods_categoryid']
			);
		}
		if(!empty($filter['keywords'])) {
			$keywords = trim($filter['keywords']);
			if(isset($condition)) {
				$condition .= " and title like '%{$keywords}%'";
			} else {
				$condition = " where title like '%{$keywords}%'";
			}
		}
	}
	$page = max(intval($_GPC['page']), 1);
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 50;
	$condition .= " order by displayorder desc, id desc limit " . ($page - 1) * $psize . ', ' . $psize;
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_cloudgoods_goods') . $condition, $params, 'id');
	if(!empty($goods)) {
		foreach($goods as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
			if($val['is_options'] == 1) {
				$val['options'] = cloudgoods_option_fetch($val['id']);
			}
			$val['checked'] = 0;
			$val['price'] = floatval($val['price']);
		}
	}
	return $goods;
}

function cloudgoods_menu_fetch($id) {
	$menu = pdo_get('tiny_wmall_cloudgoods_menu_category', array('id' => $id));
	$goods_categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_cloudgoods_goods_category') . ' where menu_id = :menu_id order by displayorder desc', array(':menu_id' => $id));
	$result = array(
		'menu' => $menu,
		'goods_categorys' => $goods_categorys
	);
	return $result;
}

