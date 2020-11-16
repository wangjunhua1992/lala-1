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
	$_W['page']['title'] = '投诉列表';
	$condition = " where a.uniacid = :uniacid";
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (a.uid = :uid or b.nickname like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_complain') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition}", $params);
	$complain = pdo_fetchall('SELECT a.*,b.avatar,b.nickname FROM ' . tablename('tiny_wmall_complain') . ' as a left join ' . tablename('tiny_wmall_members') . " as b on a.uid = b.uid {$condition} order by a.id desc limit " . ($pindex - 1) * $psize.','.$psize, $params);
	$options = array(
		'cheat' => '网页包含欺诈信息（如：假红包）',
		'eroticism' => '网页包含欺色情信息',
		'violence' => '网页包含欺暴力恐怖信息',
		'politics' => '网页包含欺政治敏感信息',
		'privacy' => '网页在手机个人隐私信息（如：钓鱼链接）',
		'induce' => '网页包含诱导分享/关注性质的内容',
		'rumor' => '网页可能包含谣言信息',
	);
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'status') {
	mload()->model('member.extra');
	$uid = intval($_GPC['uid']);
	$status = member_to_black($uid, 'gohome');
	if($status) {
		imessage(error(0, '加入黑名单成功'), ireferer(), 'ajax');
	} else {
		imessage(error(-1, '加入黑名单失败'), ireferer(), 'ajax');
	}
}

include itemplate('complain');
