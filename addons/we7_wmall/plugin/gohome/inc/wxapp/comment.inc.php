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
icheckauth(true);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'comment';
mload()->model('plugin');
pload()->model('gohome');


if($op == 'comment') {
	$order_id = intval($_GPC['order_id']);
	$order = gohome_order_fetch($order_id, true);
	if(empty($order)) {
		imessage(error(-1, '订单不存在'), '', 'ajax');
	}
	if($order['status'] < 5) {
		imessage(error(-1, '请确认核销后再进行评价'), '', 'ajax');
	}
	if($order['status'] > 5) {
		imessage(error(-1, '订单已完成或已取消，无法进行评价'), '', 'ajax');
	}
	if($_W['ispost']) {
		if(!is_array($_GPC['thumbs'])) {
			$_GPC['thumbs'] = json_decode(htmlspecialchars_decode($_GPC['thumbs']), true);
		}
		$thumbs = array();
		if(!empty($_GPC['thumbs'])) {
			foreach($_GPC['thumbs'] as $thumb) {
				if(!empty($thumb['filename'])){
					$thumbs[] = trim($thumb['filename']);
				}
			}
		}
		if(!is_array($_GPC['tags'])) {
			$_GPC['tags'] = json_decode(htmlspecialchars_decode($_GPC['tags']), true);
		}
		$tag_goods = array();
		if(!empty($_GPC['tags'])) {
			foreach($_GPC['tags'] as $tag) {
				if($tag['active'] == 1){
					$tag_goods[] = intval($tag['id']);
				}
			}
		}
		$data['tag_goods'] = implode('|', $tag_goods);
		$update = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'oid' => $order['id'],
			'uid' => $_W['member']['uid'],
			'sid' => $order['sid'],
			'goods_id' => $order['goods_id'],
			'goods_type' => $order['order_type'],
			'username' => $order['username'],
			'mobile' => $order['mobile'],
			'goods_quality' => intval($_GPC['goods_quality']),
			'note' => trim($_GPC['note']),
			'thumbs' => empty($thumbs) ? '' : iserializer($thumbs),
			'status' => 0,
			'addtime' => TIMESTAMP,
			'data' => iserializer($data)
		);
		pdo_insert('tiny_wmall_gohome_comment', $update);
		$id = pdo_insertid();
		if($id > 0) {
			if(check_plugin_perm('spread')) {
				pload()->model('spread');
				spread_order_balance($order['id'], 'gohome');
			}
			pdo_update('tiny_wmall_gohome_order', array('status' => 6, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $order['id']));
			imessage(error(0, '评论成功'), '', 'ajax');
		} else {
			imessage(error(0, '评论失败'), '', 'ajax');
		}
	}
	$goods_tags = gohome_comment_tags('goods');
	$result = array(
		'order' => $order,
		'goods_tags' => $goods_tags
	);
	imessage(error(0, $result), '', 'ajax');
}