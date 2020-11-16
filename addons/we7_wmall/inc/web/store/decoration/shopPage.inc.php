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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index'){
	$_W['page']['title'] = '海报';
	$poster = store_get_data($sid, 'shopPage');
}

if($ta == 'post'){
	$_W['page']['title'] = '添加海报';
	$key = trim($_GPC['key']);
	if(!empty($key)){
		$posters = store_get_data($sid, 'shopPage');
		$poster = $posters[$key];
		foreach ($poster['goods'] as $val) {
			$good = pdo_fetch('select id, thumb, total, price, title from' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $val));
			$goods[] = $good;
		}
		$poster['goods'] = $goods;
	}
	
	if($_W['ispost']){
		if(empty($_GPC['title'])){
			imessage(error(-1, '请输入海报名称'), '', 'ajax');
		}
		$posters = store_get_data($sid, 'shopPage');
		if(!empty($_GPC['key'])){
			unset($posters[$_GPC['key']]);
		}
		$id = date('YmdHis', time()).random(2, true);
		$posters[$id] = array(
			'id' => $id,
			'title' => $_GPC['title'],
			'thumb' => $_GPC['thumb'],
			'wxapp_link' => $_GPC['wxapp_link'],
			'goods' => $_GPC['goods_id']
		);
		store_set_data($sid, 'shopPage', $posters);
		imessage(error(0, '设置商品海报成功'), iurl('store/decoration/shopPage/index'), 'ajax');
	}
}

if($ta == 'del'){
	$posters = store_get_data($sid, 'shopPage');
	if(!empty($_GPC['key'])){
		unset($posters[$_GPC['key']]);
		store_set_data($sid, 'shopPage', $posters);
	}
	imessage(error(0,'删除海报成功'), '', 'ajax');
}

include itemplate('store/decoration/index');