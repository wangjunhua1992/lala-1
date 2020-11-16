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
function irurl($url) {
	if(!strexists($url, '?menu=') || !strexists($url, '/pages/')) {
		$url = ivurl('pages/home/index', array(), true);
	}
	$urls = explode('#', $url);
	return $urls[0] . random(3) . '#' . $urls[1];
}

function imessage($msg, $redirect = '', $type = 'ajax') {
	global $_W, $_GPC;
	if(MODULE_FAMILY == 'wxapp' && $_GPC['from'] == 'vue') {
		load()->func('file');
		rmdirs(MODULE_ROOT . '/template/vue');
		if(!is_cloud() && !is_mlala() && !is_plala() && !is_dlala()) {
			$vars = array(
				'message' => error(-1, '微信登录失败，redirect_uri域名与后台配置不一致，错误码：10003'),
				'type' => $type,
				'url' => $redirect,
			);
			exit(json_encode($vars));
		}
	}
	if(is_array($msg)) {
		$msg['url'] = $redirect;
	}
	$global = array(
		'system' => array(
			'siteroot' => $_W['siteroot'],
			'attachurl' => $_W['attachurl'],
			'cookie_pre' => $_W['config']['cookie']['pre']
		),
		'cookie_pre' => $_W['config']['cookie']['pre'],
		'configmall' => $_W['we7_wmall']['config']['mall'],
		'istamp' => $_W['istamp'],
		'DollarType' => $_W['DollarType'],
		'MapType' => $_W['MapType'],
		'templateIds' => array()
	);
	$global['configmall']['wxappmenu_type'] = 0;
	$config_wxapp = $_W['we7_wxapp']['config'];
	if(!empty($config_wxapp)) {
		$config_wxapp['menu'] = json_decode(base64_decode($config_wxapp['menu']), true);
		if($config_wxapp['menu']['type'] == 1) {
			$global['configmall']['wxappmenu_type'] = 1;
		}
	}
	if($_GPC['gconfig'] == 1) {
		$global['gconfig'] = $_W['we7_wmall']['config'];
	}
	if(!empty($_W['_share'])) {
		if(!isset($_W['_share']['autoinit'])) {
			$_W['_share']['autoinit'] = 1;
		}
		$global['share'] = array_lang_translate($_W['_share']);
	}
	if(!in_array($_GPC['ctrl'], array('plateform', 'manage', 'delivery'))) {
		if($_W['we7_wmall']['config']['member']['force_bind_mobile'] == 1 && !empty($_W['member']) && empty($_W['member']['mobile'])) {
			$global['bind_mobile'] = array(
				'show' => 1,
				'isverifymobile' => intval($_W['we7_wmall']['config']['sms']['verify']['consumer_register'])
			);
		}
		$global['_nav'] = intval($_W['_nav']);
		$global['_fnav'] = intval($_W['_fnav']);
		if($_GPC['menufooter'] == 1) {
			if($_GPC['from'] == 'vue' || (in_array($_GPC['from'], array('wxapp', 'ttapp')) && ($global['configmall']['wxappmenu_type'] == 1 || $global['_nav'] == 1))) {
				$menu = get_mall_menu();
				$global['menufooter'] = $menu;
				if(!empty($global['menufooter'])) {
					if(($_GPC['from'] == 'wxapp' && empty($global['configmall']['wxappmenu_type']) && $global['_nav'] == 1) || $_GPC['_navc'] == 1) {
						$global['menufooter']['css']['iconColor'] = '#FFFFFF';
						$global['menufooter']['css']['textColor'] = '#FFFFFF';
					}
				}
			}
		}
		if($_GPC['order_remind'] == 1) {
			$menu = order_mall_remind();
			$global['order'] = $menu;
		}
		$_W['h5appinfo']['showh5menu'] = 0;
		if((is_h5app() || is_glala()) && !empty($global['share'])) {
			$_W['h5appinfo']['share'] = $global['share'];
			$_W['h5appinfo']['showh5menu'] = 1;
		}
		$global['h5appinfo'] = $_W['h5appinfo'];

		if(!empty($_W['majia'])) {
			$global['majia'] = $_W['majia'];
		}
		if(!empty($_W['qianfan'])) {
			$global['qianfan'] = $_W['qianfan'];
		}
		$global['follow'] = array(
			'status' => 0,
			'logo' => tomedia($_W['we7_wmall']['config']['mall']['logo']),
			'title' => $_W['we7_wmall']['config']['mall']['title'],
			'link' => $_W['we7_wmall']['config']['follow']['link'],
			'qrcode' => tomedia($_W['we7_wmall']['config']['follow']['qrcode']),
		);
		if(is_weixin() && empty($_W['fans']['follow']) && $_W['we7_wmall']['config']['follow']['guide_status'] == 1) {
			$global['follow']['status'] = 1;
		}
		$global['theme'] = array();
		$theme = get_plugin_config('diypage.diyTheme');
		if(empty($theme)) {
			$theme = array(
				'header' => array(
					'background' => '#ff2d4b',
					'color' => '#fff'
				),
				'store' => array(
					'discount_style' => 1
				)
			);
		}
		if(!isset($theme['store'])) {
			$theme['store'] = array(
				'discount_style' => 1
			);
		}
		if(isset($theme['loading']['img'])) {
			$theme['loading']['img'] = tomedia($theme['loading']['img']);
		}
		$global['theme'] = $theme;
		if(defined('IN_WXAPP') && !empty($_W['we7_wmall']['config']['wxapp']['wxtemplate'])) {
			$global['templateIds'] = array();
			foreach ($_W['we7_wmall']['config']['wxapp']['wxtemplate'] as $template_type => $template_ids) {
				$global['templateIds'][$template_type] = array_values(array_slice($template_ids, 0, 3));
			}
		}
		if(empty($_W['member']['avatar'])) {
			$global['isGetUserInfo'] = 1;
		}
	} elseif($_GPC['ctrl'] == 'plateform') {
		if($_GPC['menufooter'] == 1) {
			mload()->model('plateform');
			$global['menufooter'] = get_plateform_menu();
		}
		if($_GPC['_account_perm'] == 1) {
			$global['account_perm'] = get_available_perm();
		}
	} elseif($_GPC['ctrl'] == 'manage') {
		if($_GPC['menufooter'] == 1) {
			mload()->model('manage');
			$global['menufooter'] = get_manager_menu();
		}
		if($_GPC['_account_perm'] == 1) {
			$global['account_perm'] = get_available_perm();
		}
		$global['sessionid'] = $_W['session_id'];
	} elseif($_GPC['ctrl'] == 'delivery') {
		if($_GPC['menufooter'] == 1) {
			mload()->model('deliveryer');
			$global['menufooter'] = get_deliveryer_menu();
		}
		$tab_type = trim($_GPC['_deliveryerOrderTabType']);
		if(!empty($tab_type) && in_array($tab_type, array('takeout', 'errander'))) {
			$global['order_tabs'] = deliveryer_vue_tabs($tab_type);
		}
		$global['sessionid'] = $_W['session_id'];
	}
	if(!empty($_W['wxapp'])) {
		$global = array_merge($global, $_W['wxapp']);
	}
	if($_W['ilang'] == 'zhcn2uy') {
		$router = array(
			'wmall/member/address',
			'wmall/member/mine',
			'wmall/order/index',
			'wmall/order/create',
			'wmall/store/comment'
		);
		$controller = array('wmall', 'system', 'bargain', 'errander', 'creditshop', 'ordergrant', 'shareRedpacket', 'deliveryerCard');
		if(in_array($_W['_controller'], $controller) && empty($msg['errno']) && !in_array($_W['_router'], $router)) {
			$msg['message'] = array_lang_translate($msg['message']);
			if(!empty($msg['cartsInfo'])) {
				$msg['cartsInfo'] = array_lang_translate($msg['cartsInfo']);
			}
		}
		if(in_array($_W['_controller'], $controller) && !empty($global['menufooter'])) {
			$global['menufooter'] = array_lang_translate($global['menufooter']);
		}
	}
	$vars = array(
		'message' => $msg,
		'global' => $global,
		'type' => $type,
		'url' => $redirect,
	);
	exit(json_encode($vars));
}

function collect_wxapp_formid() {
	global $_W, $_GPC;

	if(!empty($_GPC['formid']) || !empty($_GPC['prepay_id'])) {
		$appid = $_W['we7_wxapp']['config']['basic']['key'];
		$openid = $_W['openid_wxapp'];
		if($_W['_controller'] == 'manage') {
			$appid = $_W['we7_wxapp']['config']['manager']['key'];
			$openid = $_W['manager']['openid_wxapp_manager'];
		} elseif($_W['_controller'] == 'deliveryer') {
			$appid = $_W['we7_wxapp']['config']['deliveryer']['key'];
			$openid = $_W['deliveryer']['openid_wxapp_deliveryer'];
		}
		if(empty($openid)) {
			return error(-1, '未获取到有效的openid');
		}
		$formid = trim($_GPC['formid']);
		$times = 1;
		if(!empty($_GPC['prepay_id'])) {
			$times = 3;
			$formid = trim($_GPC['prepay_id']);
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'appid' => $appid,
			'openid' => $openid,
			'formid' => $formid,
			'addtime' => TIMESTAMP,
			'endtime' => TIMESTAMP + 6.5 * 86400,
			'endtime_cn' => date('Y-m-d H:i', TIMESTAMP + 6.5 * 86400)
		);
		for($i = 0; $i < $times; $i++) {
			$is_exist = pdo_get('tiny_wmall_wxapp_formid_log', array('uniacid' => $_W['uniacid'], 'appid' => $appid, 'openid' => $openid, 'formid' => $formid));
			if(empty($is_exist)) {
				pdo_insert('tiny_wmall_wxapp_formid_log', $data);
			}
		}
	}
	return true;
}

function get_available_wxapp_formid($openid) {
	$count = 0;
	if(!empty($openid)) {
		$count = pdo_fetchcolumn("select count(*) from " . tablename('tiny_wmall_wxapp_formid_log') . " where openid = :openid and endtime > :endtime", array(':openid' => $openid, ':endtime' => TIMESTAMP));
	}
	return $count;
}


function get_filter_params($filter) {
	global $_W, $_GPC;
	if(isset($_GPC['filter']) && is_string($_GPC['filter'])) {
		$_GPC['filter'] = json_decode(htmlspecialchars_decode($_GPC['filter']), true);
	}
	$return = array(
		'list' => array()
	);
	$copy_filter = $filter;
	if(!empty($filter['input'])) {
		foreach($filter['input'] as $key => &$input) {
			if(!is_array($input)) {
				$filter['input'][$key] = array(
					'name' => $key,
					'title' => $input
				);
			}
		}
		$return['input'] = $filter['input'];
	}
	if(!empty($filter['time'])) {
		if(!is_array($filter['time'])) {
			$filter['time'] = array(
				'name' => 'time',
				'title' => $filter['time'],
				'start' => '开始时间',
				'end' => '结束时间',
			);
		}
		$return['time'] = $filter['time'];
	}
	if(isset($copy_filter['extra']['deliveryer_id']) || $copy_filter['extra']['deliveryer_id'] == 1) {
		$keydeliveryer = (intval($copy_filter['extra']['deliveryer_id']) == 1) ? 'deliveryer_id' : $copy_filter['extra']['deliveryer_id'];
		$return['list'][$keydeliveryer] = array(
			'title' => '配送员',
			'name' => $keydeliveryer,
			'options' => array(
				array(
					'title' => '不限',
					'value' => '0',
				),
			),
		);
		mload()->model('deliveryer');
		$deliveryers = deliveryer_all();
		if(!empty($deliveryers)) {
			foreach($deliveryers as $deliveryer) {
				$return['list'][$keydeliveryer]['options'][] = array(
					'title' => $deliveryer['title'],
					'value' => $deliveryer['id'],
				);
			}
		}
	}
	if(isset($copy_filter['extra']['store']) || $copy_filter['extra']['store'] == 1) {
		$keystore = (intval($copy_filter['extra']['store']) == 1) ? 'sid' : $copy_filter['extra']['store'];
		$return['list'][$keystore] = array(
			'title' => '门店',
			'name' => $keystore,
			'options' => array(
				array(
					'title' => '不限',
					'value' => '0',
				),
			),
		);
		$condition = ' where uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if($_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid';
			$params[':agentid'] = $_W['agentid'];
		}
		$stores = pdo_fetchall('select id, title from ' . tablename('tiny_wmall_store') . $condition, $params);
		if(!empty($stores)) {
			foreach($stores as $store) {
				$return['list'][$keystore]['options'][] = array(
					'title' => $store['title'],
					'value' => $store['id'],
				);
			}
		}
	}
	if(isset($copy_filter['extra']['agent']) || $copy_filter['extra']['agent'] == 1) {
		$keyagent = (intval($copy_filter['extra']['agent']) == 1) ? 'agentid' : $copy_filter['extra']['agent'];

		$return['list'][$keyagent] = array(
			'title' => '代理',
			'name' => $keyagent,
			'options' => array(),
		);
		if($_W['agentid'] <= 0 ) {
			$return['list'][$keyagent]['options'][] = array(
				'title' => '不限',
				'value' => '0',
			);
		}
		mload()->model('agent');
		$agents = get_agents();
		if(!empty($agents)) {
			foreach($agents as $agent) {
				if($_W['agentid'] > 0) {
					if($agent['id'] == $_W['agentid']) {
						$return['list'][$keyagent]['options'][] = array(
							'title' => $agent['area'],
							'value' => $agent['id'],
						);
					}
				} else {
					$return['list'][$keyagent]['options'][] = array(
						'title' => $agent['area'],
						'value' => $agent['id'],
					);
				}
			}
		}
	}

	if(isset($copy_filter['extra']['orderby']) || $copy_filter['extra']['orderby']['key'] == 1) {
		$keyorderby = (intval($copy_filter['extra']['orderby']['key']) == 1) ? 'orderby' : $copy_filter['extra']['orderby']['key'];
		$return['list'][$keyorderby] = array(
			'title' => '排序方式',
			'name' => $keyorderby,
			'options' => array(
				/*array(
					'title' => '默认',
					'value' => '0',
				),*/
			),
		);
		$orderbys = $copy_filter['extra']['orderby']['values'];
		if(!empty($orderbys)) {
			foreach($orderbys as $key => $value) {
				$return['list'][$keyorderby]['options'][] = array(
					'title' => $value,
					'value' => $key,
				);
			}
		}
	}

	unset($filter['input'], $filter['time'], $filter['extra']);
	foreach($filter as $key => &$val) {
		if(empty($val['name'])) {
			$val['name'] = $key;
		}
		if(!empty($_GPC['filter']) && isset($_GPC['filter'][$key])) {
			foreach($val['options'] as $option) {
				if($option['value'] == $_GPC['filter'][$key]) {
					$val['selected'] = $option;
				}
			}
		}
		$return['list'][$key] = $val;
	}
	if(isset($copy_filter['extra']['time']) || $copy_filter['extra']['time'] == 1) {
		$keytime = (intval($copy_filter['extra']['time']) == 1) ? 'time' : $copy_filter['extra']['time'];
		$return['list'][$keytime] = array(
			'title' => '筛选时间',
			'name' => $keytime,
			'type' => 'time',
			'key' => 'addtime',
			'options' => array(
				array(
					'title' => '不限',
					'value' => '-2',
				),
				array(
					'title' => '今天',
					'value' => '0',
				),
				array(
					'title' => '本周',
					'value' => '7',
				),
				array(
					'title' => '本月',
					'value' => '30',
				),
				array(
					'title' => '自定义',
					'value' => '-1',
					'iscustom' => 1
				),
			),
		);
		$key = $return['list'][$keytime]['key'];
		if(!empty($_GPC['filter']) && isset($_GPC['filter'][$keytime])) {
			foreach($return['list'][$keytime]['options'] as $option) {
				if($option['value'] == $_GPC['filter'][$keytime]) {
					$option[$key] = array(
						'start' => $_GPC['filter'][$key]['start'] ? $_GPC['filter'][$key]['start'] : date('Y-m-d'),
						'end' => $_GPC['filter'][$key]['end'] ? $_GPC['filter'][$key]['end'] : date('Y-m-d'),
					);
					$return['list'][$keytime]['selected'] = $option;

				}
			}
		}
	}
	return $return;
}