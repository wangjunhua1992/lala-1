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

function redpacket_channels() {
	$channel = array(
		'' => array(
			'text' => '未知',
			'css' => 'label-danger'
		),
		'shareRedpacket' => array(
			'text' => '分享有礼',
			'css' => 'label-success'
		),
		'freeLunch' => array(
			'text' => '霸王餐',
			'css' => 'label-info'
		),
		'superRedpacket' => array(
			'text' => '超级红包',
			'css' => 'label-warning'
		),
		'mealRedpacket' => array(
			'text' => '套餐红包',
			'css' => 'label-danger'
		),
		'mealRedpacket_plus' => array(
			'text' => '套餐红包plus',
			'css' => 'label-success'
		),
		'creditShop' => array(
			'text' => '积分兑换',
			'css' => 'label-info'
		),
		'svip' => array(
			'text' => '超级会员',
			'css' => 'label-warning'
		),
		'wheel' => array(
			'text' => '大转盘',
			'css' => 'label-success'
		),
		'vipRedpacket' => array(
			'text' => '会员红包',
			'css' => 'label-danger'
		),
	);
	return $channel;
}