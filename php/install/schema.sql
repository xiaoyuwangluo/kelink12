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
CREATE TABLE IF NOT EXISTS `WapDownload` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='下载表';
CREATE TABLE IF NOT EXISTS `WapRing` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='铃声表';
CREATE TABLE IF NOT EXISTS `WapLink` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='链接表';
CREATE TABLE IF NOT EXISTS `WapGuestbook` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='留言表';
CREATE TABLE IF NOT EXISTS `WapGongqiu` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='供求表';
CREATE TABLE IF NOT EXISTS `WapPaimai` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='拍卖表';
CREATE TABLE IF NOT EXISTS `WapYuehui` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='约会表';
CREATE TABLE IF NOT EXISTS `WapWabao` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='挖宝表';
CREATE TABLE IF NOT EXISTS `WapGuangbo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广播表';
CREATE TABLE IF NOT EXISTS `WapAdlink` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告表';
CREATE TABLE IF NOT EXISTS `WapAirplane` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='飞机表';
CREATE TABLE IF NOT EXISTS `WapAlbum` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='相册表';
CREATE TABLE IF NOT EXISTS `WapHotel` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='酒店表';
CREATE TABLE IF NOT EXISTS `WapRizhi` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日志表';
CREATE TABLE IF NOT EXISTS `WapXinzhang` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='新站表';

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
-- ===== 补充数据表 (全模块移植新增) =====
CREATE TABLE WapOnline (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, username VARCHAR(255) NOT NULL DEFAULT '', ip VARCHAR(255) NOT NULL DEFAULT '', page VARCHAR(255) NOT NULL DEFAULT '', lasttime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapSignin (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, signdate TEXT, days INT DEFAULT 1, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapMessage (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, fromid INT DEFAULT 0, fromname VARCHAR(255) NOT NULL DEFAULT '', toid INT DEFAULT 0, toname VARCHAR(255) NOT NULL DEFAULT '', title VARCHAR(255) NOT NULL DEFAULT '', content TEXT, isread INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapSysMsg (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, title VARCHAR(255) NOT NULL DEFAULT '', content TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapFriend (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, friendid INT DEFAULT 0, status INT DEFAULT 1, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapFav (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, module VARCHAR(255) NOT NULL DEFAULT '', contentid INT DEFAULT 0, title VARCHAR(255) NOT NULL DEFAULT '', addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapVote (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, bbsid INT DEFAULT 0, question VARCHAR(255) NOT NULL DEFAULT '', total INT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapVoteOption (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, voteid INT DEFAULT 0, optionname VARCHAR(255) NOT NULL DEFAULT '', votes INT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapVoteLog (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, voteid INT DEFAULT 0, userid INT DEFAULT 0, optionid INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapBbsFile (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, bbsid INT DEFAULT 0, filename VARCHAR(255) NOT NULL DEFAULT '', filepath VARCHAR(255) NOT NULL DEFAULT '', filesize INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapChatSpeak (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', content TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapClan (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, name VARCHAR(255) NOT NULL DEFAULT '', intro TEXT, members INT DEFAULT 1, point INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapClanMember (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, clanid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', role INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapClanChat (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, clanid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', content TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapGames (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, name VARCHAR(255) NOT NULL DEFAULT '', picurl VARCHAR(255) NOT NULL DEFAULT '', url VARCHAR(255) NOT NULL DEFAULT '', intro TEXT, clicks INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapGamesScore (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, gameid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', score INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapCard (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, cardno VARCHAR(255) NOT NULL DEFAULT '', cardpwd VARCHAR(255) NOT NULL DEFAULT '', cardtype VARCHAR(255) NOT NULL DEFAULT '', money INT DEFAULT 0, isused INT DEFAULT 0, userid INT DEFAULT 0, usetime TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapAccountLog (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, type TEXT DEFAULT 'in', num INT DEFAULT 0, balance INT DEFAULT 0, remark VARCHAR(255) NOT NULL DEFAULT '', addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapShopCart (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, shopid INT DEFAULT 0, title VARCHAR(255) NOT NULL DEFAULT '', price INT DEFAULT 0, num INT DEFAULT 1, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapVisite (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, visitdate TEXT, pv INT DEFAULT 0, uv INT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapVisiteLog (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, ip VARCHAR(255) NOT NULL DEFAULT '', url VARCHAR(255) NOT NULL DEFAULT '', ua VARCHAR(255) NOT NULL DEFAULT '', addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapArticleFile (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, classid INT DEFAULT 0, articleid INT DEFAULT 0, filename VARCHAR(255) NOT NULL DEFAULT '', filepath VARCHAR(255) NOT NULL DEFAULT '', filesize INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapForm (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, classid INT DEFAULT 0, name VARCHAR(255) NOT NULL DEFAULT '', fields TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapFormData (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, formid INT DEFAULT 0, userid INT DEFAULT 0, data TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapPageTpl (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, name VARCHAR(255) NOT NULL DEFAULT '', content TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapDomain (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, domain VARCHAR(255) NOT NULL DEFAULT '', status INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapTemplate (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL DEFAULT '', dir VARCHAR(255) NOT NULL DEFAULT '', isdefault INT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapWeixin (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, appid VARCHAR(255) NOT NULL DEFAULT '', secret VARCHAR(255) NOT NULL DEFAULT '', token VARCHAR(255) NOT NULL DEFAULT '', isopen INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapWeixinMsg (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, fromuser VARCHAR(255) NOT NULL DEFAULT '', content TEXT, reply TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapSms (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, mobile VARCHAR(255) NOT NULL DEFAULT '', content TEXT, status INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapCaiji (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, name VARCHAR(255) NOT NULL DEFAULT '', url VARCHAR(255) NOT NULL DEFAULT '', classid INT DEFAULT 0, lastrun TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapNongChang (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, level INT DEFAULT 1, exp INT DEFAULT 0, gold INT DEFAULT 100, plots TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapZhiDao (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, classid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', title VARCHAR(255) NOT NULL DEFAULT '', content TEXT, status INT DEFAULT 0, hits INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapZhiDaoReply (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, zhidoid INT DEFAULT 0, siteid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', content TEXT, isbest INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapUserLog (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, action VARCHAR(255) NOT NULL DEFAULT '', content VARCHAR(255) NOT NULL DEFAULT '', ip VARCHAR(255) NOT NULL DEFAULT '', addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapZone (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, userid INT DEFAULT 0, zonename VARCHAR(255) NOT NULL DEFAULT '', intro TEXT, pic VARCHAR(255) NOT NULL DEFAULT '', addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapZoneMsg (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, zoneid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', content TEXT, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE WapPaimaiBid (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, siteid INT DEFAULT 0, paimaiid INT DEFAULT 0, userid INT DEFAULT 0, nickname VARCHAR(255) NOT NULL DEFAULT '', price INT DEFAULT 0, addtime TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE WapOnline ADD INDEX idx_online_site (siteid,lasttime);
ALTER TABLE WapMessage ADD INDEX idx_msg_to (toid,isread);
ALTER TABLE WapFriend ADD INDEX idx_friend_user (userid,friendid);
ALTER TABLE WapShopCart ADD INDEX idx_cart_user (userid,siteid);
ALTER TABLE WapSignin ADD INDEX idx_signin_user (userid,signdate);
ALTER TABLE WapZhiDaoReply ADD INDEX idx_zdreply (zhidoid);

-- WapUser 扩展列
ALTER TABLE `WapUser` ADD COLUMN `avatar` VARCHAR(255) DEFAULT '';
ALTER TABLE `WapUser` ADD COLUMN `gender` TINYINT DEFAULT 0;
ALTER TABLE `WapUser` ADD COLUMN `signature` VARCHAR(255) DEFAULT '';

-- v2 权限/网关字段
ALTER TABLE WapUser ADD COLUMN usergroup INT DEFAULT 1;
ALTER TABLE WapClass ADD COLUMN postgroup VARCHAR(100) DEFAULT '';
ALTER TABLE WapClass ADD COLUMN managegroup VARCHAR(100) DEFAULT '';

-- 微信/QQ 登录 openid 字段
ALTER TABLE WapUser ADD COLUMN wx_openid VARCHAR(64) DEFAULT '';
ALTER TABLE WapUser ADD COLUMN qq_openid VARCHAR(64) DEFAULT '';
