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
	$_W['page']['title'] = '帖子列表';
	$is_stick = isset($_GPC['is_stick'])? intval($_GPC['is_stick']) : '-1';
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$_GPC['starttime'] = $starttime;
	$_GPC['endtime'] = $endtime;
	if(!empty($_GPC['cid'])) {
		$cid = $_GPC['cid'];
		if(strexists($cid, ':')) {
			$cid = explode(':', $cid);
			$_GPC['parentid'] = intval($cid[0]);
			$_GPC['childid'] = intval($cid[1]);
		} else {
			$_GPC['parentid'] = intval($cid);
		}
	}
	$filter = $_GPC;
	$filter['orderby'] = 'addtime';
	$filter['psize'] = 20;
	$filter['status'] = -1;
	$informations = tongcheng_get_informations($filter);
	$information = $informations['informations'];
	$pager = $informations['pager'];
	$categorys = tongcheng_get_categorys();
}

elseif($op == 'order_list') {
	$_W['page']['title'] = '订单列表';
	$type = isset($_GPC['type'])? intval($_GPC['type']) : -1;
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$_GPC['starttime'] = $starttime;
	$_GPC['endtime'] = $endtime;
	$data = tongcheng_get_orders();
	$orders = $data['orders'];
	$pager = $data['pager'];
}

elseif($op == 'detail') {
	$_W['page']['title'] = '帖子详情';
	tongcheng_cron();
	$id = intval($_GPC['id']);
	$information = tongcheng_get_information($id);
	$status = $information['status'];
	$categorys = tongcheng_get_categorys(array('type' => 'parent&child'), array('id', 'title', 'parentid'));
	if($_W['ispost']) {
		$data = array(
			'mobile' => trim($_GPC['mobile']),
			'content' => trim($_GPC['content']),
			'looknum' => intval($_GPC['looknum']),
			'likenum' => intval($_GPC['likenum']),
			'sharenum' => intval($_GPC['sharenum']),
			'is_stick' => intval($_GPC['is_stick']),
			'status' => intval($_GPC['status']),
			'parentid' => intval($_GPC['category']['parentid']),
			'childid' => intval($_GPC['category']['childid'])
		);
		$data['thumbs'] = array();
		if(!empty($_GPC['thumbs'])) {
			foreach($_GPC['thumbs'] as $thumb) {
				if(empty($thumb)) continue;
				$data['thumbs'][] = $thumb;
			}
		}
		$data['thumbs'] = iserializer($data['thumbs']);
		if($data['is_stick'] == '1') {
			$overtime = trim($_GPC['overtime']);
			$data['overtime'] = strtotime($overtime);
		}
		pdo_update('tiny_wmall_tongcheng_information', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		imessage(error(0, '编辑帖子成功'), iurl('tongcheng/information/detail', array('id' => $id)), 'ajax');
	}
}

elseif($op == 'del') {
	$ids = $_GPC['id'];
	$result = tongcheng_information_delete($ids);
	imessage($result, '', 'ajax');
}

elseif($op == 'status') {
	$status = intval($_GPC['status']);
	$ids = $_GPC['id'];
	$result = tongcheng_information_update_status($ids, $status);
	imessage($result, ireferer(), 'ajax');
}

elseif($op == 'toblack') {
	mload()->model('member.extra');
	$uid = intval($_GPC['uid']);
	$status = member_to_black($uid, 'tongcheng');
	if($status) {
		imessage(error(0, '加入黑名单成功'), ireferer(), 'ajax');
	} else {
		imessage(error(-1, '加入黑名单失败'), ireferer(), 'ajax');
	}
}

include itemplate('information');