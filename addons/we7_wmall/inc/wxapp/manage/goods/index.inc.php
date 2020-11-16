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
$_W['page']['title'] = '商品管理';

if($ta == 'list') {
	$categorys = store_fetchall_goods_category($sid);
	//$categorys = array_values($categorys);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$cid = intval($_GPC['cid']);
	if(empty($cid)) {
		$first = array_slice($categorys, 0, 1);
		$cid = $first[0]['id'];
	}
	if($cid > 0) {
		$condition .= ' AND cid = :cid';
		$params[':cid'] = $cid;
	}
	$goods = pdo_fetchall('SELECT id,sid,title,thumb,cid,child_id,price,old_price,sailed,total,displayorder,status,label,type,ts_price FROM ' . tablename('tiny_wmall_goods') . $condition . ' order by displayorder desc, id asc', $params);
	if(!empty($goods)) {
		foreach($goods as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
		}
	}
	$result = array(
		'categorys' => $categorys,
		'goods' => $goods,
		'cid' => $cid,
		'goods_num' => count($goods)
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'status') {
	$id = intval($_GPC['id']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_goods', array('status' => $value), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	$message = ($value == 0 ? '下架成功' : '上架成功');
	imessage(error(0, $message), '', 'ajax');
}

elseif($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	imessage(error(0, '删除商品成功'), '', 'ajax');
}

elseif($ta == 'turncate') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('total' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	imessage(error(0, '库存清空成功'), '', 'ajax');
}

elseif($ta == 'post') {
	$store_config = $_W['we7_wmall']['config']['store']['settle'];
	$config_goods = $_W['we7_wmall']['store']['data']['goods'];
	$id = intval($_GPC['id']);
	$goods = store_fetch_goods($id, array('options'));
	if($_W['ispost']) {
		$params = json_decode(htmlspecialchars_decode($_GPC['params']), true);
		$price = floatval($params['price']);
		if($id > 0 && !$goods['is_options']) {
			mload()->model('goods');
			$result_pricechange = goods_change_price_check($price, $goods['price'], $config_goods, $goods['data']);
			if(is_error($result_pricechange)) {
				imessage($result_pricechange, '', 'ajax');
			} else {
				$change_price_success = $result_pricechange['message'];
			}
		}

		$data = array(
			'sid' => $sid,
			'uniacid' => $_W['uniacid'],
			'title' => trim($params['title']),
			'price' => floatval($params['price']),
			'old_price' => floatval($params['old_price']),
			'ts_price' => floatval($params['ts_price']),
			'box_price' => floatval($params['box_price']),
			'unitname' => trim($params['unitname']),
			'unitnum' => intval($params['unitnum']) > 1 ? intval($params['unitnum']) : 1,
			'total' => intval($params['total']),
			'sailed' => intval($params['sailed']),
			'status' => intval($params['status']),
			'huangou_type' => isset($params['huangou_type']) ? intval($params['huangou_type']) : 1,
			'cid' => intval($params['cid']),
			'type' => intval($params['type']),
			'child_id' => intval($params['child_id']),
			'thumb' => trim($params['thumb']),
			'label' => trim($params['label']),
			'displayorder' => intval($params['displayorder']),
			'description' => htmlspecialchars_decode($params['description']),
			'is_hot' => intval($params['is_hot']),
//			'comment_good' => intval($params['comment_good'])
		);
		$data['svip_status'] = 0;
		if(check_plugin_perm('svip')) {
			$data['svip_price'] = floatval($params['svip_price']);
			if(!empty($data['svip_price']) && $data['svip_price'] < $data['price']) {
				$data['svip_status'] = 1;
			}
		}
		if(!$store_config['custom_goods_sailed_status']) {
			unset($data['sailed']);
		}
		$data['attrs'] = array();
		if(!empty($params['attrs'])) {
			foreach($params['attrs'] as $key => $val) {
				if(empty($val['name'])) {
					continue;
				}
				$data['attrs'][] = array(
					'name' => $val['name'],
					'label' => $val['label']
				);
			}
		}

		//规格暂不支持
		$options = array();
		if(!empty($params['options'])) {
			foreach($params['options'] as $val) {
				$title = trim($val['name']);
				$price = floatval($val['price']);
				if(empty($title) || $price < 0) {
					continue;
				}
				$options[] = array(
					'id' => intval($val['id']),
					'name' => $title,
					'price' => $price,
					'svip_price' => floatval($val['svip_price']),
					'total' => intval($val['total']),
					'total_warning' => intval($val['total_warning']),
					'displayorder' => intval($val['displayorder']),
				);
				if($val['id'] > 0) {
					mload()->model('goods');
					$result_pricechange = goods_change_price_check($price, $goods['options_haskey'][$val['id']]['price'], $config_goods, $goods['data']);
					if(is_error($result_pricechange)) {
						imessage($result_pricechange, '', 'ajax');
					} else {
						if(!$change_price_success) {
							$change_price_success = $result_pricechange['message'];
						}
					}
				}
			}
		}

		if($change_price_success) {
			$data['data']['price_updatetime'] = TIMESTAMP;
		}
		if(!empty($data['data'])) {
			$data['data'] = iserializer($data['data']);
		}
		$data['attrs'] = iserializer($data['attrs']);
		if($id) {
			pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_goods', $data);
			$id = pdo_insertid();
		}

		$ids = array(0);
		if(!empty($options)) {
			foreach($options as $val) {
				$option_id = $val['id'];
				if($option_id > 0) {
					pdo_update('tiny_wmall_goods_options', $val, array('uniacid' => $_W['uniacid'], 'id' => $option_id, 'goods_id' => $id));
				} else {
					$val['uniacid'] = $_W['uniacid'];
					$val['sid'] = $sid;
					$val['goods_id'] = $id;
					pdo_insert('tiny_wmall_goods_options', $val);
					$option_id = pdo_insertid();
				}
				$ids[] = $option_id;
				$i++;
			}
		}

		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_goods_options') . " WHERE uniacid = :aid AND goods_id = :goods_id and id not in ({$ids})", array(':aid' => $_W['uniacid'], ':goods_id' => $id));

		$update = array(
			'is_options' => ($i > 0 ? 1 : 0)
		);
		pdo_update('tiny_wmall_goods', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));

		imessage(error(0, '编辑商品成功'), '', 'ajax');
	}
	$categorys = pdo_getall('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid), array('id', 'title', 'parentid'));

	if(!empty($goods)) {
		if ($goods['type'] == '1') {
			$goods['type_title'] = '外卖';
		} elseif($goods['type'] == '2') {
			$goods['type_title'] = '店内';
		} elseif($goods['type'] == '3') {
			$goods['type_title'] = '店内和外卖';
		}
		if($goods['huangou_type'] == '1') {
			$goods['huangou_title'] = '支持换购和购买';
		} elseif($goods['huangou_type'] == '2') {
			$goods['huangou_title'] = '仅支持换购';
		}
		foreach($categorys as $val) {
			if($goods['child_id'] > 0 && $goods['child_id'] == $val['id']) {
				$goods['category_title'] = $val['title'];
			} else {
				if($goods['cid'] == $val['id']) {
					$goods['category_title'] = $val['title'];
				}
			}
		}
		$goods['attrs'] = iunserializer($goods['attrs']);
	}
	if(is_error($goods)) {
		$goods = array(
			'total' => -1,
			'status' => 1,
			'box_price' => 0,
			'huangou_type' => 1
		);
	}
	if(check_plugin_perm('huangou')) {
		$goods['huangou_status'] = '1';
	}
	$huangou_types = array(
		array(
			'id' => '1',
			'title' => '支持换购和购买'
		),
		array(
			'id' => 2,
			'title' => '仅支持换购'
		)
	);
	$type = array(
		array(
			'id' => '1',
			'title' => '外卖'
		),
		array(
			'id' => '2',
			'title' => '店内'
		),
		array(
			'id' => '3',
			'title' => '店内加外卖'
		),
	);
	$result = array(
		'categorys' => $categorys,
		'goods' => $goods,
		'type' => $type,
		'huangou_types' => $huangou_types
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'search') {
	mload()->model('goods');
	$sid = intval($_GPC['sid']);
	$title = trim($_GPC['keyword']);
	if(empty($title)) {
		imessage(error(-1, '请输入商品名'), '', 'ajax');
	}
	$condition = "WHERE uniacid = :uniacid AND sid = :sid and title like '%{$title}%'";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);
	$goods = pdo_fetchall('SELECT id,sid,title,thumb,cid,child_id,price,old_price,sailed,total,displayorder,status,label,type,ts_price FROM ' . tablename('tiny_wmall_goods') . $condition . ' order by displayorder desc, id asc', $params);
	if(!empty($goods)) {
		foreach($goods as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
		}
	}
	$result = array(
		'goods' => $goods
	);
	imessage(error(0, $result), '', 'ajax');
}