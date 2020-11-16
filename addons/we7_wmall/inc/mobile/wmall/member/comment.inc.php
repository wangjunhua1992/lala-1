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
icheckauth();
$_W['page']['title']  = '我的评论';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 10', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 10 * 100;
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}
		$min = min(array_keys($comments));
	}
}

if($ta == 'more') {
	$id = intval($_GPC['min']);
	$comments = pdo_fetchall('select a.id as aid,a.*,b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 10', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 10 * 100;
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime_cn'] = date('Y-m-d H:i', $row['replytime']);
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}
		$min = min(array_keys($comments));
	} else {
		$min = 0;
	}
	$comments = array_values($comments);
	$respon = array('errno' => 0, 'message' => $comments, 'min' => $min);
	imessage($respon, '', 'ajax');
}
include itemplate('member/comment');