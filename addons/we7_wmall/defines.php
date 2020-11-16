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

!defined('WE7_WMALL_PATH') && define('WE7_WMALL_PATH', IA_ROOT . '/addons/we7_wmall/');
!defined('WE7_WMALL_PLUGIN_PATH') && define('WE7_WMALL_PLUGIN_PATH', WE7_WMALL_PATH . '/plugin/');
!defined('WE7_WMALL_URL') && define('WE7_WMALL_URL', $_W['siteroot'] . 'addons/we7_wmall/');
!defined('WE7_WMALL_URL_NOHTTPS') && define('WE7_WMALL_URL_NOHTTPS', str_replace('https://', 'http://', $_W['siteroot']) . 'addons/we7_wmall/');
!defined('WE7_WMALL_STATIC') && define('WE7_WMALL_STATIC', WE7_WMALL_URL . 'static/');
!defined('WE7_WMALL_LOCAL') && define('WE7_WMALL_LOCAL', '../addons/we7_wmall/');
!defined('WE7_WMALL_DEBUG') && define('WE7_WMALL_DEBUG', '1');
!defined('WE7_WMALL_ISHTTPS') && define('WE7_WMALL_ISHTTPS', strexists($_W['siteroot'], 'https://'));
!defined('WE7_WMALL_LANG_PATH') && define('WE7_WMALL_LANG_PATH', WE7_WMALL_PATH . '/lang/');

define('IREGULAR_EMAIL', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i');
define('IREGULAR_MOBILE', '/^[01][3456789][0-9]{9}$/');
define('IREGULAR_PASSWORD', '/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/');


