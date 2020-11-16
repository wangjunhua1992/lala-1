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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
if($ta == 'list') {
	$key = trim($_GPC['key']);
	$scene = trim($_GPC['scene']) ? trim($_GPC['scene']) : 'notify';
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and (openid = :openid or openid_wxapp = :openid_wxapp or nickname like :key or realname like :realname or mobile like :mobile or uid = :uid) order by id desc limit 50', array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%", ':realname' => "%{$key}%", ':mobile' => "%{$key}%", ':openid' => $key, ':openid_wxapp' => $key, ':uid' => $key), 'id');
	if(!empty($data)) {
		foreach($data as $key => &$row) {
			if($scene == 'notify') {
				if(MODULE_FAMILY == 'wxapp') {
					if(empty($row['openid']) && empty($row['openid_wxapp'])) {
						unset($data[$key]);
						continue;
					}
				} else {
					if(empty($row['openid'])) {
						unset($data[$key]);
						continue;
					}
				}
			} elseif($scene == 'getcash') {
				if($_W['we7_wmall']['config']['getcash']['channel']['wechat'] == 'wxapp') {
					if(empty($row['openid_wxapp'])) {
						unset($data[$key]);
						continue;
					}
				} else {
					if(empty($row['openid'])) {
						unset($data[$key]);
						continue;
					}
				}
			}
			$row['avatar'] = tomedia($row['avatar']);
		}
		$fans = array_values($data);
	}
	$result = array(
		'fans' => $fans,
		'qrcode' => imurl('system/common/oauth', array(), true)
	);
	imessage(error(0, $result), '', 'ajax');
}

