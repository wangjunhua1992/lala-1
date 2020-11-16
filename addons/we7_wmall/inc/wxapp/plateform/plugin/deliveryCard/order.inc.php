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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'order';

if($ta == 'order') {
	$condition = ' where a.uniacid = :uniacid and a.is_pay = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$status = intval($_GPC['status']);
	if($status == 0) {
		$condition .= ' and a.endtime <= :endtime';
		$params[':endtime'] = TIMESTAMP;
	} elseif($status == 1) {
		$condition .= ' and a.endtime > :endtime';
		$params[':endtime'] = TIMESTAMP;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (a.uid = :uid or b.realname like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}

	$page = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 15;
	$records = pdo_fetchall('SELECT a.*,b.realname,b.avatar,c.title as card_name FROM ' . tablename('tiny_wmall_delivery_cards_order') . 'as a left join' . tablename('tiny_wmall_members') . 'as b on a.uid = b.uid left join ' . tablename('tiny_wmall_delivery_cards') . "as c on a.card_id = c.id {$condition} order by a.id desc limit " . ($page - 1) * $psize.','.$psize, $params);
	if (!empty($records)){
		$pay_types = order_pay_types();
		foreach($records as &$val){
			$val['pay_type_cn'] = $pay_types[$val['pay_type']]['text'];
			$val['paytime_cn'] = date('Y-m-d', $val['paytime']);
			$val['starttime_cn'] = date('Y-m-d', $val['starttime']);
			$val['endtime_cn'] = date('Y-m-d', $val['endtime']);
			if ($val['endtime'] <= time()){
				$val['card_status'] = '已到期';
			} else {
				$val['card_status'] = '生效中';
			}
			$val['avatar'] = tomedia($val['avatar']);
		}
	}
	$result = array(
		'records' => $records
	);
	imessage(error(0, $result), '', 'ajax');
}