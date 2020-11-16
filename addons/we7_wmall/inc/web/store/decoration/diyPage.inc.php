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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']): 'list';
if($ta == 'list') {
	$_W['page']['title'] = '门店自定义页面';
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid
	);

	$type = intval($_GPC['type']);
	if(in_array($type, array(6, 7))) {
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	} else {
		$condition .= ' and type != :type';
		$params[':type'] = 'home';
	}

	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_page') .  $condition, $params);
	$pages = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_page') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	include itemplate('store/decoration/diyPage');
}

elseif($ta == 'post') {
	$_W['page']['title'] = '新建门店自定义页面';
	$id = intval($_GPC['id']);
	$type = intval($_GPC['type']);
	$store_id = $sid;
	if($id > 0) {
		$_W['page']['title'] = '编辑门店自定义页面';
		mload()->model('page');
		$page = store_page_get($sid, $id, false);
	}
	pload()->model('diypage');
	$diymenus = diypage_menus(2);
	$activitys = store_all_activity();
	$plugins = get_available_plugin();
	if($_W['ispost']) {
		$data = $_GPC['data'];
		$diydata = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'name' => $data['page']['name'],
			'type' => $data['page']['type'],
			'data' => base64_encode(json_encode($_GPC['data'])),
			'addtime' => TIMESTAMP,
		);

		if(!check_plugin_exist('diypage')) {
			imessage(error(-1, '注意：自定义DIY功能目前仅对购买过"平台装修"插件的客户开放'), '', 'ajax');
		}
		if(!empty($id)) {
			pdo_update('tiny_wmall_store_page', $diydata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			pdo_insert('tiny_wmall_store_page', $diydata);
			$id = pdo_insertid();
		}
		imessage(error(0, '编辑成功'), iurl('store/decoration/diyPage/post', array('id' => $id)), 'ajax');
	}
	include itemplate('store/decoration/diyPage');
}

elseif($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_store_page', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除门店自定义页面成功'), ireferer(), 'ajax');
}

elseif($ta == 'copy') {
	$id = intval($_GPC['id']);
	$page = pdo_get('tiny_wmall_store_page', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($page)) {
		imessage(error(-1, '门店自定义页面不存在或已删除'), '', 'ajax');
	}
	$page['name'] = $page['name'] . "-复制";
	unset($page['id']);
	pdo_insert('tiny_wmall_store_page', $page);
	imessage(error(0, '复制门店自定义页面成功'), '', 'ajax');
}
