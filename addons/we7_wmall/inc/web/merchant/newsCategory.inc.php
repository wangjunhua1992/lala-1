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
	$_W['page']['title'] = '资讯分类';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_update('tiny_wmall_news_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage(error(0, '编辑成功'), ireferer(), 'ajax');
	}
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0){
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_news_category') .  $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '添加资讯分类';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$item = pdo_get('tiny_wmall_news_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']) {
		$_GPC['title'] = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '分类名称不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(!$id) {
			pdo_insert('tiny_wmall_news_category', $data);
		} else {
			pdo_update('tiny_wmall_news_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		imessage(error(0, '编辑分类成功'),iurl('merchant/newsCategory/list'), 'ajax');
	}

}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_news_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除分类成功'), '', 'ajax');
}
include itemplate('merchant/newsCategory');
