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
	$_W['page']['title'] = '订单评论';
	$condition = "where uniacid = :uniacid and agentid = :agentid";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_gohome_comment') . $condition, $params);
	$comments = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_gohome_comment') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($comments)) {
		$tag_goods = gohome_comment_tags('goods');
		foreach($comments as &$val) {
			$val['thumbs'] = iunserializer($val['thumbs']);
			if(!empty($val['thumbs'])) {
				foreach($val['thumbs'] as &$thumb) {
					$thumb = tomedia($thumb);
				}
			}
			$val['goods'] = gohome_order_goods($val['goods_id'], $val['goods_type']);
			$val['data'] = iunserializer($val['data']);
			if(!empty($val['data']['tag_goods'])) {
				$tags = array();
				$tags_keys = explode('|', $val['data']['tag_goods']);
				if(!empty($tags_keys)) {
					foreach($tags_keys as $keys) {
						$tags[] = $tag_goods[$val['goods_quality']]['tags'][$keys];
					}
				}
			}
			$val['tag_goods'] = $tags;
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'status') {
	$id = intval($_GPC['comment_id']);
	if(!empty($id)) {
		$comment = pdo_get('tiny_wmall_gohome_comment', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		if(empty($comment)) {
			imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
		}
		pdo_update('tiny_wmall_gohome_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	} else {
		$ids = $_GPC['id'];
		if(!empty($ids)) {
			foreach($ids as $value) {
				$comment = pdo_get('tiny_wmall_gohome_comment', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $value));
				if(empty($comment)) {
					imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
				}
				pdo_update('tiny_wmall_gohome_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $value));
			}
		}
	}
	imessage(error(0, '设置评论状态成功'), ireferer(), 'ajax');
}

elseif($op == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$reply = trim($_GPC['reply']);
	pdo_update('tiny_wmall_gohome_comment', array('reply' => $reply, 'replytime' => TIMESTAMP, 'status' => 1), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

include itemplate('comment');
