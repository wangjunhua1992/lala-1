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

function cover_build($params = array()) {
	global $_W;
	if(empty($params['module'])) {
		$params['module'] = 'we7_wmall';
	}
	$where = '';
	$condition_params = array(':uniacid' => $_W['uniacid'], ':module' => $params['module'], ':do' => $params['do']);
	$cover = pdo_fetch("SELECT * FROM " . tablename('cover_reply') . " WHERE `module` = :module AND uniacid = :uniacid and do = :do {$where}", $condition_params);
	if(empty($cover['rid'])) {
		$rule = array(
			'uniacid' => $_W['uniacid'],
			'name' => $params['title'],
			'module' => 'cover',
			'status' => $params['status'],
		);
		pdo_insert('rule', $rule);
		$rid = pdo_insertid();
	} else {
		$rule = array(
			'name' => $params['title'],
		);
		pdo_update('rule', $rule, array('id' => $cover['rid']));
		$rid = $cover['rid'];
	}
	if (!empty($rid)) {
		//更新，添加，删除关键字
		$sql = 'DELETE FROM '. tablename('rule_keyword') . ' WHERE `rid`=:rid AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':rid'] = $rid;
		$pars[':uniacid'] = $_W['uniacid'];
		pdo_query($sql, $pars);

		$keywordrow = array(
			'rid' => $rid,
			'uniacid' => $_W['uniacid'],
			'module' => 'cover',
			'status' => $params['status'],
			'displayorder' => 0,
			'type' => 1,
			'content' => $params['keyword'],
		);
		pdo_insert('rule_keyword', $keywordrow);
	}
	$entry = array(
		'uniacid' => $_W['uniacid'],
		'multiid' => 0,
		'rid' => $rid,
		'title' => $params['title'],
		'description' => $params['description'],
		'thumb' => $params['thumb'],
		'url' => $params['url'],
		'do' => $params['do'],
		'module' => $params['module'],
	);

	if (empty($cover['id'])) {
		pdo_insert('cover_reply', $entry);
	} else {
		pdo_update('cover_reply', $entry, array('id' => $cover['id']));
	}
	return true;
}

function cover_fetch($params = array()) {
	global $_W;
	if(empty($params['module'])) {
		$params['module'] = 'we7_wmall';
	}
	$where = '';
	$params = array(':uniacid' => $_W['uniacid'], ':module' => $params['module'], ':do' => $params['do']);
	$cover = pdo_fetch("SELECT * FROM " . tablename('cover_reply') . " WHERE `module` = :module AND uniacid = :uniacid and do = :do {$where}", $params);
	if(empty($cover)) {
		return array();
	}
	$keyword = pdo_get('rule_keyword', array('uniacid' => $_W['uniacid'], 'rid' => $cover['rid']));
	$cover['keyword'] = $keyword['content'];
	$cover['status'] = $keyword['status'];
	return $cover;
}

