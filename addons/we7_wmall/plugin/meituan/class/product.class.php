<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
pload()->classs('meituan');
class product extends meituan{
	public function queryCatList() {
		$data = $this->httpGet('dish/queryCatList');
		return $data;
	}

	/*
	 * $offset 起始条目数
	 * $limit 每页大小，须小于200
	 * */
	public function queryListByEPoiId($offset = 0, $limit = 150, $shopid = 0) {
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$params = array(
			'ePoiId' => $shopid,
			'offset' => $offset > 200 ? 200 : $offset,
			'limit' => $limit,
		);
		$data = $this->httpGet('dish/queryListByEPoiId', $params);
		return $data;
	}

	/*
	 * 根据ERP的门店id查询门店下的菜品基础信息【包含美团的菜品Id】
	 * */
	public function queryBaseListByEPoiId($shopid = 0) {
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$params = array(
			'ePoiId' => $shopid,
		);
		$data = $this->httpGet('dish/queryBaseListByEPoiId', $params);
		return $data;
	}

	/*
	 * 根据eDishCode批量查询外卖菜品信息（目前仅支持单个商品）
	 * $goodsid 本地数据库id
	 * */
	public function queryListByEdishCodes($goodsid, $shopid = 0) {
		global $_W;
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $goodsid));
		if(empty($goods)) {
			return error(-1, '商品不存在');
		}
		if(empty($goods['openplateformCode'])) {
			$goods['openplateformCode'] = random(20, true);
			pdo_update('tiny_wmall_goods', array('openplateformCode' => $goods['openplateformCode']), array('uniacid' => $_W['uniacid'], 'id' => $goodsid));
		}
		//请求接口和美团外卖商品映射
		$mapping = array(
			array(
				'dishId' => $goods['meituanId'],
				'eDishCode' => $goods['openplateformCode'],
			),
		);
		$result = $this->mapping($mapping, $shopid);
		if(is_error($result)) {
			return error(-1, "进行美团商品映射失败:{$result['message']}");
		}

		$params = array(
			'ePoiId' => $shopid,
			'eDishCodes' => $goods['openplateformCode'],
		);
		$data = $this->httpGet('dish/queryListByEdishCodes', $params);
		if(is_error($data)) {
			return error(-1, "获取商品基本信息失败:{$data['message']}");
		}
		$openplateformCode = $goods['openplateformCode'];
		$goods = $data['list'][0];
		$attrs = $this->queryPropertyList($openplateformCode);
		if(is_error($attrs)) {
			return error(-1, "获取商品属性信息失败:{$data['message']}");
		}
		$goods['attrs'] = $attrs;
		return $goods;
	}

	/*
	 * $mappings array(array('dishId' => 100, 'eDishCode' => 100))
	 * 文档地址：http://developer.meituan.com/openapi#7.2.3
	 * */
	public function mapping($mappings, $shopid = 0) {
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$params = array(
			'ePoiId' => $shopid,
			'dishMappings' => json_encode($mappings),
		);
		$data = $this->httpPost('dish/mapping', $params);
		return $data;
	}

	/*
	 * 查询菜品属性
	 * 文档地址：http://developer.meituan.com/openapi#7.2.14
	 * */
	public function queryPropertyList($eDishCode) {
		$params = array(
			'eDishCode' => $eDishCode,
		);
		$data = $this->httpGet('dish/queryPropertyList', $params);
		return $data;
	}








}