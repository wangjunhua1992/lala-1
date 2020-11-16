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
	$urls = array();
	$urls['system'] = array(
		array(
			'title' => '商户',
			'icon' => 'icon-shop',
			'iconColor' => 'c-yellow',
			'url' => 'pages/merchant/index',
			'checkperm' => 'merchant'
		),
		array(
			'title' => '顾客',
			'icon' => 'icon-friendfill',
			'iconColor' => 'c-danger',
			'url' => '/pages/member/list',
			'checkperm' => 'member'
		),
		array(
			'title' => '当面付',
			'icon' => 'icon-news_hot_fill_light',
			'iconColor' => 'c-info',
			'url' => '/pages/paycenter/paybill',
			'checkperm' => 'paycenter'
		),
		array(
			'title' => '售后',
			'icon' => 'icon-refund',
			'iconColor' => 'c-info',
			'url' => '/pages/service/comment',
			'checkperm' => 'service'
		),
		array(
			'title' => '配送员',
			'icon' => 'icon-friend',
			'iconColor' => 'c-primary',
			'url' => '/pages/deliveryer/index',
			'checkperm' => 'deliveryer'
		),
		array(
			'title' => '统计',
			'icon' => 'icon-data',
			'iconColor' => 'c-danger',
			'url' => '/pages/statcenter/index',
			'checkperm' => 'statcenter'
		),
/*		array(
			'title' => '设置',
			'icon' => 'icon-settings',
			'iconColor' => 'c-gray',
			'url' => '/pages/config/index',
			'checkperm' => 'config'
		),*/
	);
	if(check_plugin_perm('errander')) {
		$urls['plugin']['errander'] = array(
			'title' => '跑腿',
			'icon' => 'icon-i-activitymonitoring',
			'iconColor' => 'c-yellow',
			'url' => 'pages/plugin/paotui/index',
			'checkperm' => 'errander'
		);
	}
	if(check_plugin_perm('advertise')){
		$urls['plugin']['advertise'] = array(
			'title' => '商户广告通',
			'icon' => 'icon-news_hot_fill_light',
			'iconColor' => 'c-info',
			'url' => '/pages/plugin/advertise/order',
			'checkperm' => 'advertise'
		);
	}
	if(check_plugin_perm('agent')){
		$urls['plugin']['agent'] = array(
			'title' => '区域代理',
			'icon' => 'icon-friendfill',
			'iconColor' => 'c-danger',
			'url' => '/pages/plugin/agent/index',
			'checkperm' => 'agent'
		);
	}
	if(check_plugin_perm('creditshop')){
		$urls['plugin']['creditshop'] = array(
			'title' => '积分商城',
			'icon' => 'icon-refund',
			'iconColor' => 'c-info',
			'url' => '/pages/plugin/creditshop/order',
			'checkperm' => 'creditshop'
		);
	}
	if(check_plugin_perm('deliveryCard')){
		$urls['plugin']['deliveryCard'] = array(
			'title' => '配送会员卡',
			'icon' => 'icon-ticket',
			'iconColor' => 'c-primary',
			'url' => '/pages/plugin/deliveryCard/order',
			'checkperm' => 'deliveryCard'
		);
	}
	if(check_plugin_perm('mealRedpacket')){
		$urls['plugin']['mealRedpacket'] = array(
			'title' => '套餐红包',
			'icon' => 'icon-redpacket',
			'iconColor' => 'c-danger',
			'url' => '/pages/plugin/mealRedpacket/order',
			'checkperm' => 'mealRedpacket'
		);
	}
	if(check_plugin_perm('wheel')){
		$urls['plugin']['wheel'] = array(
			'title' => '幸运大转盘',
			'icon' => 'icon-gifts',
			'iconColor' => 'c-danger',
			'url' => '/pages/plugin/wheel/record',
			'checkperm' => 'wheel'
		);
	}
	if(check_plugin_perm('gohome')){
		$urls['plugin']['gohome'] = array(
			'title' => '生活圈订单',
			'icon' => 'icon-order',
			'iconColor' => 'c-info',
			'url' => '/pages/plugin/gohome/order',
			'checkperm' => 'gohome'
		);
	}
	if(check_plugin_perm('svip')){
		$urls['plugin']['svip'] = array(
			'title' => '超级会员购买记录',
			'icon' => 'icon-vip',
			'iconColor' => 'c-yellow',
			'url' => '/pages/plugin/svip/order',
			'checkperm' => 'svip'
		);
	}
	$config_kefu = get_plugin_config('kefu.system');
	if($_W['plateformer']['role'] == 'operator' && check_plugin_perm('kefu') && $config_kefu['status'] == 1 && $config_kefu['kefu_status'] == 1) {
		$urls['plugin']['kefu'] = array(
			'title' => '消息中心',
			'icon' => 'icon-message',
			'iconColor' => 'c-danger',
			'url' => '/pages/plugin/kefu/index',
			'checkperm' => 'kefu',
			'notread' => intval(pdo_fetchcolumn('select sum(kefunotread) from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and kefuopenid = :kefuopenid', array(':uniacid' => $_W['uniacid'], ':kefuopenid' => $_W['plateformer']['token'])))
		);
	}
	$urls['plugin'] = array_values($urls['plugin']);
	$result = array(
		'urls' => $urls,
	);
	imessage(error(0, $result), '', 'ajax');
}



