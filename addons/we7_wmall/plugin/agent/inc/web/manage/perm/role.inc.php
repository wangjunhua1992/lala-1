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
mload()->model('agent');
$all_perms = get_agent_perms(false, 'web');
if($op == 'list') {
	$_W['page']['title'] = '角色管理';
	$condition = ' where uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']): -1;
	if($status > -1) {
		$condition .= ' and status = :status';
		$params['status'] = $status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and rolename like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_perm_role') . $condition, $params);
	$roles = pdo_fetchall('select * from ' . tablename('tiny_wmall_perm_role') . $condition .' order by id desc limit '. ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$user_nums = pdo_fetchall('select count(*) as total, roleid from ' . tablename('tiny_wmall_perm_user') . ' where uniacid = :uniacid and agentid = :agentid group by roleid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'roleid');
}

if($op == 'post') {
	$_W['page']['title'] = '编辑角色';
	$id = intval($_GPC['id']);
	if($_W['ispost']) {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'rolename' => trim($_GPC['rolename']),
			'status' => intval($_GPC['status']),
			'perms' => implode(',', $_GPC['perms'])
		);
		if($id > 0) {
			pdo_update('tiny_wmall_perm_role', $insert, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_perm_role', $insert);
		}
		imessage(error(0, '编辑角色成功'), iurl('perm/role/list'), 'ajax');
	}
	if($id > 0) {
		$role = pdo_get('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		$role['perms'] = explode(',', $role['perms']);
	}
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_perm_role', array('status' => $status), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_perm_role', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		pdo_update('tiny_wmall_perm_user', array('roleid' => 0), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'roleid' => $id));
	}
	imessage(error(0, '删除角色成功'), '', 'ajax');
}

include itemplate('perm/role');