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
$_W['page']['title'] = '协议';
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'agreement';
if($ta == 'agreement') {
	$key = trim($_GPC['key']);
	$pageid = trim($_GPC['pageid']);
	if($key == 'errander_diypage_agreement') {
		$config = pdo_get('tiny_wmall_errander_page', array('uniacid' => $_W['uniacid'], 'id' => $pageid));
		$result = array('agreement' => $config['agreement'], 'title' => '服务协议');
	} elseif($key == 'mealPlus_rules') {
		$redpacket = pdo_get('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'type' => 'meal', 'status' => 1));
		if(!empty($redpacket)) {
			$redpacket['data'] = json_decode(base64_decode($redpacket['data']), true);
			if(!empty($redpacket['data']['rules'])) {
				$redpacket['data']['rules'] = htmlspecialchars_decode(base64_decode($redpacket['data']['rules']));
			}
		}
		$result = array('agreement' => $redpacket['data']['rules'], 'title' => '套餐红包规则');
	} elseif($key == 'meal_rules') {
		mload()->model('plugin');
		pload()->model('mealRedpacket');
		$mealRedpacket = mealRedpacket_available_get();
		$result = array('agreement' => $mealRedpacket['data']['rules'], 'title' => '特权说明');
	} elseif($key == 'help') {
		$helpid = trim($_GPC['helpid']);
		if($helpid > 0) {
			$config = pdo_get('tiny_wmall_help', array('uniacid' => $_W['uniacid'], 'id' => $helpid));
			$result = array('agreement' => $config['content'], 'title' => $config['title']);
		}
	} elseif($key == 'notice') {
		$noticeid = intval($_GPC['noticeid']);
		$type = intval($_GPC['type']);
		$tables = array(
			'0' => 'tiny_wmall_notice',
			'2' => 'tiny_wmall_gohome_notice'
		);
		$notice = pdo_get($tables[$type], array('uniacid' => $_W['uniacid'], 'id' => $noticeid), array('title', 'content'));
		$result = array('agreement' => $notice['content'], 'title' => $notice['title']);
	} elseif($key == 'zhunshibao_agreement') {
		$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'name' => 'zhunshibao:agreement'));
		$result = array('agreement' => $config['value'], 'title' => $config['title']);
	} elseif($key == 'yinsihao_agreement') {
		$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'name' => 'yinsihao:agreement'));
		$result = array('agreement' => $config['value'], 'title' => $config['title']);
	} elseif($key == 'spread_agreement') {
		$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'name' => 'spread:agreement'));
		$result = array('agreement' => $config['value'], 'title' => $config['title']);
	} elseif($key == 'kabao_agreement') {
		$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'name' => 'kabao:agreement'));
		$result = array('agreement' => $config['value'], 'title' => $config['title']);
	} elseif($key == 'vipRedpacket') {
		pload()->model('vipRedpacket');
		$vipRedpacket = vipRedpacket_available_fetch();
		$result = array('agreement' => $vipRedpacket['data']['rules'], 'title' => '常见问题与说明');
	} else {
		$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'name' => $key));
		$result = array('agreement' => $config['value'], 'title' => $config['title']);
	}

	imessage(error(0, $result), '', 'ajax');
}
