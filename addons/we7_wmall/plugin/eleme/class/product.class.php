<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
pload()->classs('eleme');
class product extends Eleme{
	public function getShopCategories($shopid = 0) {
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$params = array(
			'shopId' => $shopid,
		);
		$data = $this->httpPost('eleme.product.category.getShopCategories', $params);
		return $data;
	}

	public function getShopCategoriesWithChildren($shopid = 0) {
		// 查询店铺商品分类，包含二级分类
		if(empty($shopid)) {
			$shopid = $this->shopid;
		}
		$params = array(
			'shopId' => $shopid,
		);
		$data = $this->httpPost('eleme.product.category.getShopCategoriesWithChildren', $params);
		return $data;
	}

	public function getItemsByCategoryId($categoryId) {
		//获取一个分类下的所有商品
		$params = array(
			'categoryId' => $categoryId,
		);
		$data = $this->httpPost('eleme.product.item.getItemsByCategoryId', $params);
		return $data;
	}

	public function getItem($itemId) {
		//查询商品详情
		$params = array(
			'itemId' => $itemId,
		);
		$data = $this->httpPost('eleme.product.item.getItem', $params);
		return $data;
	}


}