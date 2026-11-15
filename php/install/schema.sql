-- =============================================
-- 小宇网络 WAP建站系统 PHP版 数据库建表脚本 (MySQL)
-- 对应原 ASP.NET 版本的核心数据表
-- 版权所有: 小宇网络
-- =============================================

CREATE TABLE IF NOT EXISTS `WapSite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(100) NOT NULL DEFAULT '',
  `copyright` varchar(255) DEFAULT 'Copyright (C) 小宇网络 All Rights Reserved.',
  `siteinfo` text,
  `logourl` varchar(255) DEFAULT '',
  `domain` varchar(100) DEFAULT '',
  `template` varchar(50) DEFAULT 'default',
  `isclose` tinyint(1) DEFAULT 0,
  `closetip` varchar(255) DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点表';

CREATE TABLE IF NOT EXISTS `WapClass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `parentid` int(11) DEFAULT 0,
  `childid` int(11) DEFAULT 0,
  `classname` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT 'article',
  `classpic` varchar(255) DEFAULT '',
  `orderid` int(11) DEFAULT 0,
  `isshow` tinyint(1) DEFAULT 1,
  `config` text,
  PRIMARY KEY (`id`),
  KEY `idx_site` (`siteid`,`parentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='栏目表';

CREATE TABLE IF NOT EXISTS `WapUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nickname` varchar(50) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `money` int(11) DEFAULT 0,
  `point` int(11) DEFAULT 0,
  `islock` tinyint(1) DEFAULT 0,
  `regtime` datetime DEFAULT CURRENT_TIMESTAMP,
  `regip` varchar(50) DEFAULT '',
  `lastlogintime` datetime DEFAULT NULL,
  `lastloginip` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员表';

CREATE TABLE IF NOT EXISTS `WapAdmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- 文章模块
CREATE TABLE IF NOT EXISTS `WapArticle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `author` varchar(50) DEFAULT '',
  `content` text,
  `picurl` varchar(255) DEFAULT '',
  `downurl` varchar(255) DEFAULT '',
  `istop` tinyint(1) DEFAULT 0,
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`),
  KEY `idx_site` (`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表';

-- 论坛模块
CREATE TABLE IF NOT EXISTS `WapBbs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `nickname` varchar(50) DEFAULT '',
  `title` varchar(255) NOT NULL,
  `content` text,
  `istop` tinyint(1) DEFAULT 0,
  `isgood` tinyint(1) DEFAULT 0,
  `islock` tinyint(1) DEFAULT 0,
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `replynum` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='论坛帖子表';

CREATE TABLE IF NOT EXISTS `WapBbsReply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bbsid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `nickname` varchar(50) DEFAULT '',
  `content` text,
  `isshow` tinyint(1) DEFAULT 1,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bbs` (`bbsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='论坛回复表';

-- 商城模块
CREATE TABLE IF NOT EXISTS `WapShop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` text,
  `picurl` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城商品表';

CREATE TABLE IF NOT EXISTS `WapShopOrder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL,
  `shopid` int(11) NOT NULL,
  `num` int(11) DEFAULT 1,
  `totalprice` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 0,
  `address` varchar(255) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城订单表';

-- 小说模块
CREATE TABLE IF NOT EXISTS `WapNovel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `author` varchar(50) DEFAULT '',
  `intro` text,
  `picurl` varchar(255) DEFAULT '',
  `size` varchar(20) DEFAULT '',
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小说表';

CREATE TABLE IF NOT EXISTS `WapNovelChapter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novelid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_novel` (`novelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小说章节表';

-- 视频模块
CREATE TABLE IF NOT EXISTS `WapVideo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` text,
  `picurl` varchar(255) DEFAULT '',
  `videourl` varchar(255) DEFAULT '',
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='视频表';

-- 通用评论表
CREATE TABLE IF NOT EXISTS `WapReply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `contentid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `nickname` varchar(50) DEFAULT '',
  `content` text,
  `isshow` tinyint(1) DEFAULT 1,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_module` (`module`,`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通用评论表';

-- 其他内容模块通用表
CREATE TABLE IF NOT EXISTS `WapPicture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT 0,
  `classid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` text,
  `picurl` varchar(255) DEFAULT '',
  `downurl` varchar(255) DEFAULT '',
  `istop` tinyint(1) DEFAULT 0,
  `isshow` tinyint(1) DEFAULT 1,
  `hits` int(11) DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`classid`,`siteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图片表';

CREATE TABLE IF NOT EXISTS `WapDownload` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapRing` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapLink` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapGuestbook` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapGongqiu` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapPaimai` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapYuehui` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapWabao` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapGuangbo` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapAdlink` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapAirplane` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapAlbum` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapHotel` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapRizhi` (LIKE WapPicture);
CREATE TABLE IF NOT EXISTS `WapXinzhang` (LIKE WapPicture);

-- 在线用户表
CREATE TABLE IF NOT EXISTS `WapOnline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) DEFAULT 0,
  `userid` int(11) DEFAULT 0,
  `ip` varchar(50) DEFAULT '',
  `lasttime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_site` (`siteid`,`lasttime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在线用户表';

-- 默认管理员 (密码: admin123, md5)
INSERT INTO `WapAdmin` (`username`,`password`,`role`) VALUES ('admin','0192023a7bbd73250516f069df18b500','super')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- 默认站点
INSERT INTO `WapSite` (`id`,`sitename`,`copyright`) VALUES (1,'小宇网络默认站点','Copyright (C) 小宇网络 All Rights Reserved.')
ON DUPLICATE KEY UPDATE `sitename`=`sitename`;
