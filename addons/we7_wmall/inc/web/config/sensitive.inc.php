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
$_W['page']['title'] = '敏感词过滤';
if($_W['ispost']) {
	$sensitive = array(
		'sensitive_words' => array(),
		'replace_words' => array(),
		'group' => array(),
	);
	$sensitive_words = $_GPC['sensitive_words'];
	$replace_words = $_GPC['replace_words'];
	$group = array();
	if(!empty($sensitive_words)) {
		foreach($sensitive_words as $key => $val) {
			if(empty($val)) {
				unset($sensitive_words[$key]);
				unset($replace_words[$key]);
				continue;
			}
			if(empty($replace_words[$key])) {
				$replace_words[$key] = $val;
			}
			$group[] = array(
				'sensitive_words' => $val,
				'replace_words' => $replace_words[$key]
			);
		}
		$sensitive = array(
			'sensitive_words' => $sensitive_words,
			'replace_words' => $replace_words,
			'group' => $group
		);
	}
	set_system_config('sensitive', $sensitive);
	imessage(error(0, '设置敏感词成功'), ireferer(), 'ajax');
}

$sensitive = get_system_config('sensitive');
include itemplate('config/sensitive');