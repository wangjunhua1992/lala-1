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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
$sid = intval($_GPC['__mg_sid']);

if($ta == 'list') {
	$_W['page']['title'] = '公告列表';
	$condition = ' as b on a.id = b.notice_id where b.uid = :uid and a.uniacid = :uniacid and a.agentid = :agentid and a.type = :type and a.status = 1';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':uid' => $_W['manager']['id'],
		':type' => 'store',
		':agentid' => $store['agentid'],
	);

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= " and a.id < :id";
		$params[':id'] = $id;
	}

	$data = pdo_fetchall('select a.*,b.uid,b.is_new from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . $condition . ' order by id desc, displayorder desc limit 10', $params, 'id');

	$min = 0;
	if(!empty($data)) {
		foreach ($data as &$val) {
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
		}
		$min = min(array_keys($data));
	}
	if($_W['ispost']) {
		$data = array_values($data);
		$respon = array('errno' => 0, 'message' => $data, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($ta == 'detail') {
	$_W['page']['title'] = '公告详情';
	$notice = pdo_get('tiny_wmall_notice', array('id' => $_GPC['id'], 'uniacid' => $_W['uniacid'], 'status' => 1, 'type' => 'store'));
	if(empty($notice)) {
		imessage('该消息不存在或已删除', 'manage/news/notice/index', 'error');
	}
	pdo_update('tiny_wmall_notice_read_log', array('is_new' => 0), array('notice_id' => $_GPC['id'], 'uid' => $_W['manager']['id']));
}


include itemplate('news/notice');

