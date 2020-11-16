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
mload()->model('print');

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '打印机列表';

	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		foreach($data as &$da) {
			if(!empty($da['print_no'])) {
				if(in_array($da['type'], array('feie', 'feie_new', '365', '365_s2'))) {
					$da['status_cn'] = print_query_printer_status($da);
				} else {
					$da['status_cn'] = '打印机不支持查询状态';
				}
			} else {
				$da['status_cn'] = '未知';
			}
		}
	}
	$types = print_printer_types();
}

if($ta == 'post') {
	$_W['page']['title'] = '编辑打印机';

	$id = intval($_GPC['id']);
	if($id > 0) {
		$item = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	}
	if(!empty($item)) {
		$item['print_label'] = explode(',', $item['print_label']);
		$item['data'] = iunserializer($item['data']);
	} else {
		$item = array('status' => 1, 'print_nums' => 1, 'type' => 'feie', 'print_label' => array());
	}
	if($_W['ispost']) {
		$_GPC['feie_user'] = strexists($_GPC['feie_user'], '*') ? $item['member_code'] : $_GPC['feie_user'];
		$_GPC['feie_ukey'] = strexists($_GPC['feie_ukey'], '*') ? $item['api_key'] : $_GPC['feie_ukey'];
		$_GPC['jinyun_member_code'] = strexists($_GPC['jinyun_member_code'], '*') ? $item['member_code'] : $_GPC['jinyun_member_code'];
		$_GPC['jinyun_api_key'] = strexists($_GPC['jinyun_api_key'], '*') ? $item['api_key'] : $_GPC['jinyun_api_key'];
		$_GPC['print_no'] = strexists($_GPC['print_no'], '*') ? $item['print_no'] : $_GPC['print_no'];
		$_GPC['jinyun_key'] = strexists($_GPC['jinyun_key'], '*') ? $item['key'] : $_GPC['jinyun_key'];
		$_GPC['key'] = strexists($_GPC['key'], '*') ? $item['key'] : $_GPC['key'];
		$_GPC['member_code'] = strexists($_GPC['member_code'], '*') ? $item['member_code'] : $_GPC['member_code'];
		$_GPC['userid'] = strexists($_GPC['userid'], '*') ? $item['member_code'] : $_GPC['userid'];
		$_GPC['api_key'] = strexists($_GPC['api_key'], '*') ? $item['api_key'] : $_GPC['api_key'];

		$data['uniacid'] = $_W['uniacid'];
		$data['sid'] = $sid;
		$data['type'] = trim($_GPC['type']);
		$data['status'] = intval($_GPC['status']);
		$data['name'] = !empty($_GPC['name']) ? trim($_GPC['name']) : imessage(error(-1, '打印机名称不能为空'), '', 'ajax');
		$data['print_no'] = !empty($_GPC['print_no']) ? trim($_GPC['print_no']) : imessage(error(-1, '机器号不能为空'), '', 'ajax');
		$data['key'] = trim($_GPC['key']);
		$data['api_key'] = trim($_GPC['api_key']);
		$data['member_code'] = trim($_GPC['member_code']);
		if($data['type'] == 'yilianyun' || $data['type'] == 'qiyun') {
			$data['member_code'] = trim($_GPC['userid']);
		}
		if($data['type'] == 'feie_new' || $data['type'] == 'feie') {
			//兼容飞鹅新版需要的USER，UKEY，
			$data['member_code'] = trim($_GPC['feie_user']);
			$data['api_key'] = trim($_GPC['feie_ukey']);
			$data['language'] = trim($_GPC['language']);
		}
		if($data['type'] == 'jinyun') {
			$data['member_code'] = trim($_GPC['jinyun_member_code']);
			$data['api_key'] = trim($_GPC['jinyun_api_key']);
			$data['key'] = trim($_GPC['jinyun_key']);
		}
		$data['print_nums'] = intval($_GPC['print_nums']) ? intval($_GPC['print_nums']) : 1;
		$data['qrcode_type'] = trim($_GPC['qrcode_type']);
		$data['qrcode_link'] = '';
		if(!empty($_GPC['qrcode_link']) && (strexists($_GPC['qrcode_link'], 'http://') || strexists($_GPC['qrcode_link'], 'https://'))) {
			$data['qrcode_link'] = trim($_GPC['qrcode_link']);
		}
		$data['print_header'] = trim($_GPC['print_header']);
		$data['print_footer'] = trim($_GPC['print_footer']);
		$data['is_print_all'] = intval($_GPC['is_print_all']);
		$data['print_label'] = 0;
		if($_GPC['print_label_type'] == 1 && !empty($_GPC['print_label'])) {
			$print_label = array();
			foreach($_GPC['print_label'] as $label) {
				if($label > 0) {
					$print_label[] = $label;
				}
			}
			if(!empty($print_label)) {
				$data['print_label'] = implode(',', $print_label);
			}
		}
		$data['data'] = array();
		if(!empty($item) && !empty($item['data'])) {
			$data['data'] = $item['data'];
		}
		if(check_plugin_exist('svip')) {
			$data['data']['format'] = array(
				'goods_title' => array(
					'font_size' => trim($_GPC['goods_title_font_size'])
				),
				'note' => array(
					'font_size' => trim($_GPC['note_font_size'])
				)
			);
		}
		$data['data'] = iserializer($data['data']);
		if(!empty($item) && $id) {
			pdo_update('tiny_wmall_printer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_printer', $data);
		}
		imessage(error(0, '更新打印机设置成功'), iurl('store/shop/printer/list'), 'ajax');
	}
	$print_labels = pdo_fetchall('select * from ' . tablename('tiny_wmall_printer_label') . ' where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
}

if($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_printer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除打印机成功'), '', 'ajax');
}

if($ta == 'label_list') {
	$_W['page']['title'] = '打印标签列表';

	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_printer_label', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => intval($v)));
			}
			imessage(error(0, '编辑打印标签成功'),  iurl('store/shop/printer/label_list'), 'ajax');
		}
	}

	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer_label') . $condition . ' ORDER BY displayorder DESC,id ASC', $params);
}

if($ta == 'label_post') {
	$_W['page']['title'] = '编辑打印标签';

	if($_W['ispost']) {
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $k => $v) {
				$v = trim($v);
				if(empty($v)) continue;
				$data = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $sid,
					'title' => $v,
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_insert('tiny_wmall_printer_label', $data);
			}
		}
		imessage(error(0, '添加打印标签成功'), iurl('store/shop/printer/label_list'), 'ajax');
	}
}

if($ta == 'label_del') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('print_label' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'print_label' => $id));
	pdo_delete('tiny_wmall_printer_label', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	imessage(error(0, '删除打印标签成功'), iurl('store/shop/printer/label_list'), 'ajax');
}

include itemplate('store/shop/printer');