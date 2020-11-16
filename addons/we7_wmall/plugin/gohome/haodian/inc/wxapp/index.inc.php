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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth(true);

if($op == 'index') {
	mload()->model('diy');
	if($_config_wxapp['diy']['use_diy_haodian'] != 1) {
		$pageOrid = get_wxapp_defaultpage('haodian');
		$config_share = $_config_plugin['share'];
		$share = array(
			'title' => $config_share['title'],
			'desc' => $config_share['detail'],
			'link' => empty($config_share['link']) ? ivurl('gohome/pages/haodian/index', array(), true) : $config_share['link'],
			'imgUrl' => tomedia($config_share['thumb'])
		);
	} else {
		//使用自定义页面
		$pageOrid = $_config_wxapp['diy']['shopPage']['haodian'];
		if(empty($pageOrid)) {
			imessage(error(-1, '未设置好店DIY页面'), '', 'ajax');
		}
	}
	$page = get_wxapp_diy($pageOrid, true, array('pagepath' => 'haodian'));
	if(empty($page)) {
		imessage(error(-1, '页面不能为空'), '', 'ajax');
	}
	$_W['_share'] = array(
		'title' => $page['data']['page']['title'],
		'desc' => $page['data']['page']['desc'],
		'link' => ivurl('gohome/pages/haodian/index', array(), true),
		'imgUrl' => tomedia($page['data']['page']['thumb'])
	);
	if($_config_wxapp['diy']['use_diy_haodian'] != 1) {
		$_W['_share'] = $share;
	}
	$default_location = array();
	if(empty($_GPC['lat']) || empty($_GPC['lng'])) {
		$config_takeout = $_W['we7_wmall']['config']['takeout']['range'];
		if(!empty($config_takeout['map']['location_x']) && !empty($config_takeout['map']['location_y'])) {
			$_GPC['lat'] = $config_takeout['map']['location_x'];
			$_GPC['lng'] = $config_takeout['map']['location_y'];
			$default_location = array(
				'location_x' => $config_takeout['map']['location_x'],
				'location_y' => $config_takeout['map']['location_y'],
				'address' => $config_takeout['city'],
			);
		}
	}
	$result = array(
		'cart_sum' => $page['is_show_cart'] == 1 ? get_member_cartnum() : 0,
		'config' => $_W['we7_wmall']['config']['mall'],
		'diy' => $page,
	);
	$result['config']['default_location'] = $default_location;
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'store') {
	$store = haodian_store_fetchall(array('get_activity' => 1));
	$result = array(
		'store' => $store['store'],
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'detail') {
	$_W['_fnav'] = 1;
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid);
	if(empty($store)){
		imessage(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$store['is_favorite'] = is_favorite_store($sid, $_W['member']['uid']);
	mload()->model('coupon');
	$coupon = coupon_collect_member_available($sid);
	if(!empty($coupon)) {
		$coupon['can_collect'] = 1;
		$coupon['endtime_cn'] = date('Y-m-d', $coupon['endtime']);
		$coupon['collect_percent'] = round($coupon['dosage'] / $coupon['amount'], 2) * 100;
	}
	$records = pdo_fetchall('select a.id,a.discount,a.condition,a.endtime,a.sid,b.title from' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b on a.couponid = b.id where a.uniacid = :uniacid and a.status = 1 and a.sid = :sid and a.uid = :uid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if(!empty($records)) {
		foreach($records as &$record) {
			$record['endtime_cn'] = date('Y-m-d', $record['endtime']);
		}
		$coupon['record'] = $records;
	}
	$filter = array(
		'status' => 1,
		'sid' => $sid,
		'psize' => 10,
	);
	pload()->model('kanjia');
	$kanjia = kanjia_get_activitylist($filter);
	pload()->model('pintuan');
	$pintuan = pintuan_get_activitylist($filter);
	pload()->model('seckill');
	$seckill = seckill_allgoods($filter);
	$comment = haodian_comment_fetchall($sid);
	if(!empty($comment)) {
		$comment = $comment['comment'];
	}
	$can_comment = haodian_member_can_comment($sid);
	$_W['_share'] = array(
		'title' => $store['title'],
		'desc' => $store['content'],
		'imgUrl' => tomedia($store['logo']),
		'link' => ivurl('/gohome/pages/haodian/detail', array('sid' => $store['id']), true),
	);
	$sharedata = array(
		'title' => $store['title'],
		'desc' => $store['content'],
		'imgUrl' => tomedia($store['logo']),
		'path' => "/gohome/pages/haodian/detail?sid={$store['id']}",
	);
	$result = array(
		'store' => $store,
		'coupon' => $coupon,
		'kanjia' => $kanjia,
		'pintuan' => $pintuan,
		'seckill' => $seckill,
		'comment' => $comment,
		'can_comment' => $can_comment,
		'sharedata' => $sharedata
	);

	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'comment') {
	$sid = intval($_GPC['sid']);
	$comment = haodian_comment_fetchall($sid);
	if(!empty($comment)) {
		$comment = $comment['comment'];
	}
	$result = array(
		'comment' => $comment
	);
	imessage(error(0, $result), '', 'ajax');

}

elseif($op == 'category') {
	$haodian_cid = intval($_GPC['haodian_cid']);
	$category = pdo_get('tiny_wmall_haodian_category', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $haodian_cid), array('id', 'title'));
	$categorys = pdo_fetchall('select id, title as text from ' . tablename('tiny_wmall_haodian_category') . ' where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':parentid' => 0));
	if(!empty($categorys)) {
		foreach($categorys as &$cate) {
			$cate['children'] = pdo_fetchall('select id, title as text from ' . tablename('tiny_wmall_haodian_category') . ' where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':parentid' => $cate['id']));
			//此处的数据处理视为了兼容小程序的van-tree-select组件
			if(!empty($cate['children'])) {
				foreach($cate['children'] as &$child) {
					$child['id'] = intval($child['id']);
				}
			}
		}
	}
	$store = haodian_store_fetchall(array('get_activity' => 1));
	$result = array(
		'category' => $category,
		'categorys' => $categorys,
		'store' => $store['store']
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'favorite') {
	$config_mall = $_W['we7_wmall']['config']['mall'];
	$id = intval($_GPC['min']);
	$condition = " where a.uniacid = :uniacid and a.uid = :uid and is_haodian = 1";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':uid' => $_W['member']['uid'],
	);

	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}
	$stores = pdo_fetchall('select a.id as aid, b.* from ' . tablename('tiny_wmall_store_favorite') . ' as a inner join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id' . $condition . ' order by a.id desc limit 10', $params, 'aid');
	$min = 0;
	if(!empty($stores)) {
		$store_label = store_category_label();
		foreach($stores as &$val) {
			$val['logo'] = tomedia($val['logo']);
			$val['haodian_starttime_cn'] = date('Y-m-d H:i', $val['haodian_starttime']);
			$val['haodian_endtime_cn'] = date('Y-m-d H:i', $val['haodian_endtime']);
			$val['scores'] = score_format($val['score']);
			$val['haodian_score'] = floatval($val['haodian_score']);
			$val['business_hours'] = iunserializer($val['business_hours']);
			$val['is_in_business_hours'] = intval($val['is_in_business']);
			if(isset($val['business_hours'])) {
				if($val['is_in_business'] == 1) {
					$val['is_in_business_hours'] = $val['is_in_business_hours'] && store_is_in_business_hours($val['business_hours']);
				}
				$hour = array();
				foreach($val['business_hours'] as $li) {
					if(!is_array($li)) continue;
					$hour[] = "{$li['s']}~{$li['e']}";
				}
				$val['business_hours_cn'] = implode(',', $hour);
			}
			$val['haodian_data'] = iunserializer($val['haodian_data']);
			$val['haodian_tags'] = array();
			if(!empty($val['haodian_data']['tags'])) {
				$val['haodian_tags'] = $val['haodian_data']['tags'];
			}
			$val['activity'] = array_values(haodian_get_activity($val['id']));
		}
		$min = min(array_keys($stores));
	}
	$stores = array_values($stores);
	$respon = array('errno' => 0, 'message' => $stores, 'min' => $min);
	imessage($respon, '', 'ajax');
}