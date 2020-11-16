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
$_W['page']['title'] = '微信信息';
if(!is_weixin()) {
	imessage('请在微信中打开该链接', '', 'info');
}

$fans = mc_oauth_userinfo();
if(is_error($fans) || empty($fans['openid'])) {
	imessage('获取微信信息失败', '', 'info');
}
$params = json_decode(base64_decode($_GPC['params']), true);
if(!empty($params)) {
	$_SESSION['params'] = $params;
} else {
	$params = $_SESSION['params'];
}
if(empty($params)) {
	imessage('参数不合法', '', 'info');
}

$url = imurl("wmall/auth/oauth", array(), true);
$oauth = member_oauth_info($url, $params);
if(is_error($oauth)) {
	imessage('获取粉丝身份出错,请重新操作', 'close', 'error');
} else {
	unset($_SESSION['params']);
	imessage('获取微信信息成功', 'close', 'success');
}