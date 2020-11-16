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
	if($status == 1) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	} else {
		$condition .= ' and status > 1';
	}
	$redPackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . $condition . ' order by id desc limit 6', $params, 'id');
	$min = 0;
	if(!empty($redPackets)) {
		$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
		mload()->model('agent');
		$agents = get_agents(1);
		$channels = array(
			'mealRedpacket' => '红包套餐',
			'mealRedpacket_plus' => '红包套餐',
			'svip' => '超级会员',
			'creditShop' => '积分兑换',
			'vipRedpacket' => '会员红包'
		);
		foreach($redPackets as &$row) {
			$row['mobile'] = $_W['member']['mobile'];
			$row['starttime'] = date('Y-m-d', $row['starttime']);
			$row['endtime'] = date('Y-m-d', $row['endtime']);
			if($row['agentid'] > 0 && empty($row['sid'])) {
				$row['agent_cn'] = "仅限{$agents[$row['agentid']]['area']}使用";
			}
			$row['category_cn'] = tocategory($row['category_limit']);
			if(!empty($row['category_cn'])) {
				$row['category_cn'] = "仅限{$row['category_cn']}分类使用";
			}
			$row['time_cn'] = totime($row['times_limit']);
			if(!empty($row['time_cn'])) {
				$row['time_cn'] = "仅限{$row['time_cn']}时段使用";
			}
			if($row['sid'] > 0) {
				$row['title'] = $stores[$row['sid']]['title'];
				$row['category_cn'] = "仅限{$stores[$row['sid']]['title']}门店使用";
			}
			$limit = array(
				'agent_cn' => $row['agent_cn'],
				'category_cn' => $row['category_cn'],
				'time_cn' => $row['time_cn']
			);
			$row['limit_cn'] = implode(' ', $limit);
			$row['channel_cn'] = $channels[$row['channel']];
		}
		$min = min(array_keys($redPackets));
	}
	$redPackets = array_values($redPackets);
	$respon = array('errno' => 0, 'message' => $redPackets, 'min' => $min);
	imessage($respon, '', 'ajax');
}
