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
$_W['_process'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_pay = 1 and order_type < 3 and status >= 1 and status <= 4', array(':uniacid' => $_W['uniacid']));
$_W['_remind'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and is_remind = 1 and is_pay = 1 and order_type < 3 and status >= 1 and status <= 4 ', array(':uniacid' => $_W['uniacid']));
$_W['_refund'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and refund_status = 1 and is_pay = 1 and order_type < 3 ', array(':uniacid' => $_W['uniacid']));



