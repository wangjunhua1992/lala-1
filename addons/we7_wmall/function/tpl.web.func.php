<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function tpl_form_field_fans($name, $value, $scene = 'notify', $required = false) {
	global $_W;
	if(empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	$s = '';
	if (!defined('TPL_INIT_TINY_FANS')) {
		$option = array(
			'scene' => $scene,
		);
		$option = json_encode($option);
		$s = '
		<script type="text/javascript">
			function showFansDialog(elm) {
				var btn = $(elm);
				var openid_wxapp = btn.parent().prev();
				var openid = btn.parent().prev().prev();
				var avatar = btn.parent().prev().prev().prev();
				var nickname = btn.parent().prev().prev().prev().prev();
				var img = btn.parent().parent().next().find("img");
				irequire(["web/tiny"], function(tiny){
					tiny.selectfan(function(fans){
						console.log(fans);
						if(img.length > 0){
							img.get(0).src = fans.avatar;
						}
						openid_wxapp.val(fans.openid_wxapp);
						openid.val(fans.openid);
						avatar.val(fans.avatar);
						nickname.val(fans.nickname);
					}, '. $option .');
				});
			}
		</script>';
		define('TPL_INIT_TINY_FANS', true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" name="' . $name . '[nickname]" value="' . $value['nickname'] . '" class="form-control" readonly ' . ($required ? 'required' : '') . '>
			<input type="hidden" name="' . $name . '[avatar]" value="' . $value['avatar'] . '">
			<input type="hidden" name="' . $name . '[openid]" value="' . $value['openid'] . '">
			<input type="hidden" name="' . $name . '[openid_wxapp]" value="' . $value['openid_wxapp'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showFansDialog(this);">选择粉丝</button>
			</span>
		</div>
		<div class="input-group" style="margin-top:.5em;">
			<img src="' . $value['avatar'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'头像未找到.\'" class="img-responsive img-thumbnail" width="150" />
		</div>';
	return $s;
}

function itpl_form_field_daterange($name, $value = array(), $time = false) {
	global $_GPC;
	$placeholder = ((isset($value['placeholder']) ? $value['placeholder'] : ''));
	$s = '';

	if (empty($time) && !defined('TPL_INIT_TINY_DATERANGE_DATE')) {
		$s = '
<script type="text/javascript">
	require(["daterangepicker"], function() {
		$(".daterange.daterange-date").each(function(){
			var elm = this;
			var container =$(elm).parent().prev();
			$(this).daterangepicker({
				format: "YYYY-MM-DD"
			}, function(start, end){
				$(elm).find(".date-title").html(start.toDateStr() + " 至 " + end.toDateStr());
				container.find(":input:first").val(start.toDateTimeStr());
				container.find(":input:last").val(end.toDateTimeStr());
			});
		});
	});

	function clearTime(obj){
		$(obj).prev().html("<span class=date-title>" + $(obj).attr("placeholder") + "</span>");
		$(obj).parent().prev().find("input").val("");
	 }
</script>';
		define('TPL_INIT_TINY_DATERANGE_DATE', true);
	}

	if (!empty($time) && !defined('TPL_INIT_TINY_DATERANGE_TIME')) {
		$s = '
<script type="text/javascript">
	require(["daterangepicker"], function($){
		$(function(){
			$(".daterange.daterange-time").each(function() {
				var elm = this;
				var container =$(elm).parent().prev();
				$(this).daterangepicker({
					format: "YYYY-MM-DD HH:mm",
					timePicker: true,
					timePicker12Hour : false,
					timePickerIncrement: 1,
					minuteStep: 1
				}, function(start, end){
					$(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
					container.find(":input:first").val(start.toDateTimeStr());
					container.find(":input:last").val(end.toDateTimeStr());
				});
			});
		});
	});

	function clearTime(obj){
		$(obj).prev().html("<span class=date-title>" + $(obj).attr("placeholder") + "</span>");
		$(obj).parent().prev().find("input").val("");
	 }
</script>';
		define('TPL_INIT_TINY_DATERANGE_TIME', true);
	}

	$str = $placeholder;
	$value['starttime'] = ((isset($value['starttime']) ? $value['starttime'] : (($_GPC[$name]['start'] ? $_GPC[$name]['start'] : ''))));
	$value['endtime'] = ((isset($value['endtime']) ? $value['endtime'] : (($_GPC[$name]['end'] ? $_GPC[$name]['end'] : ''))));
	if ($value['starttime'] && $value['endtime']) {
		if (empty($time)) {
			$str = date('Y-m-d', strtotime($value['starttime'])) . '至 ' . date('Y-m-d', strtotime($value['endtime']));
		}
		else {
			$str = date('Y-m-d H:i', strtotime($value['starttime'])) . ' 至 ' . date('Y-m-d  H:i', strtotime($value['endtime']));
		}
	}

	$s .= '
		<div style="float:left">
			<input name="' . $name . '[start]' . '" type="hidden" value="' . $value['starttime'] . '" />
			<input name="' . $name . '[end]' . '" type="hidden" value="' . $value['endtime'] . '" />
		</div>
		<div class="btn-group" style="padding-right:0;">
			<button style="width:240px" class="btn btn-default daterange ' . ((!empty($time) ? 'daterange-time' : 'daterange-date')) . '"  type="button"><span class="date-title">' . $str . '</span></button>
			<button class="btn btn-default" type="button" onclick="clearTime(this)" placeholder="' . $placeholder . '"><i class="fa fa-remove"></i></button>
		</div>';
	return $s;
}

function tpl_form_field_tiny_link($name, $value = '', $options = array()) {
	global $_GPC;
	$s = '';
	if (!defined('TPL_INIT_TINY_LINK')) {
		$s = '
		<script type="text/javascript">
			function showTinyLinkDialog(elm) {
			
				irequire(["web/tiny"], function(tiny){
					var ipt = $(elm).parent().prev();
					var options = {
						scene : "vuepage",
						type : "wmall",
						addhost: 1
					};
					tiny.selectWxappLink(function(href){
						ipt.val(href);
					}, options);
				});
			}
		</script>';
		define('TPL_INIT_TINY_LINK', true);
	}
	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control ' . $options['css']['input'] . '" autocomplete="off">
		<span class="input-group-btn">
			<button class="btn btn-default ' . $options['css']['btn'] . '" type="button" onclick="showTinyLinkDialog(this);">选择链接</button>
		</span>
	</div>
	';
	return $s;
}

function tpl_form_field_tiny_wxapp_link($name, $value = '', $options = array()) {
	global $_GPC;
	$s = '';
	if (!defined('TPL_INIT_TINY_WXAPP_LINK')) {
		$s = '
		<script type="text/javascript">
			function showTinyWxappLinkDialog(elm) {
				irequire(["web/tiny"], function(tiny){
					var ipt = $(elm).parent().prev();
					tiny.selectWxappLink(function(href){
						ipt.val(href);
					});
				});
			}
		</script>';
		define('TPL_INIT_TINY_WXAPP_LINK', true);
	}
	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control ' . $options['css']['input'] . '" autocomplete="off">
		<span class="input-group-btn">
			<button class="btn btn-default ' . $options['css']['btn'] . '" type="button" onclick="showTinyWxappLinkDialog(this);">选择链接</button>
		</span>
	</div>
	';
	return $s;
}

function tpl_form_field_tiny_coordinate($field, $value = array(), $required = false) {
	global $_W;
	$s = '';
	if(!defined('TPL_INIT_TINY_COORDINATE')) {
		$s .= '<script type="text/javascript">
			function showCoordinate(elm) {
				irequire(["web/tiny"], function(tiny){
					var val = {};
					val.mapType = "' . $_W['MapType'] . '"
					val.lng = parseFloat($(elm).parent().prev().prev().find(":text").val());
					val.lat = parseFloat($(elm).parent().prev().find(":text").val());
					tiny.map(val, function(r){
						$(elm).parent().prev().prev().find(":text").val(r.lng);
						$(elm).parent().prev().find(":text").val(r.lat);
					});
				});
			}
		</script>';
		define('TPL_INIT_TINY_COORDINATE', true);
	}
	$s .= '
		<div class="row row-fix">
			<div class="col-xs-4 col-sm-4">
				<input type="text" name="' . $field . '[lng]" value="'.$value['lng'].'" placeholder="地理经度"  class="form-control" ' . ($required ? 'required' : '') . '/>
			</div>
			<div class="col-xs-4 col-sm-4">
				<input type="text" name="' . $field . '[lat]" value="'.$value['lat'].'" placeholder="地理纬度"  class="form-control" ' . ($required ? 'required' : '') . '/>
			</div>
			<div class="col-xs-4 col-sm-4">
				<button onclick="showCoordinate(this);" class="btn btn-default" type="button">选择坐标</button>
			</div>
		</div>';
	return $s;
}

function cloud_w_upgrade_version($family, $version, $release = 0) {
	$verfile = MODULE_ROOT . '/version.php';
	$verdat = <<<VER
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
define('MODULE_FAMILY', '{$family}');
define('MODULE_VERSION', '{$version}');
define('MODULE_RELEASE_DATE', '{$release}');
VER;
	file_put_contents($verfile, trim($verdat));
}

function tpl_select2($name, $data, $value = 0, $filter = array('id', 'title'), $default = '请选择') {
	$element_id = "select2-{$name}";
	$json_data = array();
	foreach($data as $da) {
		$json_data[] = array(
			'id' => $da[$filter[0]],
			'text' => $da[$filter[1]],
		);
	}
	$json_data = json_encode($json_data);
	$html = '<select name="' . $name. '" class="form-control" id="' . $element_id . '"></select>';
	$html .= '<script type="text/javascript">
					require(["jquery", "select2"], function($) {
						$("#'. $element_id .'").select2({
							placeholder: "'. $default .'",
							data: '. $json_data .',
							val: '. $value.'
						});
					});
			  </script>';
	return $html;
}

function tpl_form_field_tiny_image($name, $value = '') {
	global $_W;
	$default = '';
	$val = $default;
	if (!empty($value)) {
		$val = tomedia($value);
	}
	if (!empty($options['global'])) {
		$options['global'] = true;
	} else {
		$options['global'] = false;
	}
	if (empty($options['class_extra'])) {
		$options['class_extra'] = '';
	}
	if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
		if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
			exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
		}
	}
	$options['direct'] = true;
	$options['multiple'] = false;
	if (isset($options['thumb'])) {
		$options['thumb'] = !empty($options['thumb']);
	}
	$s = '';
	if (!defined('TPL_INIT_TINY_IMAGE')) {
		$s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().parent().find(".input-group-addon img");
					options = '.str_replace('"', '\'', json_encode($options)).';
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.attachment);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, null, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
		define('TPL_INIT_TINY_IMAGE', true);
	}

	$s .= '
		<div class="input-group ' . $options['class_extra'] . '">
			<div class="input-group-addon">
				<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" width="20" height="20" />
			</div>
			<input type="text" name="' . $name . '" value="' . $value . '" class="form-control" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>
			</span>
		</div>';
	return $s;
}

function tpl_form_field_store($name, $value = '', $option = array('mutil' => 0)) {
	global $_W;
	if (empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	if(!is_array($value)) {
		$value = intval($value);
		$value = array($value);
	}
	$value_ids = implode(',', $value);
	$stores_temp = pdo_fetchall('select id, title, logo from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$value_ids})" , array(':uniacid' => $_W['uniacid']));
	$stores = array();
	if(!empty($stores_temp)) {
		foreach($stores_temp as $row) {
			$row['logo'] = tomedia($row['logo']);
			$stores[] = $row;
		}
	}

	$definevar = 'TPL_INIT_TINY_STORE';
	$function = 'showStoreDialog';
	if(!empty($option['mutil'])) {
		$definevar = 'TPL_INIT_TINY_MUTIL_STORE';
		$function = 'showMutilStoreDialog';
	}
	$s = '';
	if (!defined($definevar)) {
		$option_json = json_encode($option);
		$s = '
		<script type="text/javascript">
			function '. $function .'(elm) {
				var btn = $(elm);
				var value_cn = btn.parent().prev();
				var logo = btn.parent().parent().next().find("img");
				irequire(["web/tiny"], function(tiny){
					tiny.selectstore(function(stores, option){
						if(option.mutil == 1) {
							$.each(stores, function(idx, store){
								$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+store.logo+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+store.id+\'"><em class="close" title="删除该门店" onclick="deleteStore(this)">×</em><span>\'+store.title+\'</span></div>\');
							});
						} else {
							value_cn.val(stores.title);
							logo[0].src = stores.logo;
							logo.prev().val(stores.id);
							logo.next().removeClass("hide").html(stores.title);
						}
					}, ' . $option_json . ');
				});
			}

			function deleteMutilStore(elm){
				$(elm).parent().remove();
			}
		</script>';
		define($definevar, true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" class="form-control store-cn" readonly value="' . $stores[0]['title'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="' . $function . '(this);">选择商家</button>
			</span>
		</div>';
	if(empty($option['mutil'])) {
		$s .='
		<div class="input-group single-item" style="margin-top:.5em;">
			<input type="hidden" name="'. $name .'" value="'. $value[0] .'">
			<img src="' . $stores[0]['logo'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" />
		';
		if(empty($stores[0]['title'])) {
			$s .= '<span class="hide"></span>';
		} else {
			$s .= '<span>' . $stores[0]['title'] . '</span>';
		}
		$s .= '</div>';
	} else {
		$s .= '<div class="input-group multi-img-details">';
		foreach ($stores as $store) {
			$s .= '
			<div class="multi-item">
				<img src="' . $store['logo'] . '" title="'. $store['title'] .'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
				<input type="hidden" name="' . $name . '[]" value="' . $store['id'] . '">
				<em class="close" title="删除该门店" onclick="deleteMutilStore()">×</em>
				<span>' . $store['title'] . '</span>
			</div>';
		}
		$s .= '</div>';
	}
	return $s;
}

function tpl_form_field_mutil_store($name, $value = '') {
	return tpl_form_field_store($name, $value, $option = array('mutil' => 1));
}

function tpl_form_field_goods($name, $value = '', $option = array('mutil' => 0, 'sid' => 0, 'ignore' => array())) {
	global $_W;
	if(!isset($option['mutil'])) {
		$option['mutil'] = 0;
	}
	if (empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	if(!is_array($value)) {
		$value = intval($value);
		$value = array($value);
	}
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$value_ids = implode(',', $value);
	$condition .= " and id in ({$value_ids})";
	$goods_temp = pdo_fetchall('select id, title, thumb from ' . tablename('tiny_wmall_goods') . "{$condition}", $params);
	$goods = array();
	if(!empty($goods_temp)) {
		foreach($goods_temp as $row) {
			$row['thumb'] = tomedia($row['thumb']);
			$goods[] = $row;
		}
	}

	$definevar = 'TPL_INIT_TINY_GOODS';
	$function = 'showGoodsDialog';
	if(!empty($option['mutil'])) {
		$definevar = 'TPL_INIT_TINY_MUTIL_GOODS';
		$function = 'showMutilGoodsDialog';
	}
	$s = '';
	if (!defined($definevar)) {
		$option_json = json_encode($option);
		$s = '
		<script type="text/javascript">
			function '. $function .'(elm) {
				var btn = $(elm);
				var value_cn = btn.parent().prev();
				var thumb = btn.parent().parent().next().find("img");
				tiny.selectgoods(function(goods, option){
					if(option.mutil == 1) {
						$.each(goods, function(idx, good){
							$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+store.good+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+good.id+\'"><em class="close" title="删除该商品" onclick="deleteStore(this)">×</em><span>\'+good.title+\'</span></div>\');
						});
					} else {
						value_cn.val(goods.title);
						thumb[0].src = goods.thumb;
						thumb.prev().val(goods.id);
						thumb.next().removeClass("hide").html(goods.title);
					}
				}, ' . $option_json . ');
			}

			function deleteMutilGoods(elm){
				$(elm).parent().remove();
			}
		</script>';
		define($definevar, true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" class="form-control store-cn" readonly value="' . $goods[0]['title'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="' . $function . '(this);">选择商品</button>
			</span>
		</div>';
	if(empty($option['mutil'])) {
		$s .='
		<div class="input-group single-item" style="margin-top:.5em;">
			<input type="hidden" name="'. $name .'" value="'. $value[0] .'">
			<img src="' . $goods[0]['thumb'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" />
		';
		if(empty($goods[0]['title'])) {
			$s .= '<span class="hide"></span>';
		} else {
			$s .= '<span>' . $goods[0]['title'] . '</span>';
		}
		$s .= '</div>';
	} else {
		$s .= '<div class="input-group multi-img-details">';
		foreach ($goods as $good) {
			$s .= '
			<div class="multi-item">
				<img src="' . $good['thumb'] . '" title="'. $good['title'] .'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
				<input type="hidden" name="' . $name . '[]" value="' . $good['id'] . '">
				<em class="close" title="删除该商品" onclick="deleteMutilStore()">×</em>
				<span>' . $good['title'] . '</span>
			</div>';
		}
		$s .= '</div>';
	}
	return $s;
}

function tpl_form_field_mutil_goods($name, $value = '', $option = array('sid' => 0, 'ignore' => array())) {
	if(!isset($option['mutil'])) {
		$option['mutil'] = 1;
	}
	return tpl_form_field_goods($name, $value, $option);
}

function tpl_form_filter_hidden($ctrls, $do = 'web') {
	global $_W;
	$html = '
		<input type="hidden" name="c" value="site">
		<input type="hidden" name="a" value="entry">
		<input type="hidden" name="m" value="we7_wmall">
		<input type="hidden" name="i" value="'. $_W['uniacid'] .'">
		<input type="hidden" name="do" value="'. $do .'"/>
	';

	list($ctrl, $ac, $op, $ta) = explode('/', $ctrls);
	if(!empty($ctrl)) {
		$html .= '<input type="hidden" name="ctrl" value="'. $ctrl .'"/>';
		if(!empty($ac)) {
			$html .= '<input type="hidden" name="ac" value="'. $ac .'"/>';
			if(!empty($ac)) {
				$html .= '<input type="hidden" name="op" value="'. $op .'"/>';
				if(!empty($ta)) {
					$html .= '<input type="hidden" name="ta" value="'. $ta .'"/>';
				}
			}
		}
	}
	return $html;
}

function tpl_form_field_tiny_account($name, $value = 0, $required = false) {
	$account = array();
	if(!empty($value)) {
		$account = pdo_get('account_wechats', array('uniacid' => $value));
	}
	$s = '';
	if (!defined('TPL_INIT_TINY_ACCOUNT')) {
		$s = '
		<script type="text/javascript">
			function showTinyAccountDialog(elm) {
				irequire(["web/tiny"], function(tiny){
					var $uniacid = $(elm).parent().prev();
					var $name = $(elm).parent().prev().prev();
					tiny.selectaccount(function(account){
						$uniacid.val(account.uniacid);
						$name.val(account.name);
					});
				});
			}
		</script>';
		define('TPL_INIT_TINY_ACCOUNT', true);
	}
	$s .= '
	<div class="input-group">
		<input type="text" name="'.$name.'_cn" value="'.$account['name'].'" class="form-control" autocomplete="off" readonly>
		<input type="hidden" name="'.$name.'" value="'.$value.'">
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" onclick="showTinyAccountDialog(this);">选择公众号</button>
		</span>
	</div>
	';
	return $s;
}


function tpl_form_field_tiny_category_2level($name, $parents, $children, $parentid, $childid){
	global $_W, $_GPC;
	$disabled = '';
	if($_W['role'] == 'merchanter' && $_GPC['op'] == 'setting') {
		$disabled = 'disabled';
	}

	$html = '
		<script type="text/javascript">
			window._' . $name . ' = ' . json_encode($children) . ';
		</script>';
	if (!defined('TPL_INIT_TINY_CATEGORY')) {
		$html .= '
					<script type="text/javascript">
						function irenderCategory(obj, name){
							var index = obj.options[obj.selectedIndex].value;
							require([\'jquery\', \'util\'], function($, u){
								$selectChild = $(\'#\'+name+\'_child\');
								var html = \'<option value="0">请选择二级分类</option>\';

								if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
									$selectChild.html(html);
									return false;
								}
								for(var i in window[\'_\'+name][index]){
									html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
								}
								$selectChild.html(html);
							});
						}
					</script>
					';
		define('TPL_INIT_TINY_CATEGORY', true);
	}

	$html .=
		'<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="irenderCategory(this,\'' . $name . '\')"' . $disabled . '>
					<option value="0">请选择一级分类</option>';
	$ops = '';
	foreach ($parents as $row) {
		$html .= '
					<option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
	}
	$html .= '
				</select>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]"' . $disabled . '>
					<option value="0">请选择二级分类</option>';
	if (!empty($parentid) && !empty($children[$parentid])) {
		foreach ($children[$parentid] as $row) {
			$html .= '
					<option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
		}
	}
	$html .= '
				</select>
			</div>
		</div>
	';
	return $html;
}

function wxapp_urls($type = 'wmall', $addhost = false) {
	global $_W, $_GPC;
	$data = array();
	if($type == 'wmall') {
		$params = $addhost ? array() : array('nouniacid' => 1);
		$data['takeout']['sys'] = array(
			'title' => '外卖链接',
			'items' => array(
				array(
					'title' => '平台首页',
					'url' => ivurl('pages/home/index', $params, $addhost)
				),
				array(
					'title' => '搜索商家',
					'url' => ivurl('pages/home/search', $params, $addhost)
				),
				array(
					'title' => '会员中心',
					'url' => ivurl('pages/member/mine', $params, $addhost)
				),
				array(
					'title' => '我的订单',
					'url' => ivurl('pages/order/index', $params, $addhost)
				),
				array(
					'title' => '我的代金券',
					'url' => ivurl('pages/member/coupon/index', $params, $addhost)
				),
				array(
					'title' => '我的红包',
					'url' => ivurl('pages/member/redPacket/index', $params, $addhost)
				),
				array(
					'title' => '我的收货地址',
					'url' => ivurl('pages/member/address', $params, $addhost)
				),
				array(
					'title' => '我的收藏',
					'url' => ivurl('pages/member/favorite', $params, $addhost)
				),
				array(
					'title' => '配送会员卡',
					'url' => ivurl('package/pages/deliveryCard/index', $params, $addhost)
				),
				array(
					'title' => '领券中心',
					'url' => ivurl('plugin/pages/channel/coupon', $params, $addhost)
				),
				array(
					'title' => '余额充值',
					'url' => ivurl('pages/member/recharge', $params, $addhost)
				),
				array(
					'title' => '天天特价',
					'url' => ivurl('plugin/pages/bargain/index', $params, $addhost)
				),
				array(
					'title' => '购物车',
					'url' => ivurl('pages/order/cart', $params, $addhost)
				),
				array(
					'title' => '为您优选',
					'url' => ivurl('plugin/pages/channel/brand', $params, $addhost)
				),
				array(
					'title' => '帮助中心',
					'url' => ivurl('pages/home/help', $params, $addhost)
				),
				array(
					'title' => '客服中心',
					'url' => ivurl('pages/home/help', $params, $addhost)
				),
			)
		);
		if($_W['we7_wmall']['config']['mall']['store_use_child_category'] == 1) {
			$data['takeout']['sys']['items'][] = array(
				'title' => '全部分类',
				'url' => ivurl('pages/home/allcategory', $params, $addhost)
			);
		}
		$data['takeout']['dis'] = array(
			'title' => '优惠活动',
			'items' => array()
		);
		$discounts = store_discounts();
		if(!empty($discounts)) {
			foreach($discounts as $row) {
				$data['takeout']['dis']['items'][] = array(
					'title' => $row['title'],
					'url' => ivurl('pages/home/category', array_merge($params, array('dis' => $row['key'])), $addhost)
				);
			}
		}

		$data['other'] = array();
		if(check_plugin_perm('spread')){
			$data['other']['spread'] = array(
				'title' => $_W['_plugins']['spread']['title'],
				'items' => array(
					array(
						'title' => '推广中心',
						'url' => ivurl('plugin/pages/spread/index', $params, $addhost)
					)
				)
			);
		}
		if(check_plugin_perm('deliveryCard')){
			$data['other']['deliveryCard'] = array(
				'title' => $_W['_plugins']['deliveryCard']['title'],
				'items' => array(
					array(
						'title' => '配送会员卡特权说明',
						'url' => ivurl('package/pages/deliveryCard/power', $params, $addhost)
					)
				)
			);
		}
		if(check_plugin_perm('ordergrant')){
			$data['other']['ordergrant'] = array(
				'title' => $_W['_plugins']['ordergrant']['title'],
				'items' => array(
					array(
						'title' => '下单有礼',
						'url' => ivurl('package/pages/ordergrant/index', $params, $addhost)
					)
				)
			);
		}
		if(check_plugin_perm('shareRedpacket')){
			$data['other']['shareRedpacket'] = array(
				'title' => $_W['_plugins']['shareRedpacket']['title'],
				'items' => array(
					array(
						'title' => '分享有礼',
						'url' => ivurl('package/pages/shareRedpacket/index', $params, $addhost)
					)
				)
			);
		}
		if(check_plugin_perm('creditshop')){
			$data['other']['creditshop'] = array(
				'title' => $_W['_plugins']['creditshop']['title'],
				'items' => array(
					array(
						'title' => '积分商城',
						'url' => ivurl('pages/creditshop/index', $params, $addhost)
					)
				)
			);
		}
		if(check_plugin_perm('mealRedpacket')){
			$data['other']['mealRedpacket'] = array(
				'title' =>  $_W['_plugins']['mealRedpacket']['title'],
				'items' => array(
					array(
						'title' => '套餐红包',
						'url' => ivurl('package/pages/mealRedpacket/meal', $params, $addhost)
					),
					array(
						'title' => '套餐红包Plus',
						'url' => ivurl('package/pages/mealRedpacket/plus', $params, $addhost)
					),
				),
			);
		}
		if(check_plugin_perm('freeLunch')){
			$data['other']['freelunch'] = array(
				'title' => $_W['_plugins']['freeLunch']['title'],
				'items' => array(
					array(
						'title' => '霸王餐',
						'url' => ivurl('package/pages/freelunch/index', $params, $addhost)
					),
				),
			);
		}
		if(check_plugin_perm('errander')) {
			$data['errander'] = array(
				array(
					'title' => '平台链接',
					'items' => array(
						array(
							'title' => '跑腿首页',
							'url' => ivurl('pages/paotui/guide', $params, $addhost)
						),
						array(
							'title' => '跑腿订单',
							'url' => ivurl('pages/paotui/order', $params, $addhost)
						),
					)
				),
			);
			$data['errander']['scene'] = array(
				'title' => '跑腿场景',
				'items' => array()
			);
			$scenes = pdo_getall('tiny_wmall_errander_page', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'type' => 'scene'), array('id', 'name'));
			if(!empty($scenes)) {
				foreach($scenes as $scene) {
					$data['errander']['scene']['items'][] = array(
						'title' => $scene['name'],
						'url' => ivurl('pages/paotui/diy?id='.$scene['id'], $params, $addhost)
					);
				}
			}
		}
		if(check_plugin_perm('diypage')) {
			$diypages = pdo_getall('tiny_wmall_diypage', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'version' => 2), array('id', 'name'));
			if(!empty($diypages)) {
				$data['diyPages'] = $diypages;
			}
		}
		if(check_plugin_perm('storebd')){
			$data['other']['storebd'] = array(
				'title' => $_W['_plugins']['storebd']['title'],
				'items' => array(
					array(
						'title' => '推广员入口',
						'url' => ivurl('package/pages/storebd/index', $params, $addhost)
					),
				),
			);
		}
		if(check_plugin_perm('gohome')) {
			$data['other']['gohome'] = array(
				'title' => $_W['_plugins']['gohome']['title'],
				'items' => array(
					array(
						'title' => '生活圈首页',
						'url' => ivurl('gohome/pages/home/index', $params, $addhost)
					),
					array(
						'title' => '订单列表',
						'url' => ivurl('gohome/pages/order/index', $params, $addhost)
					),
					array(
						'title' => '我的收藏',
						'url' => ivurl('gohome/pages/member/favorite', $params, $addhost)
					),
					array(
						'title' => '拼团首页',
						'url' => ivurl('gohome/pages/pintuan/index', $params, $addhost)
					),
					array(
						'title' => '限时抢购首页',
						'url' => ivurl('gohome/pages/seckill/index', $params, $addhost)
					),
					array(
						'title' => '砍价首页',
						'url' => ivurl('gohome/pages/kanjia/index', $params, $addhost)
					),
					array(
						'title' => '我的砍价',
						'url' => ivurl('gohome/pages/kanjia/record', $params, $addhost)
					)
				),
			);
			$data['other']['haodian'] = array(
				'title' => '好店',
				'items' => array(
					array(
						'title' => '好店首页',
						'url' => ivurl('gohome/pages/haodian/index', $params, $addhost)
					),
					array(
						'title' => '收藏',
						'url' => ivurl('gohome/pages/haodian/favorite', $params, $addhost)
					),
				),
			);
			$data['other']['tongcheng'] = array(
				'title' => '同城信息',
				'items' => array(
					array(
						'title' => '同城首页',
						'url' => ivurl('gohome/pages/tongcheng/index', $params, $addhost)
					),
					array(
						'title' => '同城搜索页',
						'url' => ivurl('gohome/pages/tongcheng/search', $params, $addhost)
					),
					array(
						'title' => '信息发布首页',
						'url' => ivurl('gohome/pages/tongcheng/publish/index', $params, $addhost)
					),
					array(
						'title' => '我的发布',
						'url' => ivurl('gohome/pages/tongcheng/publish/list', $params, $addhost)
					),
				),
			);
		}
		if(check_plugin_perm('svip')){
			$data['other']['svip'] = array(
				'title' =>  $_W['_plugins']['svip']['title'],
				'items' => array(
					array(
						'title' => '超级会员入口',
						'url' => ivurl('package/pages/svip/index', $params, $addhost)
					),
					array(
						'title' => '超级会员个人中心',
						'url' => ivurl('package/pages/svip/mine', $params, $addhost)
					)
				),
			);
		}
		if(check_plugin_perm('kefu')) {
			$data['other']['kefu'] = array(
				'title' =>  $_W['_plugins']['kefu']['title'],
				'items' => array(
					array(
						'title' => '消息中心',
						'url' => ivurl('plugin/pages/kefu/index', $params, $addhost)
					)
				),
			);
		}
		$data['operation'] = array(
			'scanCode' => array(
				'title' => '扫码',
				'items' => array(
					array(
						'title' => '扫码',
						'url' => 'wx:scanCode'
					),
				)
			),
			'refresh' => array(
				'title' => '刷新',
				'items' => array(
					array(
						'title' => '刷新',
						'url' => 'refresh'
					),
				)
			)
		);
		$data['store'] = array(
			array(
				'title' => '商户',
				'items' => array(
					array(
						'title' => '门店详情',
						'url' => "pages/store/home?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '点外卖',
						'url' => "pages/store/goods?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '预定',
						'url' => "tangshi/pages/reserve/index?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '当面付',
						'url' => "pages/store/paybill?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '排号',
						'url' => "tangshi/pages/assign/assign?sid={$_GPC['__sid']}"
					),
				)
			),
			array(
				'title' => '门店',
				'items' => array(
					array(
						'title' => '自定义首页',
						'url' => "pages/shop/index?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '自定义分类页',
						'url' => "pages/shop/category?sid={$_GPC['__sid']}"
					),
					array(
						'title' => '门店购物车',
						'url' => "/pages/shop/cart?sid={$_GPC['__sid']}"
					)
				)
			)
		);
		if(check_plugin_perm('kabao')) {
			$data['other']['kabao'] = array(
				'title' =>  $_W['_plugins']['kabao']['title'],
				'items' => array(
					array(
						'title' => '门店会员卡首页',
						'url' => ivurl('plugin/pages/kabao/index', $params, $addhost)
					)
				),
			);
			$data['store'][0]['items'][] = array(
				'title' => '门店会员卡',
				'url' => "plugin/pages/kabao/detail?sid={$_GPC['__sid']}"
			);
		}
	}
	elseif($type == 'deliveryer') {
		$data['takeout']['sys'] = array(
			'title' => '订单',
			'items' => array(
				array(
					'title' => '订单列表',
					'url' => 'pages/order/takeout'
				),
			)
		);
		$data['store']['sys'] = array(
			'title' => '资产',
			'items' => array(
				array(
					'title' => '我的账户',
					'url' => 'pages/finance/index'
				),
				array(
					'title' => '提现记录',
					'url' => 'pages/finance/getcashList'
				),
				array(
					'title' => '账户明细',
					'url' => 'pages/finance/current'
				),
				array(
					'title' => '申请提现',
					'url' => 'pages/finance/getcash'
				),
				array(
					'title' => '提现账户',
					'url' => 'pages/finance/account'
				)
			)
		);
		$data['deliveryer']['sys'] = array(
			'title' => '统计',
			'items' => array(
				array(
					'title' => '配送统计',
					'url' => 'pages/statcenter/index'
				),
				array(
					'title' => '外卖统计',
					'url' => 'pages/statcenter/takeout'
				),
			)
		);
		if(check_plugin_perm('errander')) {
			$data['plugin']['errander'] = array(
				'title' => $_W['_plugins']['errander']['title'],
				'items' => array(
					array(
						'title' => '跑腿订单',
						'url' => 'pages/paotui/index'
					),
					array(
						'title' => '跑腿统计',
						'url' => 'pages/statcenter/errander'
					),
				),
			);
		}
		if(check_plugin_perm('kefu')) {
			$data['plugin']['kefu'] = array(
				'title' =>  $_W['_plugins']['kefu']['title'],
				'items' => array(
					array(
						'title' => '消息中心',
						'url' => 'pages/kefu/index'
					)
				),
			);
		}
		$data['other']['sys'] = array(
			'title' => '其他',
			'items' => array(
				array(
					'title' => '修改密码',
					'url' => 'pages/member/setting'
				),
				array(
					'title' => '我的',
					'url' => 'pages/member/mine'
				),
				array(
					'title' => '语音设置',
					'url' => 'pages/member/phonic'
				),
				array(
					'title' => '忘记密码',
					'url' => 'pages/auth/forget'
				),
				array(
					'title' => '我的评价',
					'url' => 'pages/comment/list'
				),
			)
		);
	}
	elseif($type == 'manager') {
		$data['takeout']['sys'] = array(
			'title' => '订单',
			'items' => array(
				array(
					'title' => '订单列表',
					'url' => 'pages/order/index'
				),
				array(
					'title' => '店内订单',
					'url' => 'pages/order/tangshi/index'
				),
			)
		);
		$data['store']['sys'] = array(
			'title' => '商户',
			'items' => array(
				array(
					'title' => '用户评价',
					'url' => 'pages/service/comment'
				),
				array(
					'title' => '店铺活动',
					'url' => 'pages/activity/index'
				),
				array(
					'title' => '全部商品',
					'url' => 'pages/goods/index'
				),
				array(
					'title' => '我的资产',
					'url' => 'pages/finance/index'
				),
				array(
					'title' => '店内桌台',
					'url' => 'pages/tangshi/table'
				),
				array(
					'title' => '排队',
					'url' => 'pages/tangshi/assign'
				),
				array(
					'title' => '店铺推广',
					'url' => 'pages/advertise/index'
				),
				array(
					'title' => '公告列表',
					'url' => 'pages/news/notice'
				),
				array(
					'title' => '账单',
					'url' => 'pages/paybill/index'
				),
			)
		);
		$data['deliveryer']['sys'] = array(
			'title' => '统计',
			'items' => array(
				array(
					'title' => '商户统计',
					'url' => 'pages/statcenter/index'
				),
				array(
					'title' => '营业统计',
					'url' => 'pages/statcenter/order'
				),
				array(
					'title' => '热门商品统计',
					'url' => 'pages/statcenter/goods'
				),
			)
		);
		if(check_plugin_perm('gohome')) {
			$data['plugin']['gohome'] = array(
				'title' => $_W['_plugins']['gohome']['title'],
				'items' => array(
					array(
						'title' => '生活圈首页',
						'url' => 'pages/gohome/index'
					),
					array(
						'title' => '砍价列表',
						'url' => 'pages/gohome/kanjia/goods/list'
					),
					array(
						'title' => '拼团列表',
						'url' => 'pages/gohome/pintuan/goods/list'
					),
					array(
						'title' => '抢购列表',
						'url' => 'pages/gohome/seckill/goods/list'
					),
					array(
						'title' => '订单列表',
						'url' => 'pages/gohome/order/index'
					),
				),
			);
		}
		if(check_plugin_perm('kefu')) {
			$data['plugin']['kefu'] = array(
				'title' =>  $_W['_plugins']['kefu']['title'],
				'items' => array(
					array(
						'title' => '消息中心',
						'url' => 'pages/kefu/index'
					)
				),
			);
		}
		$urls['other']['sys'] = array(
			'title' => '其他',
			'items' => array(
				array(
					'title' => '基础设置',
					'url' => 'pages/shop/index'
				),
				array(
					'title' => '商户首页',
					'url' => 'pages/shop/home'
				),
				array(
					'title' => '账户设置',
					'url' => 'pages/shop/account'
				),
				array(
					'title' => '支付设置设置',
					'url' => 'pages/shop/pill'
				),
				array(
					'title' => '营业资质',
					'url' => 'pages/shop/qualification'
				),
				array(
					'title' => '商家中心',
					'url' => 'pages/shop/setting'
				),
				array(
					'title' => '更多设置',
					'url' => 'pages/shop/settingMore'
				),
				array(
					'title' => '语音提醒',
					'url' => 'pages/shop/phonic'
				),
			)
		);
	}
	elseif($type == 'plateform') {
		$data['takeout']['sys'] = array(
			'title' => '外卖',
			'items' => array(
				array(
					'title' => '外卖订单',
					'url' => 'pages/order/takeout'
				),
				array(
					'title' => '当面付',
					'url' => 'pages/paycenter/paybill'
				),
				array(
					'title' => '售后',
					'url' => 'pages/service/comment?'
				),
				array(
					'title' => '统计',
					'url' => 'pages/statcenter/index'
				)
			)
		);
		$data['store']['sys'] = array(
			'title' => '商户',
			'items' => array(
				array(
					'title' => '商户列表',
					'url' => 'pages/merchant/store'
				),
				array(
					'title' => '商户活动列表',
					'url' => 'pages/merchant/activity/list'
				),
				array(
					'title' => '提现申请记录',
					'url' => 'pages/merchant/getcash'
				),
				array(
					'title' => '账户明细记录',
					'url' => 'pages/merchant/current'
				),
				array(
					'title' => '商户入驻列表',
					'url' => 'pages/merchant/settle'
				),
				array(
					'title' => '商家回收站',
					'url' => 'pages/merchant/storage'
				),
				array(
					'title' => '投诉列表',
					'url' => 'pages/merchant/report'
				)
			)
		);
		$data['deliveryer']['sys'] = array(
			'title' => '配送员',
			'items' => array(
				array(
					'title' => '配送员管理',
					'url' => 'pages/deliveryer/index'
				),
				array(
					'title' => '配送员列表',
					'url' => 'pages/deliveryer/deliveryer'
				),
				array(
					'title' => '提现申请记录',
					'url' => 'pages/deliveryer/getcash'
				),
				array(
					'title' => '账户明细记录',
					'url' => 'pages/deliveryer/current'
				),
				array(
					'title' => '配送员位置',
					'url' => 'pages/deliveryer/location'
				)
			)
		);

		if(check_plugin_perm('errander')) {
			$data['plugin']['errander'] = array(
				'title' => $_W['_plugins']['errander']['title'],
				'items' => array(
					array(
						'title' => '跑腿管理',
						'url' => 'pages/plugin/paotui/index'
					),
					array(
						'title' => '跑腿订单',
						'url' => 'pages/plugin/paotui/list'
					),
					array(
						'title' => '跑腿设置',
						'url' => 'pages/plugin/paotui/config'
					)
				),
			);
		}
		if(check_plugin_perm('agent')) {
			$data['plugin']['agent'] = array(
				'title' => $_W['_plugins']['agent']['title'],
				'items' => array(
					array(
						'title' => '区域代理管理',
						'url' => 'pages/plugin/agent/index'
					),
					array(
						'title' => '代理列表',
						'url' => 'pages/plugin/agent/agent'
					),
					array(
						'title' => '提现记录',
						'url' => 'pages/plugin/agent/getcash'
					),
					array(
						'title' => '账户明细',
						'url' => 'pages/plugin/agent/current'
					)
				),
			);
		}
		if(check_plugin_perm('creditshop')){
			$data['plugin']['creditshop'] = array(
				'title' => $_W['_plugins']['creditshop']['title'],
				'items' => array(
					array(
						'title' => '兑换列表',
						'url' => 'pages/plugin/creditshop/order'
					)
				),
			);
		}
		if(check_plugin_perm('deliveryCard')){
			$data['plugin']['deliveryCard'] = array(
				'title' => $_W['_plugins']['deliveryCard']['title'],
				'items' => array(
					array(
						'title' => '购买记录',
						'url' => 'pages/plugin/deliveryCard/order'
					)
				),
			);
		}
		if(check_plugin_perm('mealRedpacket')){
			$data['plugin']['mealRedpacket'] = array(
				'title' => $_W['_plugins']['mealRedpacket']['title'],
				'items' => array(
					array(
						'title' => '购买记录',
						'url' => 'pages/plugin/mealRedpacket/order'
					)
				),
			);
		}
		if(check_plugin_perm('wheel')){
			$data['plugin']['wheel'] = array(
				'title' => $_W['_plugins']['wheel']['title'],
				'items' => array(
					array(
						'title' => '参与记录',
						'url' => 'pages/plugin/wheel/record'
					)
				),
			);
		}
		if(check_plugin_perm('advertise')){
			$data['plugin']['advertise'] = array(
				'title' => $_W['_plugins']['advertise']['title'],
				'items' => array(
					array(
						'title' => '购买记录',
						'url' => 'pages/plugin/advertise/order'
					)
				),
			);
		}

		if(check_plugin_perm('kefu')) {
			$data['plugin']['kefu'] = array(
				'title' =>  $_W['_plugins']['kefu']['title'],
				'items' => array(
					array(
						'title' => '消息中心',
						'url' => 'pages/plugin/kefu/index'
					)
				),
			);
		}

		$data['other']['sys'] = array(
			'title' => '其他',
			'items' => array(
				array(
					'title' => '顾客列表',
					'url' => 'pages/member/list'
				),
				array(
					'title' => '系统设置',
					'url' => 'pages/config/index'
				),
				array(
					'title' => '更多',
					'url' => 'pages/more/index'
				),
				array(
					'title' => '我的',
					'url' => 'pages/member/mine'
				)
			)
		);
	}
	return $data;
}

