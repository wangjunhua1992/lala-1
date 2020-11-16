<?php
/**
 * 外送系统
 * @author --5.G.云.资.源-
 * @QQ 570602783
 * 5.G云.每天分.享最新.源.码
 * @url https://www.yunziyuan.com.cn
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
pload()->classs('product');
$product = new product(1);
/*$result = $product->queryBaseListByEPoiId(59);
p($result);
die;*/
$result = $product->queryListByEdishCodes(625, 59);
p($result);
die;
$result = $product->queryListByEPoiId(0, 20, 59);
p($result);
die;

$description = get_config_text('eleme:description');
include itemplate('index');