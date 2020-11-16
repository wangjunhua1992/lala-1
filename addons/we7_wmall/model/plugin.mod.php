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

class Ploader {
	private $cache = array();
	public function __construct() {
		global $_W;
		if(empty($_W['LangType'])) {
			$_W['LangType'] = 'zh-cn';
		}
	}
	function func($name) {
		global $_W;
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = WE7_WMALL_PLUGIN_PATH . "{$_W['_plugin']['name']}/function/{$name}.func.php";
		if (file_exists($file)) {
			include $file;
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Helper Function /addons/we7_wmall/{$_W['_plugin']['name']}/function/{$name}.func.php", E_USER_ERROR);
			return false;
		}
	}

	function model($name) {
		global $_W;
		if(isset($this->cache['model'][$name])) {
			return true;
		}
		$file = WE7_WMALL_PLUGIN_PATH . "{$name}/model.php";
		$filelang = WE7_WMALL_PLUGIN_PATH . "{$name}/lang/{$_W['LangType']}/model.php";
		if(!is_file($file)) {
			$file = WE7_WMALL_PLUGIN_PATH . "{$_W['_plugin']['name']}/model/{$name}.mod.php";
			$filelang = WE7_WMALL_PLUGIN_PATH . "{$_W['_plugin']['name']}/model/lang/{$_W['LangType']}/{$name}.mod.php";
		}
		if(!is_file($file)) {
			if(in_array($name, array('seckill', 'kanjia', 'pintuan', 'tongcheng', 'haodian'))) {
				$file = WE7_WMALL_PLUGIN_PATH . "gohome/{$name}/model.php";
				$filelang = WE7_WMALL_PLUGIN_PATH . "gohome/{$name}/lang/{$_W['LangType']}/model.php";
			}
		}
		if(file_exists($file)) {
			include $file;
			if(file_exists($filelang)) {
				include $filelang;
			}
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Helper Model /addons/we7_wmall/{$_W['_plugin']['name']}/model/{$name}.mod.php", E_USER_ERROR);
			return false;
		}
	}

	function classs($name) {
		global $_W;
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = WE7_WMALL_PLUGIN_PATH . "{$_W['_plugin']['name']}/class/{$name}.class.php";
		if (file_exists($file)) {
			include $file;
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Helper Class /addons/we7_wmall/{$_W['_plugin']['name']}/class/{$name}.class.php", E_USER_ERROR);
			return false;
		}
	}
}

function plugin_types() {
	return array(
		'biz' => array(
			'name' => 'biz',
			'title' => '业务类'
		),
		'activity' => array(
			'name' => 'activity',
			'title' => '营销类'
		),
		'tool' => array(
			'name' => 'tool',
			'title' => '工具类'
		),
		'help' => array(
			'name' => 'help',
			'title' => '辅助类'
		)
	);
}

function plugin_fetchall($status = 1) {
	$condition = ' where is_show = 1';
	$params = array();
	if(!empty($status)) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$condition .= ' order by displayorder desc';
	$plugins = pdo_fetchall('select * from ' . tablename('tiny_wmall_plugin') . $condition, $params, 'name');
	return $plugins;
}

function plugin_fetch($name) {
	$routers = array(
		'pintuan' => array(
			'name' => 'pintuan',
			'title' => '拼团',
			'status' => 1,
		),
		'kanjia' => array(
			'name' => 'kanjia',
			'title' => '砍价',
			'status' => 1,
		),
		'seckill' => array(
			'name' => 'seckill',
			'title' => '抢购',
			'status' => 1,
		),
		'tongcheng' => array(
			'name' => 'tongcheng',
			'title' => '同城',
			'status' => 1,
		),
		'haodian' => array(
			'name' => 'haodian',
			'title' => '好店',
			'status' => 1,
		),
	);
	if(in_array($name, array_keys($routers))) {
		return $routers[$name];
	}
	$condition = ' where name = :name';
	$params = array(
		':name' => $name,
	);
	$plugin = pdo_fetch('select * from ' . tablename('tiny_wmall_plugin') . $condition, $params);
	return $plugin;
}

function plugin_account_has_perm($name) {
	$perm = get_account_perm();
	if(in_array($name, array('seckill', 'kanjia', 'pintuan', 'tongcheng', 'haodian'))) {
		$name = 'gohome';
	}
	if(empty($perm)) {
		return true;
	} elseif(empty($perm['plugins']) || !in_array($name, $perm['plugins'])) {
		return false;
	}
	return true;
}




