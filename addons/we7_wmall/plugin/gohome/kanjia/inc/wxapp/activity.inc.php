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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	if($_W['is_agent'] && $_W['agentid'] == -1) {
		imessage(error(41200, '您所在的区域暂未开启砍价功能,建议您手动搜索地址或切换到此前常用的地址再试试'), '', 'ajax');
	}
	if($_config_plugin['basic']['status']['kanjia'] != 1) {
		imessage(error(-1, '砍价功能暂时关闭，敬请关注'), '', 'ajax');
	}
	$is_black = member_is_in_black('kanjia');
	if($is_black) {
		$result['black_member'] = array(
			'status' => $is_black,
			'tip' => $_config_plugin['basic']['black_tip']
		);
		imessage(error(-1000, $result), '', 'ajax');
	}
	kanjia_cron();
	$records = kanjia_get_activitylist();
	$result = array(
		'records' => $records
	);

	$cateid = intval($_GPC['cateid']);
	if($cateid > 0) {
		$category = kanjia_get_cate($cateid);
		if(empty($category)) {
			imessage(error(-1, '砍价分类不存在'), '', 'ajax');
		} else {
			$result['category'] = $category;
		}
	} else {
		$kanjia_navs = kanjia_get_categorys();
		$navs = array_chunk($kanjia_navs, 10);
		$result['navs'] = $navs;
	}
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'detail') {
	$_W['_fnav'] = 1;
	if($_config_plugin['basic']['status']['kanjia'] != 1) {
		imessage(error(-1, '砍价功能暂时关闭，敬请关注'), '', 'ajax');
	}
	$is_black = member_is_in_black('kanjia');
	if($is_black) {
		$result['black_member'] = array(
			'status' => $is_black,
			'tip' => $_config_plugin['basic']['black_tip']
		);
		imessage(error(-1000, $result), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	$activity = kanjia_get_activity($id, 'all');
	$comment = gohome_get_goods_comment($id, 'kanjia');
	if(empty($activity)) {
		imessage(error(-1, '活动不存在或已删除'), '', 'ajax');
	}
	$store = store_fetch($activity['sid'], array('id', 'title', 'telephone', 'address', 'forward_mode', 'forward_url', 'location_x', 'location_y'));
	if(!empty($store)) {
		$store['url'] = store_forward_url($store['id'], $store['forward_mode'], $store['forward_url']);
	}
	//参与状态
	$take_status = 0;
	//当前用户的参与信息
	$member_takeinfo = kanjia_member_takeinfo($activity['id']);
	if(!empty($member_takeinfo)) {
		$take_status = 1;
		$_W['_share'] = array(
			'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
			'desc' => "我正在参加最低{$activity['price']}{$_W['Lang']['dollarSignCn']}购买{$activity['name']}的活动，快来帮我砍价吧",
			'imgUrl' => $activity['thumb'],
			'link' => ivurl('/gohome/pages/kanjia/share', array('activityid' => $activity['id'], 'uid' => $member_takeinfo['uid']), true),
		);
		$sharedata = array(
			'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
			'desc' => "我正在参加最低{$activity['price']}{$_W['Lang']['dollarSignCn']}购买{$activity['name']}的活动，快来帮我砍价吧",
			'imageUrl' => $activity['thumb'],
			'path' => "/gohome/pages/kanjia/share?activityid={$activity['id']}&uid={$member_takeinfo['uid']}",
		);
	} else {
		$_W['_share'] = array(
			'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
			'desc' => $activity['share']['share_detail'],
			'imgUrl' => tomedia($activity['share']['share_thumb']),
			'link' => ivurl('/gohome/pages/kanjia/detail', array('id' => $activity['id']), true),
		);
		$sharedata = array(
			'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
			'desc' => $activity['share']['share_detail'],
			'imageUrl' => tomedia($activity['share']['share_thumb']),
			'path' => "/gohome/pages/kanjia/detail?id={$activity['id']}",
		);
	}

	gohome_update_activity_flow('kanjia', $id, 'looknum');
	$result = array(
		'activity' => $activity,
		'store' => $store,
		'take_status' => $take_status,
		'member_takeinfo' => $member_takeinfo,
		'comment' => $comment['comment'],
		'danmu' => gohome_get_danmu($id, 'kanjia'),
		'sharedata' => $sharedata,
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'create') {
	if($_config_plugin['basic']['status']['kanjia'] != 1) {
		imessage(error(-1, '砍价功能暂时关闭，敬请关注'), '', 'ajax');
	}
	$activityid = intval($_GPC['activityid']);
	$activity = kanjia_get_activity($activityid);
	if(empty($activity)) {
		imessage(error(-1, '活动不存在或已删除'), '', 'ajax');
	}
	$check = kanjia_check_activity_order_num($activity);
	if($check['errno']) {
		imessage($check, '','ajax');
	}
	//检测用户目前是否能参与
	$takeinfo = kanjia_member_takeinfo($activityid);
	if(!empty($takeinfo)) {
		imessage(error(-1, '已参加该砍价活动，且仍未完成'), '', 'ajax');
	}
	$update = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $activity['agentid'],
		'activityid' => $activity['id'],
		'sid' => $activity['sid'],
		'uid' => $_W['member']['uid'],
		'status' => 1,
		'price' => $activity['oldprice'],
		'createtime' => TIMESTAMP,
		'updatetime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_kanjia_userlist', $update);
	imessage(error(0, $_W['member']['uid']), '', 'ajax');
}

elseif($op == 'share') {
	$activityid = intval($_GPC['activityid']);
	$activity = kanjia_get_activity($activityid, 'all');
	if(empty($activity)) {
		imessage(error(-1, '活动不存在或已删除'), '', 'ajax');
	}
	$uid = intval($_GPC['uid']);
	$takeinfo = kanjia_member_takeinfo($activityid, $uid);
	if(empty($takeinfo)) {
		imessage(error(-1, '暂无参与该活动的记录'), '', 'ajax');
	}
	$_W['_share'] = array(
		'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
		'desc' => "我正在参加最低{$activity['price']}{$_W['Lang']['dollarSignCn']}购买{$activity['name']}的活动，快来帮我砍价吧",
		'imgUrl' => $activity['thumb'],
		'link' => ivurl('/gohome/pages/kanjia/share', array('activityid' => $activity['id'], 'uid' => $takeinfo['uid']), true),
	);
	$sharedata = array(
		'title' => !empty($activity['share']['share_title']) ? $activity['share']['share_title'] : $activity['name'],
		'desc' => "我正在参加最低{$activity['price']}{$_W['Lang']['dollarSignCn']}购买{$activity['name']}的活动，快来帮我砍价吧",
		'imageUrl' => $activity['thumb'],
		'path' => "/gohome/pages/kanjia/share?activityid={$activity['id']}&uid={$takeinfo['uid']}",
	);
	$more = kanjia_get_activitylist();
	$self = 1;
	if($uid != $_W['member']['uid']) {
		$self = 0;
	}
	$rank = kanjia_get_helper($takeinfo['id'], 'bargainprice desc');
	$result = array(
		'self' => $self,
		'activity' => $activity,
		'takeinfo' => $takeinfo,
		'more' => $more,
		'rank' => $rank,
		'sharedata' => $sharedata
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'bargain') {
	$activityid = intval($_GPC['activityid']);
	$uid = intval($_GPC['uid']);
	$check = kanjia_bargain_check($activityid, $uid);
	if($check['errno']) {
		imessage($check, '', 'ajax');
	} else {
		$activity = $check['message']['activity'];
		$takeinfo = $check['message']['takeinfo'];
		$bargainprice = $check['message']['bargainprice'];
		$afterprice = $takeinfo['price'] - $bargainprice;
		$update = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $activity['agentid'],
			'activityid' => $activity['id'],
			'userid' => $takeinfo['id'],
			'authorid' => $takeinfo['uid'],
			'uid' => $_W['member']['uid'],
			'bargainprice' => $bargainprice,
			'afterprice' => $afterprice,
			'createtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_kanjia_helprecord', $update);
		$id = pdo_insertid();
		if($id > 0) {
			pdo_update('tiny_wmall_kanjia_userlist', array('price' => $afterprice, 'updatetime' => $update['createtime']), array('id' => $takeinfo['id']));
		}
		$update['id'] = $id;
		$update['createtime_cn'] = date('Y-m-d H:i:s', $update['createtime']);
		$update['nickname'] = $_W['member']['nickname'];
		$update['avatar'] = $_W['member']['avatar'];
		imessage(error(0, $update), '', 'ajax');
	}
}
