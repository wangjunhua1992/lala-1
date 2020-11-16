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
function poster_trimPx($data) {
	$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
	$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
	$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
	$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
	$data['fontsize'] = intval(str_replace('px', '', $data['fontsize'])) * 2;
	$data['src'] = tomedia($data['src']);
	return $data;
}

function poster_mergeImage($target, $imgurl, $data) {
	$img = poster_createImage($imgurl);
	$w = imagesx($img);
	$h = imagesy($img);
	if ($data['border'] == 'radius' || $data['border'] == 'circle') {
		$img = imageRadius($img, $data['border'] == 'circle');
	}
	if($data['position'] == 'cover') {
		$oldheight = $data['height'];
		$data['height'] = $data['width']*$h/$w;
		if ($data['height'] > $oldheight) {
			$data['top'] = $data['top'] - ($data['height']-$oldheight)/2;
		}
	}
	imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
	imagedestroy($img);
	return $target;
}

function poster_createImage($imgurl) {
	global $_W;
	load()->func('communication');
	$resp = ihttp_request($imgurl, array(), array('CURLOPT_REFERER' => $_W['siteroot']));
	if(($resp['code'] == 200) && !empty($resp['content'])) {
		return imagecreatefromstring($resp['content']);
	}
	$i = 0;
	while($i < 3) {
		$resp = ihttp_request($imgurl);
		if(($resp['code'] == 200) && !empty($resp['content'])) {
			return imagecreatefromstring($resp['content']);
		}
		++$i;
	}
	return '';
}

function poster_mergeText($target, $text, $data) {
	$font = MODULE_ROOT . '/static/fonts/pingfang.ttf';//字体文件
	if(!is_file($font)) {
		$font = MODULE_ROOT . '/static/fonts/msyh.ttf';//字体文件
	}
	$colors = poster_hex2rgb($data['color']);
	$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
	$text = poster_autowrap($data['fontsize'], 0, $font, $text, $data['width'], $data['line']);
	imagettftext($target, $data['fontsize'], 0, $data['left'], $data['top'] + $data['fontsize'], $color, $font, $text);
	return $target;
}

function poster_hex2rgb($colour) {
	if ($colour[0] == '#') {
		$colour = substr($colour, 1);
	}
	if (strlen($colour) == 6) {
		list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
	} elseif (strlen($colour) == 3) {
		list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
	} else {
		return false;
	}
	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);
	return array('red' => $r, 'green' => $g, 'blue' => $b);
}

function poster_create($poster){
	global $_W;
	$file = "/resource/poster/{$poster['plugin']}/{$_W['uniacid']}/iposter_{$poster['name']}.jpg";
	$qrcode = MODULE_ROOT . "{$file}";
	if(file_exists($qrcode)) {
		return "{$_W['siteroot']}addons/we7_wmall{$file}?t=" . time();
	}
	load()->func('file');
	mkdirs(dirname($qrcode));
	set_time_limit(0);
	@ini_set('memory_limit', '256M');
	$bg = tomedia($poster['config']['bg']);
	if(empty($bg)) {
		return error(-1, '背景图片不存在');
	}
	$size = getimagesize($bg);
	if(empty($size)) {
		return error(-1, '获取背景图片信息失败');
	}
	$target = imagecreatetruecolor($size[0], $size[1]);
	$bg = poster_createImage($bg);
	imagecopy($target, $bg, 0, 0, 0, 0,$size[0], $size[1]);
	imagedestroy($bg);
	$extra = $poster['extra'];
	$parts = $poster['config']['data']['items'];
	foreach($parts as $part) {
		$style = poster_trimPx($part['style']);
		if($part['id'] == 'qrcode'){
			$qrcode_url = $poster['config']['qrcode_url'];
			poster_mergeImage($target, $qrcode_url, $style);
		} elseif($part['id'] == 'image'){
			poster_mergeImage($target, tomedia($part['params']['imgurl']), $style);
		}  elseif($part['id'] == 'avatar'){
			poster_mergeImage($target, $extra['avatar'], $style);
		} elseif($part['id'] == 'nickname'){
			poster_mergeText($target, $extra['nickname'], $style);
		} elseif($part['id'] == 'text'){
			poster_mergeText($target, $part['params']['content'], $style);
		}
	}
	$quality = intval($poster['config']['data']['page']['quality']);
	if(empty($quality)) {
		$quality = 75;
	}
	imagejpeg($target, $qrcode, $quality);
	imagedestroy($target);
	return "{$_W['siteroot']}addons/we7_wmall{$file}?t=" . time();
}

function poster_getQR($fans,$poster,$sid,$modulename){
	global $_W;
	$pid = $poster['id'];
	//看看是否已有记录
	$share = pdo_fetch('select * from '.tablename($modulename."_share")." where id='{$sid}'");
	if (!empty($share['url'])){
		$out = false;
		if ($poster['rtype']){//若是临时二维码 需要查看时间
			$qrcode = pdo_fetch('select * from '.tablename('qrcode')
				." where uniacid='{$_W['uniacid']}' and qrcid='{$share['sceneid']}' "
				." and ticket='{$share['ticketid']}' and url='{$share['url']}'");
			if($qrcode['createtime'] + $qrcode['expire'] < time()){//过期
				pdo_delete('qrcode',array('id'=>$qrcode['id']));
				$out = true;
			}
		}
		if (!$out){
			return $share['url'];
		}
	}
	$model = 2 - intval($poster['rtype']);
	//找出已经有的最大的场景id
	$sceneid = pdo_fetchcolumn('select qrcid from '.tablename("qrcode")." where uniacid='{$_W['uniacid']}' and model='{$model}' order by qrcid desc limit 1");
	if (empty($sceneid)) $sceneid = 20000;
	else $sceneid++;
	$barcode['action_info']['scene']['scene_id'] = $sceneid;

	load()->model('account');
	$acid = pdo_fetchcolumn('select acid from '.tablename('account')." where uniacid={$_W['uniacid']}");
	$uniacccount = WeAccount::create($acid);
	$time = 0;
	if ($poster['rtype']){//七天临时二维码
		$barcode['action_name'] = 'QR_SCENE';
		$barcode['expire_seconds'] = 30*24*3600;
		$res = $uniacccount->barCodeCreateDisposable($barcode);
		$time = $barcode['expire_seconds'];
	}else{
		$barcode['action_name'] = 'QR_LIMIT_SCENE';
		$res = $uniacccount->barCodeCreateFixed($barcode);
	}
	//将二维码存于微擎官方二维码表
	pdo_insert('qrcode',
		array('uniacid'=>$_W['uniacid'],'acid'=>$acid,'qrcid'=>$sceneid,'name'=>$poster['title'],'keyword'=>$poster['kword']
		,'model'=>$model,'ticket'=>$res['ticket'],'expire'=>$time,'createtime'=>time(),'status'=>1,'url'=>$res['url']
		)
	);

	pdo_update($modulename."_share",array('sceneid'=>$sceneid,'ticketid'=>$res['ticket'],'url'=>$res['url']),array('id'=>$sid));
	return $res['url'];
}

function imageRadius($target = false, $circle = false) {
	$w = imagesx($target);
	$h = imagesy($target);
	$w = min($w, $h);
	$h = $w;
	$img = imagecreatetruecolor($w, $h);
	imagesavealpha($img, true);
	$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
	imagefill($img, 0, 0, $bg);
	$radius = $circle ? $w / 2 : 20;
	$r = $radius;
	$x = 0;

	while ($x < $w) {
		$y = 0;
		while ($y < $h) {
			$rgbColor = imagecolorat($target, $x, $y);
			if ($radius <= $x && $x <= $w - $radius || $radius <= $y && $y <= $h - $radius) {
				imagesetpixel($img, $x, $y, $rgbColor);
			} else {
				$y_x = $r;
				$y_y = $r;

				if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}

				$y_x = $w - $r;
				$y_y = $r;

				if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}

				$y_x = $r;
				$y_y = $h - $r;

				if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}

				$y_x = $w - $r;
				$y_y = $h - $r;

				if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}
			}

			++$y;
		}

		++$x;
	}

	return $img;
}

// 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
function poster_autowrap($fontsize, $angle, $fontface, $string, $width, $needhang = 1) {
	$content = "";
	$hang = 1;
	// 将字符串拆分成一个个单字 保存到数组 letter 中
	for ($i = 0; $i < mb_strlen($string, 'UTF8'); $i++) {
		$letter[] = mb_substr($string, $i, 1, 'UTF8');
	}
	foreach ($letter as $l) {
		$teststr = $content . " " . $l;
		$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
		// 判断拼接后的字符串是否超过预设的宽度
		if (($testbox[2] > $width) && ($content !== "")) {
			if ($hang < $needhang) {
				$content .= "\n";
				$hang++;
			} else {
				break;
			}
		}
		$content .= $l;
	}
	return $content;
}


