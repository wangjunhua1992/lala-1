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
load()->func('communication');
global $_W, $_GPC;
$sid = $_GPC['store_id'];
if(empty($sid)) {
	$sid = $_GPC['_sid'];
}
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'build';

if($ta == 'build') {
	if($_W['account']['level'] != 4) {
		if($_W['isajax']) {
			imessage(error(-1, '您的公众号没有创建二维码的权限'), ireferer(), 'ajax');
		} else {
			imessage('您的公众号没有创建二维码的权限', ireferer(), 'error');
		}
	}
	$type = trim($_GPC['type']);
	$store = store_fetch($sid);
	if(empty($store)) {
		if($_W['isajax']) {
			imessage(error(-1, '门店不存在或已删除'), ireferer(), 'ajax');
		} else {
			imessage('门店不存在或已删除', ireferer(), 'error');
		}
	}

	$table_id = intval($_GPC['table_id']);
	$types = array(
		'store' => array(
			'scene_str' => "we7_wmall__store_{$sid}",
			'keyword' => "we7_wmall__store_{$sid}",
			'name' => "{$store['title']}门店二维码",
		),
		'assign' => array(
			'scene_str' => "we7_wmall__assign_{$sid}",
			'keyword' => "we7_wmall__assign_{$sid}",
			'name' => "{$store['title']}排号二维码",
		),
		'table' => array(
			'scene_str' => "we7_wmall__table_{$sid}_{$table_id}",
			'keyword' => "we7_wmall__table_{$sid}_{$table_id}",
			'name' => "{$store['title']}桌台{$table_id}二维码",
		),
	);
	$key = $types[$type]['scene_str'];
	$rule = pdo_get('rule_keyword', array('uniacid' => $_W['uniacid'], 'content' => $key));
	if(!empty($rule)) {
		pdo_delete('rule_keyword', array('uniacid' => $_W['uniacid'], 'id' => $rule['id']));
		pdo_delete('rule', array('uniacid' => $_W['uniacid'], 'id' => $rule['rid']));
		pdo_delete('tiny_wmall_reply', array('uniacid' => $_W['uniacid'], 'id' => $rule['rid']));
	}
	pdo_delete('qrcode', array('uniacid' => $_W['uniacid'], 'scene_str' => $key));

	//生成二维码
	$acc = WeAccount::create($_W['acid']);
	$barcode = array(
		'expire_seconds' => '',
		'action_name' => '',
		'action_info' => array(
			'scene' => array(),
		),
	);

	$barcode['action_info']['scene']['scene_str'] = $types[$type]['scene_str'];
	$barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
	$result = $acc->barCodeCreateFixed($barcode);
	if(is_error($result)) {
		if($_W['isajax']) {
			imessage(error(-1, "生成微信二维码出错,错误详情:{$result['message']}"), ireferer(), 'ajax');
		} else {
			imessage("生成微信二维码出错,错误详情:{$result['message']}", ireferer(), 'error');
		}
	}
	$qrcode = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'qrcid' => '',
		'scene_str' => $barcode['action_info']['scene']['scene_str'],
		'keyword' => $types[$type]['keyword'],
		'name' =>  $types[$type]['name'],
		'model' => 1,
		'ticket' => $result['ticket'],
		'url' => $result['url'],
		'expire' => $result['expire_seconds'],
		'createtime' => TIMESTAMP,
		'status' => '1',
		'type' => 'we7_wmall',
	);
	pdo_insert('qrcode', $qrcode);

	$rule = array(
		'uniacid' => $_W['uniacid'],
		'name' =>  $types[$type]['name'],
		'module' => 'we7_wmall',
		'status' => 1
	);
	pdo_insert('rule', $rule);
	$rid = pdo_insertid();

	$keyword = array(
		'uniacid' => $_W['uniacid'],
		'module' => 'we7_wmall',
		'content' => $types[$type]['keyword'],
		'status' => 1,
		'type' => 1,
		'displayorder' => 1,
		'rid' => $rid
	);

	pdo_insert('rule_keyword', $keyword);
	$kid = pdo_insertid();

	$data = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'type' => $type,
		'rid' => $rid,
		'table_id' => $table_id
	);
	pdo_insert('tiny_wmall_reply', $data);
	$reply_id = pdo_insertid();

	$qrcode = array(
		'ticket' => $result['ticket'],
		'url' => $result['url'],
	);
	if($type == 'store') {
		pdo_update('tiny_wmall_store', array('wechat_qrcode' => iserializer($qrcode)), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	} elseif($type == 'assign') {
		pdo_update('tiny_wmall_store', array('assign_qrcode' => iserializer($qrcode)), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	} elseif($type == 'table') {
		pdo_update('tiny_wmall_tables', array('qrcode' => iserializer($qrcode), 'version' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $table_id));
		if($_W['isajax']) {
			imessage(error(0, '生成桌号二维码成功'), iurl('store/tangshi/table/list'), 'ajax');
		} else {
			imessage('生成桌号二维码成功', iurl('store/tangshi/table/list'), 'success');
		}
	}
	if($_W['isajax']) {
		imessage(error(0, '生成微信二维码成功'), ireferer(), 'ajax');
	} else {
		imessage('生成微信二维码成功', ireferer(), 'success');
	}
}