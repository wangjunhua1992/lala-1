

<?php

$installSql = <<<sql



CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_bargain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `content` varchar(255) NOT NULL,
  `order_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goods_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starthour` smallint(5) unsigned NOT NULL DEFAULT '0',
  `endhour` smallint(5) unsigned NOT NULL DEFAULT '0',
  `use_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'ongoing',
  `total_updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL DEFAULT 'bargain',
  `thumb` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_bargain_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `bargain_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `discount_price` varchar(10) NOT NULL DEFAULT '0',
  `max_buy_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `poi_user_type` varchar(10) NOT NULL DEFAULT 'all',
  `discount_total` int(10) NOT NULL DEFAULT '-1',
  `discount_available_total` int(10) NOT NULL DEFAULT '-1',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `dosage` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mall_displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `bargain_id` (`bargain_id`),
  KEY `goods_id` (`goods_id`),
  KEY `status` (`status`),
  KEY `mall_displayorder` (`mall_displayorder`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `type` varchar(20) NOT NULL DEFAULT 'collect',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `discount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '折扣券(折扣率), 代金券(面额)',
  `condition` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单满多少可用',
  `type_limit` int(10) NOT NULL DEFAULT '1' COMMENT '1:所有用户都可领取,2:新用户可领取',
  `dosage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `amount` int(10) unsigned NOT NULL COMMENT '总发行数量',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:可领取,2:暂停领取',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发行时间',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `coupons` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`type`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`),
  KEY `status` (`status`),
  KEY `activity_id` (`activity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_coupon_grant_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `grant_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:一次性领取,2:每天领取 ',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_coupon_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:未使用,2:已使用 ',
  `remark` varchar(300) NOT NULL DEFAULT '',
  `granttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发放时间',
  `usetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `type` varchar(20) NOT NULL DEFAULT 'couponCollect',
  `discount` varchar(10) NOT NULL DEFAULT '0',
  `condition` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(30) NOT NULL,
  `is_notice` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `noticetime` int(10) unsigned NOT NULL DEFAULT '0',
  `give_status` tinyint(3) NOT NULL DEFAULT '0',
  `from_uid` int(10) NOT NULL DEFAULT '0',
  `givetime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `is_notice` (`is_notice`),
  KEY `channel` (`channel`),
  KEY `noticetime` (`noticetime`),
  KEY `endtime` (`endtime`),
  KEY `uniacid_sid_uid_orderid` (`uniacid`,`sid`,`uid`,`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=759 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_redpacket_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `super_share_id` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `category_limit` varchar(500) NOT NULL,
  `times_limit` varchar(500) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `is_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `remark` varchar(300) NOT NULL DEFAULT '',
  `granttime` int(10) unsigned NOT NULL DEFAULT '0',
  `usetime` int(10) unsigned NOT NULL DEFAULT '0',
  `scene` varchar(100) NOT NULL DEFAULT 'waimai',
  `is_notice` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `noticetime` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `grantday` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `order_type_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `redpacketid` (`activity_id`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `is_show` (`is_show`),
  KEY `scene` (`scene`),
  KEY `endtime` (`endtime`),
  KEY `is_notice` (`is_notice`),
  KEY `noticetime` (`noticetime`),
  KEY `uniacid_uid_orderid` (`uniacid`,`uid`,`order_id`),
  KEY `uniacid_type_uid_aid` (`uniacid`,`type`,`uid`,`activity_id`),
  KEY `uniacid_type_openid_aid` (`uniacid`,`type`,`openid`,`activity_id`),
  KEY `uniacid_status_endtime` (`uniacid`,`status`,`endtime`)

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_activity_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(40) NOT NULL,
  `formuser_redpacket_min` tinyint(3) NOT NULL DEFAULT '1',
  `formuser_redpacket_max` tinyint(3) NOT NULL DEFAULT '10',
  `formuser_redpacket_time_limit` tinyint(3) NOT NULL DEFAULT '7',
  `touser_redpacket_min` tinyint(3) NOT NULL DEFAULT '1',
  `touser_redpacket_max` tinyint(3) NOT NULL DEFAULT '10',
  `touser_redpacket_time_limit` tinyint(3) NOT NULL DEFAULT '7',
  `share` varchar(500) NOT NULL,
  `agreement` text NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `realname` varchar(15) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(50) NOT NULL,
  `number` varchar(60) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `mode` varchar(20) NOT NULL DEFAULT 'favorite',
  `area_id` int(10) unsigned NOT NULL DEFAULT '0',
  `area_parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `tag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=57096 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_advertise_trade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `displayorder` tinyint(3) NOT NULL,
  `type` varchar(50) NOT NULL,
  `final_fee` varchar(20) NOT NULL,
  `days` tinyint(10) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`displayorder`),
  KEY `status` (`status`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_agent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `area` varchar(30) NOT NULL,
  `initial` varchar(10) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `password` varchar(32) NOT NULL,
  `status` tinyint(3) unsigned DEFAULT '0',
  `sysset` text NOT NULL,
  `pluginset` text NOT NULL,
  `account` varchar(1000) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee` varchar(5000) NOT NULL,
  `geofence` text NOT NULL,
  `data` text NOT NULL,
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `token` varchar(50) NOT NULL,
  `amount_min` decimal(10,2) NOT NULL DEFAULT '0.00',
  `kefu_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `uniacid_token` (`uniacid`,`token`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_agent_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `order_type` varchar(20) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB AUTO_INCREMENT=3725 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_agent_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `channel` varchar(10) NOT NULL DEFAULT 'weixin',
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_agent_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `salt` varchar(10) NOT NULL,
  `token` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid_mobile` (`uniacid`,`agentid`,`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_area_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_assign_board` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(15) NOT NULL,
  `openid` varchar(64) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `number` varchar(20) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_notify` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_assign_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` varchar(10) NOT NULL,
  `endtime` varchar(10) NOT NULL,
  `prefix` varchar(10) NOT NULL COMMENT '前缀',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `position` int(10) unsigned NOT NULL DEFAULT '1',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根据这个时间,判断是否将position重新至0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cache` (
  `name` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  `starttime|516|basic|18.6.0|20190604093003` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'member',
  `alias` varchar(20) NOT NULL,
  `score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `color` varchar(15) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_system` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_clerk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL,
  `nickname` varchar(15) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `avatar` varchar(255) NOT NULL,
  `token` varchar(50) NOT NULL,
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `openid_wxapp` varchar(60) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid_wxapp_manager` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `openid_wxapp` (`openid_wxapp`),
  KEY `openid` (`openid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=975 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cloudgoods_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `title` varchar(30) NOT NULL,
  `number` varchar(50) NOT NULL,
  `price` varchar(10) NOT NULL,
  `old_price` varchar(10) NOT NULL,
  `ts_price` varchar(10) NOT NULL DEFAULT '0.00',
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `is_options` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `unitname` varchar(10) NOT NULL DEFAULT '份',
  `total` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `slides` varchar(1000) NOT NULL,
  `label` varchar(5) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `content` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `attrs` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `category_id` (`category_id`),
  KEY `is_hot` (`is_hot`),
  KEY `title` (`title`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cloudgoods_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `store_categoryid` (`menu_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cloudgoods_goods_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `price` varchar(50) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '-1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cloudgoods_menu_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_complain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `addtime` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sysset` text NOT NULL,
  `pluginset` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `wxapp_link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(10) DEFAULT '0',
  `status` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `displayorder` (`displayorder`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `category_id` int(10) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT '',
  `thumb` varchar(255) NOT NULL,
  `old_price` varchar(10) NOT NULL,
  `chance` tinyint(3) unsigned NOT NULL,
  `totalday` tinyint(3) unsigned NOT NULL,
  `use_credit1` varchar(10) NOT NULL DEFAULT '0',
  `use_credit2` varchar(10) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `credit2` varchar(10) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redpacket` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credits` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `itemcode` int(10) unsigned NOT NULL DEFAULT '0',
  `actualprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `faceprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `description` varchar(255) NOT NULL,
  `ordernum` varchar(255) NOT NULL,
  `ordersn` varchar(50) NOT NULL,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_order_new` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_type` varchar(20) NOT NULL,
  `order_sn` varchar(50) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `total_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `transaction_id` varchar(60) NOT NULL,
  `use_credit1` varchar(10) NOT NULL DEFAULT '0.00',
  `use_credit2` varchar(10) NOT NULL DEFAULT '0.00',
  `data` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `grant_status` int(10) NOT NULL DEFAULT '0',
  `use_credit1_status` tinyint(3) NOT NULL DEFAULT '0',
  `channel` varchar(15) NOT NULL DEFAULT 'wechat',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `is_pay` (`is_pay`),
  KEY `pay_type` (`pay_type`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB AUTO_INCREMENT=585 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_cube` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `tips` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wxapp_link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_delivery_cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `price` int(10) unsigned NOT NULL DEFAULT '0',
  `day_free_limit` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_fee_free_limit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_delivery_cards_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliverycard_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL DEFAULT '0',
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `exchangetime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid_code` (`uniacid`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_delivery_cards_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `ordersn` varchar(20) NOT NULL,
  `card_id` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` varchar(20) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=122 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL,
  `nickname` varchar(15) NOT NULL,
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `token` varchar(32) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `credit1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `work_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `auth_info` varchar(500) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `order_takeout_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_errander_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_takeout` tinyint(3) NOT NULL DEFAULT '0',
  `is_errander` tinyint(3) NOT NULL DEFAULT '0',
  `extra` varchar(500) NOT NULL,
  `openid_wxapp` varchar(60) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `collect_max_takeout` int(10) NOT NULL,
  `collect_max_errander` int(10) NOT NULL,
  `perm_transfer` varchar(200) NOT NULL,
  `perm_cancel` varchar(200) NOT NULL,
  `fee_delivery` varchar(500) NOT NULL,
  `fee_getcash` varchar(500) NOT NULL,
  `openid_wxapp_deliveryer` varchar(60) NOT NULL,
  `account` varchar(2000) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `deltime` int(10) unsigned NOT NULL DEFAULT '0',
  `kefu_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fee_include_tips` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `work_status` (`work_status`),
  KEY `token` (`token`),
  KEY `is_takeout` (`is_takeout`),
  KEY `is_errander` (`is_errander`),
  KEY `openid_wxapp` (`openid_wxapp`),
  KEY `openid` (`openid`),
  KEY `registration_id` (`registration_id`),
  KEY `uniacid_token` (`uniacid`,`token`)
) ENGINE=MyISAM AUTO_INCREMENT=393 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'order',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `trade_type` (`trade_type`),
  KEY `uniacid_stat_month` (`uniacid`,`deliveryer_id`,`stat_month`)
) ENGINE=MyISAM AUTO_INCREMENT=333769 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `account` varchar(1000) NOT NULL,
  `channel` varchar(10) NOT NULL DEFAULT 'weixin',
  `toaccount_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2350 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL DEFAULT '',
  `group_condition` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_fee` varchar(2000) NOT NULL DEFAULT '',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer_location_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime_cn` varchar(50) NOT NULL,
  `from` varchar(10) NOT NULL DEFAULT 'app',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=744 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_deliveryer_transfer_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'takeout',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`)
) ENGINE=MyISAM AUTO_INCREMENT=564 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_diypage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `diymenu` int(10) unsigned NOT NULL DEFAULT '0',
  `version` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`),
  KEY `version` (`version`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_diypage_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `version` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `addtime` (`addtime`),
  KEY `updatetime` (`updatetime`),
  KEY `version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_diypage_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `preview` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_errander_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `goods_thumbs_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_on_upload` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'buy',
  `label` varchar(1000) NOT NULL,
  `labels` varchar(1000) NOT NULL,
  `delivery_within_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `start_fee` varchar(10) NOT NULL,
  `start_km` varchar(10) NOT NULL,
  `pre_km` varchar(10) NOT NULL DEFAULT '1',
  `pre_km_fee` varchar(10) NOT NULL DEFAULT '0',
  `weight_fee_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `weight_fee` text NOT NULL,
  `multiaddress` varchar(500) NOT NULL,
  `tip_min` varchar(10) NOT NULL DEFAULT '0',
  `tip_max` varchar(10) NOT NULL DEFAULT '0',
  `group_discount` varchar(1000) NOT NULL,
  `delivery_times` text NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rule` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `notice` varchar(500) NOT NULL,
  `deliveryers` varchar(255) NOT NULL,
  `goods_value_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `distance_calculate_type` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_errander_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `order_type` varchar(20) NOT NULL DEFAULT 'buy',
  `order_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(60) NOT NULL,
  `goods_price` varchar(30) NOT NULL,
  `goods_weight` varchar(10) NOT NULL,
  `buy_username` varchar(20) NOT NULL,
  `buy_sex` varchar(5) NOT NULL,
  `buy_mobile` varchar(15) NOT NULL,
  `buy_address` varchar(100) NOT NULL,
  `buy_location_x` varchar(20) NOT NULL,
  `buy_location_y` varchar(20) NOT NULL,
  `accept_username` varchar(20) NOT NULL,
  `accept_sex` varchar(5) NOT NULL,
  `accept_mobile` varchar(15) NOT NULL,
  `accept_address` varchar(100) NOT NULL,
  `accept_location_x` varchar(20) NOT NULL,
  `accept_location_y` varchar(20) NOT NULL,
  `distance` varchar(20) NOT NULL,
  `delivery_time` varchar(30) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_handle_type` varchar(15) NOT NULL DEFAULT 'wechat',
  `delivery_assign_time` int(10) NOT NULL DEFAULT '0',
  `delivery_instore_time` int(10) NOT NULL DEFAULT '0',
  `delivery_success_time` int(10) NOT NULL DEFAULT '0',
  `delivery_success_location_x` varchar(15) NOT NULL,
  `delivery_success_location_y` varchar(15) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_fee` varchar(10) NOT NULL,
  `delivery_tips` varchar(10) NOT NULL,
  `total_fee` varchar(10) NOT NULL,
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `deliveryer_fee` varchar(10) NOT NULL,
  `deliveryer_total_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `agent_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(1000) NOT NULL,
  `agent_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve` varchar(1000) NOT NULL,
  `thumbs` varchar(1000) NOT NULL,
  `note` varchar(500) NOT NULL,
  `is_remind` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `anonymous_username` varchar(15) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `refund_out_no` varchar(40) NOT NULL,
  `refund_apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_channel` varchar(30) NOT NULL,
  `refund_account` varchar(30) NOT NULL,
  `stat_year` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `delivery_collect_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `transfer_deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `transfer_delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadbalance` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `plateform_discount_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `is_pay` (`is_pay`),
  KEY `pay_type` (`pay_type`),
  KEY `refund_status` (`refund_status`),
  KEY `delivery_status` (`delivery_status`),
  KEY `status` (`status`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `agentid` (`agentid`),
  KEY `delivery_collect_type` (`delivery_collect_type`),
  KEY `transfer_deliveryer_id` (`delivery_collect_type`),
  KEY `transfer_delivery_status` (`delivery_collect_type`)
) ENGINE=MyISAM AUTO_INCREMENT=3793 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_errander_order_discount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `note` varchar(50) NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '0.00',
  `store_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_errander_order_status_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(30) NOT NULL,
  `role_cn` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=21804 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_errander_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'sence',
  `scene` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `agreement` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `isdefault` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `start_hour` varchar(20) NOT NULL DEFAULT '00:00',
  `end_hour` varchar(20) NOT NULL DEFAULT '23:59',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`),
  KEY `isdefault` (`isdefault`),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_freelunch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  `partake_grant_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reward_grant_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pre_partaker_num` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pre_reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pre_max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  `plus_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `plus_thumb` varchar(255) NOT NULL,
  `plus_partaker_num` int(10) unsigned NOT NULL DEFAULT '0',
  `plus_reward_num` int(10) unsigned NOT NULL DEFAULT '1',
  `plus_pre_partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pre_plus_reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `plus_pre_max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `plus_serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `share` varchar(3000) NOT NULL,
  `agreement` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `credit_pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_freelunch_partaker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `freelunch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `number` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `order_sn` varchar(50) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `freelunch_id` (`freelunch_id`),
  KEY `record_id` (`record_id`),
  KEY `uid` (`uid`),
  KEY `serial_sn` (`serial_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=4090 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_freelunch_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `freelunch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL DEFAULT 'common',
  `partaker_total` int(10) unsigned NOT NULL DEFAULT '0',
  `partaker_dosage` int(10) unsigned NOT NULL DEFAULT '0',
  `partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `reward_uid` varchar(1000) NOT NULL,
  `reward_number` int(10) unsigned NOT NULL DEFAULT '0',
  `startime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `freelunch_id` (`freelunch_id`),
  KEY `serial_sn` (`serial_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `wxapp_link` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_type` varchar(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `goods_quality` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `store_service` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL,
  `thumbs` varchar(3000) NOT NULL,
  `reply` varchar(500) NOT NULL,
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `oid` (`oid`),
  KEY `uid` (`uid`),
  KEY `sid` (`sid`),
  KEY `goods_id` (`goods_id`),
  KEY `status` (`status`),
  KEY `uniacid_goods_id` (`uniacid`,`agentid`,`goods_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_uid_type_goodsid` (`uniacid`,`uid`,`type`,`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `wxapp_link` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `description` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `order_type` varchar(32) NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `order_channel` varchar(20) NOT NULL DEFAULT 'wechat',
  `is_team` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `takepart_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordersn` varchar(60) NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `discount_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_type` varchar(20) NOT NULL,
  `paytime` varchar(145) NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `remark` text,
  `transaction_id` varchar(60) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `buyremark` text NOT NULL,
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `refund_out_no` varchar(40) NOT NULL,
  `refund_apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_channel` varchar(30) NOT NULL,
  `refund_account` varchar(30) NOT NULL,
  `spec` text NOT NULL,
  `fightstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `fightgroupid` int(10) unsigned NOT NULL DEFAULT '0',
  `expressid` int(10) unsigned NOT NULL DEFAULT '0',
  `recordid` int(10) unsigned NOT NULL DEFAULT '0',
  `refundtime` varchar(145) NOT NULL,
  `applyrefund` int(10) unsigned NOT NULL DEFAULT '0',
  `applytime` varchar(145) NOT NULL,
  `disorderid` int(10) unsigned NOT NULL DEFAULT '0',
  `failtimes` int(10) unsigned NOT NULL DEFAULT '0',
  `vipbuyflag` int(10) unsigned NOT NULL DEFAULT '0',
  `specid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  `address` varchar(60) NOT NULL DEFAULT '',
  `paidprid` int(10) unsigned NOT NULL DEFAULT '0',
  `shareid` int(10) unsigned NOT NULL DEFAULT '0',
  `settlementmoney` decimal(10,2) NOT NULL,
  `goodsprice` decimal(10,2) NOT NULL,
  `overtime` int(10) unsigned NOT NULL DEFAULT '0',
  `changedispatchprice` decimal(10,2) NOT NULL,
  `changeprice` decimal(10,2) NOT NULL,
  `originalprice` decimal(10,2) NOT NULL,
  `estimatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `store_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `store_final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `plateform_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `plateform_serve_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `plateform_serve` varchar(500) NOT NULL,
  `agent_final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(500) NOT NULL,
  `agent_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stat_year` int(10) unsigned NOT NULL,
  `stat_month` int(10) unsigned NOT NULL,
  `stat_day` int(10) unsigned NOT NULL,
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `print_sn` varchar(100) NOT NULL DEFAULT '0',
  `print_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadbalance` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_uid_status` (`uniacid`,`uid`,`status`),
  KEY `uniacid_sid_status` (`uniacid`,`sid`,`status`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_gohome_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `wxapp_link` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'gohome',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `price` varchar(500) NOT NULL,
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `min_buy_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_options` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `unitname` varchar(10) NOT NULL DEFAULT '份',
  `total` int(10) NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `slides` varchar(1000) NOT NULL,
  `label` varchar(5) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `comment_total` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_good` int(10) unsigned NOT NULL DEFAULT '0',
  `print_label` int(10) unsigned NOT NULL DEFAULT '0',
  `number` varchar(50) NOT NULL,
  `old_price` varchar(10) NOT NULL,
  `total_warning` int(10) unsigned NOT NULL DEFAULT '0',
  `total_update_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `content` varchar(100) NOT NULL,
  `attrs` varchar(1000) NOT NULL,
  `elemeId` varchar(50) NOT NULL DEFAULT '0',
  `meituanId` varchar(50) NOT NULL DEFAULT '0',
  `openplateformCode` varchar(50) NOT NULL DEFAULT '0',
  `child_id` int(20) unsigned NOT NULL DEFAULT '0',
  `is_showtime` tinyint(3) NOT NULL DEFAULT '0',
  `start_time1` varchar(10) NOT NULL,
  `end_time1` varchar(10) NOT NULL,
  `start_time2` varchar(10) NOT NULL,
  `end_time2` varchar(10) NOT NULL,
  `week` varchar(50) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `ts_price` varchar(10) NOT NULL DEFAULT '0.00',
  `svip_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `svip_price` varchar(10) NOT NULL DEFAULT '0',
  `unitnum` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `data` varchar(2000) NOT NULL,
  `huangou_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `kabao_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kabao_price` varchar(500) NOT NULL,
  `caigou_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `displayorder` (`displayorder`),
  KEY `elemeId` (`elemeId`),
  KEY `meituanId` (`meituanId`),
  KEY `openplateformCode` (`openplateformCode`),
  KEY `is_showtime` (`is_showtime`),
  KEY `sid` (`uniacid`,`sid`),
  KEY `cid` (`uniacid`,`sid`,`type`,`status`,`cid`),
  KEY `title` (`uniacid`,`sid`,`title`),
  KEY `is_hot` (`uniacid`,`sid`,`type`,`status`,`is_hot`),
  KEY `status` (`uniacid`,`sid`,`type`,`status`),
  KEY `child_id` (`uniacid`,`sid`,`type`,`status`,`cid`,`child_id`),
  KEY `uniacid_svip_status_status` (`uniacid`,`status`,`svip_status`)
) ENGINE=MyISAM AUTO_INCREMENT=23509 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_fee` int(10) unsigned NOT NULL DEFAULT '0',
  `elemeId` varchar(50) NOT NULL DEFAULT '0',
  `parentid` int(10) NOT NULL DEFAULT '0',
  `description` varchar(100) NOT NULL,
  `is_showtime` tinyint(3) NOT NULL DEFAULT '0',
  `start_time` varchar(10) NOT NULL,
  `end_time` varchar(10) NOT NULL,
  `week` varchar(50) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`),
  KEY `elemeId` (`elemeId`),
  KEY `is_showtime` (`is_showtime`)
) ENGINE=MyISAM AUTO_INCREMENT=3046 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_goods_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `price` varchar(50) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '-1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `total_warning` int(10) unsigned NOT NULL DEFAULT '0',
  `svip_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `kabao_price` varchar(500) NOT NULL,
  `caigou_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3175 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_haodian_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_haodian_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` varchar(20) NOT NULL,
  `days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ordersn` varchar(50) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `order_channel` varchar(15) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `agent_final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_uid` (`uniacid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_help` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime|516|basic|10.6.0|20180111192937` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kanjia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `cateid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `oldprice` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `vipprice` decimal(10,2) NOT NULL,
  `submitmoneylimit` decimal(10,2) NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `helplimit` int(10) unsigned NOT NULL DEFAULT '0',
  `dayhelplimit` int(10) unsigned NOT NULL DEFAULT '0',
  `joinlimit` int(10) unsigned NOT NULL DEFAULT '0',
  `falsejoinnum` int(10) unsigned NOT NULL DEFAULT '0',
  `falselooknum` int(10) unsigned NOT NULL DEFAULT '0',
  `falsesharenum` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(50) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `thumbs` text NOT NULL,
  `unit` varchar(45) NOT NULL,
  `bgmusic` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `activity_rules` text NOT NULL,
  `rules` text NOT NULL,
  `vipstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `total` int(10) NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `malldisplayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `looknum` int(10) unsigned NOT NULL DEFAULT '0',
  `sharenum` int(10) unsigned NOT NULL DEFAULT '0',
  `usestatus` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `share` varchar(2000) NOT NULL,
  `total_update_type` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`),
  KEY `uniacid_agentid_cateid_status` (`uniacid`,`agentid`,`cateid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kanjia_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kanjia_helprecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  `activityid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `authorid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `bargainprice` decimal(10,2) NOT NULL,
  `afterprice` decimal(10,2) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kanjia_userlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  `activityid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `createtime` int(11) NOT NULL,
  `updatetime` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `qrcode` int(11) NOT NULL,
  `usetimes` int(11) NOT NULL,
  `usedtime` text NOT NULL,
  `expressid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=493 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kefu_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `relation` varchar(20) NOT NULL DEFAULT 'member2kefu',
  `orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `fansopenid` varchar(100) NOT NULL,
  `kefuopenid` varchar(100) NOT NULL,
  `kefuunionid` varchar(100) NOT NULL,
  `fansavatar` varchar(200) NOT NULL,
  `kefuavatar` varchar(200) NOT NULL,
  `fansnickname` varchar(100) NOT NULL,
  `kefunickname` varchar(100) NOT NULL,
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0',
  `notread` int(10) unsigned NOT NULL DEFAULT '0',
  `lastcontent` varchar(255) NOT NULL,
  `kefulasttime` int(10) unsigned NOT NULL DEFAULT '0',
  `kefulastcontent` varchar(255) NOT NULL,
  `kefunotread` int(10) unsigned NOT NULL DEFAULT '0',
  `msgtype` varchar(20) NOT NULL DEFAULT 'text',
  `kefumsgtype` varchar(20) NOT NULL DEFAULT 'text',
  `ishei` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fansdel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kefudel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fansisonline` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kefuisonline` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bindopenid` varchar(100) NOT NULL,
  `handlestatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `handletime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `reason` varchar(255) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_relation` (`uniacid`,`relation`),
  KEY `uniacid_relation_fans_kefu` (`uniacid`,`relation`,`fansopenid`,`kefuopenid`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kefu_chat_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `chatid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `openid` varchar(100) NOT NULL,
  `toopenid` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `isread` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `voicetime` smallint(6) NOT NULL,
  `isreadvoice` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isjqr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fansdel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kefudel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isck` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mp3du` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_chatid` (`uniacid`,`chatid`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_kefu_fastreply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `relation` varchar(20) NOT NULL DEFAULT 'member2clerk',
  `useropenid` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_relation_useropenid` (`uniacid`,`relation`,`useropenid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_lewaimai_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `storeidOrgoodsid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT 'goods',
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `storeidOrgoodsid` (`storeidOrgoodsid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_mealredpacket_exchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `redpacketid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `grant_days_effect` int(10) unsigned NOT NULL DEFAULT '0',
  `use_days_limit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `redpacketid` (`redpacketid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_black` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `plugin` varchar(30) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_uid_plugin` (`uniacid`,`uid`,`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_footmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `sid` (`sid`),
  KEY `stat_day` (`stat_day`),
  KEY `agentid` (`agentid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=1488343 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL DEFAULT '',
  `group_condition` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `recognition` varchar(50) NOT NULL DEFAULT '',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_recharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `order_sn` varchar(40) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` varchar(15) NOT NULL DEFAULT 'credit',
  `tag` varchar(1000) NOT NULL,
  `is_pay` tinyint(1) NOT NULL DEFAULT '0',
  `pay_type` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=960 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_scan_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uids` text NOT NULL,
  `nums` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_sid_stat_day` (`uniacid`,`sid`,`stat_day`)
) ENGINE=MyISAM AUTO_INCREMENT=45961 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_member_union_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `fromuid` int(10) NOT NULL DEFAULT '0',
  `touid` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `mobile_audit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `salt` varchar(10) NOT NULL,
  `password` varchar(32) NOT NULL,
  `token` varchar(50) NOT NULL,
  `openid_qq` varchar(50) NOT NULL,
  `openid_wx` varchar(50) NOT NULL,
  `credit1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `credit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `register_type` varchar(20) NOT NULL DEFAULT 'wechat',
  `setmeal_id` int(10) unsigned NOT NULL DEFAULT '0',
  `setmeal_day_free_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `setmeal_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `setmeal_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `success_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_num` int(10) unsigned DEFAULT '0',
  `success_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_num` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_sys` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:系统会员, 2:模块兼容会员',
  `search_data` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid_wxapp` varchar(50) NOT NULL,
  `uid_qianfan` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_majia` int(10) unsigned NOT NULL DEFAULT '0',
  `unionId` varchar(50) NOT NULL,
  `is_spread` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spreadcredit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadfixed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spread_groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `spread_status` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadtime` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid_updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `spread_groupid_change_from` varchar(10) NOT NULL DEFAULT 'system',
  `setmeal_deliveryfee_free_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `svip_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `svip_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `svip_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `svip_credit1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account` varchar(2000) NOT NULL,
  `stat_first_day` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_last_day` int(10) unsigned NOT NULL DEFAULT '0',
  `openid_ttapp` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `uid_qianfan` (`uid_qianfan`),
  KEY `is_spread` (`is_spread`),
  KEY `spead_groupid` (`spread_groupid`),
  KEY `spead_status` (`spread_status`),
  KEY `spreadtime` (`spreadtime`),
  KEY `openid` (`openid`),
  KEY `uid_majia` (`uid_majia`),
  KEY `spread1` (`spread1`),
  KEY `spread2` (`spread2`),
  KEY `openid_wxapp` (`openid_wxapp`),
  KEY `unionId` (`unionId`),
  KEY `first_order_time` (`success_first_time`),
  KEY `last_order_time` (`success_last_time`),
  KEY `speadid1` (`spread1`),
  KEY `speadid2` (`spread2`),
  KEY `uniacid_openid` (`uniacid`,`openid`),
  KEY `uniacid_openidwx` (`uniacid`,`openid_wx`),
  KEY `uniacid_openidwxapp` (`uniacid`,`openid_wxapp`),
  KEY `uniacid_unionId` (`uniacid`,`unionId`),
  KEY `uniacid_is_spread` (`uniacid`,`is_spread`),
  KEY `uniacid_statfirstday` (`uniacid`,`stat_first_day`),
  KEY `uniacid_statlastday` (`uniacid`,`stat_last_day`),
  KEY `uniacid_uid_nickname` (`uniacid`,`uid`,`nickname`),
  KEY `uniacid_token` (`uniacid`,`token`),
  KEY `uniacid_uid` (`uniacid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=61491 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `cateid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(50) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_display` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_show_home` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `cateid` (`cateid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_news_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'member',
  `title` varchar(60) NOT NULL,
  `link` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `flag` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime|516|basic|14.2.0|20180721162105` int(10) unsigned NOT NULL DEFAULT '0',
  `wxapp_link` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_notice_read_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `is_new` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL DEFAULT 'store',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `notice_id` (`notice_id`),
  KEY `is_new` (`is_new`)
) ENGINE=MyISAM AUTO_INCREMENT=697 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_oauth_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `oauth_openid` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'wechat',
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`),
  KEY `openid` (`openid`),
  KEY `oauth_openid` (`oauth_openid`),
  KEY `appid_openid` (`appid`,`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_operate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `role` varchar(20) NOT NULL,
  `type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `content` varchar(500) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `address` varchar(50) NOT NULL,
  `source` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=264362 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordersn` varchar(20) NOT NULL,
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` varchar(100) NOT NULL,
  `number` varchar(60) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `note` varchar(500) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `num` tinyint(3) unsigned NOT NULL,
  `delivery_day` varchar(20) NOT NULL,
  `delivery_time` varchar(20) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_assign_time` int(10) NOT NULL DEFAULT '0',
  `delivery_success_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '3:待配送, 4:配送中, 5: 配送成功, 6: 配送失败',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_comment` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '外卖配送费',
  `pack_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `serve_fee` varchar(10) NOT NULL,
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `total_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `vip_free_delivery_fee` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `invoice` varchar(500) NOT NULL,
  `data` text NOT NULL,
  `is_remind` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_refund` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `person_num` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `table_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `reserve_type` varchar(10) NOT NULL,
  `reserve_time` varchar(30) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadbalance` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mall_first_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `handletime` int(10) unsigned NOT NULL DEFAULT '0',
  `clerk_notify_collect_time` int(10) NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `is_timeout` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_handle_type` varchar(20) NOT NULL DEFAULT 'wechat',
  `delivery_success_location_x` varchar(15) NOT NULL,
  `delivery_success_location_y` varchar(15) NOT NULL,
  `deliveryingtime` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_instore_time` int(10) NOT NULL DEFAULT '0',
  `deliverysuccesstime` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `distance` varchar(20) NOT NULL DEFAULT '0.00',
  `store_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `store_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_serve` varchar(500) NOT NULL,
  `plateform_serve_rate` varchar(10) NOT NULL DEFAULT '0',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_delivery_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_deliveryer_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_serve` varchar(500) NOT NULL,
  `agent_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_serve_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `refund_fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(50) NOT NULL,
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `last_notify_deliveryer_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_notify_clerk_time` int(10) unsigned NOT NULL DEFAULT '0',
  `notify_deliveryer_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_clerk_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `elemeOrderId` varchar(60) NOT NULL,
  `elemeDowngraded` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eleme_store_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `meituanOrderId` varchar(50) NOT NULL DEFAULT '0',
  `meituan_store_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `order_plateform` varchar(20) NOT NULL DEFAULT 'we7_wmall',
  `delivery_takegoods_time` int(10) NOT NULL DEFAULT '0',
  `deliveryinstoretime` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `is_delete` tinyint(3) NOT NULL DEFAULT '0',
  `print_sn` varchar(100) NOT NULL DEFAULT '0',
  `stat_week` tinyint(3) unsigned NOT NULL,
  `meals_cn` varchar(10) NOT NULL,
  `delivery_collect_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `transfer_deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `transfer_delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `print_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `deliverytime` int(10) unsigned NOT NULL DEFAULT '0',
  `is_reserve` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dispatch_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reserve_notify_clerk_starttime` int(10) NOT NULL DEFAULT '0',
  `pindan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `zhunshibao_price` varchar(20) NOT NULL DEFAULT '0',
  `zhunshibao_compensate` varchar(20) NOT NULL DEFAULT '0',
  `zhunshibao_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `storebd_id` int(10) unsigned DEFAULT '0',
  `caigou_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid` (`uniacid`,`sid`),
  KEY `delivery_status` (`delivery_status`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `delivery_type` (`delivery_type`),
  KEY `uid` (`uid`),
  KEY `order_type` (`order_type`),
  KEY `status` (`status`),
  KEY `refund_status` (`refund_status`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `endtime` (`endtime`),
  KEY `pay_type` (`pay_type`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `is_pay` (`is_pay`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `agentid` (`agentid`),
  KEY `clerk_notify_collect_time` (`clerk_notify_collect_time`),
  KEY `handletime` (`handletime`),
  KEY `elemeOrderId` (`elemeOrderId`),
  KEY `order_plateform` (`order_plateform`),
  KEY `elemeDowngraded` (`elemeDowngraded`),
  KEY `spread1` (`spread1`),
  KEY `spread2` (`spread2`),
  KEY `spreadbalance` (`spreadbalance`),
  KEY `meituanOrderId` (`meituanOrderId`),
  KEY `order_channel` (`order_channel`),
  KEY `print_sn` (`print_sn`),
  KEY `print_nums` (`print_nums`),
  KEY `delivery_collect_type` (`delivery_collect_type`),
  KEY `transfer_deliveryer_id` (`delivery_collect_type`),
  KEY `transfer_delivery_status` (`delivery_collect_type`),
  KEY `stat_week` (`stat_week`),
  KEY `meals_cn` (`meals_cn`),
  KEY `is_remind` (`is_remind`),
  KEY `uniacid_printstatus_addtime` (`uniacid`,`print_status`,`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=390001 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `original_price` varchar(10) NOT NULL DEFAULT '0.00',
  `original_data` text NOT NULL,
  `bargain_use_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_buysvip` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pindan_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pindan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `caigou_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `uid` (`uid`),
  KEY `uniacid_sid_uid` (`uniacid`,`sid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1518288 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `goods_quality` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_service` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `thumbs` varchar(3000) NOT NULL,
  `reply` varchar(500) NOT NULL,
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `taste_score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `package_score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deliveryer_tag` varchar(255) NOT NULL,
  `is_share` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `addtime` (`addtime`),
  KEY `agentid` (`agentid`),
  KEY `delivery_service` (`delivery_service`),
  KEY `deliveryer_id` (`deliveryer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14151 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_discount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `note` varchar(50) NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '0.00',
  `store_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=55911 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_grant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `max` int(10) unsigned NOT NULL DEFAULT '0',
  `continuous` int(10) unsigned NOT NULL DEFAULT '0',
  `sum` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `continuous` (`continuous`),
  KEY `sum` (`sum`),
  KEY `updatetime` (`updatetime`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_grant_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `grant` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `credittype` varchar(20) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `mark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `times` (`days`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`)
) ENGINE=InnoDB AUTO_INCREMENT=122728 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_peerpay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `plid` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` int(11) NOT NULL DEFAULT '0',
  `peerpay_type` tinyint(1) NOT NULL DEFAULT '0',
  `peerpay_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_maxprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_realprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_selfpay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_message` varchar(500) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `orderid` (`orderid`),
  KEY `plid` (`plid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=542 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_peerpay_payinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL DEFAULT '',
  `usay` varchar(500) NOT NULL DEFAULT '',
  `final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `headimg` varchar(255) DEFAULT NULL,
  `refundstatus` tinyint(1) NOT NULL DEFAULT '0',
  `refundprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `openid` (`openid`),
  KEY `order_sn` (`order_sn`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=720 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_refund` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `reason` varchar(100) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(60) NOT NULL DEFAULT '0',
  `out_refund_no` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `handle_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(30) NOT NULL,
  `account` varchar(30) NOT NULL,
  `data` text NOT NULL,
  `total_fee` varchar(10) NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'order',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15706 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_refund_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'order',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `refund_id` (`uniacid`,`refund_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33503 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_remind_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `remindid` varchar(50) NOT NULL DEFAULT '0',
  `channel` varchar(15) NOT NULL DEFAULT 'system',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reply` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addtime` (`addtime`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `remindid` (`remindid`),
  KEY `channel` (`channel`)
) ENGINE=MyISAM AUTO_INCREMENT=35372 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_stat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_num` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_title` varchar(50) NOT NULL,
  `goods_unit_price` varchar(10) NOT NULL,
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `print_label` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_discount_num` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_number` varchar(30) NOT NULL,
  `goods_category_title` varchar(20) NOT NULL,
  `goods_original_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `bargain_id` int(10) unsigned NOT NULL DEFAULT '0',
  `total_update_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_plateform` varchar(20) NOT NULL DEFAULT 'we7_wmall',
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_week` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_type` varchar(10) NOT NULL DEFAULT 'normal',
  `data` varchar(1000) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `addtime` (`addtime`),
  KEY `bargain_id` (`bargain_id`),
  KEY `uid` (`uid`),
  KEY `agentid` (`agentid`),
  KEY `oid` (`oid`),
  KEY `order_plateform` (`order_plateform`)
) ENGINE=MyISAM AUTO_INCREMENT=907342 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_order_status_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(30) NOT NULL,
  `role_cn` varchar(50) NOT NULL,
  `hash` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=3411855 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_paybill_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `total_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `no_discount_part` varchar(10) NOT NULL DEFAULT '0.00',
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve` varchar(1000) NOT NULL,
  `store_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(50) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `stat_year` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `agent_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(1000) NOT NULL,
  `agent_final_fee` varchar(10) DEFAULT '0.00',
  `note` varchar(200) NOT NULL,
  `table_sn` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `is_pay` (`is_pay`),
  KEY `pay_type` (`pay_type`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(30) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `out_trade_order_id` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `order_sn` (`order_sn`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=400944 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_perm_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `plugins` text,
  `max_store` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_perm_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `rolename` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `perms` text NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_perm_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `roleid` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `perms` text NOT NULL,
  `realname` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `kefu_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_pintuan_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_pintuan_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `detail` text,
  `price` decimal(10,2) NOT NULL,
  `aloneprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `oldprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peoplenum` int(10) unsigned NOT NULL DEFAULT '0',
  `grouptime` decimal(10,2) NOT NULL DEFAULT '0.00',
  `specstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `specdetail` text,
  `cateid` int(10) unsigned NOT NULL DEFAULT '0',
  `tag` text,
  `total` int(10) NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL DEFAULT '0',
  `falesailed` int(10) unsigned NOT NULL DEFAULT '0',
  `falselooknum` int(10) unsigned NOT NULL DEFAULT '0',
  `falsesharenum` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `buylimit` int(10) unsigned NOT NULL DEFAULT '0',
  `unit` varchar(32) NOT NULL,
  `thumbs` text,
  `share` varchar(2000) NOT NULL,
  `usetype` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `looknum` int(10) unsigned NOT NULL DEFAULT '0',
  `sharenum` int(10) unsigned NOT NULL DEFAULT '0',
  `total_update_type` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`),
  KEY `uniacid_agentid_cateid_status` (`uniacid`,`agentid`,`cateid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL,
  `thumb` varchar(255) DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `ability` varchar(255) NOT NULL,
  `status` int(10) DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugincenter_grant_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `pluginname` varchar(50) NOT NULL,
  `pluginid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned DEFAULT '0',
  `month` int(10) NOT NULL DEFAULT '0',
  `starttime` int(10) NOT NULL DEFAULT '0',
  `endtime` int(10) NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL DEFAULT 'system',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=3621 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugincenter_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL,
  `pluginid` varchar(255) NOT NULL,
  `pluginname` varchar(500) NOT NULL,
  `final_fee` varchar(20) NOT NULL,
  `month` tinyint(10) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`username`),
  KEY `status` (`paytime`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugincenter_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `pluginid` varchar(255) NOT NULL,
  `pluginname` varchar(500) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL,
  `content` varchar(1000) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `data` text,
  `addtime` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugincenter_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `pluginid` int(10) unsigned NOT NULL DEFAULT '0',
  `pluginname` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `sailed` int(10) unsigned NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `plugintype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` text,
  `addtime` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `pluginid` (`pluginid`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plugincenter_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'feie',
  `print_no` varchar(30) NOT NULL,
  `member_code` varchar(50) NOT NULL COMMENT '商户编号',
  `key` varchar(50) NOT NULL,
  `api_key` varchar(100) NOT NULL COMMENT '心联云打印机api_key',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `print_label` varchar(100) NOT NULL,
  `is_print_all` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `qrcode_link` varchar(100) NOT NULL,
  `print_header` varchar(50) NOT NULL,
  `print_footer` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `qrcode_type` varchar(20) NOT NULL DEFAULT 'custom',
  `data` text NOT NULL,
  `language` varchar(20) NOT NULL DEFAULT 'zh-cn',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_printer_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime|516|basic|20.8.0|20200315173845` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_type` (`uniacid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime|516|basic|20.3.0|20191119144058` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `thumbs` varchar(2000) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_reserve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `time` varchar(15) NOT NULL,
  `table_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_seckill_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `price` varchar(10) NOT NULL,
  `oldprice` varchar(10) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `thumbs` varchar(1000) NOT NULL,
  `click` int(10) unsigned NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `content` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `buy_note` text NOT NULL,
  `share` varchar(2000) NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL,
  `use_limit_day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `falsejoinnum` int(10) unsigned NOT NULL DEFAULT '0',
  `falselooknum` int(10) unsigned NOT NULL DEFAULT '0',
  `falsesharenum` int(10) unsigned NOT NULL DEFAULT '0',
  `looknum` int(10) unsigned NOT NULL DEFAULT '0',
  `sharenum` int(10) unsigned DEFAULT '0',
  `total_update_type` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `cid` (`cid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_seckill_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(10) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_seckill_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` varchar(20) NOT NULL,
  `goods_num` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `final_fee` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(10) NOT NULL,
  `data` text NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `refund_out_no` varchar(40) NOT NULL,
  `refund_apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_channel` varchar(30) NOT NULL,
  `refund_account` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_shareredpacket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `share_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_min` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `follow_redpacket_min` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `share` varchar(3000) NOT NULL,
  `agreement` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_shareredpacket_invite_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `share_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `follow_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `share_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `follow_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `activity_id` (`activity_id`),
  KEY `share_uid` (`share_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'homeTop',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wxapp_link` varchar(255) NOT NULL,
  `is_showtime` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `start_time1` varchar(10) NOT NULL,
  `end_time1` varchar(10) NOT NULL,
  `start_time2` varchar(10) NOT NULL,
  `end_time2` varchar(10) NOT NULL,
  `week` varchar(50) NOT NULL,
  `show_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_spread_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `order_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `spreadid` (`spreadid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=473 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_spread_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `channel_from` varchar(10) NOT NULL DEFAULT 'weixin',
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `channel` varchar(20) NOT NULL DEFAULT 'wechat',
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `toaccount_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`),
  KEY `spreadid` (`spreadid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_spread_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL DEFAULT '',
  `commission1` varchar(20) NOT NULL DEFAULT '0',
  `commission2` varchar(20) NOT NULL DEFAULT '0',
  `group_condition` int(10) unsigned NOT NULL DEFAULT '0',
  `commission_type` varchar(10) NOT NULL DEFAULT 'ratio',
  `become_child_limit` tinyint(3) unsigned NOT NULL,
  `valid_period` varchar(10) NOT NULL,
  `admin_update_rules` varchar(10) NOT NULL,
  `takeout_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `paotui_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `gohome_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` varchar(50) NOT NULL,
  `title` varchar(30) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `business_hours` varchar(200) NOT NULL,
  `is_in_business` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `send_price` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delivery_price` varchar(255) NOT NULL DEFAULT '0',
  `delivery_free_price` int(10) unsigned NOT NULL DEFAULT '0',
  `pack_price` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `delivery_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:商家配送,2:到店自提,3:两种都支持',
  `delivery_within_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_reserve_days` tinyint(3) unsigned DEFAULT '0',
  `serve_radius` varchar(30) NOT NULL DEFAULT '0.00',
  `serve_fee` varchar(255) NOT NULL,
  `delivery_area` varchar(50) NOT NULL,
  `thumbs` varchar(1500) NOT NULL,
  `address` varchar(100) NOT NULL,
  `location_x` varchar(15) NOT NULL,
  `location_y` varchar(15) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sns` varchar(255) NOT NULL,
  `notice` varchar(100) NOT NULL COMMENT '公告',
  `tips` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `payment` varchar(255) NOT NULL,
  `invoice_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `token_status` tinyint(3) unsigned DEFAULT '0',
  `remind_time_limit` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `remind_reply` varchar(1500) NOT NULL,
  `comment_reply` varchar(2000) NOT NULL,
  `sailed` int(10) unsigned NOT NULL DEFAULT '0',
  `score` varchar(10) NOT NULL,
  `first_order_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `discount_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `grant_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bargain_price_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reserve_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `collect_coupon_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `grant_coupon_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论审核.1:直接通过',
  `sms_use_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '短信使用条数',
  `wechat_qrcode` varchar(500) NOT NULL,
  `custom_url` varchar(1000) NOT NULL,
  `addtype` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:后台添加,2:申请入驻',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `template` varchar(20) NOT NULL DEFAULT 'index',
  `not_in_serve_radius` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `auto_handel_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `auto_get_address` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `auto_notice_deliveryer` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_assign` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_reserve` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_meal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forward_mode` varchar(15) NOT NULL,
  `assign_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `assign_qrcode` varchar(255) NOT NULL,
  `delivery_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `order_note` varchar(255) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `is_rest` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_stick` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_paybill` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forward_url` varchar(100) NOT NULL,
  `delivery_fee_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_times` varchar(7000) NOT NULL,
  `delivery_areas` text NOT NULL,
  `qualification` varchar(1000) NOT NULL,
  `label` int(10) NOT NULL DEFAULT '0',
  `push_token` varchar(50) NOT NULL,
  `self_audit_comment` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_extra` varchar(255) NOT NULL,
  `elemeShopId` varchar(30) NOT NULL DEFAULT '0',
  `eleme_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `meituanShopId` varchar(30) DEFAULT '0',
  `meituan_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `openplateform_extra` varchar(1000) NOT NULL,
  `position` tinyint(3) NOT NULL DEFAULT '1',
  `deltime` int(10) unsigned NOT NULL DEFAULT '0',
  `remind_time_start` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `consume_per_person` int(10) unsigned NOT NULL DEFAULT '0',
  `waimai_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `haodian_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `haodian_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `haodian_child_id` int(10) unsigned NOT NULL DEFAULT '0',
  `haodian_starttime` int(10) unsigned NOT NULL,
  `haodian_endtime` int(10) unsigned NOT NULL,
  `is_waimai` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_haodian` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `haodian_score` varchar(10) NOT NULL DEFAULT '0',
  `haodian_data` text NOT NULL,
  `rest_can_order` tinyint(3) NOT NULL DEFAULT '0',
  `cate_parentid1` int(10) unsigned NOT NULL,
  `cate_childid1` int(10) unsigned NOT NULL,
  `cate_parentid2` int(10) unsigned NOT NULL,
  `cate_childid2` int(10) unsigned NOT NULL,
  `delivery_areas1` text NOT NULL,
  `auto_print_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `menu` text NOT NULL,
  `kefu_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `storebd_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kabao_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `title` (`title`),
  KEY `is_recommend` (`is_recommend`),
  KEY `cid` (`cid`),
  KEY `status` (`status`),
  KEY `label` (`label`),
  KEY `displayorder` (`displayorder`),
  KEY `is_stick` (`is_stick`),
  KEY `agentid` (`agentid`),
  KEY `elemeShopId` (`elemeShopId`),
  KEY `meituanShopId` (`meituanShopId`),
  KEY `uniacid_id` (`uniacid`,`id`),
  KEY `uniacid_agentid_status_is_waimai` (`uniacid`,`agentid`,`status`,`is_waimai`),
  KEY `uniacid_agentid_status_is_haodian` (`uniacid`,`agentid`,`status`,`is_haodian`)
) ENGINE=MyISAM AUTO_INCREMENT=640 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_rate` varchar(10) NOT NULL DEFAULT '0',
  `fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `wechat` varchar(1000) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_takeout` varchar(500) NOT NULL,
  `fee_selfDelivery` varchar(500) NOT NULL,
  `fee_instore` varchar(500) NOT NULL,
  `fee_paybill` varchar(500) NOT NULL,
  `fee_eleme` varchar(500) NOT NULL,
  `fee_meituan` varchar(500) NOT NULL,
  `fee_goods` varchar(10000) NOT NULL,
  `fee_period` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deposit` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_gohome` varchar(1000) NOT NULL,
  `bank` varchar(1000) NOT NULL,
  `alipay` varchar(1000) NOT NULL,
  `yucunjin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_yucunjin` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=643 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=2638 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `slide_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `slide` varchar(1500) NOT NULL,
  `nav_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `nav` varchar(1500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `wxapp_link` varchar(255) NOT NULL,
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_clerk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `clerk_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(20) NOT NULL,
  `extra` varchar(500) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `kefu_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `clerk_id` (`clerk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=821 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=336114 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_delivery_policy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `policy` text NOT NULL,
  `store` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_delivery_policy_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `policyid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `original` text NOT NULL,
  `changed` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_policyid` (`uniacid`,`policyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_deliveryer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `work_status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid_sid` (`uid`,`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=5795 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(10) NOT NULL DEFAULT 'weixin',
  `toaccount_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=7763 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `success_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_num` int(10) unsigned DEFAULT '0',
  `success_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_num` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_sys` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:系统会员, 2:模块兼容会员',
  `stat_first_day` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_last_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `cancel_first_time` (`cancel_first_time`),
  KEY `cancel_last_time` (`cancel_last_time`),
  KEY `success_first_time` (`success_first_time`),
  KEY `success_last_time` (`success_last_time`),
  KEY `first_order_time` (`success_first_time`),
  KEY `last_order_time` (`success_last_time`)
) ENGINE=MyISAM AUTO_INCREMENT=152097 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_store_yucunjin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `yucunjin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `hash` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_storebd_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `bd_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `channel` varchar(10) NOT NULL DEFAULT 'weixin',
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account` varchar(1000) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_bd_id` (`uniacid`,`bd_id`),
  KEY `uniacid_status` (`uniacid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_storebd_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `bd_id` int(10) NOT NULL DEFAULT '0',
  `fee_takeout` varchar(500) NOT NULL,
  `fee_instore` varchar(500) NOT NULL,
  `addtime` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid_bdid` (`uniacid`,`sid`,`bd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_storebd_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL,
  `nickname` varchar(15) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `openid_wxapp` varchar(60) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(32) NOT NULL,
  `token` varchar(50) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `credit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `openid` (`openid`),
  KEY `openid_wxapp` (`openid_wxapp`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_supercoupon_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `endtime` (`endtime`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_supercoupon_member_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `content` varchar(100) NOT NULL,
  `group_condition` text NOT NULL,
  `is_system` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_superredpacket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `grant_object` longtext NOT NULL,
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `addtime` (`addtime`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_superredpacket_grant` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `packet_dosage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `packet_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_superredpacket_meal_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `meal_id` varchar(20) NOT NULL,
  `order_sn` varchar(50) NOT NULL,
  `final_fee` varchar(20) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'mealRedpacket',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=3666 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_superredpacket_share` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `grant_days_effect` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `times_limit` text NOT NULL,
  `category_limit` text NOT NULL,
  `nums` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL DEFAULT '0',
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `exchangetime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid_code` (`uniacid`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_meal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `oldprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `description` varchar(50) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_meal_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `ordersn` varchar(50) NOT NULL,
  `meal_id` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` varchar(20) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `order_channel` varchar(10) NOT NULL DEFAULT 'wechat',
  `data` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_uid` (`uniacid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_redpacket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL DEFAULT '',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `use_days_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL,
  `can_exchange` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `exchange_cost` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` varchar(200) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`uniacid`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_svip_task_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_type` varchar(30) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `overtime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_uid` (`uniacid`,`task_type`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_system_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `params` varchar(5000) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=5138425 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_table_call_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `table_id` (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前对应的订单id',
  `guest_num` tinyint(3) unsigned DEFAULT '0',
  `scan_num` int(10) unsigned NOT NULL DEFAULT '0',
  `qrcode` varchar(500) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `version` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wxapp_version` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cart_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid` (`uniacid`,`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tables_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `limit_price` varchar(20) NOT NULL,
  `reservation_price` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tables_scan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `table_id` (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_text` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tongcheng_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `tags` varchar(500) NOT NULL DEFAULT '',
  `config` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_agentid` (`uniacid`,`agentid`),
  KEY `uniacid_agentid_status` (`uniacid`,`agentid`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tongcheng_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned DEFAULT '0',
  `content` text NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_tid` (`uniacid`,`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tongcheng_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `nickname` varchar(45) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `childid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `thumbs` text,
  `sid` int(10) NOT NULL,
  `is_stick` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL,
  `looknum` int(10) NOT NULL DEFAULT '0',
  `likenum` int(11) NOT NULL DEFAULT '0',
  `like_uid` varchar(3000) NOT NULL,
  `sharenum` int(11) NOT NULL DEFAULT '0',
  `share_title` varchar(500) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `overtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `edit_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `channel` varchar(20) NOT NULL DEFAULT 'wechat',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=392 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tongcheng_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `stick_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL,
  `days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ordersn` varchar(50) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) NOT NULL DEFAULT '0',
  `order_channel` varchar(15) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `agent_final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(500) NOT NULL,
  `stat_day` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid_type` (`uniacid`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=228 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_tongcheng_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` int(10) unsigned DEFAULT '0',
  `content` text NOT NULL,
  `from_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_nickname` varchar(50) NOT NULL,
  `from_avatar` varchar(255) NOT NULL,
  `to_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `to_nickname` varchar(50) NOT NULL,
  `to_avatar` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_tid_cid` (`uniacid`,`tid`,`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_wheel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `total` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_wheel_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  `award_type` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `handletime` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `award` varchar(1000) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `activity_id` (`activity_id`),
  KEY `stat_day` (`stat_day`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=19476 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_wxapp_formid_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `appid` varchar(50) NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `formid` varchar(150) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime_cn` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `formid` (`uniacid`,`appid`,`openid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_wxapp_live_room` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `roomid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `cover_img` varchar(255) NOT NULL,
  `live_status` int(3) unsigned NOT NULL DEFAULT '103',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `anchor_name` varchar(100) NOT NULL,
  `anchor_img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_wxapp_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `isdefault` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`),
  KEY `isdefault` (`isdefault`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_yinsihao_bind_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  `real_mobile` varchar(15) NOT NULL,
  `secret_mobile` varchar(15) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `subsid` varchar(50) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `expiration` int(10) unsigned NOT NULL,
  `sms_type` varchar(10) NOT NULL DEFAULT 'aliyun',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_real_mobile` (`uniacid`,`real_mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=19073 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmalldeliveryer_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sysset` text NOT NULL,
  `pluginset` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO ims_tiny_wmall_plugin VALUES 
('1','errander','biz','啦啦跑腿','../addons/we7_wmall/static/img/plugin/errander.png','','随意购,帮人取,帮人送，实现足不出户购买一切你想购买，跑腿功能一网打尽','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('2','deliveryCard','biz','配送会员卡','../addons/we7_wmall/static/img/plugin/deliveryCard.png','','配送会员卡','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('3','customerApp','tool','顾客端APP','../addons/we7_wmall/static/img/plugin/customerApp.png','','顾客端APP','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('4','bargain','activity','天天特价','../addons/we7_wmall/static/img/plugin/bargain.png','','天天特价','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('5','shareRedpacket','activity','分享有礼','../addons/we7_wmall/static/img/plugin/shareRedpacket.png','','分享订单，赠送余额或积分！！！！！！','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('6','freeLunch','activity','霸王餐','../addons/we7_wmall/static/img/plugin/freeLunch.png','','霸王餐','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('7','diypage','biz','平台装修','../addons/we7_wmall/static/img/plugin/diypage.png','','可自定义底部菜单，设置订单弹幕，自定义活动页面等','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('8','deliveryerApp','tool','配送员APP','../addons/we7_wmall/static/img/plugin/deliveryerApp.png','','可在手机上接单 顾客可实时查看配送员位置','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('9','ordergrant','activity','下单有礼','../addons/we7_wmall/static/img/plugin/ordergrant.png','','下单给顾客送积分或余额','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('10','creditshop','activity','积分商城','../addons/we7_wmall/static/img/plugin/creditshop.png','','积分兑换好礼活动利器','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('11','test','biz','啦啦外卖','../addons/we7_wmall/static/img/plugin/test.png','','费用支付，测试功能， 不要购买','1','0','0');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('12','qianfanApp','tool','千帆APP整合','../addons/we7_wmall/static/img/plugin/qianfanApp.png','','千帆APP整合','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('13','majiaApp','tool','马甲APP整合','../addons/we7_wmall/static/img/plugin/majiaApp.png','','马甲APP整合','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('14','managerApp','tool','商户APP','../addons/we7_wmall/static/img/plugin/managerApp.png','','商户APP','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('15','superRedpacket','activity','超级红包','../addons/we7_wmall/static/img/plugin/superRedpacket.png','','超级红包可实现主动发送和分享红包功能！！！！！！','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('16','eleme','biz','饿了么平台对接','../addons/we7_wmall/static/img/plugin/eleme.png','','拉取饿了么商户商品信息，对接饿了么订单进行配送','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('17','spread','biz','啦啦推广','../addons/we7_wmall/static/img/plugin/spread.png','','啦啦推广','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('18','meituan','biz','美团平台对接','../addons/we7_wmall/static/img/plugin/meituan.png','','美团平台对接','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('19','wxapp','biz','啦啦外卖小程序','../addons/we7_wmall/static/img/plugin/wxapp.png','','啦啦外卖小程序，小程序将对页面UI就行优化改进，客户体验更好。','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('20','poster','activity','活动海报','../addons/we7_wmall/static/img/plugin/poster.png','','活动海报','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('21','agent','biz','区域代理','../addons/we7_wmall/static/img/plugin/agent.png','','区域代理,平台可发展其他区域进行代理,建议有发展代理能力的平台购买 ! ','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('22','dada','biz','达达开放平台对接','../addons/we7_wmall/static/img/plugin/dada.png','','达达开放平台对接','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('23','lewaimai','biz','乐外卖采集','../addons/we7_wmall/static/img/plugin/lewaimai.png','','可采集乐外卖的商家和商品图片','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('24','advertise','biz','商户广告通','../addons/we7_wmall/static/img/plugin/advertise.png','','商户自行购买为你优选，置顶，幻灯片广告位','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('25','mealRedpacket','activity','套餐红包','../addons/we7_wmall/static/img/plugin/mealRedpacket.png','','套餐红包','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('26','superCoupon','activity','超级代金券','../addons/we7_wmall/static/img/plugin/superCoupon.png','','超级代金券可实现商户针对不同的人群发送代金券','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('27','cloudGoods','biz','云商品库','../addons/we7_wmall/static/img/plugin/cloudGoods.png','','总平台可以维护一个商品库， 商户在添加商品的时候，可以直接从商品库中选择商品','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('28','wheel','activity','大转盘','../addons/we7_wmall/static/img/plugin/wheel.png','','顾客在订单支付后，可以直接跳转到抽奖页面进行抽奖','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('29','storebd','biz','店铺业务员','../addons/we7_wmall/static/img/plugin/storebd.png','','店铺业务员','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('30','wxapp_manager','biz','商户管理小程序','../addons/we7_wmall/static/img/plugin/wxapp_manager.png','','商户管理小程序','1','0','0');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('31','uupaotui','biz','UU跑腿对接','../addons/we7_wmall/static/img/plugin/uupaotui.png','','可将本平台的订单推送到达达，由达达的配送员进行配送','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('32','plateformApp','tool','平台管理APP','../addons/we7_wmall/static/img/plugin/plateformApp.png','','平台管理APP','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('33','gohome','biz','啦啦生活圈','../addons/we7_wmall/static/img/plugin/gohome.png','','啦啦生活圈','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('34','svip','activity','超级会员svip','../addons/we7_wmall/static/img/plugin/svip.png','','超级SVIP','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('35','plugincenter','biz','应用授权管理','../addons/we7_wmall/static/img/plugin/plugincenter.jpg','','应用授权管理，是为啦啦外卖用户提供的，便于向您的客户销售外卖插件授权的功能。它可为您简化插件授权销售流程，操作便捷为您省心。','1','0','0');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('36','area','biz','指定区域','../addons/we7_wmall/static/img/plugin/area.png','','指定区域','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('37','zhunshibao','biz','准时宝','../addons/we7_wmall/static/img/plugin/zhunshibao.png','','准时宝','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('38','huangou','biz','超值换购','../addons/we7_wmall/static/img/plugin/huangou.png','','超值换购','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('39','yinsihao','biz','隐私号','../addons/we7_wmall/static/img/plugin/yinsihao.png','','隐私号','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('40','shansong','biz','闪送','../addons/we7_wmall/static/img/plugin/shansong.png','','闪送','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('41','kefu','biz','客服','../addons/we7_wmall/static/img/plugin/kefu.png','','客服','1','0','1');
INSERT INTO ims_tiny_wmall_plugin VALUES 
('42','iglobal','biz','全球','../addons/we7_wmall/static/img/plugin/iglobal.png','','全球','1','0','1');


sql;
$row = pdo_run($installSql);