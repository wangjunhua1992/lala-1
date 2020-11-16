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
mload()->model('plugin');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '应用信息';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			$statuss = $_GPC['statuss'];
			foreach($_GPC['ids'] as $k => $v) {
				$status = 0;
				if(!empty($statuss) && in_array($v, $statuss)) {
					$status = 1;
				}
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'ability' => trim($_GPC['abilitys'][$k]),
					'status' => $status,
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				if(!empty($_GPC['pluginimgs'][$k])) {
					$data['thumb'] = $_GPC['pluginimgs'][$k];
				}
				pdo_update('tiny_wmall_plugin', $data, array('id' => intval($v)));
			}
		}
		imessage(error(0, '修改成功'), 'refresh', 'ajax');
	}

	$condition = ' where 1 and is_show = 1';
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (name like :keyword or title like :keyword)';
		$params[':keyword'] = "%{$keyword}%";
	}
	$plugins = pdo_fetchall('select * from ' . tablename('tiny_wmall_plugin') . $condition, $params);
	$types = plugin_types();
}

include itemplate('system/plugin');