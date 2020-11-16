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
$config_danmu = get_plugin_config('diypage.danmu');
if(!is_array($config_danmu) || !$config_danmu['params']['status']) {
	imessage(error(-1, ''), '', 'ajax');
}

if($config_danmu['params']['dataType'] == 0) {
	$members = pdo_fetchall('select nickname, avatar from ' . tablename('tiny_wmall_members') . " where uniacid = :uniacid and nickname != '' and avatar != '' order by id desc limit 10;", array(':uniacid' => $_W['uniacid']));
} else {
	$members = pdo_fetchall('select b.nickname, b.avatar from ' . tablename('tiny_wmall_order') . " as a left join " . tablename('tiny_wmall_members') .  " as b on a.uid = b.uid where a.uniacid = :uniacid and b.nickname != '' and b.avatar != '' order by a.id desc limit 10;", array(':uniacid' => $_W['uniacid']));
}
if(!empty($members)) {
	foreach($members as &$val) {
		$val['avatar'] = tomedia($val['avatar']);
		$val['time'] = mt_rand($config_danmu['params']['starttime'], $config_danmu['params']['endtime']);
		if($val['time'] <= 0) {
			$val['time'] = '刚刚';
		} elseif($val['time'] > 0 && $val['time'] < 60) {
			$val['time'] = "{$val['time']}秒前";
		} elseif($val['time'] > 60) {
			$val['time'] = floor($val['time'] / 60);
			$val['time'] = "{$val['time']}分钟前";
		}
	}
}
imessage(error(0, $members), '', 'ajax');


