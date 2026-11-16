# 小宇网络 WAP建站系统 PHP版 - 开发指南

> 由原版 ASP.NET WAP建站系统移植而来，完整复原原版功能架构，版权所有：小宇网络

## 一、项目概述

本系统是原版 ASP.NET WAP建站系统的 PHP 完整移植版，采用 PHP + PDO 架构，支持 MySQL / SQL Server / SQLite 三种数据库。

### 核心特性
- ✅ 27 个功能模块（文章、论坛、商城、小说、视频、图片、下载、铃声、链接、留言、供求、拍卖、约会、挖宝、广播、广告、飞机、相册、酒店、日志、新站、聊天室、家族、游戏、问答、农场、表单）
- ✅ UBB 万能调用解析（[b][i][u][url][img][color][size][font][align][quote][code][list] 等）+ `[功能模块=N_M_X_Y]` 跨模块内容调用
- ✅ 多版本浏览（WAP1.0 / WAP2.0 / 触屏版 / 电脑版）自动识别 UA
- ✅ 会员系统（注册、登录、用户中心、签到、站内短信、好友、收藏、会员空间、账户流水）
- ✅ 管理后台（栏目/会员/内容审核/评论/在线用户/公告/点卡/商城订单管理）
- ✅ 商城交易全链路（购物车、下单、余额支付、点卡充值、订单管理、拍卖出价）
- ✅ 模板系统（templates/default 与 templates/touch 两套样式，站点 template 字段切换）
- ✅ 在线人数统计、流量统计（WapOnline / WapVisite）、文件缓存（kl_cache_get/put）
- ✅ 伪静态 URL 重写（.htaccess 全模块）、sitemap.php、robots.txt
- ✅ 微信回调（weixin.php）、短信接口（sms.php）、QQ登录（oauth/）、短链跳转（t.php）
- ✅ 防盗链下载、限速下载
- ✅ 防 SQL 注入、防刷新提交、每日发贴/回帖/短信/空间留言限制
- ✅ 安装向导（install/）

## 二、目录结构

```
/workspace/php/
├── config.php                  # 全局配置（数据库、开关、常量）
├── index.php                   # 主站营销首页
├── wapindex.php                # WAP站点首页（栏目导航）
├── waplogin.php / wapreg.php   # 登录 / 注册
├── waplogout.php               # 退出登录
├── myfile.php                  # 用户中心
├── search.php                  # 全站搜索
├── version.php                 # 版本信息
├── down.php                    # 下载处理（防盗链）
├── webad.php / webmodel.php / webcallme.php  # 营销页面
├── .htaccess                   # 伪静态规则
│
├── includes/                   # 核心框架
│   ├── bootstrap.php           # 引导文件（所有页面 require 此文件）
│   ├── Database.php            # PDO 数据库抽象层（MySQL/SQLServer/SQLite）
│   ├── WapPage.php             # 基类（所有页面继承，含站点/栏目/版本初始化）
│   ├── WapTool.php             # UBB解析、WML/HTML渲染、头部底部、版本切换
│   └── UserSession.php         # 会话、登录注册、防刷新、IP获取
│
├── article/                    # 文章模块
├── bbs/                        # 论坛模块
├── shop/                       # 商城模块
├── novel/                      # 小说模块（含章节）
├── video/                      # 视频模块
├── picture/                    # 图片模块
├── download/                   # 下载模块
├── ring/                       # 铃声模块
├── link/                       # 链接模块
├── guessbook/                  # 留言板
├── gongqiu/                    # 供求信息
├── paimai/                     # 拍卖
├── yuehui/                     # 约会
├── wabao/                      # 挖宝
├── guangbo/                    # 广播
├── adlink/                     # 广告链接
├── airplane/                   # 飞机
├── album/                      # 相册
├── hotel/                      # 酒店
├── rizhi/                      # 日志
├── xinzhang/                   # 新站
├── chat/                       # 聊天室
├── clan/                       # 家族
├── games/                      # 游戏
├── zhdao/                      # 问答
├── nongchang/                  # 农场
├── form/                       # 表单
├── signin/                     # 每日签到
├── message/                    # 站内短信
├── friend/                     # 好友
├── zone/                       # 会员空间
├── myaccount/                  # 账户流水/点卡充值
├── oauth/                      # QQ登录
├── templates/                  # 模板系统(default/touch两套样式+list.php)
├── page.php                    # 自定义页面(支持万能调用)
├── sitemap.php / robots.txt    # SEO
├── default.php wapgetpw.php                     # 默认入口/找回密码
├── webhelp.php webnews.php                      # 帮助/动态
├── ajax_ubb.php cache.php weixin.php sms.php t.php  # UBB预览/清缓存/微信/短信/短链
│
├── admin/                      # 管理后台
│   ├── login.php / logout.php / index.php       # 登录/退出/首页
│   ├── class.php user.php                       # 栏目/会员管理（状态筛选/搜索/余额积分调整流水/删除确认）
│   ├── content.php reply.php                    # 内容审核/评论管理
│   ├── shoporder.php                           # 商城订单（发货/完成/取消/退款）
│   ├── goods.php                               # 商品管理（新增/编辑/删除/预警/改价改库存/上下架）
│   ├── export.php                              # 运营数据导出（订单/商品 CSV）
│   └── online.php sysmsg.php card.php           # 在线/公告/点卡
│
├── install/                    # 安装向导
│   ├── index.php               # 安装页面
│   └── schema.sql              # MySQL 建表脚本
│
├── assets/css/index.css        # 样式文件
├── data/                       # SQLite 数据目录（自动创建）
├── uploadfiles/                # 上传文件目录（自动创建）
└── cache/                      # 缓存目录（自动创建）
```

## 三、快速开始

### 1. 使用 SQLite（无需安装数据库，开箱即用）

```bash
cd /workspace/php
# config.php 中 KL_DB_TYPE 已设为 sqlite，直接启动
php -S 127.0.0.1:8080
# 访问 http://127.0.0.1:8080/wapindex.php
```

### 2. 使用 MySQL

1. 修改 `config.php`：`define('KL_DB_TYPE', 'mysql');` 并填写数据库连接信息
2. 执行 `install/schema.sql` 建表
3. 或访问 `install/index.php` 使用安装向导

### 3. 默认账号

- 管理员：`admin` / `admin123`
- 独立建站版仅维护一个站点，无需站点 ID

## 四、模块开发规范

### 新增模块步骤（以 "mymod" 为例）

1. **创建目录**：`/workspace/php/mymod/`
2. **创建数据表**：`WapMymod`（参考 `WapArticle` 结构）
3. **创建页面**（每个模块标准 4 个页面）：
   - `list.php` — 列表页（继承 WapPage，调用 `$db->getPage()`）
   - `view.php` — 详情页
   - `book_re.php` — 评论页（使用通用 `WapReply` 表，module='mymod'）
   - `admin_WAPadd.php` — 添加页（需登录）

4. **在 wapindex.php 中注册模块**：栏目表 `WapClass.module` 设为 `mymod`

### 页面标准模板

```php
<?php
require_once __DIR__ . '/../includes/bootstrap.php';

class MymodListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = $this->classVo->classname ?? '模块名';
        $this->showTop($this->strTitle);

        $result = $db->getPage('WapMymod', 'id,title,addtime,hits',
            "classid=? AND isshow=1", 'id DESC',
            max(1,(int)($_GET['page']??1)), 20,
            [$this->classid]);

        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            echo '<li><a href="view.php?classid='.$this->classid.'&id='.$row['id'].'">'.$this->ToHtm($row['title']).'</a></li>';
        }
        echo '</ul></div>';
        $this->showDown();
    }
}
$page = new MymodListPage();
$page->run();
```

## 五、核心 API 速查

### WapPage 基类（所有页面可用）
| 属性/方法 | 说明 |
|---|---|
| `$this->classid` | 栏目ID |
| `$this->ver` | 版本（1=WAP1.0, 2=WAP2.0, 3=PC, 4=触屏） |
| `$this->siteVo` / `$this->classVo` | 站点/栏目信息对象 |
| `$this->wmlVo` | 版本信息对象 |
| `$this->userVo` | 当前登录用户 |
| `$this->strTitle` | 页面标题 |
| `$this->CopyRight` | 版权信息 |
| `$this->http_start` | URL前缀 |
| `$this->GetLang("WAP1\|WAP2\|PC")` | 多版本文本 |
| `$this->ToHtm($str)` | HTML编码 |
| `$this->showTop($title)` | 输出头部 |
| `$this->showDown()` | 输出底部（含版权） |
| `$this->checkLogin()` | 检查登录，未登录跳转 |
| `$this->showError($msg)` / `showSuccess($msg)` | 提示页 |

### Database 数据库类
| 方法 | 说明 |
|---|---|
| `getOne($sql, $params)` | 获取单行 |
| `getAll($sql, $params)` | 获取多行 |
| `getScalar($sql, $params)` | 获取单个值 |
| `execute($sql, $params)` | 执行写操作，返回影响行数 |
| `insert($sql, $params)` | 插入并返回自增ID |
| `getPage($table, $fields, $where, $order, $page, $pageSize, $params)` | 分页查询 |

### WapTool 工具类
| 方法 | 说明 |
|---|---|
| `ToWML($str, $wmlVo)` | UBB → HTML/WML 转换 |
| `showTop($title)` / `showDown()` | 头部/底部 |
| `GetVS($wmlVo)` | 版本切换链接 |
| `checkSqlInject($str)` | 防注入检查 |

## 六、数据库表映射

| 数据表 | 对应模块 | 说明 |
|---|---|---|
| WapSite | - | 站点信息 |
| WapClass | - | 栏目（含 module 字段指定模块） |
| WapUser | - | 会员 |
| WapAdmin | - | 管理员 |
| WapArticle | article | 文章 |
| WapBbs / WapBbsReply | bbs | 论坛帖子/回复 |
| WapShop / WapShopOrder | shop | 商品/订单 |
| WapNovel / WapNovelChapter | novel | 小说/章节 |
| WapVideo | video | 视频 |
| WapReply | 通用 | 各模块评论（module 字段区分） |
| Wap{Picture,Download,Ring,Link,...} | 对应模块 | 内容型模块通用表结构 |

## 七、与 ASP.NET 原版对应关系

| ASP.NET | PHP | 说明 |
|---|---|---|
| `web.config appSettings` | `config.php` | 配置常量 |
| `*.aspx` + DLL 代码后置 | `*.php`（逻辑直接写在文件中） | 页面逻辑 |
| `KeLin_WebSite.dll` 基类 | `includes/WapPage.php` | 页面基类 |
| `WapTool.showTop/showDown/ToWML` | `includes/WapTool.php` | 工具类 |
| `WwwClassManager.dll` 等 | `includes/Database.php` | 数据库操作 |
| `WapUser` 表会话 | `includes/UserSession.php` | 用户会话 |
| `web.config rewrite` | `.htaccess` | 伪静态 |
| `.mdf / SQL Server` | MySQL / SQL Server / SQLite | 数据库 |

## 八、后续开发路线图

### 已完成（核心功能）
- [x] 核心框架（配置/数据库/Session/基类/UBB解析）
- [x] 21 个内容模块的 list/view/book_re/admin_WAPadd 页面
- [x] 6 个新模块：聊天室/家族/游戏/问答/农场/表单
- [x] 会员注册登录、用户中心、签到、站内短信、好友、收藏、会员空间、账户流水
- [x] 商城交易链路：购物车/下单/余额支付/点卡充值/订单/拍卖出价
- [x] 管理后台 9 个页面（站点/栏目/会员/内容审核/评论/在线/公告/点卡）
- [x] 全站搜索、防盗链下载、安装向导、SQLite 支持（开箱即用）
- [x] UBB 万能调用 `[功能模块=N_M_X_Y]`
- [x] 在线人数统计（WapOnline）+ 流量统计（WapVisite，KL_VisiteCount_Close 开关）
- [x] 模板系统（templates/default、templates/touch）
- [x] 缓存系统（kl_cache_get/kl_cache_put，KL_OpenCache 开关）
- [x] SEO：sitemap.php、robots.txt、全模块伪静态
- [x] 顶层页面：建站注册/找回密码/帮助/动态/排行/ajax_ubb/清缓存/微信/短信/QQ登录/短链/自定义页

### v2 已完成
- [x] 文件上传管理（includes/Upload.php、admin/upload.php 文件管理器、文章附件/论坛图帖）
- [x] 栏目级权限（WapUser.usergroup + WapClass.postgroup/managegroup，基类自动守卫投稿/评论页）
- [x] 支付宝(RSA2)/微信(APIv3) 支付正式接口（includes/PayGateway.php + notify 幂等回调，凭据填入 config.php 即启用）
- [x] 微信公众平台对接（access_token 缓存/菜单/关注自动注册会员，WapWeixin 填凭据即启用）
- [x] QQ 登录正式流程（oauth/，KL_QQ_Login_Open=1 即走真实互联，支持账号绑定）
- [x] WAP1.0 WML 完整渲染（合法 XML/DOCTYPE/实体转义/标签降级，UA 自动识别）

### 待完善
1. 正式商户/开放平台凭据申请与线上联调（代码已就绪）
2. 高并发场景下 SQLite 建议切换 MySQL

### 新增模块方法
参考第四章"模块开发规范"，每个模块只需 4 个标准页面 + 1 张数据表，框架已提供所有基础设施。

## 九、版权声明

Copyright (C) 小宇网络 All Rights Reserved.
本系统基于柯林WAP建站系统架构移植，版权归小宇网络所有。
