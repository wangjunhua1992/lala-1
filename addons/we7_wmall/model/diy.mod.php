
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

function get_wxapp_diy($pageOrid, $mobile = false, $extra = array()) {
	global $_W;
	if(is_array($pageOrid)) {
		$page = $pageOrid;
	} else {
		$id = intval($pageOrid);
		if(empty($id)) {
			return false;
		}
		$params = array('uniacid' => $_W['uniacid'], 'id' => $id, 'version' => 2);
		if($_W['agentid'] > 0) {
			$params['agentid'] = $_W['agentid'];
		}
		$page = pdo_get('tiny_wmall_diypage', $params);
		//如果开启区域代理，但是代理没有设置自定义页面，需要兼容。
		if(empty($page)) {
			unset($params['agentid']);
			$page = pdo_get('tiny_wmall_diypage', $params);
			if(empty($page) && in_array($extra['pagepath'], array('home', 'gohome', 'haodian', 'tongcheng'))) {
				$page = get_wxapp_defaultpage();
			}
		}
	}
	if(empty($page)) {
		return false;
	}
	$page['data'] = base64_decode($page['data']);
	$page['data'] = json_decode($page['data'], true);
	$page['parts'] = array();
	$page['is_has_location'] = $page['is_has_allstore'] = $page['is_has_hotGoods'] = $page['is_show_cart'] = $page['is_show_redpacket'] = $page['is_show_service'] = $page['is_has_svip'] = $page['is_has_goodsTab'] = $page['is_has_storesTab'] = $page['cid'] = 0;
	$page['danmu'] = array();
	$page['is_show_kefu'] = 0;
	if($extra['pagepath'] == 'home' && $extra['pagetype'] == 'default') {
		if(!empty($_W['we7_wmall']['config']['mall']['meiqia'])) {
			$page['is_show_kefu'] = 1;
		}
		if(in_array($_W['ochannel'], array('wxapp', 'ttapp')) && !empty($_W['we7_wxapp']['config'])) {
			$hometheme = $_W['we7_wxapp']['config']['extPages']['pages/home/index'];
			if(!empty($hometheme)) {
				$page['data']['page']['title'] = $hometheme['navigationBarTitleText'];
				$page['data']['page']['navigationtextcolor'] = $hometheme['navigationBarTextStyle'];
				$page['data']['page']['navigationbackground'] = $hometheme['navigationBarBackgroundColor'];
			}
		}
	}
	if(empty($page['data']['page']['title']) || strexists($page['data']['page']['title'], '啦啦外卖')) {
		$page['data']['page']['title'] = $_W['we7_wmall']['config']['mall']['title'];
	}
	foreach($page['data']['items'] as &$item) {
		$page['parts'][] = $item['id'];
		if($item['id'] == 'fixedsearch') {
			$page['is_has_location'] = 1;
			if(!$item['params']['linkto']) {
				$item['params']['link'] = '/pages/home/search';
			} elseif($item['params']['linkto'] == 1) {
				$item['params']['link'] = '/gohome/pages/tongcheng/search';
			} elseif($item['params']['linkto'] == 2) {
				$item['params']['link'] = '/gohome/pages/haodian/search';
			}
			$page['fixedsearch'] = $item;
		} elseif($item['id'] == 'searchbar') {
			if(!$item['params']['linkto']) {
				$item['params']['link'] = '/pages/home/search';
			} elseif($item['params']['linkto'] == 1) {
				$item['params']['link'] = '/gohome/pages/tongcheng/search';
			} elseif($item['params']['linkto'] == 2) {
				$item['params']['link'] = '/gohome/pages/haodian/search';
			}
		} elseif($item['id'] == 'waimai_allstores') {
			$page['is_has_allstore'] = 1;
			if(check_plugin_perm('svip')) {
				$page['is_has_svip'] = 1;
			}
			if(check_plugin_perm('zhunshibao') && get_plugin_config('zhunshibao.setting.status') == 1) {
				$page['is_has_zhunshibao'] = 1;
			}
			//兼容新版公众号商户列表活动标签判断
			if ($extra['pagetype'] == 'default') {
				$discountstyle = get_plugin_config('diypage.diyTheme.store');
				$item['params']['discountstyle'] = $discountstyle['discount_style'];
				$item['params']['showhotgoods'] = $discountstyle['showhotgoods'];
			}
			$page['stores_list']['diyitems'] = $item;
			$page['stores_list']['orderbys'] = store_orderbys();
			$page['stores_list']['discounts'] = store_discounts();
			if (empty($page['stores_list']['diyitems']['params']['discountstyle'])) {
				$page['stores_list']['diyitems']['params']['discountstyle'] = '1';
			}
			//兼容数据来源及是否显示二级分类
			if(!isset($item['params']['datafrom'])) {
				$item['params']['datafrom'] = 0;
				$item['params']['categoryid'] = 0;
				$item['params']['categorytitle'] = '商户分类';
				$item['params']['showchildcategory'] = 0;
				$item['params']['store_categorys'] = array();
				$item['style']['childcategorycolor'] = '#333333';
				$item['style']['childcategoryactivecolor'] = '#ff2d4b';
			}
			if($item['params']['datafrom'] == 1) {
				$cid = intval($item['params']['categoryid']);
				if(empty($cid)) {
					$item['params']['datafrom'] = 0;
					$item['params']['showchildcategory'] = 0;
				} else {
					$page['cid'] = $cid;
					$categorys = pdo_fetchall('select id, title, thumb, parentid from ' . tablename('tiny_wmall_store_category') . " where uniacid = :uniacid and (id = :id or parentid = :parentid) order by parentid asc ", array(':uniacid' => $_W['uniacid'], ':id' => $cid, ':parentid' => $cid), 'id');
					if(empty($categorys)) {
						$item['params']['datafrom'] = 0;
						$item['params']['categoryid'] = 0;
						$item['params']['showchildcategory'] = 0;
					} else {
						foreach($categorys as &$cate) {
							$cate['thumb'] = tomedia($cate['thumb']);
							if($cate['parentid'] == 0) {
								$item['params']['categoryid'] = $cate['id'];
								$item['params']['categorytitle'] = $cate['title'];
								$cate['title'] = '全部';
							}
						}
						$item['params']['store_categorys'] = array_values($categorys);
					}
					if($item['params']['showchildcategory'] == 1) {
						if(count($categorys) < 2) {
							$item['params']['showchildcategory'] = 0;
						}
					}
				}
			}
		} elseif($item['id'] == 'cart') {
			if($item['params']['showcart'] == 1) {
				$page['is_show_cart'] = 1;
			}
		} elseif($item['id'] == 'redpacket') {
			if($item['params']['showredpacket'] == 1) {
				$page['is_show_redpacket'] = 1;
			}
		} elseif($item['id'] == 'guide') {
			$page['guide'] = $item;
			if(!isset($item['params']['guidedata'])) {
				$item['params']['guidedata'] = 0;
			}
			if(empty($item['params']['guidedata'])) {
				if (!empty($item['data'])) {
					foreach($item['data'] as &$gvalue) {
						$gvalue['imgUrl'] = tomedia($gvalue['imgUrl']);
					}
				}
			} else {
				if($item['params']['guidedata'] == 1) {
					$table = 'tiny_wmall_slide';
					$keys = 'id,title,thumb,link,displayorder';
					$type = 'startpage';
				}
				$condition = ' where uniacid = :uniacid and type = :type and status = 1 ';
				$params = array(
					':uniacid' => $_W['uniacid'],
					':type' => $type
				);
				if($mobile || $_W['agentid'] > 0) {
					$condition .= ' and agentid = :agentid ';
					$params[':agentid'] = $_W['agentid'];
				}
				$slides = pdo_fetchall("select {$keys} from " . tablename($table) . $condition . ' order by displayorder desc', $params);
				$item['data'] = array();
				if(!empty($slides)){
					foreach($slides as $val) {
						$childid =  rand(1000000, 9999999);
						$childid = "C{$childid}";
						$item['data'][$childid] = array(
							'pagePath' => $val['link'],
							'imgUrl' => tomedia($val['thumb']),
						);
					}
				}
			}
			if(empty($item['data'])) {
				unset($page['guide']);
			}
		} elseif($item['id'] == 'copyright') {
			if(!isset($item['params']['datafrom'])) {
				$item['params']['datafrom'] = 0;
				$item['params']['config'] = '';
			}
			if($item['params']['datafrom'] == 1) {
				$item['params']['config'] = $_W['we7_wmall']['config']['mall']['copyright'];
			}
		}
	}
	if(!$mobile) {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'waimai_goods') {
					$item['data'] = get_wxapp_waimai_goods($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
					if (empty($item['style']['marginbottom'])) {
						$item['style']['marginbottom'] = '0';
					}
					if (empty($item['params']['storeshow'])) {
						$item['params']['storeshow'] = '1';
					}
				} elseif($item['id'] == 'goodsTab') {
					$item['data'] = get_wxapp_goodsTab($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'waimai_stores') {
					$item['data'] = get_wxapp_waimai_store($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
					if (empty($item['params']['discountstyle'])) {
						$item['params']['discountstyle'] = '1';
					}
				} elseif($item['id'] == 'storesTab') {
					$item['data'] = get_wxapp_storesTab($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'notice') {
					$item['data'] = get_wxapp_notice($item, false);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'bargain') {
					$result = get_wxapp_bargains($item);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'selective') {
					$result = get_wxapp_waimai_recommend_store($item);
					$item['data'] = $result['data'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'navs') {
					$result = get_wxapp_navs($item);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					$item['row'] = $result['row'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'richtext') {
					$item['params']['content'] = htmlspecialchars_decode($item['params']['content']);
				}  elseif($item['id'] == 'activity') {
					$result = get_wxapp_cubes($item);
					$item['data'] = $result['data'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'picture') {
					if(empty($item['style'])) {
						$item['style'] = array(
							'background' => '#ffffff',
							'paddingtop' => '0',
							'paddingleft' => '0'
						);
					}
					if(empty($item['params'])) {
						$item['params'] = array(
							'picturedata' => 0,
						);
					}
					$result = get_wxapp_slides($item);
					$item['data'] = $result['data'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'selftake_stores') {
					$item['data'] = get_wxapp_selftake_store($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'brand_stores') {
					$item['data'] = get_wxapp_brand_store($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif ($item['id'] == 'service') {
					if(empty($item['params']['servicefrom'])) {
						$item['params']['servicefrom'] = 'meiqia';
					}
					if(!isset($item['params']['iconImg'])) {
						$item['params']['iconImg'] = '';
					}
					if(!isset($item['params']['qq'])) {
						$item['params']['iconImg'] = '';
					}
					if(!isset($item['params']['wxqrcode'])) {
						$item['params']['wxqrcode'] = '';
					}
				} else {
					if($item['id'] == 'picturew') {
						if(empty($item['style'])) {
							$item['style'] = array(
								'background' => '#ffffff',
								'paddingtop' => '0',
								'paddingleft' => '0'
							);
						}
					} elseif(empty($item['id'])) {
						unset($page['data']['items'][$itemid]);
					}
				}
			}
			unset($item);
			pdo_update('tiny_wmall_diypage', array('data' => base64_encode(json_encode($page['data']))), array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
	} else {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'richtext') {
					$item['params']['content'] = base64_decode($item['params']['content']);
				} elseif($item['id'] == 'waimai_goods') {
					$item['data'] = get_wxapp_waimai_goods($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'goodsTab') {
					$item['data'] = get_wxapp_goodsTab($item);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					} else {
						$page['is_has_goodsTab'] = 1;
					}
				} elseif($item['id'] == 'waimai_stores') {
					$item['data'] = get_wxapp_waimai_store($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'storesTab') {
					$item['data'] = get_wxapp_storesTab($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					} else {
						$page['is_has_storesTab'] = 1;
					}
				} elseif($item['id'] == 'selective') {
					$result = get_wxapp_waimai_recommend_store($item, true);
					if(!check_plugin_perm('iglobal') || empty($_W['we7_wmall']['config']['iglobal']['lang'])) {
						$item['params']['showswitch'] = 0;
					}
					if($item['params']['showswitch'] == 1) {
						if($_W['we7_wmall']['config']['iglobal']['lang'] == 'zhcn2uy') {
							$item['params']['ilang'] = array(
								array(
									'key' => 'zh-cn',
									'text' => '汉خەنزۇ ',
									'url' => ivurl('/pages/home/index', array(), true),
									'active' => $_W['LangType'] == 'zh-cn' ? 1 : 0
								),
								array(
									'key' => 'uy',
									'text' => '维ئۇ',
									'url' => ivurl('/pages/home/index', array('dir' => 'vueuy'), true),
									'active' => $_W['LangType'] == 'uy' ? 1 : 0
								),
							);
						}
					}
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'bargain') {
					$_config_bargain['status'] = 1;
					if($extra['pagetype'] == 'default') {
						$_config_bargain = get_plugin_config('bargain');
						if($_config_bargain['status'] != 1 || $_config_bargain['is_home_display'] != 1) {
							$_config_bargain['status'] = 0;
						} else {
							$item['params']['bargainnum'] = $_config_bargain['home_number'] ? $_config_bargain['home_number'] : 8;
						}
					}
					$result = get_wxapp_bargains($item, true);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					if(empty($item['data']) || !$_config_bargain['status']) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif(in_array($item['id'], array('copyright', 'notice', 'img_card'))) {
					$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
					if($item['id'] == 'notice') {
						$item['data'] = get_wxapp_notice($item, true);
						if(empty($item['data'])) {
							unset($page['data']['items'][$itemid]);
						}
					}
				} elseif(in_array($item['id'], array('banner', 'graphic')) && !empty($item['data'])) {
					foreach($item['data'] as &$v) {
						$v['imgurl'] = tomedia($v['imgurl']);
					}
				} elseif($item['id'] == 'picturew' && !empty($item['data'])) {
					foreach($item['data'] as &$v) {
						$v['imgurl'] = tomedia($v['imgurl']);
					}
					$item['data_num'] = count($item['data']);
					if(in_array($item['params']['row'], array('1','5','6'))) {
						$item['data'] = array_values($item['data']);
					} else {
						if($item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum']) {
							$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
							$item['style']['rows_num'] = ceil($item['params']['pagenum']/$item['params']['row']);
							$row_base_height = array(
								'2' => 122,
								'3' => 85,
								'4' => 65,
							);
							$item['style']['base_height'] = $row_base_height[$item['params']['row']];
						}
					}
				} elseif($item['id'] == 'navs' && !empty($item['data'])) {
					$result = get_wxapp_navs($item, true);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					$item['row'] = $result['row'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'danmu') {
					$config_danmu['params'] = $item['params'];
					$result = get_wxapp_danmu($config_danmu);
					if(empty($result['members'])) {
						unset($page['data']['items'][$itemid]);
					} else {
						$item['members'] = $result['members'];
						$page['danmu'] = $result['members'];
					}
				} elseif($item['id'] == 'memberHeader') {
					$item['member'] = $_W['member'];
					if($item['params']['headerstyle'] == 'img') {
						$item['params']['backgroundimgurl'] = tomedia($item['params']['backgroundimgurl']);
					}
					$item['kefu'] = array(
						'status' => 0,
						'notread' => 0
					);
					if(check_plugin_perm('kefu') && get_plugin_config('kefu.system.status') == 1) {
						$item['kefu']['status'] = 1;
						$item['kefu']['notread'] = intval(pdo_fetchcolumn('select sum(notread) from ' . tablename('tiny_wmall_kefu_chat') . ' where uniacid = :uniacid and fansopenid = :fansopenid', array(':uniacid' => $_W['uniacid'], ':fansopenid' => $_W['member']['token'])));
					}
				} elseif($item['id'] == 'memberBindMobile') {
					if(!empty($_W['member']['mobile'])) {
						$item['has_mobile'] = 1;
					}
				} elseif($item['id'] == 'blockNav') {
					if(!empty($item['data'])) {
						foreach($item['data'] as &$value) {
							$value['imgurl'] = tomedia($value['imgurl']);
							if($value['linkurl'] == 'pages/member/redPacket/index') {
								$redpacket_nums = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and status = 1', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));
								if($redpacket_nums > 0) {
									$value['placeholder'] = "{$redpacket_nums}个未使用";
								}
							} elseif($value['linkurl'] == 'pages/member/coupon/index') {
								$coupon_nums = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_coupon_record') . ' where uniacid = :uniacid and uid = :uid and status = 1', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));
								if($coupon_nums > 0) {
									$value['placeholder'] = "{$coupon_nums}个未使用";
								}
							} elseif($value['linkurl'] == 'package/pages/deliveryCard/index') {
								$deliveryCard_status = check_plugin_perm('deliveryCard') && get_plugin_config('deliveryCard.card_apply_status');
								$value['placeholder'] = '暂未购买';
								if($deliveryCard_status && $_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] > TIMESTAMP) {
									$value['placeholder'] = '已购买';
								}
							} elseif($value['linkurl'] == 'pages/member/recharge') {
								$value['placeholder'] = "{$_W['Lang']['dollarSign']}{$_W['member']['credit2']}";
							}
						}
					}
				} elseif($item['id'] == 'activity') {
					$result = get_wxapp_cubes($item, true);
					$item['data'] = array_values($result['data']);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'picture') {
					$result = get_wxapp_slides($item, true);
					$item['data'] = array_values($result['data']);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'gohomeActivity') {
					$item['data'] = get_wxapp_gohome_goods($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'tongchengStatistics') {
					$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
					mload()->model('plugin');
					pload()->model('tongcheng');
					$item['data'] = tongcheng_flow_update();
				} elseif($item['id'] == 'tongcheng') {
					mload()->model('plugin');
					pload()->model('tongcheng');
					$infor_filter = array();
					if($item['params']['informationdata'] != 1) {
						$infor_filter['psize'] = $item['params']['informationnum'];
					}
					$informations = tongcheng_get_informations($infor_filter);
					$page['tongcheng']['informationdata'] = $informations['informations'];
					$page['tongcheng']['has_get_all'] = !$item['params']['informationdata'];
				} elseif($item['id'] == 'haodianSettle') {
					$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
					mload()->model('plugin');
					pload()->model('haodian');
					$item['data'] = haodian_new_settle_info();
				} elseif($item['id'] == 'haodianList') {
					mload()->model('plugin');
					pload()->model('haodian');
					$stores = haodian_store_fetchall(array('get_activity' => 1));
					$page['haodian']['store'] = $stores['store'];
					$page['haodian']['haodian_child_id'] = 0;
					$categorys = pdo_fetchall('select id, title as text from ' . tablename('tiny_wmall_haodian_category') . ' where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':parentid' => 0));
					if(!empty($categorys)) {
						foreach($categorys as &$cate) {
							$cate['children'] = pdo_fetchall('select id, title as text from ' . tablename('tiny_wmall_haodian_category') . ' where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid'], ':parentid' => $cate['id']));
							//此处的数据处理视为了兼容小程序的van-tree-select组件
							if(!empty($cate['children'])) {
								foreach($cate['children'] as &$child) {
									$child['id'] = intval($child['id']);
								}
							}
						}
						if(!empty($categorys[0]['children'])) {
							$page['haodian']['haodian_child_id'] = $categorys[0]['children'][0]['id'];
						}
					}
					$page['haodian']['category'] = $categorys;
				} elseif($item['id'] == 'haodianGroup') {
					$result = get_wxapp_haodian_store($item, true);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'svipGuide') {
					if(check_plugin_perm('svip')) {
						if($_W['member']['svip_status'] == 1) {
							$item['params']['link'] = '/package/pages/svip/mine';
							mload()->model('plugin');
							pload()->model('svip');
							$total = svip_member_redpacket_total();
							$item['params']['text_left'] = "已为我节省{$total}{$_W['Lang']['dollarSignCn']}";
							$item['params']['text_right'] = "{$_W['member']['svip_credit1']}个奖励金";
						} else {
							$item['params']['link'] = '/package/pages/svip/index';
							$config_svip = get_plugin_config('svip.basic');
							$exchange_max = intval($config_svip['exchange_max']);
							$item['params']['text_left'] = "每月领{$exchange_max}个红包";
							$item['params']['text_right'] = '立即开通';
						}
					} else {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'selftake_stores') {
					$item['data'] = get_wxapp_selftake_store($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'brand_stores') {
					$item['data'] = get_wxapp_brand_store($item, true);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'service') {
					$page['is_show_kefu'] = 0;
					if(empty($item['params']['servicefrom'])) {
						$item['params']['servicefrom'] = 'meiqia';
					}
					if($item['params']['showservice'] == 1) {
						if($item['params']['servicefrom'] == 'meiqia') {
							$page['is_show_kefu'] = 1;
						}
						$item['params']['iconImg'] = tomedia($item['params']['iconImg']);
						if($item['params']['servicefrom'] == 'qq') {
							if(empty($item['params']['qq'])) {
								unset($page['data']['items'][$itemid]);
							} else {
								$item['params']['qq_url'] = "http://wpa.qq.com/msgrd?v=3&uin={$item['params']['qq']}&site=qq&menu=yes";
							}
						} elseif($item['params']['servicefrom'] == 'weixin') {
							if(empty($item['params']['wxqrcode'])) {
								unset($page['data']['items'][$itemid]);
							} else {
								$item['params']['wxqrcode'] = tomedia($item['params']['wxqrcode']);
							}
						}
					}
				} elseif($item['id'] == 'hot_goods') {
					$config_hotGoods = get_plugin_config('hotGoods');
					if($config_hotGoods['status'] != 1 || $config_hotGoods['is_home_display'] != 1) {
						unset($page['data']['items'][$itemid]);
					} else {
						$page['is_has_hotGoods'] = 1;
					}
				}
			}
			unset($item);
		}
	}
	return $page;
}

function get_wxapp_gohome_goods($item, $mobile = false) {
	global $_W;
	$type = $item['params']['type'];
	$config = get_plugin_config('gohome.basic');
	if($config['status'][$type] != 1) {
		return array();
	}
	mload()->model('plugin');
	pload()->model($type);
	$filter = array(
		'status' => 1,
		'sid' => $item['params']['sid'],
	);
	if($item['params']['goodsdata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$goodsids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['id'])) {
					$goodsids[] = $data['id'];
				}
			}
			if(!empty($goodsids)) {
				$filter['ids'] = array_unique($goodsids);
			} else {
				return array();
			}
		}
		$filter['psize'] = 50;
	} elseif($item['params']['goodsdata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		$filter['psize'] = isset($item['params']['goodsnum']) ? intval($item['params']['goodsnum']) : 10;
	}
	if($type == 'kanjia') {
		$goods = kanjia_get_activitylist($filter);
	} elseif( $type == 'pintuan') {
		$goods = pintuan_get_activitylist($filter);
	} elseif($type == 'seckill') {
		$goods = seckill_allgoods($filter);
	}
	$item['data'] = array();
	if(!empty($goods)) {
		foreach($goods as $val) {
			$peoplenum = $val['peoplenum'];
			$userlists = array();
			if(!empty($val['userlist'])) {
				$peoplenum = count($val['userlist']);
				foreach($val['userlist'] as $key => $userlist) {
					$userlists[] = tomedia($userlist['avatar']);
				}
			}
			$item['data'][] = array(
				'id' => $val['id'],
				'sid' => $val['sid'],
				'thumb' => $val['thumb'],
				'price' => $val['price'],
				'old_price' => $val['oldprice'],
				'title' => $val['name'],
				'discount' => $val['discount'],
				'falesailed_total' => $val['falesailed_total'] ? $val['falesailed_total'] : $val['sailed'],
				'sailed_percent' => $val['sailed_percent'],
				'peoplenum' => $peoplenum,
				'peopleimg' => array_slice($userlists, 0, 3),
				'total_joinnum' => $val['total_joinnum']
			);
		}
	}
	return $item['data'];
}

function get_wxapp_goodsTab($item, $mobile = false) {
	global $_W;
	if(!empty($item['data'])) {
		foreach($item['data'] as $goodsTabIndex => &$goodsTabItem) {
			$goodsTabItem['imgTitle'] = tomedia($goodsTabItem['imgTitle']);
			$tabGoods = array();
			if($goodsTabItem['goodsdata'] == '0') {
				//手动选择
				if(!empty($goodsTabItem['goods'])) {
					$goodsIds = array();
					foreach($goodsTabItem['goods'] as $goodsItem) {
						$goodsIds[] = $goodsItem['goods_id'];
					}
					$goodsIdsStr = implode(',', $goodsIds);
					$condition = " where a.uniacid = :uniacid and a.status = 1 and a.id in ({$goodsIdsStr}) order by FIELD(a.`id`, $goodsIdsStr) ";
					$params = array(
						':uniacid' => $_W['uniacid'],
					);
					$goods = pdo_fetchall('select a.*, b.id as store_id, b.agentid, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from ' . tablename('tiny_wmall_goods') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition, $params);
					if(!empty($goods)) {
						foreach($goods as $val) {
							if($val['sid'] != $val['store_id']) {
								continue;
							}
							$childid =  rand(1000000, 9999999);
							$childid = "C{$childid}";
							$tabGoods[$childid] = array(
								'sid' => $val['sid'],
								'goods_id' => $val['id'],
								'thumb' => tomedia($val['thumb']),
								'price' => $val['price'],
								'old_price' => $val['old_price'] ? $val['old_price'] : $val['price'],
								'title' => $val['title'],
								'store_title' => $val['store_title'],
								'discount' => ($val['old_price'] == 0 ? 0 : (round(($val['price'] / $val['old_price']) * 10, 1))),
								'sailed' => $val['sailed'],
								'comment_good_percent' => ($val['comment_total'] == 0 ? 0 : (round(($val['comment_good'] / $val['comment_total']) * 100, 2) . "%")),
								'store' => array(
									'id' => $val['sid'],
									'title' => $val['store_title'],
									'logo' => tomedia($val['logo']),
									'send_price' => $val['send_price'],
									'delivery_price' => $val['delivery_price'],
									'delivery_time' => $val['delivery_time'],
								),
								'svip_status' => $val['svip_status'],
								'svip_price' => $val['svip_price'],
							);
							if($val['svip_status'] == 1) {
								$tabGoods[$childid]['price'] = $val['svip_price'];
								$tabGoods[$childid]['discount'] = round(($val['svip_price'] / $tabGoods[$childid]['old_price']) * 10, 1);
							}
						}
					}
				}
			} else {
				//调用天天特价
				$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1 ';
				$params = array(
					':uniacid' => $_W['uniacid'],
					':agentid' => $_W['agentid']
				);
				$goods = pdo_fetchall('select a.discount_price, a.goods_id, a.discount_available_total, b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . " as b on a.goods_id = b.id {$condition} order by a.mall_displayorder desc ", $params);
				if(!empty($goods)) {
					$stores = pdo_fetchall('select distinct(a.sid),b.id as store_id,b.is_rest, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from  ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'sid');
					foreach($goods as &$val) {
						if(empty($stores[$val['sid']]['store_id'])) {
							continue;
						}
						$childid =  rand(1000000, 9999999);
						$childid = "C{$childid}";
						$tabGoods[$childid] = array(
							'sid' => $val['sid'],
							'goods_id' => $val['goods_id'],
							'thumb' => tomedia($val['thumb']),
							'price' => $val['discount_price'],
							'old_price' => $val['old_price'] ? $val['old_price'] : $val['price'],
							'title' => $val['title'],
							'store_title' => $stores[$val['sid']]['store_title'],
							'discount' => ($val['old_price'] == 0 ? 0 : (round(($val['discount_price'] / $val['old_price']) * 10, 1))),
							'sailed' => $val['sailed'],
							'comment_good_percent' => ($val['comment_total'] == 0 ? 0 : (round(($val['comment_good'] / $val['comment_total']) * 100, 2) . "%")),
							'store' => array(
								'id' => $stores[$val['sid']]['store_id'],
								'title' => $stores[$val['sid']]['store_title'],
								'logo'=> tomedia($stores[$val['sid']]['logo']),
								'send_price'=> $stores[$val['sid']]['send_price'],
								'delivery_time'=> $stores[$val['sid']]['delivery_time'],
								'delivery_price'=> $stores[$val['sid']]['delivery_price'],
							),
							'svip_status' => $val['svip_status'],
							'svip_price' => $val['svip_price'],
						);
						if($val['svip_status'] == 1) {
							$tabGoods[$childid]['price'] = $val['svip_price'];
							$tabGoods[$childid]['discount'] = round(($val['svip_price'] / $tabGoods[$childid]['old_price']) * 10, 1);
						}
					}
				}

			}
			if(!empty($tabGoods)) {
				$goodsTabItem['goods'] = $tabGoods;
			} else {
				unset($item['data'][$goodsTabIndex]);
			}
		}
	}
	return $item['data'];
}

function get_wxapp_waimai_goods($item, $mobile = false) {
	global $_W;
	if($item['params']['goodsdata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$goodsids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['goods_id'])) {
					$goodsids[] = $data['goods_id'];
				}
			}
			if(!empty($goodsids)) {
				$item['data'] = array();
				$goodsids_str = implode(',', $goodsids);
				$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . " where uniacid = :uniacid and status = 1 and id in ({$goodsids_str}) order by FIELD(`id`, $goodsids_str)", array(':uniacid' => $_W['uniacid']));
				if(!empty($goods)) {
					foreach($goodsids as $goodsid) {
						foreach($goods as $good) {
							if($good['id'] == $goodsid) {
								$bargain_goods = pdo_fetch('select a.discount_price,a.max_buy_limit,b.status as bargain_status from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain'). ' as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $good['sid'], ':goods_id' => $good['id']));
								if(!empty($bargain_goods['bargain_status'])) {
									$good = array_merge($good, $bargain_goods);
								}
								$childid =  rand(1000000, 9999999);
								$childid = "C{$childid}";
								$item['data'][$childid] = array(
									'goods_id' => $good['id'],
									'sid' => $good['sid'],
									'thumb' => tomedia($good['thumb']),
									'title' => $good['title'],
									'price' => $good['price'],
									'old_price' => $good['old_price'] ? $good['old_price'] : $good['price'],
									'sailed' => $good['sailed'],
									'total' => ($good['total'] != -1 ? $good['total'] : '无限'),
									'discount' => ($good['old_price'] == 0 ? 0 : (round(($good['price'] / $good['old_price']) * 10, 1))),
									'comment_good_percent' => ($good['comment_total'] == 0 ? 0 : (round(($good['comment_good'] / $good['comment_total']) * 100, 2) . "%")),
									'svip_status' => $good['svip_status'],
									'svip_price' => $good['svip_price'],
								);
								if(!empty($good['discount_price'])) {
									$item['data'][$childid]['price'] = $good['discount_price'];
									$item['data'][$childid]['discount'] = round($good['price'] / $good['old_price'], 2) * 10;
								} else {
									if($good['svip_status'] == 1) {
										$item['data'][$childid]['price'] = $good['svip_price'];
										$item['data'][$childid]['discount'] = round(($good['svip_price'] / $item['data'][$childid]['old_price']) * 10, 1);
									}
								}
								$item['data'][$childid]['store'] = pdo_fetch('select id as store_id, title as store_title, logo, send_price, delivery_price, delivery_time from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $good['sid']));
								$item['data'][$childid]['store']['price'] = store_order_condition($good['sid']);
								$item['data'][$childid]['store']['logo'] =  tomedia($item['data'][$childid]['store']['logo']);
								$item['data'][$childid]['store_title'] = $item['data'][$childid]['store']['store_title'];
								if($item['data'][$childid]['store']['store_id']) {
									$item['data'][$childid]['store'] = array(
										'id' => $item['data'][$childid]['store']['store_id'] ? $item['data'][$childid]['store']['store_id'] : 0,
										'title' => $item['data'][$childid]['store']['store_title'],
										'logo'=> $item['data'][$childid]['store']['logo'],
										'send_price'=> $item['data'][$childid]['store']['price']['send_price'],
										'delivery_time'=> $item['data'][$childid]['store']['delivery_time'],
										'price'=> $item['data'][$childid]['store']['price'],
										'delivery_price'=> $item['data'][$childid]['store']['price']['delivery_price'],
									);
								}
							};
						}
					}
				}
			}
		}
	} elseif($item['params']['goodsdata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		//在手机端获取数据
		$item['data'] = array();
		$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1';
		$params = array(
			':uniacid' => $_W['uniacid'],
			':agentid' => $_W['agentid'],
		);
		$limit = intval($item['params']['goodsnum']);
		$limit = $limit ? $limit : 20;
		$goods = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.* from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . " as b on a.goods_id = b.id {$condition} order by a.mall_displayorder desc limit {$limit}", $params);
		if(!empty($goods)) {
			$stores = pdo_fetchall('select distinct(a.sid),b.id as store_id,b.is_rest, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from  ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'sid');
			foreach($goods as &$good) {
				$childid =  rand(1000000, 9999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'goods_id' => $good['id'],
					'sid' => $good['sid'],
					'store_title' => $stores[$good['sid']]['store_title'],
					'thumb' => tomedia($good['thumb']),
					'title' => $good['title'],
					'price' => $good['discount_price'],
					'old_price' => $good['old_price'] ? $good['old_price'] : $good['price'],
					'sailed' => $good['sailed'],
					'total' => ($good['discount_available_total'] != -1 ? $good['discount_available_total'] : '无限'),
					'discount' => ($good['old_price'] == 0 ? 0 : (round(($good['discount_price'] / $good['old_price']) * 10, 1))),
					'comment_good_percent' => ($good['comment_total'] == 0 ? 0 : (round(($good['comment_good'] / $good['comment_total']) * 100, 2) . "%")),
					'store' => array(

					),
				);
				if($stores[$good['sid']]['store_id']) {
					$price = store_order_condition($good['sid']);
					$item['data'][$childid]['store'] = array(
						'id' => $stores[$good['sid']]['store_id'],
						'title' => $stores[$good['sid']]['store_title'],
						'logo'=> tomedia($stores[$good['sid']]['logo']),
						'send_price'=> $stores[$good['sid']]['send_price'],
						'delivery_time'=> $stores[$good['sid']]['delivery_time'],
						'price'=> $price,
						'delivery_price'=> $price['delivery_price'],
					);
				}
			}
		}
	}
	return $item['data'];
}

function get_wxapp_waimai_recommend_store($item, $mobile = false) {
	global $_W;
	if($item['params']['storedata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$storeids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['store_id'])) {
					$storeids[] = $data['store_id'];
				}
			}
			if(!empty($storeids)) {
				$item['data'] = array();
				$storeids_str = implode(',', $storeids);
				if($mobile || $_W['agentid'] > 0) {
					$condition = " where uniacid = :uniacid and agentid = :agentid and status = 1 and id in ({$storeids_str}) order by is_rest asc, displayorder desc";
					$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
				} else {
					$condition = " where uniacid = :uniacid and status = 1 and id in ({$storeids_str}) order by is_rest asc, displayorder desc";
					$params = array(':uniacid' => $_W['uniacid']);
				}
				$stores = pdo_fetchall('select id, title, logo, is_rest, forward_mode, forward_url from ' . tablename('tiny_wmall_store') . $condition, $params);
			}
		}
	} elseif($item['params']['storedata'] == '1') {
		$limit = intval($item['params']['storenum']);
		$limit = $limit ? $limit : 20;
		if($mobile || $_W['agentid'] > 0) {
			$condition = " where uniacid = :uniacid and agentid = :agentid and status = 1 and is_recommend = 1 order by is_rest asc, displayorder desc limit {$limit}";
			$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
		} else {
			$condition = " where uniacid = :uniacid and status = 1 and is_recommend = 1 order by is_rest asc, displayorder desc limit {$limit}";
			$params = array(':uniacid' => $_W['uniacid']);
		}
		$stores = pdo_fetchall('select id, title, logo, forward_mode, forward_url from ' . tablename('tiny_wmall_store') . $condition, $params);
	}
	$item['data'] = array();
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			$row['store_id'] = $row['id'];
			$row['logo'] = tomedia($row['logo']);
			$childid =  rand(1000000, 9999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = $row;
			unset($row);
		}
	}
	$item['data_num'] = count($item['data']);
	if($mobile && ($item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum'])) {
		$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
	}
	$result = array(
		'data' => $item['data'],
		'data_num' => $item['data_num']
	);
	return $result;
}

function get_wxapp_waimai_store($item, $mobile = false) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid and status = 1 and is_waimai = 1 ';
	$params = array(':uniacid' => $_W['uniacid']);
	if($mobile) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	if($item['params']['storedata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$storeids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['store_id'])) {
					$storeids[] = $data['store_id'];
				}
			}
			if(!empty($storeids)) {
				$item['data'] = array();
				$storeids_str = implode(',', $storeids);
				$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition . " and id in ({$storeids_str}) order by FIELD(`is_rest`, 0, 1), FIELD(`id`, $storeids_str)", $params);
			}
		}
	} elseif($item['params']['storedata'] == '1') {
		if(empty($mobile)) {
			return $item['data'];
		}
		$limit = intval($item['params']['storenum']);
		$limit = $limit ? $limit : 20;
		$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url,business_hours from ' . tablename('tiny_wmall_store') . "{$condition} and is_recommend = 1 order by is_rest asc, displayorder desc limit {$limit}", $params);
	} elseif($item['params']['storedata'] == '2') {
		$limit = intval($item['params']['storenum']);
		$limit = $limit ? $limit : 20;
		if($item['params']['categoryid'] > 0) {
			$condition .= ' and (cate_parentid1 = :cid or cate_parentid2 = :cid or cate_childid1 = :cid or cate_childid2 = :cid)';
			$params[':cid'] = $item['params']['categoryid'];
		}
		$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition  . " order by is_rest asc, displayorder desc limit {$limit}", $params);
	} elseif($item['params']['storedata'] == '3') {
		unset($item['data']);
		$store_activity = pdo_getall('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'status' => 1, 'type' => $item['params']['activitytype']), array('sid'), 'sid');
		if(!empty($store_activity)) {
			$store_ids = array_keys($store_activity);
			$storeids_str = implode(',', $store_ids);
			$condition .= " and id in ({$storeids_str})";
			$limit = intval($item['params']['storenum']);
			$limit = $limit ? $limit : 20;
			$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition  . " order by is_rest asc, displayorder desc limit {$limit}", $params);
		}
	}
	$item['data'] = array();
	if(!empty($stores)) {
		$_config_mall = $_W['we7_wmall']['config']['mall'];
		if(empty($_config_mall['delivery_title'])) {
			$_config_mall['delivery_title'] = '平台专送';
		}
		$store_label = store_category_label();
		foreach($stores as &$row) {
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			$row['store_id'] = $row['id'];
			if($row['label'] > 0) {
				$row['label_color'] = $store_label[$row['label']]['color'];
				$row['label_cn'] = $store_label[$row['label']]['title'];
			}
			$row['logo'] = tomedia($row['logo']);
			$row['price'] = store_order_condition($row['id']);
			$row['send_price'] = $row['price']['send_price'];
			$row['delivery_price'] = $row['price']['delivery_price'];
			if($row['delivery_mode'] == 2 && $row['delivery_type'] != 2) {
				$row['delivery_title'] = $_config_mall['delivery_title'];
			}
			$row['score'] = floatval($row['score']);
			$row['score_cn'] = round($row['score'] / 5, 2) * 100;
			$row['hot_goods'] = array();
			$hot_goods = pdo_fetchall('select id,title,price,old_price,thumb,svip_status,svip_price from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			if(!empty($hot_goods)) {
				foreach($hot_goods as &$goods) {
					$goods['thumb'] = tomedia($goods['thumb']);
					if($goods['old_price'] > 0 && $goods['old_price'] > $goods['price']) {
						$old_price = $goods['old_price'];
						$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
					} else {
						$old_price = $goods['price'];
						$goods['old_price'] = 0;
						$goods['discount'] = 0;
					}
					if($goods['svip_status'] == 1) {
						$goods['price'] = $goods['svip_price'];
						$goods['old_price'] = $old_price;
						$goods['discount'] = round(($goods['price'] / $old_price) * 10, 1);
					}
					$childid =  rand(1000000, 9999999);
					$childid = "C{$childid}";
					$row['hot_goods'][$childid] = $goods;
				}
				$row['hot_goods_num'] = count($row['hot_goods']);
				unset($hot_goods);
			}
			$row['activity'] = array();
			$activitys = store_fetch_activity($row['id']);
			if(!empty($activitys['items'])) {
				if(!empty($activitys['items']['zhunshibao'])) {
					$row['zhunshibao_cn'] = '准时宝';
					unset($activitys['items']['zhunshibao']);
				}
				foreach($activitys['items'] as $avtivity_item) {
					if(empty($avtivity_item['title'])) {
						continue;
					}
					$row['activity']['items'][] = array(
						'type' => $avtivity_item['type'],
						'title' => $avtivity_item['title'],
					);
				}
				$row['activity']['num'] = $activitys['num'];
				$row['activity']['is_show_all'] = 0;
				$row['activity']['labels'] = $activitys['labels'];
				$row['activity']['labels_num'] = count($row['activity']['labels']);
				unset($activitys);
			}
			$row['business_hours'] = iunserializer($row['business_hours']);
			if(!$row['is_rest'] && !store_is_in_business_hours($row['business_hours'])) {
				$row['is_rest_reserve'] = 1;
				$rest_order_info = store_rest_start_delivery_time($row);
				$row['rest_reserve_cn'] = $rest_order_info['delivery_time_cn'];
			}
			unset($row['business_hours']);
			$childid =  rand(1000000, 9999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = $row;
			unset($row);
		}
	}
	return $item['data'];
}

function get_wxapp_storesTab($item, $mobile = false) {
	global $_W;
	$condition = ' where uniacid = :uniacid and status = 1 and is_waimai = 1 ';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	if($mobile) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	if(!empty($item['data'])) {
		foreach($item['data'] as $storesTabIndex => &$storesTabItem) {
			$storesTabItem['imgTitle'] = tomedia($storesTabItem['imgTitle']);
			$tabStores = array();
			$limit = intval($storesTabItem['storenum']) ? intval($storesTabItem['storenum']) : 20;
			if($storesTabItem['storedata'] == '0') {
				$storesIds = array();
				foreach($storesTabItem['stores'] as $storesItem) {
					$storesIds[] = $storesItem['store_id'];
				}
				$storesIdsStr = implode(',', $storesIds);
				$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition . " and id in ({$storesIdsStr}) order by FIELD(`is_rest`, 0, 1), FIELD(`id`, $storesIdsStr)", $params);
			} elseif($storesTabItem['storedata'] == '1') {
				if(empty($mobile)) {
					return $item['data'];
				}
				$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url,business_hours from ' . tablename('tiny_wmall_store') . "{$condition} and is_recommend = 1 order by is_rest asc, displayorder desc limit {$limit}", $params);
			} elseif($storesTabItem['storedata'] == '2') {
				if($storesTabItem['categoryid'] > 0) {
					$condition .= ' and (cate_parentid1 = :cid or cate_parentid2 = :cid or cate_childid1 = :cid or cate_childid2 = :cid)';
					$params[':cid'] = $storesTabItem['categoryid'];
				}
				$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition  . " order by is_rest asc, displayorder desc limit {$limit}", $params);
			} elseif($storesTabItem['storedata'] == '3') {
				$store_activity = pdo_getall('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'status' => 1, 'type' => $storesTabItem['activitytype']), array('sid'), 'sid');
				if(!empty($store_activity)) {
					$store_ids = array_keys($store_activity);
					$storeids_str = implode(',', $store_ids);
					$condition .= " and id in ({$storeids_str})";
					$stores = pdo_fetchall('select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from ' . tablename('tiny_wmall_store') . $condition  . " order by is_rest asc, displayorder desc limit {$limit}", $params);
				}
			}
			if(!empty($stores)) {
				$_config_mall = $_W['we7_wmall']['config']['mall'];
				if(empty($_config_mall['delivery_title'])) {
					$_config_mall['delivery_title'] = '平台专送';
				}
				$store_label = store_category_label();
				foreach($stores as &$row) {
					$childid =  rand(1000000, 9999999);
					$childid = "C{$childid}";
					$price = store_order_condition($row['id']);
					$tabStores[$childid] = array(
						'title' => $row['title'],
						'delivery_time' => $row['delivery_time'],
						'sailed' => $row['sailed'],
						'url' => store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']),
						'store_id' => $row['id'],
						'logo' => tomedia($row['logo']),
						'send_price' => $price['send_price'],
						'delivery_price' => $price['delivery_price'],
						'score' => floatval($row['score']),
						'score_cn' => round($row['score'] / 5, 2) * 100,
					);
					if($row['label'] > 0) {
						$tabStores[$childid]['label_color'] =$store_label[$row['label']]['color'];
						$tabStores[$childid]['label_cn'] = $store_label[$row['label']]['title'];
					}
					if($row['delivery_mode'] == 2 && $row['delivery_type'] != 2) {
						$tabStores[$childid]['delivery_title'] = $_config_mall['delivery_title'];
					}
					$row['activity'] = array();
					$activitys = store_fetch_activity($row['id']);
					if(!empty($activitys['items'])) {
						if(!empty($activitys['items']['zhunshibao'])) {
							$tabStores[$childid]['zhunshibao_cn'] = '准时宝';
							unset($activitys['items']['zhunshibao']);
						}
						foreach($activitys['items'] as $avtivity_item) {
							if(empty($avtivity_item['title'])) {
								continue;
							}
							$row['activity']['items'][] = array(
								'type' => $avtivity_item['type'],
								'title' => $avtivity_item['title'],
							);
						}
						$row['activity']['num'] = $activitys['num'];
						$row['activity']['is_show_all'] = 0;
						$row['activity']['labels'] = $activitys['labels'];
						$row['activity']['labels_num'] = count($row['activity']['labels']);
						$tabStores[$childid]['activity'] = $row['activity'];
						unset($activitys);
					}
					$tabStores[$childid]['business_hours'] = iunserializer($row['business_hours']);
					if(!$row['is_rest'] && !store_is_in_business_hours($row['business_hours'])) {
						$tabStores[$childid]['is_rest_reserve'] = 1;
						$rest_order_info = store_rest_start_delivery_time($row);
						$tabStores[$childid]['rest_reserve_cn'] = $rest_order_info['delivery_time_cn'];
					}
					unset($row['business_hours']);
					$hot_goods = pdo_fetchall('select id,title,price,old_price,thumb,svip_status,svip_price from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
					if(!empty($hot_goods)) {
						foreach($hot_goods as &$goods) {
							$goods['thumb'] = tomedia($goods['thumb']);
							if($goods['old_price'] > 0 && $goods['old_price'] > $goods['price']) {
								$old_price = $goods['old_price'];
								$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
							} else {
								$old_price = $goods['price'];
								$goods['old_price'] = 0;
								$goods['discount'] = 0;
							}
							if($goods['svip_status'] == 1) {
								$goods['price'] = $goods['svip_price'];
								$goods['old_price'] = $old_price;
								$goods['discount'] = round(($goods['price'] / $old_price) * 10, 1);
							}
							$hot_childid =  rand(1000000, 9999999);
							$hot_childid = "C{$hot_childid}";
							$row['hot_goods'][$hot_childid] = $goods;
						}
						$tabStores[$childid]['hot_goods'] = $row['hot_goods'];
						$tabStores[$childid]['hot_goods_num'] = count($row['hot_goods']);
						unset($hot_goods);
					}
					unset($row);
				}
			}
			if(!empty($tabStores)) {
				$storesTabItem['stores'] = $tabStores;
			} else {
				unset($item['data'][$storesTabIndex]);
			}
		}
	}
	return $item['data'];
}

function get_wxapp_brand_store($item, $mobile = false) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid and status = 1 and is_waimai = 1 ';
	$params = array(':uniacid' => $_W['uniacid']);
	if($mobile || $_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	$contents = array();
	if($item['params']['storedata'] == '0') {
		if(!empty($item['data']) && is_array($item['data'])) {
			$storeids = array();
			foreach($item['data'] as $data) {
				if(!empty($data['store_id'])) {
					$storeids[] = $data['store_id'];
					$contents[$data['store_id']] = $data['content'];
				}
			}
			if(!empty($storeids)) {
				$item['data'] = array();
				$storeids_str = implode(',', $storeids);
				$stores = pdo_fetchall('select id, title, logo, is_rest, forward_mode, forward_url, `data` from ' . tablename('tiny_wmall_store') . $condition . " and id in ({$storeids_str}) order by FIELD(`id`, $storeids_str), is_rest asc", $params);
			}
		}
	}
	$item['data'] = array();
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			$row['store_id'] = $row['id'];
			$row['logo'] = tomedia($row['logo']);
			$row['content'] = $contents[$row['id']];
			$row['data'] = iunserializer($row['data']);
			$row['shopSign'] = tomedia($row['data']['shopSign']);
			$row['hot_goods'] = array();
			$hot_goods = pdo_fetchall('select id,title,price,old_price,thumb,svip_status,svip_price from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			if(!empty($hot_goods)) {
				foreach($hot_goods as &$goods) {
					$goods['thumb'] = tomedia($goods['thumb']);
					if($goods['old_price'] > 0 && $goods['old_price'] > $goods['price']) {
						$old_price = $goods['old_price'];
						$goods['discount'] = round(($goods['price'] / $goods['old_price']) * 10, 1);
					} else {
						$old_price = $goods['price'];
						$goods['old_price'] = 0;
						$goods['discount'] = 0;
					}
					if($goods['svip_status'] == 1) {
						$goods['price'] = $goods['svip_price'];
						$goods['old_price'] = $old_price;
						$goods['discount'] = round(($goods['price'] / $old_price) * 10, 1);
					}
					$childid =  rand(1000000, 9999999);
					$childid = "C{$childid}";
					$row['hot_goods'][$childid] = $goods;
				}
				$row['hot_goods_num'] = count($row['hot_goods']);
				unset($hot_goods);
			}
			$row['activity'] = array();
			$activitys = store_fetch_activity($row['id']);
			if(!empty($activitys['items'])) {
				foreach($activitys['items'] as $avtivity_item) {
					if(empty($avtivity_item['title'])) {
						continue;
					}
					$row['activity']['items'][] = array(
						'type' => $avtivity_item['type'],
						'title' => $avtivity_item['title'],
					);
				}
				$row['activity']['num'] = $activitys['num'];
				$row['activity']['is_show_all'] = 0;
				$row['activity']['labels'] = $activitys['labels'];
				$row['activity']['labels_num'] = count($row['activity']['labels']);
				unset($activitys);
			}
			$childid =  rand(1000000, 9999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = $row;
			unset($row);
		}
	}
	return $item['data'];
}

function get_wxapp_selftake_store($item, $mobile = false) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid and status = 1 and is_waimai = 1 and delivery_type > 1 ';
	$params = array(':uniacid' => $_W['uniacid']);
	if($mobile || $_W['agentid'] > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	$lat = trim($_GPC['lat']) ? trim($_GPC['lat']) : '37.80081';
	$lng = trim($_GPC['lng']) ? trim($_GPC['lng']) : '112.57543';
	if($item['params']['storedata'] == '1') {
		$limit = intval($item['params']['storenum']);
		$limit = $limit ? $limit : 10;
			$stores = pdo_fetchall('select id, title, logo, delivery_time, delivery_mode,forward_mode, forward_url, business_hours, 				ROUND(
					6378.138 * 2 * ASIN(
						SQRT(
							POW(
								SIN(
									(
										'.$lat.' * 3.141592654 / 180 - location_x * 3.141592654 / 180
									) / 2
								),
								2
							) + COS('.$lat.' * 3.141592654 / 180) * COS(location_x * 3.141592654 / 180) * POW(
								SIN(
									(
									   '.$lng.'  * 3.141592654 / 180 - location_y * 3.141592654 / 180
									) / 2
								),
								2
							)
						)
			) * 1000) as distance from ' . tablename('tiny_wmall_store') . $condition  . " order by is_rest asc, displayorder desc limit {$limit}", $params);
	}
	$item['data'] = array();
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['url'] = store_forward_url($row['id'], $row['forward_mode'], $row['forward_url']);
			$row['store_id'] = $row['id'];
			$row['logo'] = tomedia($row['logo']);
			$row['price'] = store_order_condition($row['id']);
			$row['send_price'] = $row['price']['send_price'];
			$row['delivery_price'] = $row['price']['delivery_price'];
			$row['distance'] = round($row['distance']/1000, 1);
			$row['activity'] = array();
			$activitys = store_fetch_activity($row['id']);
			if(!empty($activitys['items'])) {
				foreach($activitys['items'] as $avtivity_item) {
					if(empty($avtivity_item['title'])) {
						continue;
					}
					$row['activity']['items'][] = array(
						'type' => $avtivity_item['type'],
						'title' => $avtivity_item['title'],
					);
				}
				$row['activity']['num'] = $activitys['num'];
				unset($activitys);
			}
			$childid =  rand(1000000, 9999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = $row;

			unset($row);
		}
	}
	return $item['data'];
}


function get_wxapp_notice($item, $mobile = false, $from='wxapp'){
	global $_W;
	if($item['params']['noticedata'] == 0 || $item['params']['noticedata'] == 2) {
		if($item['params']['noticedata'] == 0) {
			$table = 'tiny_wmall_notice';
			$keys = 'id, title, displayorder, link, status, wxapp_link';
		} elseif($item['params']['noticedata'] == 2) {
			$table = 'tiny_wmall_gohome_notice';
			$keys = 'id, title, displayorder, status, wxapp_link';
		}
		$condition = ' where uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if($item['params']['noticedata'] == 0) {
			$condition .= ' and type = :type';
			$params[':type'] = 'member';
		}
		if($mobile || $_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid';
			$params[':agentid'] = $_W['agentid'];
		}
		$noticenum = $item['params']['noticenum'];
		$notice = pdo_fetchall("select {$keys} from " . tablename($table) . $condition . ' and status = 1 order by displayorder desc limit '.$noticenum, $params);
		$item['data'] = array();
		if (!empty($notice)) {
			foreach ($notice as &$data) {
				$childid =  rand(1000000, 9999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'id' => $data['id'],
					'title' => $data['title'],
					'linkurl' => $data['wxapp_link'],
				);
			}
		}
	}
	return $item['data'];
}

//获取好店组的商户数据
function get_wxapp_haodian_store($item, $mobile = false) {
	global $_W;
	mload()->model('plugin');
	pload()->model('haodian');
	$psize = intval($item['params']['storenum']) ? intval($item['params']['storenum']) : 20;
	$stores = haodian_store_fetchall(array('psize' => $psize));
	$item['data'] = $stores['store'];
	$item['data_num'] = count($item['data']);
	if($mobile && $item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum']) {
		$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
	}
	$result = array(
		'data' => $item['data'],
		'data_num' => $item['data_num'],
	);
	return $result;
}

function get_wxapp_bargains($item, $mobile = false) {
	global $_W;
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	if($mobile || $_W['agentid'] > 0) {
		$condition .= ' and a.agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	$limit = intval($item['params']['bargainnum']);
	$limit = $limit ? $limit : 20;
	$bargains = pdo_fetchall('select a.discount_price,a.goods_id, a.bargain_id,b.title,b.thumb,b.price,b.sid,c.title as store_title, c.is_rest from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id left join ' . tablename('tiny_wmall_store') . "as c on b.sid = c.id {$condition} and a.status = 1 and b.status = 1 and c.status = 1 order by c.is_rest asc, a.mall_displayorder desc limit {$limit}", $params);
	$item['data'] = array();
	if(!empty($bargains)) {
		foreach($bargains as $val) {
			$childid =  rand(1000000, 9999999);
			$childid = "C{$childid}";
			$item['data'][$childid] = array(
				'thumb' => tomedia($val['thumb']),
				'discount' => round(($val['discount_price'] / $val['price'] * 10), 1),
				'goods_id'=> $val['goods_id'],
				'bargain_id'=> $val['bargain_id'],
				'title'=> $val['title'],
				'discount_price'=> $val['discount_price'],
				'price'=> $val['price'],
				'sid'=> $val['sid'],
				'store_title' => $val['store_title']
			);
		}
	}
	$item['data_num'] = count($item['data']);
	if($mobile && $item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum']) {
		$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
	}
	$result = array(
		'data' => $item['data'],
		'data_num' => $item['data_num'],
	);
	return $result;
}
function get_wxapp_navs($item, $mobile = false) {
	global $_W;
	if($item['params']['navsdata'] == 0) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		if($item['params']['navsdata'] == 1) {
			$table = 'tiny_wmall_store_category';
			$keys = 'id,parentid,title,thumb,wxapp_link,displayorder';
			$empty_link = 'pages/home/category?cid=';
		} elseif($item['params']['navsdata'] == 2) {
			$table = 'tiny_wmall_gohome_category';
			$keys = 'id,title,thumb,wxapp_link,displayorder';
			$empty_link = '';
		} elseif($item['params']['navsdata'] == 3) {
			$table = 'tiny_wmall_tongcheng_category';
			$keys = 'id,title,thumb,link,displayorder';
			$empty_link = '/gohome/pages/tongcheng/category?id=';
		} elseif($item['params']['navsdata'] == 4) {
			$table = 'tiny_wmall_haodian_category';
			$keys = 'id,title,thumb,link,displayorder';
			$empty_link = '/gohome/pages/haodian/category?cid=';
		}
		$condition = ' where uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if($mobile || $_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid';
			$params[':agentid'] = $_W['agentid'];
		}
		if(in_array( $item['params']['navsdata'], array(1, 3, 4))) {
			$condition .= ' and parentid = 0';
		}
		$limit = intval($item['params']['navsnum']) ? intval($item['params']['navsnum']) : 4;
		$navs = pdo_fetchall("select {$keys} from " .tablename($table) . $condition . ' and status = 1 order by displayorder desc limit ' . $limit, $params);
		$item['data'] = array();
		if(!empty($navs)){
			foreach($navs as $val) {
				$childid = rand(1000000, 9999999);
				$childid = "C{$childid}";
				if(in_array($item['params']['navsdata'], array(3, 4))) {
					$val['wxapp_link'] = $val['link'];
				}
				$item['data'][$childid] = array(
					'linkurl' => empty($val['wxapp_link']) ? (empty($empty_link) ? '' : "{$empty_link}{$val['id']}") : $val['wxapp_link'],
					'text' => $val['title'],
					'imgurl' => tomedia($val['thumb']),
				);
			}
		}
	}
	$item['data_num'] = count($item['data']);
	if($mobile && $item['params']['showtype'] == 1 && $item['data_num'] > $item['params']['pagenum']) {
		$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
	}

	$result = array(
		'data' => $item['data'],
		'data_num' => $item['data_num'],
		'row' => ceil($item['params']['pagenum']/$item['params']['rownum']),
	);
	return $result;
}

function get_wxapp_cubes($item, $mobile = false) {
	global $_W;
	if(empty($item['params']['activitydata'])) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		$condition = ' where uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if($mobile || $_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid';
			$params[':agentid'] = $_W['agentid'];
		}
		$cubes = pdo_fetchall('select id,title,tips,thumb,wxapp_link,link from' .tablename('tiny_wmall_cube'). $condition . ' order by displayorder desc', $params);
		$item['data'] = array();
		if(!empty($cubes)){
			foreach($cubes as $val) {
				$childid =  rand(1000000, 9999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'linkurl' => $val['wxapp_link'],
					'text' => $val['title'],
					'imgurl' => tomedia($val['thumb']),
					'placeholder' => $val['tips'],
					'color' => '#ff2d4b',
					'placeholderColor' => '#7b7b7b',
				);
			}
		}
	}
	$result = array(
		'data' => $item['data']
	);
	return $result;
}

function get_wxapp_slides($item, $mobile = false) {
	global $_W;
	if(empty($item['params']['picturedata'])) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		if($item['params']['picturedata'] == 1) {
			$table = 'tiny_wmall_slide';
			$keys = 'id,title,thumb,wxapp_link,link,displayorder';
			$type = 'homeTop';
		} elseif($item['params']['picturedata'] == 2) {
			$table = 'tiny_wmall_gohome_slide';
			$keys = 'id,title,thumb,wxapp_link,displayorder';
			$type = 'gohome';
		} elseif($item['params']['picturedata'] == 3) {
			$table = 'tiny_wmall_gohome_slide';
			$keys = 'id,title,thumb,wxapp_link,displayorder';
			$type = 'tongcheng';
		} elseif($item['params']['picturedata'] == 4) {
			$table = 'tiny_wmall_gohome_slide';
			$keys = 'id,title,thumb,wxapp_link,displayorder';
			$type = 'haodian';
		}
		$condition = ' where uniacid = :uniacid and type = :type and status = 1 ';
		$params = array(
			':uniacid' => $_W['uniacid'],
			':type' => $type
		);
		if($mobile || $_W['agentid'] > 0) {
			$condition .= ' and agentid = :agentid ';
			$params[':agentid'] = $_W['agentid'];
		}
		$slides = pdo_fetchall("select {$keys} from " . tablename($table) . $condition . ' order by displayorder desc', $params);
		$item['data'] = array();
		if(!empty($slides)){
			foreach($slides as $val) {
				$childid =  rand(1000000, 9999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'linkurl' => empty($val['wxapp_link']) ? $val['link'] : $val['wxapp_link'],
					'imgurl' => tomedia($val['thumb']),
				);
			}
		}
	}
	$result = array(
		'data' => $item['data']
	);
	return $result;
}

function get_wxapp_pages($filter = array(), $search = array('*')) {
	global $_W;
	$condition = ' where uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid'],
	);
	$table = 'tiny_wmall_diypage';
	if($filter['from'] == 'wechat') {
		$condition .= ' and `version` = :version';
		$params[':version'] = 2;
	}
	if(!empty($filter) && !empty($filter['type'])) {
		$condition .= ' and type = :type';
		$params[':type'] = intval($filter['type']);
	}
	if(!empty($search)) {
		$search = implode(',', $search);
	}
	$pages = pdo_fetchall("select {$search} from " . tablename($table) . $condition, $params);
	return $pages;
}