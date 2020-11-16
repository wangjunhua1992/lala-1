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

$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$slides = sys_fetch_slide('homeTop', true);
	$categorys = store_fetchall_category('parent_child');
	$notices = pdo_fetchall('select id,title,link,wxapp_link,displayorder,status from' .tablename('tiny_wmall_notice') ." where uniacid = :uniacid and type = :type and status = 1 order by displayorder desc", array(':uniacid' => $_W['uniacid'], ':type' => 'member'));
	$cubes = pdo_fetchall('select * from ' . tablename('tiny_wmall_cube') . ' where uniacid = :uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']));
	if(!empty($cubes)){
		foreach ($cubes as &$c) {
			$c['thumb'] = tomedia($c['thumb']);
		}
	}
	$result = array(
		'slides' => $slides,
		'categorys' => $categorys,
		'notices' => $notices,
		'cubes' => $cubes
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'gohome') {
	if(!check_plugin_perm('gohome')) {
		imessage(error(-1, '无生活圈插件'), '', 'ajax');
	}
	$tables = array(
		'gohome_category' => array(
			'key' => 'gohome_category',
			'table' => 'tiny_wmall_gohome_category',
		),
		'gohome_slide' => array(
			'key' => 'gohome_slide',
			'table' => 'tiny_wmall_gohome_slide',
		),
		'kanjia_category' => array(
			'key' => 'kanjia_category',
			'table' => 'tiny_wmall_kanjia_category',
		),
		'pintuan_category' => array(
			'key' => 'pintuan_category',
			'table' => 'tiny_wmall_pintuan_category',
		),
		'seckill_goods_category' => array(
			'key' => 'seckill_goods_category',
			'table' => 'tiny_wmall_seckill_goods_category',
		),
		'tongcheng_category' => array(
			'key' => 'tongcheng_category',
			'table' => 'tiny_wmall_tongcheng_category',
		),
		'haodian_category' => array(
			'key' => 'haodian_category',
			'table' => 'tiny_wmall_haodian_category',
		),
	);
	$result = array();
	foreach($tables as $table) {
		$data = pdo_fetchall('select * from ' . tablename($table['table']) . ' where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']), 'id');
		if(!empty($data)) {
			foreach($data as &$da) {
				$da['thumb'] = tomedia($da['thumb']);
				if(in_array($table['key'], array('tongcheng_category', 'haodian_category'))) {
					if(!empty($da['parentid'])) {
						$data[$da['parentid']]['child'][] = $da;
						unset($data[$da['id']]);
					}
				}
			}
			$result[$table['key']] = $data;
		} else {
			$result[$table['key']] = array();
		}
	}
	imessage(error(0, $result), '', 'ajax');
}

elseif($ta == 'gohome_goods') {
	if(!check_plugin_perm('gohome')) {
		imessage(error(-1, '无生活圈插件'), '', 'ajax');
	}
	$type = trim($_GPC['type']);
	if(!in_array($type, array('kanjia', 'pintuan', 'seckill'))) {
		imessage(error(-1, '活动类型不存在'), '', 'ajax');
	}
	$tables = array(
		'kanjia' => array(
			'key' => 'kanjia',
			'table' => 'tiny_wmall_kanjia',
		),
		'pintuan' => array(
			'key' => 'pintuan_goods',
			'table' => 'tiny_wmall_pintuan_goods',
		),
		'seckill' => array(
			'key' => 'seckill_goods',
			'table' => 'tiny_wmall_seckill_goods',
		)
	);

	$result = array();
	$goods = pdo_fetchall('select * from ' . tablename($tables[$type]['table']) . ' where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']), 'id');
	if(!empty($goods)) {
		foreach($goods as &$good) {
			$good['thumb'] = tomedia($good['thumb']);
		}
		$result[$type] = $goods;
	}
	imessage(error(0, $result), '', 'ajax');
}
