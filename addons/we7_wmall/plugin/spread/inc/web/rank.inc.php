<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '排行榜设置';
	if($_W['ispost']){
		$num = intval($_GPC['num']);
		$arr = array(
			'status' => intval($_GPC['status']),
			'type' => intval($_GPC['type']),
			'num' => $num > 300 ? 300 : $num,
		);
		$item = array();
		if($arr['type'] == 2) {
			foreach($_GPC['nickname'] as $k => $v) {
				$item[] = array(
					'avatar' => $_GPC['avatar'][$k],
					'nickname' => $v,
					'commission' => $_GPC['commission'][$k],
				);
			}
			$arr['infomation'] = $item;
		}
		set_plugin_config('spread.rank', $arr);
		imessage(error(0, '排行榜设置成功'), 'refresh', 'ajax');
	}
	$rank = get_plugin_config('spread.rank');
	$rank['url'] = imurl('spread/rank', array(), true);
}
include itemplate('rank');