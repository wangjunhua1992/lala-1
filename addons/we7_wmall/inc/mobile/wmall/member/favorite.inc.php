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
$_W['page']['title'] = '我的收藏';

$stores = pdo_fetchall('select a.id as aid, b.* from ' . tablename('tiny_wmall_store_favorite') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
$min = 0;
if(!empty($stores)) {
	$store_label = store_category_label();
	foreach($stores as &$da) {
		$da['business_hours'] = (array)iunserializer($da['business_hours']);
		$da['is_in_business_hours'] = store_is_in_business_hours($da['business_hours']);
		$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
		$da['activity'] = store_fetch_activity($da['id']);
		$da['activity']['num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
		$da['score_cn'] = round($da['score'] / 5, 2) * 100;
		$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
		if($da['label'] > 0) {
			$da['label_color'] = $store_label[$da['label']]['color'];
			$da['label_cn'] = $store_label[$da['label']]['title'];
		}
		if($da['delivery_fee_mode'] == 2) {
			$da['delivery_price'] = iunserializer($da['delivery_price']);
			$da['delivery_price'] = $da['delivery_price']['start_fee'];
		} elseif($da['delivery_fee_mode'] == 3) {
			$da['delivery_areas'] = iunserializer($da['delivery_areas']);
			if(!is_array($da['delivery_areas'])) {
				$da['delivery_areas'] = array();
			}
			$price = store_order_condition($da);
			$da['delivery_price'] = $price['delivery_price'];
			$da['send_price'] = $price['send_price'];
		}
	}
	$min = min(array_keys($stores));
}
include itemplate('member/favorite');