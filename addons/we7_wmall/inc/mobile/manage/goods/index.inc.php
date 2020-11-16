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

	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$cid_cn = '不限';
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= ' AND cid = :cid';
		$params[':cid'] = $cid;
		$cid_cn = $categorys[$cid]['title'];
	}
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . $condition . ' order by displayorder desc, id asc', $params);
	include itemplate('goods/index');
}

if($ta == 'status') {
	$id = intval($_GPC['id']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_goods', array('status' => $value), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	$message = ($value == 0 ? '下架成功' : '上架成功');
	imessage(error(0, $message), '', 'ajax');
}

if($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	imessage(error(0, '删除商品成功'), ireferer(), 'ajax');
}

if($ta == 'turncate') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('total' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	imessage(error(0, '库存清空成功'), ireferer(), 'ajax');
}

if($ta == 'post') {
	mload()->func('tpl.app');
	$categorys = store_fetchall_goods_category($sid);
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
	$store_config = $_W['we7_wmall']['config']['store']['settle'];
	$id = intval($_GPC['id']);
	if($_W['isajax']) {
		$data = array(
			'sid' => $sid,
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'price' => floatval($_GPC['price']),
			'ts_price' => floatval($_GPC['ts_price']),
			'box_price' => floatval($_GPC['box_price']),
			'unitname' => trim($_GPC['unitname']),
			'total' => intval($_GPC['total']),
			'sailed' => intval($_GPC['sailed']),
			'status' => intval($_GPC['status']),
			'cid' => intval($_GPC['cid']),
			'type' => intval($_GPC['type']),
			'thumb' => trim($_GPC['thumb']),
			'label' => trim($_GPC['label']),
			'displayorder' => intval($_GPC['displayorder']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'is_hot' => intval($_GPC['is_hot']),
		);
		if(!$store_config['custom_goods_sailed_status']) {
			unset($data['sailed']);
		}

		if($id) {
			pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_goods', $data);
			$id = pdo_insertid();
		}

		//规格
		$options = array();
		$ids = array(0);
		if(!empty($_GPC['options'])) {
			foreach($_GPC['options'] as $val) {
				$title = trim($val['title']);
				$price = floatval($val['price']);
				if(empty($title) || empty($price)) {
					continue;
				}
				$options[] = array(
					'id' => intval($val['id']),
					'name' => $title,
					'price' => $price,
					'total' => intval($val['total']),
					'displayorder' => intval($val['displayorder']),
				);
			}

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

	$goods = store_fetch_goods($id);
	if(is_error($goods)) {
		$goods = array(
			'total' => -1,
			'status' => 1,
			'box_price' => 0
		);
	}
	include itemplate('goods/index');
}