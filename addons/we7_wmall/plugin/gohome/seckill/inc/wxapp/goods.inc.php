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
		imessage(error(41200, '您所在的区域暂未开启抢购功能,建议您手动搜索地址或切换到此前常用的地址再试试'), '', 'ajax');
	}
	if($_config_plugin['basic']['status']['seckill'] != 1) {
		imessage(error(-1, '抢购功能暂时关闭，敬请关注'), '', 'ajax');
	}
	seckill_cron();
	$records = seckill_allgoods();
	$result = array(
		'records' => $records,
	);

	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$category = seckill_goods_cate($cid);
		if(empty($category)) {
			imessage(error(-1, '抢购分类不存在'), '', 'ajax');
		} else {
			$result['category'] = $category;
		}
	} else {
		$navs = seckill_goods_categorys();
		$navs = array_chunk($navs, 10);
		$result['navs'] = $navs;
	}
	imessage(error(0, $result), '', 'ajax');
}

elseif($op == 'detail') {
	$_W['_fnav'] = 1;
	if($_config_plugin['basic']['status']['seckill'] != 1) {
		imessage(error(-1, '抢购功能暂时关闭，敬请关注'), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	$goods = seckill_goods($id, 'all');
	if(empty($goods)) {
		imessage(error(-1, '商品不存在或已删除'), '', 'ajax');
	}
	gohome_update_activity_flow('seckill', $id, 'looknum');
	$filter = array(
		'page' => 1,
		'psize' => 6,
		'status' => 1
	);
	$_W['_share'] = array(
		'title' => $goods['share']['share_title'],
		'desc' => $goods['share']['share_detail'],
		'imgUrl' => tomedia($goods['share']['share_thumb']),
		'link' => ivurl('/gohome/pages/seckill/detail', array('id' => $goods['id']), true),
	);
	$sharedata = array(
		'title' => $goods['share']['share_title'],
		'desc' => $goods['share']['share_detail'],
		'imageUrl' => tomedia($goods['share']['share_thumb']),
		'path' => "/gohome/pages/seckill/detail?id={$goods['id']}",
	);
	$recommend = seckill_allgoods($filter);
	$comment = gohome_get_goods_comment($id, 'seckill');
	$result = array(
		'goods' => $goods,
		'recommend' => $recommend,
		'danmu' => gohome_get_danmu($id, 'seckill'),
		'comment' => $comment['comment'],
		'sharedata' => $sharedata,
	);
	imessage(error(0, $result), '', 'ajax');
}