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
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index'){
	$_W['page']['title'] = '页面配置';
	$template = store_get_data($sid, 'wxapp.template');
	$template_page = store_get_data($sid, 'wxapp.template_page');
	if($_W['ispost']){
		$type = trim($_GPC['type']);
		if($type == 'template') {
			$value = intval($_GPC['value']);
			if(!check_plugin_perm('diypage') && in_array($value, array(4, 5))) {
				$value = 2;
			}
			store_set_data($sid, 'wxapp.template', $value);
			imessage(error(0, '商品列表单/双列设置成功'), ireferer(), 'ajax');
		} else {
			$value = array(
				'wxapp' => intval($_GPC['template_page']['wxapp']),
				'vue' => intval($_GPC['template_page']['vue'])
			);
			store_set_data($sid, 'wxapp.template_page', $value);
			imessage(error(0, '商品列表页风格设置成功'), ireferer(), 'ajax');
		}
	}
}

include itemplate('store/decoration/template');