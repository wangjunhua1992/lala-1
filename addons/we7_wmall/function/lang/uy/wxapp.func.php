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

function get_mall_menu($menu_id = 0) {
	global $_W, $_GPC;
	if(check_plugin_perm('diypage') && !in_array($_W['_controller'], array('spread'))) {
		if($_W['_action'] == 'shop' && $_GPC['sid'] > 0) {
			$menu = store_get_menu($_GPC['sid']);
			return $menu;
		}
		$config_app_customer = $_W['we7_wmall']['config']['app']['customer'];
		if(is_ios() && (is_h5app() || is_glala()) && !empty($config_app_customer) && empty($config_app_customer['iosstatus']) && !empty($config_app_customer['iosmenu'])) {
			$menu_id = $config_app_customer['iosmenu'];
		}
		$menu_id = intval($menu_id);
		if(empty($menu_id)) {
			$menu_id = intval($_W['_menuid']);
		}
		if($menu_id <= 0) {
			$key = 'takeout';
			if($_W['_controller'] == 'errander') {
				$key = 'errander';
			} elseif($_W['_controller'] == 'ordergrant') {
				$key = 'ordergrant';
			} elseif(in_array($_W['_controller'], array('gohome', 'kanjia', 'seckill', 'pintuan', 'tongcheng', 'haodian', 'svip'))) {
				$key = $_W['_controller'];
			}
			if(in_array($_GPC['from'], array('wxapp', 'ttapp'))) {
				$config_menu = $_W['we7_wxapp']['config']['wxappmenu'];
			} else {
				$config_menu = get_plugin_config('diypage.vuemenu');
			}
			if(is_array($config_menu) && !empty($config_menu[$key])) {
				$menu_id = intval($config_menu[$key]);
			}
		}
		if($menu_id > 0) {
			$temp = pdo_get('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'id' => $menu_id, 'version' => 2));
			if(!empty($temp)) {
				$menu = json_decode(base64_decode($temp['data']), true);
				foreach($menu['data'] as &$val) {
					if(!empty($val['img'])) {
						$val['img'] = tomedia($val['img']);
					}
				}
				return $menu;
			}
		}
	}
	if($_W['_controller'] == 'spread') {
		$result = array (
			'name' => 'default',
			'params' => array (
				'navstyle' => '0',
			),
			'css' => array (
				'iconColor' => '#163636',
				'iconColorActive' => '#ff2d4b',
				'textColor' => '#929292',
				'textColorActive' => '#ff2d4b',
			),
			'data' => array (
				'M0123456789101' => array (
					'link' => 'plugin/pages/spread/index',
					'icon' => 'icon-home',
					'text' => '分销中心',
				),
				'M0123456789104' => array (
					'link' => 'plugin/pages/spread/commission',
					'icon' => 'icon-refund',
					'text' => '推广佣金',
				),
				'M0123456789105' => array (
					'link' => 'plugin/pages/spread/current',
					'icon' => 'icon-sort',
					'text' => '佣金明细',
				),
				'M0123456789106' => array (
					'link' => 'plugin/pages/spread/down',
					'icon' => 'icon-friend',
					'text' => '我的团队',
				),
			),
		);
	} elseif(in_array($_W['_controller'], array('gohome', 'pintuan', 'kanjia', 'seckill', 'tongcheng', 'haodian'))) {
		$result = gohome_get_menu();
	} elseif($_W['_controller'] == 'svip') {
		$result = array (
			'name' => 'default',
			'params' => array (
				'navstyle' => '0',
			),
			'css' => array (
				'iconColor' => '#163636',
				'iconColorActive' => '#f2d499',
				'textColor' => '#929292',
				'textColorActive' => '#f2d499',
			),
			'data' => array (
				'M0123456789101' => array (
					'link' => 'package/pages/svip/mine',
					'icon' => 'icon-choiceness',
					'text' => '会员首页',
				),
				'M0123456789104' => array (
					'link' => 'package/pages/svip/redpacketCoupon',
					'icon' => 'icon-recharge',
					'text' => '专享红包',
				),
				'M0123456789105' => array (
					'link' => 'package/pages/svip/mission',
					'icon' => 'icon-squarecheck',
					'text' => '会员任务',
				),
			),
		);
	} else {
		$result = array (
			'name' => 'default',
			'params' => array (
				'navstyle' => '0',
			),
			'css' => array (
				'iconColor' => '#163636',
				'iconColorActive' => '#ff2d4b',
				'textColor' => '#929292',
				'textColorActive' => '#ff2d4b',
			),
			'data' => array (
				'M0123456789101' => array (
					'link' => 'pages/home/index',
					'icon' => 'icon-home',
					'text' => 'باشبەت',
				),
				'M0123456789104' => array (
					'link' => 'pages/order/index',
					'icon' => 'icon-order',
					'text' => 'زاكاسلىرىم',
				),
				'M0123456789105' => array (
					'link' => 'pages/member/mine',
					'icon' => 'icon-mine',
					'text' => 'مېنىڭ',
				),
			),
		);
	}
	return $result;
}