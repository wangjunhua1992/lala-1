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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$configfile = IA_ROOT . "/data/config.php";
require $configfile;
$_W['config'] = $config;

if($op == 'index') {
	$_W['page']['title'] = '调试模式';
	$module = pdo_get('modules', array('name' => 'we7_wmall'));
	if($_W['ispost']) {
		set_global_config('development', intval($_GPC['development']));
		set_global_config('development_delivery_location', intval($_GPC['development_delivery_location']));
		set_global_config('slog_status', intval($_GPC['slog_status']));
		$version = trim($_GPC['version']);
		if(!empty($version) && $module['version'] != $version) {
			pdo_run("update ims_modules set version = '{$version}' where name = 'we7_wmall' ");
			load()->model('cache');
			load()->model('setting');
			load()->object('cloudapi');
			cache_updatecache();
		}
		imessage(error(0, '调试模式设置成功'), ireferer(), 'ajax');
	}
	$config_global = $_W['we7_wmall']['global'];
}

elseif($op == 'agent_config') {
	mload()->model('agent');
	$agents = get_agents();
	foreach ($agents as $val) {
		$sysset = get_agent_system_config('', $val['id']);	
		if (!empty($sysset['takeout']['order']['notify_rule_clerk'])) {
			unset($sysset['takeout']['order']['notify_rule_clerk'],$sysset['takeout']['order']['notify_rule_deliveryer'],$sysset['takeout']['order']['pay_time_limit'],$sysset['takeout']['order']['handle_time_limit'],$sysset['takeout']['order']['auto_success_hours'],$sysset['takeout']['order']['deliveryer_collect_time_limit']);
			pdo_update('tiny_wmall_agent', array('sysset' => iserializer($sysset)), array('uniacid' => $_W['uniacid'], 'id' => $val['id']));
		}
	}
	imessage(error(0, '处理成功'), ireferer(), 'ajax');
}

elseif($op == 'creditshop') {
	if($_W['ispost']) {
		$sql = "drop table if exists ims_tiny_wmall_creditshop_goods;
			CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_goods` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
			  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
			  `category_id` int(10) NOT NULL,
			  `type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
			  `thumb` varchar(255) CHARACTER SET utf8 NOT NULL,
			  `old_price` varchar(10) CHARACTER SET utf8 NOT NULL,
			  `chance` tinyint(3) unsigned NOT NULL,
			  `totalday` tinyint(3) unsigned NOT NULL,
			  `use_credit1` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '0',
			  `use_credit2` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '0',
			  `description` text CHARACTER SET utf8 NOT NULL,
			  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `credit2` varchar(10) CHARACTER SET utf8 NOT NULL,
			  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `redpacket` text CHARACTER SET utf8 NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `uniacid` (`uniacid`),
			  KEY `type` (`type`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
			";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'session') {
	if($_W['ispost']) {
		$sql = "TRUNCATE ims_core_sessions";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'goodsprice') {
	if($_W['ispost']) {
		$sql = "update ims_tiny_wmall_goods set ts_price = price";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'goodstype') {
	if($_W['ispost']) {
		$sql = "update ims_tiny_wmall_goods set type = 3";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'plugin') {
	if($_W['ispost']) {
		$sql = "TRUNCATE ims_tiny_wmall_plugin";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'discountprice') {
	if($_W['ispost']) {
		set_system_config('itime', 0);
		$sql = "alter table `ims_tiny_wmall_goods` drop discount_price";
		pdo_run($sql);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'develop_status') {
	if($_W['ispost']) {
		$_W['setting']['copyright']['develop_status'] = 0;
		$test = setting_save($_W['setting']['copyright'], 'copyright');
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'bargain') {
	if($_W['ispost']) {
		$goods = pdo_fetchall('select a.id,a.discount_price,a.goods_id,b.sid,b.title,c.uniacid from ' . tablename('tiny_wmall_activity_bargain_goods') . 'as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id left join ' . tablename('tiny_wmall_store') . ' as c on b.sid = c.id where 1 order by c.is_rest asc, a.mall_displayorder desc limit 10000');
		if(!empty($goods)) {
			foreach($goods as $item) {
				$item['uniacid'] = intval($item['uniacid']);
				if(empty($item['title']) || empty($item['uniacid'])) {
					pdo_delete('tiny_wmall_activity_bargain_goods', array('id' => $item['id']));
				}
			}
		}
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'file') {
	if($_W['ispost']) {
		$files = array(
			array(
				'from' => 'api.php',
				'to' => 'app/api.php',
			),
			array(
				'from' => 'wxapp.php',
				'to' => 'app/wxapp.php',
			),
			array(
				'from' => 'wmerchant.php',
				'to' => 'web/wmerchant.php',
			),
			array(
				'from' => 'wagent.php',
				'to' => 'web/wagent.php',
			),
			array(
				'from' => 'ifile.ctrl.php',
				'to' => 'web/source/utility/ifile.ctrl.php',
			),
		);
		load()->func('file');
		foreach($files as $file) {
			$src = MODULE_ROOT . "/{$file['from']}";
			$filename = IA_ROOT . "/{$file['to']}";
			mkdirs(dirname($filename));
			copy($src, $filename);
		}
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'py') {
	if($_W['ispost']) {
		load()->func('file');
		$src = MODULE_ROOT . "/py.php";
		$filename = IA_ROOT . "/py.php";
		mkdirs(dirname($filename));
		copy($src, $filename);
		imessage(error(0, '处理成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'cache') {
	if($_W['ispost']) {
		load()->model('cache');
		load()->model('setting');
		load()->object('cloudapi');
		cache_updatecache();
		imessage(error(0, '缓存更新成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'opcache') {
	if($_W['ispost']) {
		opcache_reset();
		imessage(error(0, '性能优化缓存更新成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'manifest') {
	if($_W['ispost']) {
		$manifest_file = MODULE_ROOT . '/manifest.xml';
		@unlink($manifest_file);
		imessage(error(0, '删除manifest文件成功'), ireferer(), 'ajax');
	}
}

elseif($op == 'ignoreupdate') {
	$manifest_file = MODULE_ROOT . '/manifest.xml';
	@unlink($manifest_file);

	load()->model('cloud');
	$manifest_cloud = cloud_m_upgradeinfo('we7_wmall');
	//print_r($manifest_cloud);
	if(!is_error($manifest_cloud)) {
		if(!empty($manifest_cloud['site_branch']['version'])) {
			$version = $manifest_cloud['site_branch']['version']['version'];
			$module = pdo_get('modules', array('name' => 'we7_wmall'));
			if(!empty($version) && $module['version'] != $version) {
				pdo_run("update ims_modules set version = '{$version}' where name = 'we7_wmall' ");
				load()->model('cache');
				load()->model('setting');
				load()->object('cloudapi');
				cache_updatecache();
			}
		}
	}
	imessage(error(0, '更改模块版本号成功'), ireferer(), 'ajax');
}

include itemplate('system/development');