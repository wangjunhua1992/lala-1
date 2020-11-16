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
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'all';

if($op == 'all') {
	$datas = deliveryer_fetchall();
	$datas = array_values($datas);
	imessage(error(0, $datas), '', 'ajax');
}

if($op == 'list'){
	if(isset($_GPC['key'])){
		$key = trim($_GPC['key']);
		$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'is_takeout';
		$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer') . " where uniacid = :uniacid and status = 1 and {$type} = 1 and title like :key order by id desc", array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"), 'id');
		if(!empty($data)){
			foreach ($data as &$value) {
				if($value['work_status'] == 1){
					$value['work_status'] = '接单中';
				} else {
					$value['work_status'] = '休息中';
				}
				$value['avatar'] = tomedia($value['avatar']);
			}
		}
		$deliveryers = array_values($data);
		imessage(array('errno' => 0, 'message' => $deliveryers, 'data' => $data), '', 'ajax');
	}
	include itemplate('public/deliveryer');
}

if($op == 'link') {
	$type = empty($_GPC['type']) ? 'deliveryer' :  trim($_GPC['type']);
	$urls = wxapp_urls($type);
	include itemplate('public/plateformLink');
}