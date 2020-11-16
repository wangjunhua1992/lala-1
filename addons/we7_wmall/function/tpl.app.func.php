<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function tpl_select($title, $name, $value, $selects, $filter = array('id', 'title'), $multi = false) {
	if(empty($selects)) return false;
	$items = array();
	$value_cn = '请选择';
	foreach($selects as $select) {
		$items[] = array(
			'title' => $select[$filter[1]],
			'value' => $select[$filter[0]],
		);
		if($select[$filter[0]] == $value) {
			$value_cn = $select[$filter[1]];
		}
	}

	$container = "tpl-select-{$name}";
	$params = json_encode(array(
		'title' => $title,
		'multi' => $multi,
		'items' => $items
	));

	$html = '
		<div class="tpl-select ' . $container . '">
			<span>' . $value_cn . '</span>
			<input type="hidden" class="select-value" name="' . $name . '" value="' . $value . '"/>
			<input type="hidden" class="select-title" name="' . $name . '_cn" value="' . $value . '"/>
		</div>
		<script type="text/javascript">
			$(".'. $container .'").select(' . $params . ');
		</script>
	';
	return $html;
}

function tpl_image($name, $value, $extra = array()) {
	$url = empty($value) ? WE7_WMALL_TPL_URL . 'static/img/add_pic.png' : tomedia($value);
	$channel = 'wap';
	if(!empty($extra['channel'])) {
		$channel = trim($extra['channel']);
	}
	if (!defined('TPL_INIT_TINY_IMAGE')) {
		$html = '
		<script>
			function uploadImage(obj){
				tiny.image(obj, function(obj, data){
					var img_value = data.message ? data.message : data.attachment;
					obj.find("img").attr("src", data.url);
					obj.find("input").val(img_value);
				}, {channel: \'' . $channel . '\'});
			}
		</script>';
		define('TPL_INIT_TINY_IMAGE', true);
	}
	$html .= '
		<div class="row image-container tpl-image">
			<div class="col-25 image-item image-add" onclick="uploadImage(this)">
				<input type="hidden" name="'. $name .'" value="'. $value .'"/>
				<img src="'. $url .'" alt=""/>';
	if(!is_weixin() || $channel == 'wap') {
		$html .= '<input type="file" accept="image*/" multiple="false" @change="upload">';
	}
	$html .= '</div></div>';
	return $html;
}

function tpl_mutil_image($name, $values, $file_nums = 9, $extra = array()) {
	$channel = 'wap';
	if(!empty($extra['channel'])) {
		$channel = trim($extra['channel']);
	}
	if (!defined('TPL_INIT_TINY_MUTIL_IMAGE')) {
		$html = '
		<script>
			var fileNum = '. $file_nums .';
			var options = {
				fileNum: '. $file_nums .'
			};
			var fileNum = options.fileNum;
			function uploadMutilImage(obj){
				var $parent = $(obj).parents(".tpl-image");
				var nowFileNum = $parent.find(".image-edit").size();
				options.fileNum = fileNum - nowFileNum;
				require(["tiny"], function(tiny){
					if(nowFileNum >= fileNum) {
						$.toast("最多能上传" + fileNum + "张图片");
						return false;
					}
					tiny.image(obj, function(obj, data){
						var img_value = data.message ? data.message : data.attachment;
						if(obj.hasClass("image-edit")) {
							obj.parent().find("img").attr("src", data.url);
							obj.parent().find("input").val(img_value);
						} else {
							obj.before(\'<div class="col-25 image-item"><img src="\'+data.url+\'" class="image-edit" onclick="uploadMutilImage(this)" alt=""/><input type="hidden" name="'. $name .'" value="\'+img_value+\'"/><i class="icon icon-close" onclick="delMutilImage(this)"></i></div>\')
						}
					}, options, {
						channel: \'' . $channel . '\'
					});
				});
			}
			function delMutilImage(obj){
				var $parent = $(obj).parents(".image-item");
				$parent.remove();
				event.stopPropagation();
				return false;
			}
		</script>';
		define('TPL_INIT_TINY_MUTIL_IMAGE', true);
	}
	$name = "{$name}[]";
	$html .= '<div class="row image-container tpl-image border-1px-tb">';
	if(!empty($values)) {
		foreach($values as $value) {
			$html .= '
				<div class="col-25 image-item">
					<input type="hidden" name="'. $name .'" value="'. $value .'"/>
					<img src="'. tomedia($value) .'" class="image-edit" onclick="uploadMutilImage(this)" alt=""/>
					<i class="icon icon-close" onclick="delMutilImage(this)"></i>
				</div>
			';
		}
	}
	$src = WE7_WMALL_TPL_URL . 'static/img/add_pic.png';
	$html .= '
		<div class="col-25 image-item image-add" onclick="uploadMutilImage(this)">
			<img src="' . $src . '" alt=""/>
	';
	if(!is_weixin() || $channel == 'wap') {
		$html .= '<input type="file" accept="image*/" multiple="true" @change="upload">';
	}
	$html .= '</div></div>';
	return $html;
}

