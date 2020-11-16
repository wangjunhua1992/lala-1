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
	$_W['page']['title'] = '资讯列表';
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . ' WHERE uniacid = :uniacid', array(':uniacid' =>  $_W['uniacid']));
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' WHERE a.uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0){
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	if(!empty($_GPC['cateid'])) {
		$condition .= ' and cateid = :cateid';
		$params[':cateid'] = intval($_GPC['cateid']);
	}
	$createtime = intval($_GPC['createtime']);
	if(!empty($createtime)) {
		$time = TIMESTAMP - $createtime*24*60*60;
		$condition .= ' and addtime > :time';
		$params[':time'] = $time ;
	}
	$atitle = $_GPC['title'];
	if(!empty($atitle)) {
		$condition .= " and a.title like '%{$atitle}%'";
	}
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_news') . 'as a left join' . tablename('tiny_wmall_news_category'). ' as b on a.cateid = b.id' . $condition, $params);
	$lists = pdo_fetchall('SELECT *,a.id as aid,a.title as atitle,b.title as btitle,a.displayorder as adisplayorder FROM ' . tablename('tiny_wmall_news') . ' as a left join'. tablename('tiny_wmall_news_category') . ' as b on a.cateid = b.id' . $condition . ' ORDER BY a.displayorder DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'click' => intval($_GPC['click'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_update('tiny_wmall_news', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '编辑成功'), ireferer(), 'ajax');
	}
}

if($op == 'post') {
	$_W['page']['title'] = '添加资讯';
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . ' WHERE uniacid = :uniacid ORDER BY id DESC', array(':uniacid' =>  $_W['uniacid']));
	$id = intval($_GPC['id']);
	if($id > 0) {
		$item = pdo_get('tiny_wmall_news', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']) {
		$_GPC['title'] = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '资讯名称不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'cateid' => intval($_GPC['cateid']),
			'desc' => trim($_GPC['desc']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'thumb' => trim($_GPC['thumb']),
			'author' => trim($_GPC['author']),
			'click' => intval($_GPC['click']),
			'is_display' => intval($_GPC['is_display']),
			'is_show_home' => intval($_GPC['is_show_home']),
			'displayorder' => intval($_GPC['displayorder']),
			'addtime' => TIMESTAMP,
		);
		if(!$id) {
			pdo_insert('tiny_wmall_news', $data);
		} else {
			pdo_update('tiny_wmall_news', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		imessage(error(0, '编辑资讯成功'), iurl('merchant/news/list'), 'ajax');
	}

}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_news', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除资讯成功'), '', 'ajax');
}

if($op == 'is_show_home') {
	$id = intval($_GPC['id']);
	$is_show_home = intval($_GPC['is_show_home']);
	pdo_update('tiny_wmall_news', array('is_show_home' => $is_show_home), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'is_display') {
	$id = intval($_GPC['id']);
	$is_display = intval($_GPC['is_display']);
	pdo_update('tiny_wmall_news', array('is_display' => $is_display), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}
include itemplate('merchant/news');
