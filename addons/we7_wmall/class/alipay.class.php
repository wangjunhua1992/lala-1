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

class AliPay{
	public $alipay;
	public function __construct($pay_type = 'wap') {
		global $_W;
		$alipay = $_W['we7_wmall']['config']['payment']['alipay'];
		if($pay_type == 'h5app')  {
			$alipay = $_W['we7_wmall']['config']['payment']['app_alipay'];
		}
		$this->alipay = array(
			'app_id' => $alipay['appid'],
			'rsa_type' => empty($alipay['rsa_type']) ? 'RSA' : $alipay['rsa_type'],
			'sign_type' => empty($alipay['sign_type']) ? 'key' : $alipay['sign_type'],
		);
		$this->cert = array(
			'private_key' => $alipay['private_key'],
			'public_key' => $alipay['public_key'],

			'app_public_key' => $alipay['app_public_key'],
			'app_private_key' => $alipay['app_private_key'],
			'alipay_public_key' => $alipay['alipay_public_key'],
			'alipay_public_root_key' => $alipay['alipay_public_root_key'],
		);
	}

	public function array2url($params, $force = false) {
		$str = '';
		foreach($params as $key => $val) {
			if($force && empty($val)) {
				continue;
			}
			$str .= "{$key}={$val}&";
		}
		$str = trim($str, '&');
		return $str;
	}

	public function buildCertSn($key) {
		$keys = array(
			'app_cert_sn' => 'app_public_key',
			'alipay_root_cert_sn' => 'alipay_public_root_key'
		);
		$key = $keys[$key];
		$cert = file_get_contents(MODULE_ROOT . "/cert/{$this->cert[$key]}/{$key}.crt");
		$res = openssl_x509_parse($cert);
		//$value = md5("name={$res['name']}&serialNumber={$res['serialNumber']}");
		$name = '';
		if(!empty($res['subject'])) {
			foreach($res['subject'] as $key => $value) {
				$name .= "/{$key}={$value}";
			}
		};
		$value = md5($name . $res['serialNumber']);
		return $value;
	}

	public function bulidSign($params) {
		unset($params['sign']);
		ksort($params);
		$string = $this->array2url($params, true);
		$priKeyName = 'private_key';
		$fileName = 'pem';
		if($this->alipay['sign_type'] == 'sn') {
			$priKeyName = 'app_private_key';
			$fileName = 'crt';
		}
		$priKey = file_get_contents(MODULE_ROOT . "/cert/{$this->cert[$priKeyName]}/{$priKeyName}.{$fileName}");
		$res = openssl_get_privatekey($priKey);
		if($params['sign_type'] == 'RSA') {
			openssl_sign($string, $sign, $res);
		} else {
			openssl_sign($string, $sign, $res, OPENSSL_ALGO_SHA256);
		}
		openssl_free_key($res);
		$sign = base64_encode($sign);
		return $sign;
	}

	/*
	 * 检查支付证书
	 * */
	public function checkCert() {
		global $_W;
		if($this->alipay['sign_type'] == 'key' && (empty($this->cert['private_key']) || empty($this->cert['public_key']))) {
			return error(-1, '支付宝支付证书不完整');
		} elseif($this->alipay['sign_type'] == 'sn' && (empty($this->cert['app_public_key']) || empty($this->cert['alipay_public_root_key']))) {
			return error(-1, '支付宝支付证书不完整');
		}
		return true;
	}

	/*
	 * 退款接口
	 * */
	public function payRefund_build($params) {
		global $_W;
		$status = $this->checkCert();
		if(is_error($status)) {
			return $status;
		}
		$elements = array('refund_fee', 'out_trade_no', 'out_refund_no', 'refund_reason', 'out_request_no');
		$params = array_elements($elements, $params);
		if(empty($params['refund_fee'])) {
			return error(-1, '退款金额不能为空');
		}
		if(empty($params['out_trade_no'])) {
			return error(-1, '商户订单号不能为空');
		}
		$set = array();
		$set['app_id'] = $this->alipay['app_id'];
		$set['method'] = 'alipay.trade.refund';
		$set['charset'] = 'utf-8';
		$set['sign_type'] = $this->alipay['rsa_type'];
		$set['timestamp'] =  date('Y-m-d H:i:s');
		$set['version'] =  '1.0';
		if($this->alipay['sign_type'] == 'sn') {
			$set['app_cert_sn'] = $this->buildCertSn('app_cert_sn');
			$set['alipay_root_cert_sn'] = $this->buildCertSn('alipay_root_cert_sn');
		}
		$other = array(
			'out_trade_no' => $params['out_trade_no'],
			'refund_amount' => $params['refund_fee'],
			'refund_reason' => $params['refund_reason'] ? $params['refund_reason'] : '正常退款',
		);
		if(!empty($params['out_request_no'])) {
			$other['out_request_no'] = $params['out_request_no'];
		}

		$set['biz_content'] = json_encode($other);
		$set['sign'] = $this->bulidSign($set);
		load()->func('communication');
		$result = ihttp_post('https://openapi.alipay.com/gateway.do', $set);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!is_array($result)) {
			return error(-1, '返回数据错误');
		}
		if($result['alipay_trade_refund_response']['code'] != 10000) {
			return error(-1, $result['alipay_trade_refund_response']['sub_msg']);
		}
		return $result['alipay_trade_refund_response'];
	}

	/**
	 * 单笔转账到支付宝账户
	 */
	public function transfer($params, $payee_type = 'ALIPAY_LOGONID') {
		global $_W;
		$status = $this->checkCert();
		if(is_error($status)) {
			return $status;
		}
		$elements = array('out_biz_no', 'payee_type', 'payee_account', 'amount', 'payee_real_name', 'remark');
		$params = array_elements($elements, $params);
		if(empty($params['out_biz_no'])) {
			return error(-1, '商户转账订单号不能为空');
		}
		if(!in_array($payee_type, array('ALIPAY_USERID', 'ALIPAY_LOGONID'))) {
			return error(-1, '收款方账户类型');
		}
		$params['payee_type'] = $payee_type;
		if(empty($params['payee_account'])) {
			return error(-1, '收款方账户不能为空');
		}
		if(empty($params['amount'])) {
			return error(-1, '转账金额不能为空');
		}
		if(empty($params['payee_real_name'])) {
			return error(-1, '收款方真实姓名不能为空');
		}
		$set['app_id'] = $this->alipay['app_id'];
		$set['method'] = 'alipay.fund.trans.toaccount.transfer';
		$set['charset'] = 'utf-8';
		$set['sign_type'] = $this->alipay['rsa_type'];
		$set['timestamp'] =  date('Y-m-d H:i:s');
		$set['version'] =  '1.0';
		$set['biz_content'] = json_encode($params);
		$set['sign'] = $this->bulidSign($set);
		load()->func('communication');
		$result = ihttp_post('https://openapi.alipay.com/gateway.do', $set);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!is_array($result)) {
			return error(-1, '返回数据错误');
		}
		if($result['alipay_fund_trans_toaccount_transfer_response']['code'] != 10000 || empty($result['alipay_fund_trans_toaccount_transfer_response']['pay_date'])) {
			return error(-1, $result['alipay_fund_trans_toaccount_transfer_response']['sub_msg']);
		}
		return true;
	}

	/**
	 * 查询转账订单接口
	 **/
	function transOrderQuery($params) {
		global $_W;
		$status = $this->checkCert();
		if(is_error($status)) {
			return $status;
		}
		$elements = array('out_biz_no', 'order_id');
		$params = array_elements($elements, $params);
		if(empty($params['out_biz_no']) && empty($params['order_id'])) {
			return error(-1, '请输入商户转账订单号或者支付宝转账单据号');
		}
		if(!empty($params['out_biz_no'])) {
			unset($params['order_id']);
		}
		$set['app_id'] = $this->alipay['app_id'];
		$set['method'] = 'alipay.fund.trans.order.query';
		$set['charset'] = 'utf-8';
		$set['sign_type'] = $this->alipay['rsa_type'];
		$set['timestamp'] =  date('Y-m-d H:i:s');
		$set['version'] =  '1.0';
		$set['biz_content'] = json_encode($params);
		$set['sign'] = $this->bulidSign($set);
		load()->func('communication');
		$result = ihttp_post('https://openapi.alipay.com/gateway.do', $set);
		if(is_error($result)) {
			return $result;
		}
		$result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
		$result = json_decode($result['content'], true);
		if(!is_array($result)) {
			return error(-1, '返回数据错误');
		}
		if($result['alipay_fund_trans_order_query_response']['code'] != 10000) {
			return error(-1, $result['alipay_fund_trans_order_query_response']['sub_msg']);
		}
		return true;
	}
}