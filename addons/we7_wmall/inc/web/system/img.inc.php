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
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
set_time_limit(0);

load()->func('file');

function ifile_write($content, $name = '', $remote = false) {
	global $_W;
	if(empty($name)) {
		$name = "/images/{$_W['uniacid']}/" . date('Y/m/') . random(30) . ".jpg";
	}

	$filename = ATTACHMENT_ROOT . "/{$name}";
	mkdirs(dirname($filename));
	file_put_contents($filename, $content);
	@chmod($filename, $_W['config']['setting']['filemode']);
	if(!is_file($filename)) {
		return error(-1, '保存图片失败');
	}
	if($remote || !empty($_W['setting']['remote']['type'])) {
		$status = file_remote_upload($name);
		if(is_error($status)) {
			return error(-1, '上传到远程失败');
		}
	}
	return $name;
}

$goods_img = pdo_fetchall('select * from' . tablename('tiny_wmall_goods') . 'where imgstatus = 0 order by id asc limit 3', array());
if(!empty($goods_img)){
	foreach ($goods_img as $value) {
		$value['img'] = 'http://waimai.dsjax.com/upload/' . $value['thumb'];
		$img = ihttp_get($value['img']);
		if(is_error($img)){
			continue;
		}
		$content = $img['content'];
		$name = ifile_write($content, '', true);
		if(is_error($name)){
			continue;
		} else {
			pdo_update('tiny_wmall_goods', array('thumb' => $name, 'imgstatus' => 1), array('id' => $value['id']));
		}
	}
}
echo 'ddd';
die;
if(!empty($goods_img)){
	imessage("正在拉取商品图片,请勿关闭浏览器", iurl('lewaimai/batch/goodsImg'), 'success');
} else {
	imessage("拉取成功", iurl('lewaimai/config/index'), 'success');
}
