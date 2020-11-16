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
icheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	mload()->classs('wxpay');
	$wxpay = new wxpay();
	$bank_list = $wxpay->getback();
	$bank_list = array_values($bank_list);
	$account = $_W['member']['account'];

	$cashcredit = get_plugin_config('spread.settle.cashcredit');
	$status = array(
		'bank' => in_array('bank', $cashcredit) ? 1 : 0,
		'alipay' => in_array('alipay', $cashcredit) ? 1 : 0
	);

	if($_W['ispost']) {
		$params = json_decode(htmlspecialchars_decode($_GPC['params']), true);
		if($status['bank'] == 1) {
			$bank = array(
				'id' => intval($params['bank']['id']),
				'title' => trim($params['bank']['title']),
				'account' => trim($params['bank']['account']),
				'realname' => trim($params['bank']['realname'])
			);
			$account['bank'] = $bank;
		}
		if($status['alipay'] == 1) {
			$alipay = array(
				'realname' => trim($params['alipay']['realname']),
				'account' => trim($params['alipay']['account'])
			);
			$account['alipay'] = $alipay;
		}
		$data = array(
			'account' => iserializer($account)
		);
		pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		imessage(error(0, '提现账户设置成功'), '', 'ajax');
	}

	$result = array(
		'bank_list' => $bank_list,
		'bank' => $account['bank'],
		'alipay' => $account['alipay'],
		'status' => $status
	);
	imessage(error(0, $result), '', 'ajax');
}


