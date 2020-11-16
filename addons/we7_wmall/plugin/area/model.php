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

//获取平台的配送区域
function area_plateform_areas_fetchAll($filter = array()) {
	global $_W, $_GPC;
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	if(empty($filter)) {
		$filter = $_GPC;
	} else {
		$filter = array_merge($_GPC, $filter);
	}
	$agentid = isset($filter['agentid']) ? intval($filter['agentid']) : $_W['agentid'];
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid ';
		$params[':agentid'] = $agentid;
	}
	$status = isset($filter['status']) ? intval($filter['status']) : -1;
	if($status > -1) {
		$condition .= ' and status = :status ';
		$params[':status'] = $status;
	}
	$areas = pdo_fetchall('select * from ' . tablename('tiny_wmall_area_list') . " {$condition} order by displayorder desc, id desc", $params, 'id');
	$data = array();
	if(!empty($areas)) {
		foreach($areas as $key => $value) {
			if(empty($value['parentid'])) {
				if(empty($value['title'])) {
					continue;
				}
				$data[$value['id']] = $value;
			} else {
				if(empty($value['title'])) {
					continue;
				}
				$data[$value['parentid']]['child'] = $value;
			}
		}
	}
	if(!empty($data)) {
		foreach($data as $key => $value) {
			if(!empty($value['child'])) {
				$data[$key]['child'] = array_values($data[$key]['child']);
			} else {
				unset($data[$key]);
			}
		}
		$data = array_values($data);
	}
	return $data;
}

//获取平台显示状态的配送区域
function area_plateform_area_all($force_update = true) {
	global $_W;
	//$cache_key = "we7_wmall:areas:{$_W['uniacid']}:{$_W['agentid']}";
	$cache_key = "we7_wmall:areas:{$_W['uniacid']}";
	$data = cache_read($cache_key);
	if(!empty($data) && !$force_update) {
		return $data;
	}
	$condition = " where uniacid = :uniacid and status = :status";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':status' => 1
	);
	if($_W['agentid'] > 0 && 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $_W['agentid'];
	}
	$areas = pdo_fetchall('select * from ' . tablename('tiny_wmall_area_list') . " {$condition} order by displayorder desc, id asc ", $params, 'id');
	$areas_group = array();
	if(!empty($areas)) {
		foreach($areas as $key => $value) {
			if(empty($value['parentid'])) {
				if(empty($value['title']) || empty($value['location_x']) || empty($value['location_y'])) {
					continue;
				}
				$areas_group[$value['id']] = $value;
			}
		}

		foreach($areas as $key => $value) {
			if(!empty($value['parentid'])) {
				if(empty($value['title']) || empty($value['location_x']) || empty($value['location_y'])) {
					continue;
				}
				$areas_group[$value['parentid']]['child'][$value['id']] = $value;
			}
		}
	}
	if(!empty($areas_group)) {
		foreach($areas_group as $key => $value) {
			if(!empty($value['child'])) {
				$areas_group[$key]['child'] = array_values($areas_group[$key]['child']);
			} else {
				unset($areas_group[$key]);
			}
		}
		$areas_group = array_values($areas_group);
	}
	$cache_value = array(
		'areas' => $areas,
		'areas_group' => $areas_group
	);
	cache_write($cache_key, $cache_value);
	return $cache_value;
}

//检测配送区域是否有效
function area_check_area_status($id) {
	global $_W;
	if(empty($id)) {
		return false;
	}
	$cache = area_plateform_area_all();
	$areas = $cache['areas'];
	if(empty($areas) || empty($areas[$id]) || empty($areas[$id]['status']) || empty($areas[$areas[$id]['parentid']]['status'])) {
		return false;
	}
	return true;
}
//获取商户设置的配送区域
function area_store_areas_fetchAll($storeOrId) {
	global $_W;
	$areas = area_plateform_area_all();
	$area_group = $areas['areas_group'];
	if(is_array($storeOrId)) {
		$store = $storeOrId;
		if(empty($store['delivery_areas1'])) {
			$store = store_fetch($store['id'], array('delivery_areas1'));
		}
	} else {
		$store = store_fetch($storeOrId, array('delivery_areas1'));
	}
	if(!empty($store)) {
		$ids = $store['delivery_areas1_ids'];
		$temp = $area_group;
		if(!empty($temp)) {
			foreach($temp as $key1 => $parent) {
				if(!empty($parent['child'])) {
					foreach($parent['child'] as $key2 => $child) {
						if(!in_array($child['id'], $ids)) {
							unset($temp[$key1]['child'][$key2]);
						}
					}
				}
			}
			foreach($temp as $key => $value) {
				if(empty($value['child'])) {
					unset($area_group[$key]);
				}
			}
		}
		if(!empty($area_group)) {
			foreach($area_group as $key1 => $parent) {
				if(!empty($parent['child'])) {
					foreach ($parent['child'] as $key2 => $child) {
						if(!in_array($child['id'], $ids)) {
							$area_group[$key1]['child'][$key2]['status'] = 0;
						}
					}
				}
			}
		}
	}
	return array_values($area_group);
}


