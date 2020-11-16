<?php
/**
 * 外送系统
 * @author ONESTOP团队
 */
defined('IN_IA') or exit('Access Denied');
include('version.php');
include('defines.php');
include('model.php');
require 'class/TyAccount.class.php';
class We7_wmallModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W, $_GPC;
		$config = pdo_get('tiny_wmall_config', array('uniacid' => $_W['uniacid']), array('sysset', 'id'));
		$_W['we7_wmall']['config'] = iunserializer($config['sysset']);

		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('tiny_wmall_reply') . " WHERE uniacid = :uniacid and `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':rid' => $rid));
		if(empty($row)) {
			return '';
		}
		$row['extra'] = iunserializer($row['extra']);
		if(in_array($row['type'], array('store', 'assign', 'table'))) {
			$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']));
			if(empty($store)) {
				return '';
			}
			$sid = $store['id'];
			if($row['type'] == 'store') {
				//商家二维码
				$url = ivurl('pages/store/goods', array('sid' => $sid), true);
				$news = array();
				$news[] = array(
					'title' => $store['title'],
					'description' => $store['content'],
					'picurl' => tomedia($store['logo']),
					'url' => $url
				);
				return $this->respNews($news);
			} elseif($row['type'] == 'assign') {
				//排号二维码
				if(!$store['is_assign']) {
					return $this->respText("{$store['title']} 已关闭排号功能,请联系商家");
				}

				$url = ivurl('tangshi/pages/assign/assign', array('sid' => $sid), true);
				$news = array();
				$news[] = array(
					'title' => $store['title'] . "-点击进入排号",
					'description' => $store['content'],
					'picurl' => tomedia($store['logo']),
					'url' => $url
				);
				return $this->respNews($news);
			} elseif($row['type'] == 'table') {
				//扫桌号
				$table = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'id' => $row['table_id']));
				if(empty($table)) {
					return '';
				}
				$fans = mc_fansinfo($_W['openid']);
				$data = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $row['sid'],
					'table_id' => $row['table_id'],
					'openid' => $_W['openid'],
					'nickname' => $fans['nickname'],
					'avatar' => $fans['tag']['avatar'],
					'createtime' => TIMESTAMP,
				);
				pdo_insert('tiny_wmall_tables_scan', $data);
				pdo_update('tiny_wmall_tables', array('scan_num' => $table['scan_num'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $row['table_id']));
				$url = ivurl('tangshi/pages/table/goods', array('sid' => $sid, 'table_id' => $row['table_id']), true);
				$news = array();
				$news[] = array(
					'title' => $store['title'] . "-{$table['title']}号桌",
					'description' => "欢迎光临{$store['title']}, 您当前在{$table['title']}号桌点餐",
					'picurl' => tomedia($store['logo']),
					'url' => $url
				);
				return $this->respNews($news);
			}
		} elseif($row['type'] == 'spread') {
			$invite_uid = $_GPC['code'] = intval($row['extra']['uid']);
			$spread = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $invite_uid), array('uid', 'nickname'));
			if(empty($spread)) {
				return '';
			}
			load()->model('mc');
			mload()->model('common');
			mload()->model('member');
			mload()->model('plugin');
			pload()->model('spread');
			$openid = $this->message['fromusername'];
			flog('a', $openid, 'opop');
			$fansInfo = mc_init_fans_info($openid, true);
			flog('b', $fansInfo, 'opop');
			if(empty($fansInfo)) {
				$fansInfo = array(
					'openid' => $openid,
					'unionid' => '',
					'headimgurl' => '',
				);
			}
			if(!empty($fansInfo['unionid'])) {
				pdo_update('tiny_wmall_members', array('openid' => $fansInfo['openid']), array('unionId' => $fansInfo['unionid']));
				pdo_update('tiny_wmall_members', array('unionId' => $fansInfo['unionid']), array('openid' => $fansInfo['openid']));
				member_union($fansInfo['unionid']);
				$member = get_member($fansInfo['unionid'], 'unionId');
			} else {
				$member = get_member($fansInfo['openid']);
			}
			flog('e33', $member, 'opop');
			if(empty($member)) {
				$mc = pdo_fetch('select a.fanid,b.credit1,b.credit2,b.uid,b.nickname,b.avatar,b.realname,b.mobile,b.gender from' . tablename('mc_mapping_fans') . ' as a left join ' . tablename('mc_members') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and a.acid = :acid and a.openid = :openid', array(':uniacid' => $_W['uniacid'], ':acid' => $_W['acid'], ':openid' => $_W['openid']));
				flog('c', $mc, 'opop');
				if(empty($mc['uid'])) {
					$member = array(
						'uniacid' => $_W['uniacid'],
						'uid' => date('His') . random(3, true),
						'openid' => $fansInfo['openid'],
						'unionId' => $fansInfo['unionid'],
						'nickname' => $fansInfo['nickname'],
						'realname' => $fansInfo['nickname'],
						'sex' => '保密',
						'avatar' => $fansInfo['avatar'],
						'is_sys' => 2, //模拟用户
						'status' => 1,
						'token' => random(32),
						'addtime' => TIMESTAMP,
					);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = 0;
					$member['credit2'] = 0;
				} else {
					$member = array(
						'uniacid' => $_W['uniacid'],
						'uid' => $mc['uid'],
						'openid' => !empty($_W['openid']) ? $_W['openid'] : $fansInfo['openid'],
						'unionId' => $fansInfo['unionid'],
						'nickname' => $mc['nickname'],
						'realname' => $mc['realname'],
						'mobile' => $mc['mobile'],
						'sex' => ($mc['gender'] == 1 ? '男' : '女'),
						'avatar' => $mc['avatar'],
						'is_sys' => 1,
						'status' => 1,
						'token' => random(32),
						'addtime' => TIMESTAMP,
					);
					pdo_insert('tiny_wmall_members', $member);
					$member['credit1'] = $mc['credit1'];
					$member['credit2'] = $mc['credit2'];
				}
			} else {
				if(0 && (($member['nickname'] != $fansInfo['nickname']) || ($member['avatar'] != $avatar))) {
					$update = array(
						'nickname' => $fansInfo['nickname'],
						'avatar' => $avatar
					);
					pdo_update('tiny_wmall_members', $update, array('id' => $member['id']));
				}
			}
			$_W['member'] = $member;
			$_W['member']['is_mall_newmember'] = 1;
			$config_newmember_condition = 0;
			if(!empty($_W['we7_wmall']['config']['activity'])) {
				$config_newmember_condition = $_W['we7_wmall']['config']['activity']['newmember']['newmember_condition'];
			}
			if($config_newmember_condition == 1) {
				$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status != 6', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
			} else {
				$is_exist = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), array('id'));
			}
			if(!empty($is_exist)) {
				$_W['member']['is_mall_newmember'] = 0;
			}

			$status = member_spread_bind();
			if(is_error($status)) {
				slog('spreadscan', "啦啦推广下线失败-推广员:{$spread['nickname']}", array(), "推广粉丝：{$member['nickname']},uid:{$member['uid']}，失败原因：{$status['message']}");
			}
			flog('d', $status, 'opop');
			$config_mall = $_W['we7_wmall']['config']['mall'];
			$config_share = $_W['we7_wmall']['config']['share'];
			$news = array(
				array(
					'title' => "您的好友({$spread['nickname']})向您推荐了{$config_mall['title']}",
					'description' => "{$config_share['desc']}",
					'picurl' => tomedia($config_mall['logo']),
					'url' => ivurl('pages/home/index', array('code' => $row['extra']['uid']), true)
				)
			);
			return $this->respNews($news);
		}
	}
}
