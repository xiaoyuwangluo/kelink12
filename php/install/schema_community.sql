-- =============================================
-- 小宇网络 WAP建站系统 PHP版 社区扩展建表脚本 (MySQL)
-- 积分商城 + 自定义敏感词库 + 审核记录，可重复执行
-- 版权所有: 小宇网络
-- =============================================

CREATE TABLE IF NOT EXISTS `WapPointGoods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `intro` varchar(255) DEFAULT '',
  `points` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT '',
  `isshow` tinyint(1) NOT NULL DEFAULT 1,
  `sortnum` int(11) NOT NULL DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pgoods_site` (`isshow`,`sortnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分商品表';

CREATE TABLE IF NOT EXISTS `WapPointOrder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `goodsid` int(11) NOT NULL DEFAULT 0,
  `goodsname` varchar(100) NOT NULL DEFAULT '',
  `points` int(11) NOT NULL DEFAULT 0,
  `num` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待发放 1已发放 2已取消',
  `note` varchar(255) DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_porder_user` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分兑换订单表';

CREATE TABLE IF NOT EXISTS `WapPointLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '正为获得 负为消耗',
  `balance` int(11) NOT NULL DEFAULT 0,
  `remark` varchar(255) DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_plog_user` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分流水表';

CREATE TABLE IF NOT EXISTS `WapFilter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_filter` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自定义敏感词表';

CREATE TABLE IF NOT EXISTS `WapAudit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `module` varchar(50) DEFAULT '',
  `contentid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT '',
  `content` text,
  `word` varchar(50) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待审核 1通过 2删除',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_site` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词审核记录表';

-- v16 审核操作日志
CREATE TABLE IF NOT EXISTS `WapAuditLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auditid` int(11) NOT NULL DEFAULT 0,
  `adminid` int(11) NOT NULL DEFAULT 0,
  `act` varchar(10) NOT NULL DEFAULT '',
  `reason` varchar(255) DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_auditlog` (`auditid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='审核操作日志表';

-- v7 社区基础能力: 关注 / 点赞 / 通知
CREATE TABLE IF NOT EXISTS `WapFollow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0 COMMENT '关注者',
  `followuid` int(11) NOT NULL DEFAULT 0 COMMENT '被关注者',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_follow` (`userid`,`followuid`),
  KEY `idx_followuid` (`followuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关注关系表';

CREATE TABLE IF NOT EXISTS `WapLike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) DEFAULT 'bbs',
  `contentid` int(11) NOT NULL DEFAULT 0,
  `replyid` int(11) NOT NULL DEFAULT 0 COMMENT '0=主帖',
  `userid` int(11) NOT NULL DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_like` (`module`,`contentid`,`replyid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='点赞表';

CREATE TABLE IF NOT EXISTS `WapNotice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '接收者',
  `fromuid` int(11) DEFAULT 0,
  `ntype` varchar(20) DEFAULT '',
  `module` varchar(20) DEFAULT '',
  `contentid` int(11) NOT NULL DEFAULT 0,
  `content` varchar(255) DEFAULT '',
  `isread` tinyint(1) NOT NULL DEFAULT 0,
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`,`isread`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知表';

CREATE TABLE IF NOT EXISTS `WapBbsTag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bbsid` int(11) NOT NULL DEFAULT 0,
  `tagname` varchar(30) NOT NULL DEFAULT '',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bbstag` (`bbsid`,`tagname`),
  KEY `idx_tag` (`tagname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帖子标签表';

CREATE TABLE IF NOT EXISTS `WapBlock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0 COMMENT '拉黑发起者',
  `blockuid` int(11) NOT NULL DEFAULT 0 COMMENT '被拉黑者',
  `addtime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_block` (`userid`,`blockuid`),
  KEY `idx_blockuid` (`blockuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户拉黑表';
