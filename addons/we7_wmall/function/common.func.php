<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function itemplate($filename, $flag = TEMPLATE_DISPLAY) {
	global $_W, $_GPC;
	$module = 'we7_wmall';
	if(defined('IN_SYS')) {
		if(!defined('IN_PLUGIN')) {
			if($filename =='store/decoration/diyTpl') {
				$source = WE7_WMALL_PLUGIN_PATH . "wxapp/template/web/diyTpl.html";
			} elseif($filename =='store/decoration/diyPage/diyTpl') {
				$source = WE7_WMALL_PLUGIN_PATH . "diypage/template/web/diyTpl.html";
			} elseif($filename =='store/kabao/diyTpl') {
				$source = WE7_WMALL_PLUGIN_PATH . "kabao/template/web/diyTpl.html";
			} else {
				$source = WE7_WMALL_PATH . "template/web/{$filename}.html";
				if($_W['_controller'] == 'store' && defined('IN_GOHOME_APLUGIN')) {
					$source = WE7_WMALL_PATH . "template/web/{$filename}.html";
				}
			}
		} else {
			$filename_old = $filename;
			$filename = "{$_W['_plugin']['name']}/template/web/{$filename}.html";
			$source = WE7_WMALL_PLUGIN_PATH . $filename;
			if(defined('IN_AGENT')) {
				$source = WE7_WMALL_PLUGIN_PATH . "agent/template/web/manage/{$filename_old}.html";
				if(defined('IN_AGENT_PLUGIN')) {
					$filename = "agent/{$_W['_plugin']['name']}/template/web/{$filename}.html";
					$source = WE7_WMALL_PLUGIN_PATH . "agent/plugin/{$_W['_controller']}/template/web/{$filename_old}.html";
					if($_W['_controller'] == 'diypage' && $filename_old != 'tabs') {
						$filename = "diypage/template/web/{$filename_old}.html";
						$source = WE7_WMALL_PLUGIN_PATH . "diypage/template/web/{$filename_old}.html";
					}
					if($_W['_controller'] == 'superRedpacket' && $filename_old != 'tabs') {
						$filename = "superRedpacket/template/web/{$filename_old}.html";
						$source = WE7_WMALL_PLUGIN_PATH . "superRedpacket/template/web/{$filename_old}.html";
					}
					if(defined('IN_GOHOME_WPLUGIN')) {
						$source = WE7_WMALL_PLUGIN_PATH . "agent/plugin/gohome/{$_W['_controller']}/template/web/{$filename_old}.html";
						if(!is_file($source) || $filename_old == 'tabs') {
							$source = WE7_WMALL_PLUGIN_PATH . "agent/plugin/gohome/template/web/{$filename_old}.html";
						}
					}
				}
			} elseif(defined('IN_GOHOME_WPLUGIN')) {
				$source = WE7_WMALL_PLUGIN_PATH . "gohome/{$_W['_controller']}/template/web/{$filename_old}.html";
				if(!is_file($source) || $filename_old == 'tabs') {
					$source = WE7_WMALL_PLUGIN_PATH . "gohome/template/web/{$filename_old}.html";
				}
			}
			if(!is_file($source)) {
				$source = WE7_WMALL_PATH . "template/web/{$filename_old}.html";
			}
		}
		$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$module}/{$filename}.tpl.php";
	} else {
		$filename_old = $filename;
		$config = $_W['we7_wmall']['config']['mall'];
		$template = $config['template_mobile'];
		if(empty($template)) {
			$template = 'default';
		}
		$template_base = "wmall/{$template}";
		if(empty($_W['_controller']) || $_W['_controller'] == 'wmall') {
			$template_dir = "wmall/{$template}";
		} else {
			$template_dir = $_W['_controller'];
		}
		if(!defined('IN_PLUGIN')) {
			$source = WE7_WMALL_PATH . "template/mobile/{$template_dir}/{$filename}.html";
		} else {
			$config_plugin = $_W['_plugin']['config'];
			$template_plugin = $config_plugin['template_mobile'];
			if(empty($template_plugin)) {
				$template_plugin = 'default';
			}
			$filename = "{$_W['_plugin']['name']}/template/mobile/{$template_plugin}/{$filename}.html";
			$source = WE7_WMALL_PLUGIN_PATH . $filename;
		}
		if(!is_file($source)) {
			$names = $names_ext = explode('/', $filename_old);
			unset($names_ext[0]);
			$names_ext = implode('/', $names_ext);
			$source = WE7_WMALL_PLUGIN_PATH . "{$names[0]}/template/mobile/default/{$names_ext}.html";
		}
		if(!is_file($source)) {
			$source = WE7_WMALL_PATH . "template/mobile/wmall/default/{$filename_old}.html";
		}
		$compile = IA_ROOT . "/data/tpl/mobile/{$_W['template']}/{$module}/{$template_dir}/{$filename}.tpl.php";
	}
	if(!is_file($source)) {
		exit("Error: template source '{$filename}' is not exist!");
	}
	$paths = pathinfo($compile);
	$compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . $paths['filename'], $compile);
	if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
		itemplate_compile($source, $compile, false);
	}
	return $compile;
}

function itemplate_compile($from, $to, $inmodule = false) {
	$path = dirname($to);
	if (!is_dir($path)) {
		load()->func('file');
		mkdirs($path);
	}
	$content = itemplate_parse(file_get_contents($from), $inmodule);
	if(IMS_FAMILY == 'x' && !preg_match('/(footer|header|account\/welcome|login|register)+/', $from)) {
		$content = str_replace('微擎', '系统', $content);
	}
	file_put_contents($to, $content);
}

function h($url, $post = '', $extra = array(), $timeout = 60) {
	load()->func('communication');
	return ihttp_request($url, $post, $extra, $timeout);
}

function itemplate_parse($str, $inmodule = false) {
	global $_W, $_GPC;
	$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
	$str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || '.intval($inmodule).') ? (include $this->template($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
	$str = preg_replace('/{itemplate\s+(.+?)}/', '<?php include itemplate($1, TEMPLATE_INCLUDEPATH);?>', $str);
	$str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
	$str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
	$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
	$str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
	$str = preg_replace('/{\/if}/', '<?php } ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)\s+\|\|\s+(.+?)\s+\|\|\s+(.+?)\s+\|\|\s+(.+?)}/', '<?php if(check_perm($1) || check_perm($2) || check_perm($3) || check_perm($4)) { ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)\s+\|\|\s+(.+?)\s+\|\|\s+(.+?)}/', '<?php if(check_perm($1) || check_perm($2) || check_perm($3)) { ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)\s+\|\|\s+(.+?)}/', '<?php if(check_perm($1) || check_perm($2)) { ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)}/', '<?php if(check_perm($1)) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
	$str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
	$str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
	$str = preg_replace('/{media\s+(\S+)}/', '<?php echo tomedia($1);?>', $str);
	$str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
	$str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
	$str = str_replace('{##', '{', $str);
	$str = str_replace('##}', '}', $str);
	if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
		$str = str_replace('</body>', "<script>$(function(){\$('img').attr('onerror', '').on('error', function(){if (!\$(this).data('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('{$GLOBALS['_W']['attachurl']}') == -1 ? this.src.replace('{$GLOBALS['_W']['attachurl_remote']}', '{$GLOBALS['_W']['attachurl']}') : this.src.replace('{$GLOBALS['_W']['attachurl']}', '{$GLOBALS['_W']['attachurl_remote']}');\$(this).data('check-src', true);}});});</script></body>", $str);
	}
	$str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
	return $str;
}

function ireferer($default = '') {
	global $_GPC, $_W;
	$_W['referer'] = !empty($_GPC['referer']) ? $_GPC['referer'] : $_SERVER['HTTP_REFERER'];;
	$_W['referer'] = substr($_W['referer'], -1) == '?' ? substr($_W['referer'], 0, -1) : $_W['referer'];

	if (strpos($_W['referer'], 'member.php?act=login')) {
		$_W['referer'] = $default;
	}
	$_W['referer'] = $_W['referer'];
	$_W['referer'] = str_replace('&amp;', '&', $_W['referer']);
	$reurl = parse_url($_W['referer']);

	if (!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.' . $_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.' . $reurl['host']))) {
		$_W['referer'] = $_W['siteroot'];
	} elseif (empty($reurl['host'])) {
		$_W['referer'] = ivurl('pages/home/index', array(), true);
	}
	return strip_tags($_W['referer']);
}

function is_weixin() {
	global $_GPC;
	if($_GPC['u'] == 'weixin') {
		return true;
	}
	elseif (empty($_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false))) {
		return false;
	}
	return true;
}

function is_wxapp() {
	global $_GPC;
	if(defined('IN_WXAPP') || $_GPC['from'] == 'wxapp') {
		return true;
	}
	return false;
}

function is_ttapp() {
	global $_GPC;
	if(defined('IN_TTAPP') || $_GPC['from'] == 'ttapp') {
		return true;
	}
	return false;
}

function is_h5app() {
	global $_GPC;
	if($_GPC['u'] == 'h5app') {
		return true;
	}
	if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'CK 2.0')) {
		return true;
	}
	return false;
}

function is_ios() {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
		return true;
	}
	return false;
}

function get_agent_os() {
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if(strpos($agent, 'windows nt')) {
		return 'Windows';
	} elseif(strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
		return 'IOS';
	} elseif(strpos($agent, 'android')) {
		return 'Android';
	}
}

function is_mobile() {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr($useragent, 0, 4))) {
		return true;
	}
	return false;
}

function is_validMobile($mobile) {
	global $_W;
	if(preg_match('/^[01][3456789][0-9]{9}$/', $mobile) || preg_match('/^[8][0-9]{11}$/', $mobile)) {
		return true;
	}
	if($_W['DollarType'] != 'zh-cn') {
		if(preg_match('/^\d{5,}$/', $mobile)) {
			return true;
		}
	}
	return false;
}

function is_qianfan() {
	global $_GPC;
	if($_GPC['u'] == 'qianfan') {
		return true;
	}
	elseif(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'QianFan')) {
		return true;
	}
	return false;
}

function is_majia() {
	global $_GPC;
	if($_GPC['u'] == 'majia') {
		return true;
	}
	elseif(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MAGAPPX')) {
		return true;
	}
	return false;
}

function is_cloud() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'APICloud')) {
		return true;
	}
	return false;
}

function is_plala() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'PLALAWAIMAI')) {
		return true;
	}
	return false;
}

function is_mlala() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MLALAWAIMAI')) {
		return true;
	}
	return false;
}

function is_dlala() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'DLALAWAIMAI')) {
		return true;
	}
	return false;
}

function is_glala() {
	global $_GPC;
	if($_GPC['u'] == 'glala') {
		return true;
	}
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'GLALAWAIMAI')) {
		return true;
	}
	return false;
}

function is_vue() {
	global $_GPC;
	if(defined('IN_VUE') || $_GPC['from'] == 'vue') {
		return true;
	}
	return false;
}

function dikaer($arr, $join_key = '_', $join_value = '+'){
	if(count($arr) == 1) {
		return $arr[0];
	}
	$arr1 = array();
	$result = array_shift($arr);
	while($arr2 = array_shift($arr)){
		$arr1 = $result;
		$result = array();
		foreach($arr1 as  $k1 => $v){
			foreach($arr2 as $k2 => $v2){
				if(!is_array($v))$v = array($k1 => $v);
				if(!is_array($v2))$v2 = array($k2 => $v2);
				$result[] = array_merge_recursive($v,$v2);
			}
		}
	}
	$results = array();
	foreach($result as $row) {
		$keys = implode($join_key, array_keys($row));
		$results[$keys] = implode($join_value, $row);
	}
	return $results;
}

function create_uuid() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}

function ifile_write($content, $name = '', $remote = false) {
	global $_W;
	if(empty($name)) {
		$name = "/images/{$_W['uniacid']}/" . date('Y/m/') . random(30) . ".jpg";
	}
	load()->func('file');
	$filename = ATTACHMENT_ROOT . "/{$name}";
	mkdirs(dirname($filename));
	file_put_contents($filename, $content);
	@chmod($filename, $_W['config']['setting']['filemode']);
	if(!is_file($filename)) {
		return error(-1, '保存图片失败');
	}
	if($remote || !empty($_W['setting']['remote']['type'])) {
		$name = ltrim($name, '/');
		$status = file_remote_upload($name);
		if(is_error($status)) {
			return error(-1, "上传到远程失败,{$status['message']}");
		}
	}
	return $name;
}

function iaes_pkcs7_decode($encrypt_data, $key, $iv = false) {
	mload()->classs('pkcs7');
	$encrypt_data = base64_decode($encrypt_data);
	if (!empty($iv)) {
		$iv = base64_decode($iv);
	}
	$pc = new Prpcrypt($key);
	$result = $pc->decrypt($encrypt_data, $iv);
	if ($result[0] != 0) {
		return error($result[0], '解密失败');
	}
	return $result[1];
}

function iresult($errno, $message = '', $url = '') {
	return array(
		'errno' => $errno,
		'message' => $message,
		'url' => $url,
	);
}

function geocode_geo($address, $city = '') {
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'address' => $address,
		'city' => $city,
	);
	$url = 'http://restapi.amap.com/v3/geocode/geo?';
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get($url . $query);
	if(is_error($result)) {
		return $result;
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] == 0) {
		return error(-1, $result['info']);
	}
	$result['geocodes'][0]['location'] = explode(',', $result['geocodes'][0]['location']);
	return $result['geocodes'][0];
}

function geocode_regeo($location) {
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'location' => implode(',', $location),
	);
	$url = 'http://restapi.amap.com/v3/geocode/regeo?';
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get($url . $query);
	if(is_error($result)) {
		return $result;
	}
	$result = @json_decode($result['content'], true);
	if(!$result['status']) {
		return error(-1, $result['info']);
	}
	return $result['regeocode']['formatted_address'];
}

function ifile_exists($file){
	global $_W;
	if(!strexists($file, 'https://') && !strexists($file, 'http://')) {
		$file_local = ATTACHMENT_ROOT . '/' . ltrim($file, "/");
		if(file_exists($file_local)) {
			return true;
		}
		$file = tomedia($file);
	}
	if(strtolower(substr($file, 0, 4)) == 'http' || strtolower(substr($file, 0, 5)) == 'https'){
		$header = get_headers($file, true);
		if(isset($header[0]) && (strpos($header[0], '200') || strpos($header[0], '304'))) {
			return true;
		} else {
			load()->func('communication');
			$response = ihttp_request($file, array(), array('CURLOPT_REFERER' => $_W['siteroot']));
			if(is_error($response)) {
				return false;
			}
			if($response['code'] == 200) {
				return true;
			}
		}
		return ;
	} else {
		return file_exists($file);
	}
}

function randFloat($min=0, $max=1){
	return round($min + mt_rand()/mt_getrandmax() * ($max-$min), 2);
}

function removeEmoji($str) {
	$str = preg_replace_callback(
		'/./u',
		function (array $match) {
			return strlen($match[0]) >= 4 ? '' : $match[0];
		},
	$str);
	return $str;
}

function sub_day($staday) {
	$value = TIMESTAMP - $staday;
	if($value < 0) {
		return '';
	} elseif($value >= 0 && $value < 59) {
		return ($value+1)."秒";
	} elseif($value >= 60 && $value < 3600) {
		$min = intval($value / 60);
		return $min." 分钟";
	} elseif($value >=3600 && $value < 86400) {
		$h = intval($value / 3600);
		return $h." 小时";
	} elseif($value >= 86400 && $value < 86400*30) {
		$d = intval($value / 86400);
		return intval($d)." 天";
	} elseif($value >= 86400*30 && $value < 86400*30*12) {
		$mon  = intval($value / (86400*30));
		return $mon." 月";
	} else {
		$y = intval($value / (86400*30*12));
		return $y." 年";
	}
}

function sub_time($time) {
	$rtime = date("m-d H:i", $time);
	$htime = date("H:i", $time);
	$time = time() - $time;
	if ($time < 60) {
		$str = '刚刚';
	} elseif ($time < 3600) {
		$min = floor($time / 60);
		$str = $min . '分钟前';
	} elseif ($time < 86400) {
		$h = floor($time / (60 * 60));
		$str = $h.'小时前 '. $htime;
	} elseif ($time < 259200) {
		$d = floor($time / 86400);
		if($d == 1) {
			$str = '昨天 '. $rtime;
		} else {
			$str = '前天 '. $rtime;
		}
	} else {
		$str = $rtime;
	}
	return $str;
}

function transform_time($time) {
	$data = '';
	if ($time >= 0) {
		$days = intval($time / 86400);
		if($days > 0) {
			$data .= "{$days}天";
		}
		$remain = $time % 86400;
		$hours = intval($remain / 3600);
		if($hours > 0) {
			$data .= "{$hours}小时";
		}
		$remain = $remain % 3600;
		$minutes = intval($remain / 60);
		if($minutes > 0) {
			$data .= "{$minutes}分钟";
		}
		$seconds = $remain % 60;
		if($seconds > 0 || empty($days) && empty($hours) && empty($minutes)) {
			$data .= "{$seconds}秒";
		}
	}
	return $data;
}

function date2week($timestamp) {
	$weekdays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
	$week = date('w', $timestamp);
	return $weekdays[$week];
}

/**
 * 计算两个坐标之间的距离(米)
 * @param float $fP1Lat 起点(纬度)
 * @param float $fP1Lon 起点(经度)
 * @param float $fP2Lat 终点(纬度)
 * @param float $fP2Lon 终点(经度)
 * @return int
 */
function distanceBetween($longitude1, $latitude1, $longitude2, $latitude2) {
	$radLat1 = radian ( $latitude1 );
	$radLat2 = radian ( $latitude2 );
	$a = radian ( $latitude1 ) - radian ( $latitude2 );
	$b = radian ( $longitude1 ) - radian ( $longitude2 );
	$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) *
			cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
	$s = $s * 6378.137; //乘上地球半径，单位为公里
	$s = round ( $s * 10000 ) / 10000; //单位为公里(km)
	return $s * 1000; //单位为m
}

function radian($d) {
	return $d * 3.1415926535898 / 180.0;
}

function calculate_distance($origins, $destination, $type = 1) {
	global $_W;
	load()->func('communication');
	//$type 0直线 1驾车 2骑行 3步行
	if($_W['MapType'] == 'gaode') {
		$query = array(
			'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
			'destination' => implode(',', $destination),
		);
		if($type == 2) {
			$query['origin'] = implode(',', $origins);
			$url = 'http://restapi.amap.com/v4/direction/bicycling?';
		} else {
			$query['origins'] = implode(',', $origins);
			$query['type'] = $type;
			$query['output'] = 'json';
			$url = 'http://restapi.amap.com/v3/distance?';
		}
		$query = http_build_query($query);
		$result = ihttp_get($url . $query);
		if(is_error($result)) {
			return $result;
		}
		$result = @json_decode($result['content'], true);
		if($type == 2) {
			if(!empty($result['errcode'])) {
				if($result['errcode'] == '30007') {
					$dis = calculate_distance($origins, $destination, 1);
					return $dis;
				}
				return error($result['errcode'], $result['errmsg']);
			}
			return round($result['data']['paths'][0]['distance'] / 1000, 3);
		} else {
			if($result['status'] != 1) {
				return error(-1, $result['info']);
			}
			if(round($result['results'][0]['distance'] / 1000, 3) < 0 && $type == 3) {
				$dis = calculate_distance($origins, $destination, 2);
				return $dis;
			}
			return round($result['results'][0]['distance'] / 1000, 3);
		}
	} elseif($_W['MapType'] == 'google') {
		if($type == 0) {
			//计算直线距离
			$dis = distanceBetween($origins[0], $origins[1], $destination[0], $destination[1]);
			return round($dis / 1000, 3);
		} else {
			$modes = array('', 'driving', 'bicycling', 'walking', 'transit');
			$origins = array_reverse($origins);
			$origins = implode(',', $origins);
			$destination = array_reverse($destination);
			$destination = implode(',', $destination);
			$query = array(
				'origins' => $origins,
				'destinations' => $destination,
				'key' => 'AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw',
				'mode' => $modes[$type],
			);
			$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
			$query = http_build_query($query);
			$result = ihttp_get($url . $query);
			if(is_error($result)) {
				return $result;
			}
			$result = @json_decode($result['content'], true);
			if($result['status'] != 'OK') {
				return error(-1, $result['error_message']);
			}
			$data = $result['rows'][0]['elements'][0];
			if(empty($data)) {
				return error(-1, '无法计算两点之间的距离');
			}
			if($data['status'] != 'OK') {
				$message = array(
					'NOT_FOUND' => '起点和终点无法进行地理编码',
					'ZERO_RESULTS' => '在起点和终点之间找不到路线',
					'MAX_ROUTE_LENGTH_EXCEEDED' => '请求的路由太长，无法处理',
				);
				return error(-1, $message[$data['status']]);
			}
			return round($data['distance']['value'] / 1000, 3);
		}
	}
}

/**
 * 批量计算某点与多点之间的距离
 * 高德文档地址：https://lbs.amap.com/api/webservice/guide/api/direction
 **/
function batch_calculate_distance($origins, $destination, $type = 1) {
	if(!is_array($origins) || !is_array($destination) || !in_array($type, array(0, 1, 2, 3))) {
		return error(-1, '参数错误');
	}
	if(count($origins) == count($origins, 1)) {
		$origins = implode(',', $origins);
	} else {
		$temp = array();
		foreach($origins as $value) {
			$temp[] = implode(',', $value);
		}
		$origins = implode('|', $temp);
	}
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'destination' => implode(',', $destination),
		'type' => $type,
		'output' => 'json',
		'origins' => $origins,
	);
	$url = 'http://restapi.amap.com/v3/distance?';
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get($url . $query);
	if(is_error($result)) {
		return $result;
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] == 0) {
		return error(-1, $result['info']);
	}
	return $result['results'];
}

function ip2city($ip = '') {
	global $_W;
	if(empty($ip)) {
		$ip = $_W['client_ip'];
	}
	$query = array(
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
		'ip' => $ip,
		'output' => 'json',
	);
	$query = http_build_query($query);
	load()->func('communication');
	$result = ihttp_get('http://restapi.amap.com/v3/ip?' . $query);
	if(is_error($result)) {
		return error(-1, $result['info']);
	}
	$result = @json_decode($result['content'], true);
	if($result['status'] != 1) {
		return error(-1, $result['info']);
	}
	return $result;
}

function isPointInPolygon($polygon, $lnglat){
	$count = count($polygon);
	$px = $lnglat[1];
	$py = $lnglat[0];
	$flag = FALSE;
	for ($i = 0, $j = $count - 1; $i < $count; $j = $i, $i++) {
		$sy = $polygon[$i][0];
		$sx = $polygon[$i][1];
		$ty = $polygon[$j][0];
		$tx = $polygon[$j][1];
		if ($px == $sx && $py == $sy || $px == $tx && $py == $ty)
			return TRUE;
		if ($sy < $py && $ty >= $py || $sy >= $py && $ty < $py) {
			$x = $sx + ($py - $sy) * ($tx - $sx) / ($ty - $sy);
			if ($x == $px)
				return TRUE;
			if ($x > $px)
				$flag = !$flag;
		}
	}
	return $flag;
}

function array_order($value, $array) {
	$array[] = $value;
	asort($array);
	$array = array_values($array);
	$index = array_search($value, $array);
	return $array[$index + 1];
}

function ierror($result_code, $result_message = '调用接口成功', $data = array('resultCode' => '')) {
	$result = array(
		'resultCode' => $result_code,
		'resultMessage' => $result_message,
		'data' => $data,
	);
	return $result;
}

function array_sort($array, $sort_key, $sort_order = SORT_ASC) {
	if(is_array($array)){
		foreach ($array as $row_array){
			$key_array[] = $row_array[$sort_key];
		}
		array_multisort($key_array, $sort_order, $array);
		return $array;
	}
	return false;
}

function array_depth($array) {
	if(!is_array($array)) return 0;
	$max_depth = 1;
	foreach ($array as $value) {
		if (is_array($value)) {
			$depth = array_depth($value) + 1;
			if ($depth > $max_depth) {
				$max_depth = $depth;
			}
		}
	}
	return $max_depth;
}

function multimerge(){
	$arrs = func_get_args();
	$merged = array();
	while($arrs){
		$array = array_shift($arrs);
		if(!$array){
			continue;
		}
		foreach ($array as $key => $value){
			if (1 || is_string($key)){
				if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])){
					$merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
				}else{
					$merged[$key] = $value;
				}
			}else{
				$merged[] = $value;
			}
		}
	}
	return $merged;
}

function is_time_in_period($period, $time = 0) {
	if(!is_array($period)) {
		return true;
	}
	if(empty($time)) {
		$time = TIMESTAMP;
	}
	foreach($period as $val) {
		if(!is_array($val)) {
			$val = $period;
		}
		$val = array_values($val);
		$starttime = strtotime($val[0]);
		$endtime = strtotime($val[1]);
		if(!$starttime) {
			$starttime = $val[0];
			$endtime = $val[1];
		}
		//时间段可否跨夜
		if($starttime >= $endtime) {
			$endtime = $endtime + 86399;
		}
		if($time >= $starttime && $time <= $endtime) {
			return true;
		}
	}
	return false;
}

function get_rand($proArr) {
	$result = '';
	$proSum = array_sum($proArr);
	foreach ($proArr as $key => $proCur) {
		$randNum = mt_rand(1, $proSum);
		if ($randNum <= $proCur) {
			$result = $key;
			break;
		} else {
			$proSum -= $proCur;
		}
	}
	unset ($proArr);
	return $result;
}

function array_compare($key, $array) {
	$keys = array_keys($array);
	$keys[] = $key;
	asort($keys);
	$values = array_values($keys);
	$index = array_search($key, $values);
	if($index >= 0) {
		$now = $values[$index];
		$next = $values[$index + 1];
		if($now == $next) {
			$next = intval($next);
			return $array[$next];
		}
		$index = $values[$index - 1];
		return $array[$index];
	}
	return false;
}

function upload_file($file, $type, $name = '', $path = '') {
	global $_W;
	if (empty($file['name'])) {
		return error(-1, '上传失败, 请选择要上传的文件！');
	}
	if ($file['error'] != 0) {
		return error(-1, '上传失败, 请重试.');
	}
	load()->func('file');
	$pathinfo = pathinfo($file['name']);
	$ext = strtolower($pathinfo['extension']);
	$basename = strtolower($pathinfo['basename']);
	if($name != '') {
		$basename = $name;
	}
	if(empty($path)) {
		$path = "resource/{$type}s/{$_W['uniacid']}/";
	}
	mkdirs(MODULE_ROOT . '/' . $path);
	if (!strexists($basename, $ext)) {
		$basename .= '.' . $ext;
	}
	if (!file_move($file['tmp_name'],  MODULE_ROOT . '/' . $path . $basename)) {
		return error(-1, '保存上传文件失败');
	}
	return $path . $basename;
}

function read_excel($filename) {
	include_once (IA_ROOT . '/framework/library/phpexcel/PHPExcel.php');
	$filename = MODULE_ROOT . '/' . $filename;
	if(!file_exists($filename)) {
		return error(-1, '文件不存在或已经删除');
	}
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if($ext == 'xlsx') {
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	} else {
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}

	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filename);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$highestRow = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	$excelData = array();
	for ($row = 1; $row <= $highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
		}
	}
	return $excelData;
}

function ifile_put_contents($filename, $data) {
	global $_W;
	load()->func('file');
	$filename = MODULE_ROOT . '/' . $filename;
	mkdirs(dirname($filename));
	file_put_contents($filename, $data);
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($filename);
}

function longurl2short($longurl) {
	load()->func('communication');
	$token = WeAccount::token();
	$url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$token}";
	$send = array(
		'action' => 'long2short',
		'long_url' => $longurl,
	);
	$response = ihttp_request($url, json_encode($send));
	if(is_error($response)) {
		return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
	}
	$result = @json_decode($response['content'], true);
	if(empty($result)) {
		return error(-1, "接口调用失败, 源数据: {$response['meta']}");
	} elseif(!empty($result['errcode'])) {
		return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
	}
	return $result['short_url'];
}

function media_id2url($media_id) {
	mload()->classs('wxaccount');
	$acc = new WxAccount();
	$data = $acc->media_download($media_id);
	if(is_error($data)) {
		return $data;
	}
	return $data;
}

function score_format($score) {
	$score = array(
		'all' => intval($score),
		'half' => intval($score) != $score,
	);
	$score['gray'] = 5 - $score['all'] - $score['half'];
	$scores = array();
	for($i = 0; $i < $score['all']; $i++) {
		$scores[] = 'all';
	}
	for($i = 0; $i < $score['half']; $i++) {
		$scores[] = 'half';
	}
	for($i = 0; $i < $score['gray']; $i++) {
		$scores[] = 'gray';
	}
	return $scores;
}

function uy2ch($str, $type = 'uy'){
	if($type == 'uy'){
		$result = preg_replace("/[\x{4e00}-\x{9fa5}]+/u",'', $str);
		return trim($result);
	} elseif($type == 'ch'){
		$uy = "عغشسژزردخچجتپبئەئائېۋئۈئۆئۇئوھنملڭگكقفيئى";
		$uyarr = preg_split('/(?<!^)(?!$)/u', $uy);
		$arr = preg_split('/(?<!^)(?!$)/u', $str);
		$ret = array();
		foreach ($arr as $k => $v) {
			if(in_array($v,$uyarr)){
				unset($arr[$k]);
			}else{
				$ret[] = $v;
			}
		}
		$res = implode('',$ret);
		return trim($res);
	}
}

function array_lang_translate($arr) {
	global $_W;
	if($_W['ilang'] != 'zhcn2uy') {
		return $arr;
	}
	$type = $_W['LangType'] == 'uy' ? 'uy' : 'ch';
	if(is_array($arr)) {
		$str = json_encode($arr, JSON_UNESCAPED_UNICODE);
		$str = uy2ch($str, $type);
		$arr = json_decode($str);
	}
	return $arr;
}

function j($string, $operation = 'DECODE', $key = '5b186210af4529ce_', $expiry = 0) {
	return authcode($string, $operation, $key, $expiry);
}





