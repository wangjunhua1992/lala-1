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
global $_GPC,$_W;
icheckauth();
mload()->model('redPacket');
redPacket_cron();

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
$_W['page']['title'] = '我的红包';

if($ta == 'list') {
	$id = intval($_GPC['min']);
	$condition = ' where uniacid = :uniacid and uid = :uid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':uid'] = $_W['member']['uid'];
	if($id > 0) {
		$condition .= ' and id < :id';
		$params[':id'] = $id;
	}
	$status = intval($_GPC['status']) ? intval($_GPC['status']): 1;
	if($status > 0) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$redPackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . $condition . ' order by id desc limit 15', $params, 'id');
	$min = 0;
	if(!empty($redPackets)) {
		foreach($redPackets as &$row) {
			$row['mobile'] = $_W['member']['mobile'];
			$row['starttime'] = date('Y-m-d', $row['starttime']);
			$row['endtime'] = date('Y-m-d', $row['endtime']);
			$row['time_cn'] = totime($row['times_limit']);
			if(!empty($row['time_cn'])) {
				$row['time_cn'] = "仅限{$row['time_cn']}时段使用";
			}
			$row['category_cn'] = tocategory($row['category_limit']);
			if(!empty($row['category_cn'])) {
				$row['category_cn'] = "仅限{$row['category_cn']}分类使用";
			}
		}
		$min = min(array_keys($redPackets));
	}
	if($_W['isajax']) {
		$redPackets = array_values($redPackets);
		$respon = array('errno' => 0, 'message' => $redPackets, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}
include itemplate('member/redPacket');