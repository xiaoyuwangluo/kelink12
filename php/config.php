<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 全局配置文件 (对应原 ASP.NET web.config appSettings)
 * 版权所有: 小宇网络
 */

// ============ 数据库配置 ============
define('KL_SQL_SERVERIP', '(local)');      // 数据库IP或服务名
define('KL_SQL_UserName', 'sql444001');    // 数据库用户名
define('KL_SQL_PassWord', '7220cac2');     // 数据库密码
define('KL_DatabaseName', 'sql444001');    // 主数据库名
// 历史备份数据库连接串 (对应 KL_BAK_SQLCONN)
define('KL_BAK_SQLCONN', '');
// 数据库类型: mssql | mysql (PHP版默认支持MySQL，可切换SQL Server)
define('KL_DB_TYPE', 'sqlite');

// ============ 站点基础信息 ============
define('KL_SITE_NAME', '小宇网络WAP建站系统');
define('KL_SITE_URL', 'http://localhost');
// 版权信息 (对应 base.CopyRight，数据库优先，此处为默认)
define('KL_COPYRIGHT', 'Copyright (C) 小宇网络 All Rights Reserved.');

// ============ 系统开关 ============
define('KL_VisiteCount_Close', 1);         // 关闭系统自带流量统计 0开1关
define('ISAPI_Rewrite3_Open', 1);          // 伪静态 0关1开
define('KL_search_need_login', 1);         // 全局强制查询需登录
define('KL_CLOSE_LOG_INFO', 0);            // 关闭会员活动日志
define('KL_ShowAllError', 1);              // 显示所有详细错误
define('KL_OpenCache', 0);                 // 缓存开关
define('KL_OpenCacheTime', 1);             // 缓存小时数
define('KL_CheckDomain', 0);               // 校验域名绑定
define('KL_HiddenQuery', 1);               // 域名访问隐藏参数

// ============ 安全过滤 ============
define('KL_In', 'select|unicode|ascii|exec|insert|delete|master|truncate|declare|drop|create|eval|xp_|sp_|command|update');
define('KL_Filter_All', 'display|操蛋|内射');
define('KL_In_Open', 1);                   // 0全局防注入 1局部
define('KL_NotDownAndUpload', 'ad|asp|aspx|asa|asax|ascx|ashx|asmx|php|jsp|exe|mdb|config|shtml');

// ============ 会话与防刷 ============
define('KL_SessionTimeOut', 50);           // session过期(分钟)
define('KL_CheckIPTime', 1);               // 防刷新提交间隔(秒)
define('KL_DelOnlineTime', 60);            // 在线人不活动删除(分钟)
define('KL_LoginTime', 0);                 // 登录失败次数限制
define('KL_CHECKCODE', '');                // 超级管理员伪验证码
define('KL_RegUser_CheckCode', 0);         // 会员注册/登录验证码开关 0关1开
define('KL_WEB_RegUser_CheckCode', 0);     // WEB注册验证码
define('KL_RegSite_CheckCode', 0);         // 站点注册验证码
define('KL_GetIP_FromCDN', 0);             // 是否从CDN获取真实IP
define('KL_WAPAdmin_NeedPassWord', 1);     // 后台需密码
define('KL_Close_IPtoCity', 1);            // 关闭IP转城市
define('KL_ShowPreNextTitle_article', 0);  // 文章上一条下一条是否隐藏标题
define('KL_PAGE_TOP', '');                 // 全局顶部广告
define('KL_PAGE_DOWN', '');

// ============ 模块限制 ============
define('KL_CheckBBSCount', 100);           // 每天发贴数
define('KL_CheckBBSreCount', 200);         // 每天回贴数
define('KL_SendMSGCount', 300);            // 每天站内短信数
define('KL_CheckZoneReCount', 100);        // 每天空间留言数
define('KL_ADDFriendCount', 100);          // 每天加好友数

// ============ 游客发贴/评论开关 (0关1开，部分模块相反) ============
define('KL_BBS_Anonymous_Open', 0);
define('KL_BBSRE_Anonymous_Open', 0);
define('KL_ARTRE_Anonymous_Open', 0);
define('KL_VIDRE_Anonymous_Open', 0);
define('KL_PICRE_Anonymous_Open', 0);
define('KL_RINRE_Anonymous_Open', 0);
define('KL_DOWRE_Anonymous_Open', 0);
define('KL_NOVRE_Anonymous_Open', 0);
define('KL_GUERE_Anonymous_Open', 1);      // 留言相反:0开1关
define('KL_GUE_Anonymous_Open', 1);
define('KL_SHORE_Anonymous_Open', 1);

// ============ UA版本识别 ============
define('KL_GoVersion0', 'Opera|SP|QQ|Mini|series'); // 强制WAP1.0
define('KL_GoVersion1', '');                         // WAP1.0
define('KL_GoVersion4', '');                         // 平板触屏
define('KL_GoVersion3', 'Windows NT|MSIE|Firefox');  // 电脑版
define('KL_GoVersion2', 'Safari|iPhone|ios|Android|Opera|CE|UC|QQ|Phone|Mobile|Mozilla'); // WAP2.0

// ============ 下载防盗链 ============
define('KL_DownCheck', 1);
define('KL_DownCheckReferrer', 0);
define('KL_DownThread', 1);
define('KL_DownSpeed', 102400);

// ============ 邮件 ============
define('KL_SMTP_ADRRESS', '');
define('KL_SMTP_UID', '85403498');
define('KL_SMTP_PW', '12345678');
define('KL_SMTP_EMAIL', '85403498@qq.com');

// ============ 微信 ============
define('KL_Weixin_Reg_Open', 0);

// ============ 版本 ============
define('KL_Version', '12.2018.05.18');

// ============ 授权码 ============
define('KL_License', 'n9FZc/xMKOxg6+2ZLixJjr4mlgGK+7jio8ABuv4KQ2P8HjpJDXeJY49FICIaaOPfbZkSjfDfB7TXIlrJXCeYBrC7e1VoFf2gg7uY78A8F4YNA0WbA5yHvDVEBxaD5YDAGww82SVKzTXna8YhtsShGrblkY/H/FytZ2yCSJKwK/4=d+kcmOBwUpZCFhwGCVrP9ewsbR/ANms3r618FMoyKT1IIroDK5Pin+544MXivK8Q8ndzt6Mv27c63tELkNCo+TclUiY9FAGdp7K8AMJDgjhGWLwQx1DpQkPBYrD3PPx837zNehSwOJU=');

// ============ 路径 ============
define('KL_ROOT', dirname(__FILE__));
define('KL_UPLOAD_DIR', KL_ROOT . '/uploadfiles');
define('KL_CACHE_DIR', KL_ROOT . '/cache');

// 初始化上传/缓存目录
if (!is_dir(KL_UPLOAD_DIR)) @mkdir(KL_UPLOAD_DIR, 0755, true);
if (!is_dir(KL_CACHE_DIR)) @mkdir(KL_CACHE_DIR, 0755, true);

// 错误显示
if (KL_ShowAllError == 1) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 时区
date_default_timezone_set('Asia/Shanghai');
