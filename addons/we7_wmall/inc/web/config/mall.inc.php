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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	$hasCustomerApp = check_plugin_perm('customerApp');
	$hasDeliveryerApp = check_plugin_perm('deliveryerApp');
	$hasManagerApp = check_plugin_perm('managerApp');
	$hasPlateformApp = check_plugin_perm('plateformApp');
	if($_W['ispost']) {
		$mall = array(
			'title' => trim($_GPC['title']),
			'logo' => trim($_GPC['logo']),
			'mobile' => trim($_GPC['mobile']),
			'version' => intval($_GPC['version']),
			'is_to_nearest_store' => intval($_GPC['is_to_nearest_store']),
			'default_sid' => intval($_GPC['default_sid']),
			//'template_consumer' => trim($_GPC['template_consumer']) ? trim($_GPC['template_consumer']) : 'default',
			'store_orderby_type' => trim($_GPC['store_orderby_type']),
			'store_overradius_display' => intval($_GPC['store_overradius_display']),
			'delivery_title' => trim($_GPC['delivery_title']),
			'lazyload_store' => trim($_GPC['lazyload_store']),
			'lazyload_goods' => trim($_GPC['lazyload_goods']),
			'copyright' => htmlspecialchars_decode($_GPC['copyright']),
			'seniverse' => htmlspecialchars_decode($_GPC['seniverse']),
			'meiqia' => htmlspecialchars_decode(str_replace(array("&#039;"), array('&quot;'), $_GPC['meiqia'])),
			'store_use_child_category' => intval($_GPC['store_use_child_category']),
		);
		set_system_config('mall', $mall);
		$manager = $_GPC['manager'];
		set_system_config('manager', $manager);
		if(!empty($hasCustomerApp)) {
			set_config_text('顾客服务协议和隐私政策', "member:agreement", htmlspecialchars_decode($_GPC['member_agreement']));
		}
		if(!empty($hasManagerApp)) {
			set_config_text('商户服务协议和隐私政策', "manager:agreement", htmlspecialchars_decode($_GPC['manager_agreement']));
		}
		if(!empty($hasDeliveryerApp)) {
			set_config_text('配送员服务协议和隐私政策', "deliveryer:agreement", htmlspecialchars_decode($_GPC['deliveryer_agreement']));
		}
		if(!empty($hasPlateformApp)) {
			set_config_text('平台服务协议和隐私政策', "plateform:agreement", htmlspecialchars_decode($_GPC['plateform_agreement']));
		}
		/*if($mall['template_consumer'] != $_config['mall']['template_consumer']) {
			$src = IA_ROOT . '/addons/we7_wmall/template/consumer/' . $mall['template_consumer'];
			$des = IA_ROOT . '/addons/we7_wmall/template/vue/';
			load()->func('file');
			rmdirs($des, true);
			file_copy($src, $des);
		}*/
		imessage(error(0, '基础设置成功'), ireferer(), 'ajax');
	}
	$config = $_config['mall'];
	$config['manager'] = $_config['manager'];
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']));
	$member_agreement = !empty($hasCustomerApp) ? get_config_text("member:agreement") : '';
	$manager_agreement = !empty($hasManagerApp) ? get_config_text("manager:agreement") : '';
	$deliveryer_agreement = !empty($hasDeliveryerApp) ? get_config_text("deliveryer:agreement") : '';
	$plateform_agreement = !empty($hasPlateformApp) ? get_config_text("plateform:agreement") : '';
	include itemplate('config/basic');
}

if($op == 'follow') {
	$_W['page']['title'] = '分享及关注';
	if($_W['ispost']) {
		$share = array(
			'title' => trim($_GPC['title']),
			'imgUrl' => trim($_GPC['imgUrl']),
			'desc' => trim($_GPC['desc']),
			'link' => trim($_GPC['link']),
		);
		set_system_config('share', $share);

		$follow = array(
			'guide_status' => intval($_GPC['guide_status']),
			'link' => trim($_GPC['followurl']),
			'qrcode' => trim($_GPC['qrcode']),
		);
		set_system_config('follow', $follow);
		imessage(error(0, '分享关注设置成功'), ireferer(), 'ajax');
	}
	$share = $_config['share'];
	$follow = $_config['follow'];
	include itemplate('config/follow');
}

if($op == 'close') {
	$_W['page']['title'] = '平台状态';
	if($_W['ispost']) {
		$close = array(
			'status' => intval($_GPC['status']) ? intval($_GPC['status']) : 1,
			'url' => trim($_GPC['url']),
			'tips' => trim($_GPC['tips']),
		);
		set_system_config('close', $close);
		imessage(error(0, '平台状态设置成功'), ireferer(), 'ajax');
	}
	$close = $_config['close'];
	include itemplate('config/close');
}

if($op == 'oauth') {
	$_W['page']['title'] = '公众平台oAuth设置';
	if($_W['ispost']) {
		$oauth_host = trim($_GPC['oauth_host']);
		if(!empty($oauth_host) && !strexists($oauth_host, 'https://') && !strexists($oauth_host, 'http://')) {
			imessage(error(-1, '域名必须以https://或者https//开头'), '', 'ajax');
		}
		$oauth = array(
			'oauth_host' => trim($_GPC['oauth_host']),
		);
		set_system_config('oauth', $oauth);
		imessage(error(0, '平台状态设置成功'), ireferer(), 'ajax');
	}
	$oauth = $_config['oauth'];
	include itemplate('config/oauth');
}