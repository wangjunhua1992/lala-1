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

$op = trim($_GPC['op'])? trim($_GPC['op']): 'index';

if($op == 'index'){
	$_W['page']['title'] = '数据初始化';
	set_time_limit(0);
	if($_W['ispost']){
		$tables = Array(
			'tiny_wmall_activity_bargain',
			'tiny_wmall_activity_bargain_goods',
			'tiny_wmall_cube',
			'tiny_wmall_deliveryer',
			'tiny_wmall_deliveryer_current_log',
			'tiny_wmall_deliveryer_getcash_log',
			'tiny_wmall_errander_category',
			'tiny_wmall_errander_order',
			'tiny_wmall_news',
			'tiny_wmall_news_category',
			'tiny_wmall_notice',
			'tiny_wmall_order',
			'tiny_wmall_order_comment',
			'tiny_wmall_order_stat',
			'tiny_wmall_paybill_order',
			'tiny_wmall_paylog',
			'tiny_wmall_report',
			'tiny_wmall_slide',
			'tiny_wmall_store',
			'tiny_wmall_store_account',
			'tiny_wmall_store_category',
			'tiny_wmall_store_current_log',
			'tiny_wmall_store_yucunjin_log',
			'tiny_wmall_store_deliveryer',
			'tiny_wmall_store_getcash_log',
			'tiny_wmall_store_activity',
			'tiny_wmall_store_delivery_policy',
		);
		$tables_plugin = array(
			'tiny_wmall_text',
		);
		if(check_plugin_perm('gohome')) {
			$tables_plugin = Array(
				'tiny_wmall_tongcheng_category',
				'tiny_wmall_tongcheng_comment',
				'tiny_wmall_tongcheng_information',
				'tiny_wmall_tongcheng_order',
				'tiny_wmall_haodian_category',
				'tiny_wmall_haodian_order',
				'tiny_wmall_seckill_goods',
				'tiny_wmall_seckill_goods_category',
				'tiny_wmall_pintuan_category',
				'tiny_wmall_pintuan_goods',
				'tiny_wmall_kanjia',
				'tiny_wmall_kanjia_category',
				'tiny_wmall_kanjia_helprecord',
				'tiny_wmall_kanjia_userlist',
				'tiny_wmall_gohome_order',
				'tiny_wmall_gohome_slide',
				'tiny_wmall_gohome_notice',
				'tiny_wmall_gohome_comment',
				'tiny_wmall_gohome_category',
			);
		}
		if(check_plugin_perm('errander')) {
			$tables_plugin[] = 'tiny_wmall_errander_category';
			$tables_plugin[] = 'tiny_wmall_errander_page';
			$tables_plugin[] = 'tiny_wmall_errander_order';
		}
		if(check_plugin_perm('diypage')) {
			$tables_plugin[] = 'tiny_wmall_diypage';
		}
		$tables = array_merge($tables, $tables_plugin);
		$agentid = isset($_GPC['agentid']) ? intval($_GPC['agentid']) : 0;
		foreach($tables as $table) {
			if(pdo_fieldexists($table, 'agentid')) {
				pdo_update($table, array('agentid' => $agentid), array('uniacid' => $_W['uniacid']));
			}
		}
		cache_clean('we7_wmall:deliveryers:');
		imessage(error(0, '数据初始化成功'), 'referer', 'ajax');
	}
}

include itemplate('initialize');