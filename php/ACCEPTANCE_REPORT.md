# 小宇网络 WAP建站系统 PHP版 — 复刻完成度验收报告

验收日期：2026-11-16　　验收方式：自动化全量 URL 矩阵测试 + 功能链路实测 + 双数据库引擎验证 + 安全/压测/兼容性核查
验收结论：**通过（独立建站版 SQLite 377/377；全部 PHP 文件 php -l 零错误；SQLite 新库建表零错误）**
交付包：`/workspace/xiaoyu-wap-php-v1.0.zip`

## 独立建站版改造（当前交付）

| 项 | 结果 |
|---|---|
| 站群功能移除 | 删除 `admin/site.php`（站点管理）、`wapregsite.php`（建站申请）、`webreg.php`（注册站长）、`webrank.php`（站点PV排行）及相关导航/统计入口 |
| 数据模型 | 全库移除 `siteid` 列与含 `siteid` 的索引/唯一键；`WapSite` 保留为单行站点配置表（id=1） |
| URL / SQL | 全站移除 `siteid` 查询参数、SQL 条件、INSERT 列与绑定参数；`WapPage` / `WapTool` / `Community` 全部单站化 |
| 在线与流量统计 | `WapOnline` / `WapVisite` / `WapVisiteLog` 去掉 siteid，按全站统计 |
| 验收 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) **377/377 PASS**（移除 4 个已删除页面用例，新增商城订单、商品库存、数据导出、会员/栏目/内容/评论/充值卡/在线/公告/举报/文件管理/系统设置/采集管理/数据库维护/订单筛选/钱包收支筛选/商品详情/商品列表/购物车/结算校验/订单详情/订单搜索排序/直购校验/积分兑换记录/积分商品筛选/站内消息筛选与搜索/好友搜索/UI 结构断言与后台页面迁移用例）；SQLite 新库 `schema.sql` + `schema_community.sql` 共 80+12 表导入零错误 |
| 交付数据库 | `data/sql444001.db` 为全新空库：仅 `admin/admin123` 与默认站点配置，无测试会员、无内容数据 |

## 〇〇〇〇〇〇〇、v61 后台页面迁移第十三步（采集管理接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/caiji.php](file:///workspace/php/admin/caiji.php) 列表与删除采集源确认改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“采集管理” | 列表、搜索、删除确认均 1 个 DOCTYPE、`admin-shell`、导航 `is-active` |
| 功能不变 | 采集源搜索、新增校验（名称/地址协议/栏目模块）、删除确认、批量导入文章均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **377 项**（新增采集列表与删除确认外壳断言） | **377/377 PASS** |

## 〇〇〇〇〇〇〇、v60 后台页面迁移第十二步（文件管理与数据库维护接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/upload.php](file:///workspace/php/admin/upload.php)（列表/删除确认）、[admin/backup.php](file:///workspace/php/admin/backup.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“文件管理”“数据库维护” | 列表、删除确认、维护页与检查结果均 1 个 DOCTYPE、`admin-shell`、导航 `is-active` |
| 附件目录 | 上传目录探测文件仅用于确认删除确认页渲染，验证后立即清理 | `act=del&file=_ui_probe.txt` 输出“删除文件确认”，目录恢复为空 |
| 功能不变 | 文件上传、搜索、类型筛选、删除；备份下载、完整性检查、VACUUM 优化均未改 | 备份下载仍返回 `application/octet-stream`；相关用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **375 项**（新增两页外壳断言） | **375/375 PASS** |

## 〇〇〇〇〇〇〇、v59 后台页面迁移第十一步（充值卡与举报管理接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/card.php](file:///workspace/php/admin/card.php)（列表/作废确认）、[admin/report.php](file:///workspace/php/admin/report.php)（列表/删除确认）改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“充值卡”“举报管理” | 六个入口均 1 个 DOCTYPE、`admin-shell`、导航 `is-active` |
| 功能不变 | 批量生成、搜索、使用状态筛选、作废；举报搜索、处理状态筛选、标记处理、删除确认均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **373 项**（新增两页外壳断言） | **373/373 PASS** |

## 〇〇〇〇〇〇〇、v58 后台页面迁移第十步（积分商品与兑换订单接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/pointgoods.php](file:///workspace/php/admin/pointgoods.php)（列表/新增编辑/删除确认三分支）、[admin/pointorder.php](file:///workspace/php/admin/pointorder.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“积分商品”“兑换订单” | 列表、新增/编辑、删除确认、兑换订单筛选均 1 个 DOCTYPE、`admin-shell`、导航 `is-active` |
| 功能不变 | 积分商品上下架切换、保存校验、删除；兑换订单状态筛选、发放、取消退积分均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **371 项**（新增两页外壳断言） | **371/371 PASS** |

## 〇〇〇〇〇〇〇、v57 后台页面迁移第九步（社区报表与系统公告接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/community.php](file:///workspace/php/admin/community.php)、[admin/sysmsg.php](file:///workspace/php/admin/sysmsg.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“社区报表”“系统公告” | 报表近7/30天、公告列表与删除确认均 1 个 DOCTYPE、`admin-shell`、导航高亮正确 |
| 表格样式 | 报表两张统计表在统一外壳内按 [admin.css](file:///workspace/php/assets/css/admin.css) 表格样式渲染 | 概览与每日趋势表单元格对齐、表头区分 |
| 功能不变 | 时间范围切换、发帖/标签 Top10、公告新增编辑、搜索、分页、删除确认均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **369 项**（新增两页外壳断言） | **369/369 PASS** |

## 〇〇〇〇〇〇〇、v56 后台页面迁移第八步（敏感词库与在线用户接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/filter.php](file:///workspace/php/admin/filter.php)、[admin/online.php](file:///workspace/php/admin/online.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“敏感词库”“在线用户” | 敏感词页、在线列表/筛选、踢下线确认均 1 个 DOCTYPE、`admin-shell`、导航 `is-active` |
| 功能不变 | 敏感词批量添加与删除；在线类型筛选、搜索、过期清理、分页、踢下线确认均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **367 项**（新增两页外壳断言） | **367/367 PASS** |

## 〇〇〇〇〇〇〇、v55 后台页面迁移第七步（内容审核与标签管理接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/audit.php](file:///workspace/php/admin/audit.php)、[admin/tag.php](file:///workspace/php/admin/tag.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“内容审核”“标签管理” | 审核各状态页、标签列表与搜索页均 1 个 DOCTYPE、输出 `admin-shell` |
| 空列表分支 | 审核无记录时提前返回也保证外壳闭合 | `status=rejected` 输出 `</html>` 完整 |
| 功能不变 | 审核单条/批量通过、拒绝理由、状态页签与分页；标签搜索、改名合并、删除确认均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **365 项**（新增审核与标签外壳断言） | **365/365 PASS** |

## 〇〇〇〇〇〇〇、v54 后台页面迁移第六步（栏目管理与系统设置接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/class.php](file:///workspace/php/admin/class.php)、[admin/setting.php](file:///workspace/php/admin/setting.php) 改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏分别高亮“栏目管理”“系统设置” | 栏目列表/删除确认/设置页均 1 个 DOCTYPE、输出 `admin-shell`、导航 `is-active` 正确 |
| 功能不变 | 栏目模块筛选、搜索、新增编辑、父栏目循环校验、删除拦截；设置项保存、常量分组筛选与搜索均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **363 项**（新增栏目管理与系统设置外壳断言） | **363/363 PASS** |

## 〇〇〇〇〇〇〇、v53 后台页面迁移第五步（内容管理接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/content.php](file:///workspace/php/admin/content.php)：内容列表、附件管理、删除确认三个分支改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)；侧栏按模块高亮“文章管理/论坛管理” | 文章/论坛列表、附件页、删除确认均 1 个 DOCTYPE、输出 `admin-shell` |
| 功能不变 | 模块切换、栏目筛选、关键词搜索、隐藏状态筛选、批量操作、附件列表、删除确认与分页保留条件均未改 | 内容管理相关后台用例全部通过 |
| 边界行为 | 记录不存在时仍走基类 `showError` 提示，不产生页面结构错误 | `act=del&id=999` 正常提示内容不存在 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **361 项**（新增内容管理页外壳结构断言） | **361/361 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v52 后台页面迁移第四步（评论审核接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/reply.php](file:///workspace/php/admin/reply.php)：评论审核列表与删除确认两个分支改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“评论审核” | 内容评论/论坛回复/两种删除确认四个入口均 1 个 DOCTYPE、输出 `admin-shell` |
| 功能不变 | 内容评论与论坛回复切换、关键词搜索、显示/隐藏筛选、审核状态切换、删除确认与回跳参数均未改 | 相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **360 项**（新增评论审核页外壳结构断言） | **360/360 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v51 后台页面迁移第三步（商城订单接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/shoporder.php](file:///workspace/php/admin/shoporder.php)：列表页改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“商城订单”并显示待发货徽标 | 页面仅 1 个 DOCTYPE、输出 `admin-shell`、导航高亮正确 |
| 列表与操作 | 状态页签、关键词搜索、CSV 导出入口、发货/退款/完成/取消按钮与分页沿用原有逻辑，统一在内容区渲染 | `status=1` 下展示订单 #2 与发货/退款按钮 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **359 项**（新增商城订单页外壳结构断言） | **359/359 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v50 后台页面迁移第二步（商品库存接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/goods.php](file:///workspace/php/admin/goods.php)：列表、新增/编辑、删除确认三个分支统一改用 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“商品库存” | 四个入口（列表/新增/编辑/删除确认）均仅 1 个 DOCTYPE、输出 `admin-shell` |
| 提示样式 | [admin.css](file:///workspace/php/assets/css/admin.css) 增加 `.ok` 成功提示样式，与 `.error` 对应 | 库存调整成功/失败提示样式统一 |
| 功能不变 | 关键词搜索、上架状态筛选、库存预警排序、新增/编辑保存、删除订单占用拦截逻辑未改 | 商品库存相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **358 项**（新增商品库存页外壳结构断言） | **358/358 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v49 后台页面迁移第一步（会员管理接入统一外壳）

| 能力 | 实现 | 实测 |
|---|---|---|
| 外壳迁移 | [admin/user.php](file:///workspace/php/admin/user.php)：列表页与删除确认页的 `showTop/showDown` 替换为 [inc/header.php](file:///workspace/php/admin/inc/header.php) / [inc/footer.php](file:///workspace/php/admin/inc/footer.php)，侧栏高亮“会员管理” | 页面仅一个 DOCTYPE，输出 `admin-shell`，导航 `is-active` 正确 |
| 旧类名兼容 | [admin.css](file:///workspace/php/assets/css/admin.css)：`.admin-content` 作用域内补齐 `.subtitle/.content/.list/.pager/.error/.success/.info/.time/.price` 与表格样式，迁移页无需重写标记 | 搜索框、筛选下拉、会员列表、分页、删除确认均正常渲染 |
| 功能不变 | 关键词搜索、状态筛选、锁定/解锁、重置密码、余额/积分调整、CSV 导出、删除保护逻辑均未改动 | 会员管理相关后台用例全部通过 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **357 项**（新增会员管理页外壳结构断言） | **357/357 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v48 全栈 UI 设计底座与后台外壳（tokens / 组件库 / 仪表盘改版）

| 能力 | 实现 | 实测 |
|---|---|---|
| 设计令牌 | 新增 [tokens.css](file:///workspace/php/assets/css/tokens.css)：品牌色/功能色/中性色、间距 4-32、圆角、阴影、字号、层级、布局宽度，附深色主题变量 | 前台页面 `<head>` 输出 `assets/css/tokens.css` 链接，结构断言通过 |
| 通用组件库 | 新增 [components.css](file:///workspace/php/assets/css/components.css)：`ui-` 前缀的按钮/卡片/标签/页签/统计卡/表格/表单/提示条/空态/面包屑/分页/列表与工具类 | 后台仪表盘实际渲染 8 个统计卡、订单状态标签与 2 张数据表 |
| 后台外壳 | 新增 [admin/inc/header.php](file:///workspace/php/admin/inc/header.php) 与 [footer.php](file:///workspace/php/admin/inc/footer.php)、[admin.css](file:///workspace/php/assets/css/admin.css)：深色侧栏分组导航（内容/会员/商城/运营/系统）、待办徽标、顶栏、内容区，900px 以下自动横向导航 | 后台首页输出 `class="admin-shell"` 并通过结构断言；导航含待发货 1、待审核等动态徽标 |
| 仪表盘改版 | [admin/index.php](file:///workspace/php/admin/index.php) 改用统一外壳：8 项统计卡（成交额/库存预警等语义色）、最近订单（状态标签）、最新会员、8 个快捷入口，全部保留原有数据口径 | 统计卡 8 个、订单表 1 行、会员表 1 行、快捷入口 8 个 |
| 前台接入 | [WapTool.php](file:///workspace/php/includes/WapTool.php) 的 HTML 头部在模板样式之前加载 tokens/components，模板样式仍可覆盖，不影响 default/touch 既有外观 | 前台页面均带两份新样式链接，原有 354 项用例全部保持通过 |
| 测试治理 | 管理员退出用例移至脚本末尾，避免提前销毁后台会话导致后续后台用例空跑；`check/check_admin` 去除 NUL 字节（备份下载为二进制） | 修复后暴露并修复社区报表 SQL 缺陷 |
| 缺陷修复 | [admin/community.php](file:///workspace/php/admin/community.php)：`cnt()` 动态补 `WHERE`、发帖 Top10 基础 SQL 补 `WHERE 1=1`，修复 `days=7/30` 触发 `near "AND": syntax error` | 近 7 天/近 30 天报表正常输出统计与趋势，不再报错 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **356 项**（新增后台外壳与前台令牌 2 条结构断言） | **356/356 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v47 好友列表增强（关键词搜索 / 数量汇总 / 失效标记）

| 能力 | 实现 | 实测 |
|---|---|---|
| 关键词搜索 | [friend/index.php](file:///workspace/php/friend/index.php)：`q` 联表 `WapUser` 按用户名或昵称模糊匹配，仅检索本会员已通过的好友（`status=1`），附「清除搜索」入口 | `q=user1` 由 2 位收窄为 1 位（会员一）；`q=会员三` 命中会员三(user3) |
| 数量汇总 | 顶部输出“共 N 位好友（当前条件 M 位），本页 K 位”，随搜索变化 | 默认“共 2 位好友，本页 2 位”；搜索后“共 2 位好友，当前条件 1 位，本页 1 位” |
| 展示与入口 | 好友名统一“昵称(用户名)”格式，保留空间/发短信/删除入口与添加时间；待确认好友不进入列表 | 列表显示会员三(user3)、会员一(user1)，待确认的 user4 不出现 |
| 失效与空态 | 关联会员不存在时标记“已注销用户”且隐藏操作入口；无结果区分“没有找到匹配的好友”与“您还没有好友” | `q=zzz` 显示“没有找到匹配的好友”且不报错 |
| 分页 | 分页链接携带 `q`，翻页不丢搜索条件；页码越界自动收敛到最后一页 | 搜索条件保留在翻页 URL 中 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **354 项**（新增用户名搜索、昵称搜索、无结果 3 条用例）；[seed.php](file:///workspace/php/tests/seed.php) 补充 3 条好友关系种子 | **354/354 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v46 站内消息中心增强（状态筛选 / 关键词搜索 / 计数汇总）

| 能力 | 实现 | 实测 |
|---|---|---|
| 状态筛选 | [message/index.php](file:///workspace/php/message/index.php)：收件箱提供 全部/未读/已读，发件箱提供 全部/对方未读/对方已读，页签显示各态数量；非法状态回落“全部”并提示 | user2 收件箱 全部(2)/未读(1)/已读(1)；发件箱 全部(2)/对方未读(1)/对方已读(1) |
| 关键词搜索 | `q` 对本箱内标题、发件人、收件人三字段模糊匹配，仅在本箱范围内检索，附「清除筛选」入口 | `box=in&q=私信` 命中 2 条；条件保留在状态页签链接中 |
| 计数汇总 | 一次统计本箱总数与未读数，顶部输出“共 N 条收件/发件，未读（或对方未读）M 条，当前条件 K 条，本页 J 条”，随筛选变化 | 默认“共 2 条收件，未读 1 条，当前条件 2 条，本页 2 条” |
| 发件状态标记 | 发件箱逐条显示「对方未读 / 对方已读」，收件箱保留「未读」强调标记 | 发件箱 #3 显示“对方未读”、#4 显示“对方已读” |
| 条件保持 | 分页链接携带 `box`、`q`、`status`，翻页与切页签不丢筛选条件；无结果时区分“没有符合条件的消息”与空箱提示 | 页签 URL 形如 `index.php?box=in&q=私信&status=unread` |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **351 项**（新增未读筛选、关键词搜索、发件箱已读、非法状态 4 条用例）；[seed.php](file:///workspace/php/tests/seed.php) 补充 4 条站内消息种子 | **351/351 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v45 积分商城列表增强（搜索 / 积分区间 / 排序 / 可兑换判断）

| 能力 | 实现 | 实测 |
|---|---|---|
| 关键词搜索 | [shop/point.php](file:///workspace/php/shop/point.php)：`q` 对商品名称模糊匹配，仅检索在售且有库存商品 | `q=乙` 命中 1 件，汇总同步为“当前条件 1 件” |
| 积分区间 | `minpoints`/`maxpoints` 过滤所需积分；非数字条件忽略并提示，最小值大于最大值自动交换并提示 | `10-100` 命中 2 件；`abc/xyz` 提示“最低积分需为数字，已忽略该条件”；`200-10` 提示“已自动交换” |
| 排序 | 默认（sortnum）/ 积分从低到高 / 积分从高到低，非法值回落默认，下拉框保持选中 | `sort=points_asc` 依次输出 20、100、150 积分 |
| 可兑换判断 | 登录后按当前积分判断：不足时显示“积分不足（还差 N 分）”并提供去签到入口，余额足够才显示兑换链接 | 积分 100 时 150 分商品显示“积分不足（还差 50 分）”，20/100 分商品显示兑换 |
| 条件保持 | 分页链接携带 `q`、`minpoints`、`maxpoints`、`sort`，并显示“清除筛选”入口 | 分页 URL 形如 `point.php?q=乙&sort=points_asc&page=2` |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **347 项**（新增搜索/排序/区间/非法区间 4 条用例） | **347/347 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v44 积分兑换记录增强（状态筛选 / 积分汇总 / 备注展示）

| 能力 | 实现 | 实测 |
|---|---|---|
| 状态筛选 | [shop/point_orders.php](file:///workspace/php/shop/point_orders.php)：全部/待发放/已发放/已取消四个页签并显示各态数量，非法状态回落“全部”，仅在本会员记录内筛选 | 全部(3)、待发放(1)、已发放(1)、已取消(1)；`status=0` 仅显示待发放记录 |
| 积分汇总 | 顶部展示当前积分余额与“共 N 条兑换记录，当前条件 M 条，累计消耗积分 X，本页 K 条”，随筛选变化 | 默认“共 3 条…累计消耗积分 230”；`status=1` 显示累计 100；`status=2` 显示累计 30 |
| 记录明细 | 展示单号、商品名、积分 × 数量、状态标签与时间，取消/发放备注单独换行显示 | #3 显示“已取消”并附带“备注：管理员取消” |
| 条件保持 | 分页链接携带 `status`，切换状态页签与翻页不丢筛选条件 | 页签链接形如 `point_orders.php?status=1`，分页保留状态参数 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **343 项**（新增兑换记录与三种状态筛选 4 条用例） | **343/343 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v43 商品直购页整合（状态校验 / 库存收敛 / 统一结算）

| 能力 | 实现 | 实测 |
|---|---|---|
| 在售校验 | [shop/order.php](file:///workspace/php/shop/order.php)：进入直购页先校验商品存在与 `isshow=1`，不存在或已下架直接提示并给出返回入口 | `id=999` 提示“商品不存在”；`id=3`（下架商品）提示“商品已下架” |
| 库存校验 | 库存为 0 提示“商品已售罄”；请求数量小于 1 归正为 1，超过库存收敛到库存上限并在页面提示，输入框 `max` 同步库存 | `id=2&num=5` 提示“购买数量已调整为库存上限 2 件”且 `max="2"`；`num=-3` 归正为 1 |
| 统一结算 | 移除直购页自建订单逻辑，改为携带 `shopid`/`num` 跳转 [shop/checkout.php](file:///workspace/php/shop/checkout.php)，复用其库存复核、收货地址/手机必填、余额提示与事务下单 | `checkout.php?shopid=1&num=2` 显示应付合计 ￥198 与收货地址输入框 |
| 信息展示 | 确认页展示商品名、单价、库存与数量输入，附返回商品/商城入口 | `id=1` 显示“商品:测试商品 单价:￥99.00 库存:94 件” |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **339 项**（新增直购正常/下架/不存在/超库存/非法数量 5 条用例） | **339/339 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v42 订单列表增强（搜索 / 排序 / 汇总 / 内联操作）

| 能力 | 实现 | 实测 |
|---|---|---|
| 关键词搜索 | [shop/myorder.php](file:///workspace/php/shop/myorder.php)：`q` 命中订单号（纯数字精确匹配）或商品标题模糊匹配，仅在本会员订单内检索，附「清除搜索」入口 | `q=测试` 命中 2 单；`q=不存在` 显示“没有符合条件的订单” |
| 排序 | `sort` 支持 最新下单 / 金额从高到低 / 金额从低到高，非法值回落默认，下拉框保持当前选中 | `sort=amount_desc` 正常返回，条目金额顺序正确 |
| 金额汇总 | 顶部输出“共 N 单，当前条件 M 单 合计￥X，本页 K 单”，合计随筛选条件变化 | 默认显示“共 2 单，当前条件 2 单 合计￥198.00，本页 2 单” |
| 待付款提醒 | 统计待付款单数与金额并在页面顶部以醒目提示输出，直接跳转待付款筛选 | 显示“您有 1 个待付款订单，合计￥99.00，去支付” |
| 内联操作 | 列表内直接提供详情、支付、二次确认取消（待付款）、二次确认确认收货（已发货），无需进详情页 | #1 显示 详情/支付/取消；将 #2 置为已发货后显示“确认收货”按钮 |
| 条件保持 | 状态页签与分页链接携带 `q`、`sort`，翻页切换筛选不丢条件 | 页签链接形如 `myorder.php?classid=1&status=0`，搜索后页签保留 `q` |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **334 项**（新增搜索/排序 4 条用例） | **334/334 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v41 订单详情增强（进度时间线 / 单价小计 / 状态操作）

| 能力 | 实现 | 实测 |
|---|---|---|
| 订单进度 | [shop/myorder.php](file:///workspace/php/shop/myorder.php)：按 提交订单→完成付款→商家发货→确认收货 输出时间线，区分【已完成】/【当前】/【未开始】 | #1 显示【当前】提交订单、【未开始】完成付款；#2 显示【已完成】提交订单、【当前】完成付款 |
| 金额明细 | 展示商品链接、单价 × 数量 = 总额，避免只看总额无法核对 | #1 显示“单价:￥99 × 1 = ￥99” |
| 状态操作 | 待付款提供去支付与二次确认取消；待发货提示备货中；已发货提供二次确认收货；已完成提示交易完成；取消/退款单独提示原因 | #1 显示去支付与取消订单；#2 显示“商家正在备货” |
| 越权隔离 | 详情查询仍按 `id + userid` 绑定，访问他人订单提示订单不存在 | user2 访问 user1 的 #3 返回订单不存在 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **330 项**（新增已付款订单详情用例） | **330/330 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v40 结算页增强（在售/库存校验 / 价格刷新 / 地址预填 / 余额提示）

| 能力 | 实现 | 实测 |
|---|---|---|
| 结算校验 | [shop/checkout.php](file:///workspace/php/shop/checkout.php)：提交前逐条校验商品是否存在、在售、库存充足，任一不通过即列出具体原因并跳回购物车调整，阻止生成订单 | 购物车含下架商品时提示“以下商品暂不可结算 / 商品「下架商品」已下架”；购买 5 件低库存商品提示“库存不足，仅剩 2 件” |
| 价格刷新 | 结算与下单均使用商品当前标题/价格，避免购物车历史价格与实际价格不一致 | 条目价格由商品表实时读取后参与合计 |
| 地址预填 | 自动带出该会员最近一次订单的收货地址与手机号，手机号附格式说明（6-20 位数字，可含 +、-） | 预填“上海市测试路2号 / 13800002222”；非法手机号提交被拦截 |
| 费用提示 | 展示应付合计与会员当前余额，余额不足时提示可在支付页选择其他方式；提交按钮带金额 | ￥99、余额￥500、按钮“提交订单（￥99）” |
| 下单结果 | 下单成功展示订单数量与逐笔支付入口；订单生成后清除已结算购物车条目 | 提交后“下单成功！共 1 个订单 / 支付订单 #4”，购物车条目被清除 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **329 项**（新增无有效商品、非法购物车ID两条用例） | **329/329 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v39 购物车增强（操作提示 / 失效标记 / 结算拦截 / 清空）

| 能力 | 实现 | 实测 |
|---|---|---|
| 操作反馈 | [shop/cart.php](file:///workspace/php/shop/cart.php)：加购/改量/删除/清空均返回提示并跳转展示；加购区分下架、售罄、超库存、参数错误 | 加购 2 件提示“已加入购物车（2 件）”；再购 200 件提示“库存不足，最多还可加入 92 件” |
| 库存与状态校验 | 加购校验库存（含已加购数量）；修改数量超过库存自动截断到库存值并提示；下架商品拒绝加购 | `shopid=3` 提示“商品不存在或已下架”；改量 500 → “数量已按库存调整为 94 件”；低库存商品仅可再购 2 件 |
| 失效商品拦截 | 列表联表读取库存/上架状态，下架、售罄、超库存条目标红并显示「查看商品」，结算勾选框置灰禁用，顶部汇总失效数量 | 注入下架条目后显示 `[已下架]`、“有 1 件商品已下架/售罄/库存不足，已置灰不可结算”，勾选框 `disabled` |
| 汇总与清空 | 统计总件数与合计金额；新增“清空购物车”并二次确认 | 共 8 件、合计￥703；清空后提示“购物车已清空”，页面回到空购物车状态 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **327 项**（新增购物车提示页用例） | **327/327 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v38 商品列表增强（排序 / 关键词 / 价格区间 / 售罄标记）

| 能力 | 实现 | 实测 |
|---|---|---|
| 排序 | [shop/list.php](file:///workspace/php/shop/list.php)：最新/价格升/价格降/人气四种排序，非法值回落“最新”，页签高亮当前项 | `sort=price_asc` 顺序为 19.90 低库存商品、99.00 测试商品 |
| 关键词搜索 | 支持商品 ID 精确与标题模糊，附清除筛选入口 | `q=低库存` 由 2 件收窄为 1 件 |
| 价格区间 | 最低价/最高价校验非负数字（最多两位小数），非法值忽略并提示；最低价大于最高价时提示并忽略最高价 | `minprice=abc` 提示“最低价必须为非负数字”；`20-50` 命中 0 件符合预期 |
| 售罄标记 | 列表查询补充 `stock`，库存 ≤0 显示“售罄”并隐藏加购/立即购买，仅在售商品可下单 | 在售 2 件正常显示加购；下架商品不进入列表 |
| 分页 | 分页链接保留 `q/sort/minprice/maxprice` 全部条件 | 顶部显示“共 X 件商品”，翻页条件不丢失 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **326 项**（新增排序、搜索、非法价格三条用例） | **326/326 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v37 商品详情增强（库存展示 / 售罄拦截 / 下架提示 / 相关推荐）

| 能力 | 实现 | 实测 |
|---|---|---|
| 库存与热度 | [shop/view.php](file:///workspace/php/shop/view.php)：展示库存、点击量；库存 <5 显示“仅剩少量”，=0 显示“已售罄”；面包屑显示栏目并可返回列表 | 商品 #1 库存 94、点击 11；#2 显示“仅剩少量” |
| 售罄拦截 | 库存为 0 时不渲染加入购物车/立即购买表单，仅提示已售罄；购买数量 `max` 取库存与 99 的较小值 | #1 数量框 max=94；#2 max=2 |
| 下架提示 | 下架商品仅展示标题/价格与“已下架”提示，隐藏购买区且不累加点击数 | #3 显示“该商品已下架，暂不可购买。”，无购买按钮，hits 保持 10 |
| 相关推荐 | 同栏目、在售、排除当前商品，最多 5 个，售罄商品带标记 | #1 推荐“低库存商品”；#2 仅推荐“测试商品”，下架商品不出现 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **323 项**（新增低库存详情、下架详情两条用例） | **323/323 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v36 我的钱包增强（收支筛选 / 类型统计 / 分页保留筛选）

| 能力 | 实现 | 实测 |
|---|---|---|
| 收支筛选 | [myaccount/index.php](file:///workspace/php/myaccount/index.php)：新增全部/收入/支出三个筛选页签，非法值回落“全部”，无记录时区分“暂无收支记录”与“该类型下暂无收支记录” | 全部(2)/收入(1)/支出(1)；`type=in` 仅“测试充值”，`type=out` 仅“测试消费”，`type=bad` 回落全部 |
| 汇总统计 | 一次 `GROUP BY type` 统计各类型条数与金额，顶部展示累计收入、累计支出与当前显示条数 | 累计收入￥600、累计支出￥100、当前显示 2 条 |
| 列表可读性 | 明细保留收支类型、金额符号、变动后余额与时间 | 收入 +600、支出 -100 与余额展示正确 |
| 分页 | 分页链接保留 `type` 筛选参数 | 翻页地址携带 type，筛选不丢失 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **321 项**（新增 type=in、type=out 两条用例）；种子补充 user2 两条收支流水 | **321/321 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v35 我的订单增强（状态筛选 / 各状态计数 / 分页保留筛选）

| 能力 | 实现 | 实测 |
|---|---|---|
| 状态筛选 | [myorder.php](file:///workspace/php/shop/myorder.php)：新增全部/待付款/已付款(待发货)/已发货/已完成/已取消/已退款七个状态页签，非法值回落“全部” | `status=0` 仅显示 #1，`status=1` 仅显示 #2，`status=-1` 提示“该状态下暂无订单” |
| 数量统计 | 一次 GROUP BY 统计各状态数量，页签展示「名称(数量)」，顶部显示“共 X 单，当前显示 Y 单” | user2 显示 全部(2)/待付款(1)/已付款(待发货)(1)/其余 0；共 2 单 |
| 列表信息 | 订单行补充购买数量与下单时间，仍保留详情/待付款支付入口 | #2、#1 均展示数量、时间与操作链接 |
| 分页 | 分页链接保留 `status` 筛选参数，避免翻页丢失筛选条件 | 构造多页时翻页地址携带 status |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **319 项**（新增 status=0、status=1 两条用例） | **319/319 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v34 数据库维护（备份下载 / 完整性检查 / 空间优化）

| 能力 | 实现 | 实测 |
|---|---|---|
| 运行信息 | 新增 [backup.php](file:///workspace/php/admin/backup.php)：展示数据库类型、库文件名、主库大小、WAL 日志大小、最后修改时间、数据表数量 | 页面显示 SQLITE、352.00 KB、数据表 79、时间正确 |
| 备份下载 | `action=download` 先执行 `PRAGMA wal_checkpoint(TRUNCATE)` 合并 WAL，再以 `application/octet-stream` 附件下载，文件名`库名_时间戳.db`；仅 SQLite 可用 | 下载文件 360,448 字节，内含 5 个会员、80 张表，响应头正确 |
| 完整性检查 | `action=check` 执行 `PRAGMA integrity_check`，通过显示 ok，异常时输出具体问题 | 返回“完整性检查通过（integrity_check = ok）” |
| 空间优化 | `action=vacuum` 执行 VACUUM 并展示优化前后大小，失败给出错误信息 | 优化完成，前后大小 352.00 KB，库仍可正常读取 |
| 入口与安全 | 后台首页新增“数据库维护”入口；所有操作先 `checkAdminLogin`，非 SQLite 类型明确提示不支持 | 首页链接可直达；未登录访问跳转登录页 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **317 项**（新增页面、完整性检查、备份下载三条用例） | **317/317 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v33 采集管理增强（搜索 / 校验 / 删除确认 / 批量导入文章）

| 能力 | 实现 | 实测 |
|---|---|---|
| 采集源管理 | [caiji.php](file:///workspace/php/admin/caiji.php)：列表显示栏目名称、最近运行时间与采集源/文章总数；关键词 `q` 支持 ID 精确、名称/地址模糊；新增采集源校验名称必填、地址必须 `http(s)://`、栏目必须属于 article 模块 | 列表显示 2 个采集源、栏目“文章测试栏目”；`q=测试` 收窄为 1 个；`url=ftp://bad` 被拦截 |
| 删除确认 | 删除改为 `act=del&id=N` 二次确认页，展示名称/地址与影响说明；POST `deldone` 重新校验存在后删除 | 确认页显示“测试采集源”；提交后采集源 3→2 |
| 批量导入文章 | 新增批量导入表单：选择文章栏目，按“标题\|正文”每行一篇，最多 100 篇；标题必填、超长截断、正文清除 HTML 标签，导入后清站点缓存 | 3 行输入（含 1 行空标题）→“成功 2 篇，跳过 1 行”；库内 2 篇文章 isshow=1、author=采集导入、`<b>` 标签已清除 |
| 触发采集 | 采集前校验采集源存在，更新最近运行时间；提示正式环境需对接远程采集源 | 采集源不存在时返回明确错误；正常触发更新时间 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **314 项**（新增搜索、删除确认两条用例）；种子脚本补充 2 个采集源 | **314/314 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v32 系统设置增强（可编辑项扩展 / 校验 / 清缓存 / 常量检索）

| 能力 | 实现 | 实测 |
|---|---|---|
| 可编辑项扩展 | [setting.php](file:///workspace/php/admin/setting.php)：新增站点名称、关闭站点开关、绑定域名、Logo 地址，保留版权/关闭提示/站点信息；当前模板只读展示 | 保存后库内 sitename=测试站点、domain=test.example.com、logourl=/logo.png、isclose=1 全部落库 |
| 输入校验 | 站点名称必填且 ≤50 字；版权/关闭提示 ≤255 字；站点信息 ≤2000 字；Logo 仅允许 `http(s)://` 或 `/` 开头；域名仅允许字母数字点与短横线 | `domain=bad_domain` 与 `logourl=ftp://x` 均被拦截并给出明确提示 |
| 保存与缓存 | 保存成功后清理站点缓存，提示“缓存已清理”，避免前台读取旧配置 | 提示正确，前台缓存键被清除 |
| 常量检索 | 配置常量支持分组筛选（全部/数据库/开关/安全/模块限制/下载）与关键词搜索（常量名或说明），显示各组命中数 | `group=安全` 仅显示安全组 12 项；`q=KL_OpenCache` 命中 KL_OpenCache 与 KL_OpenCacheTime 共 2 项 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **312 项**（新增分组筛选与常量搜索两条用例）；新增可复用种子脚本 [seed.php](file:///workspace/php/tests/seed.php) | **312/312 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v31 文件管理器增强（搜索 / 类型筛选 / 排序 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 类型筛选 | [upload.php](file:///workspace/php/admin/upload.php)：按扩展名归类为全部/图片/文档/压缩包/其他，显示各类数量 | 4 个测试文件显示 全部(4)/图片(1)/文档(1)/压缩包(1)/其他(1) |
| 搜索 | 文件名不区分大小写模糊匹配，支持清除搜索，筛选与排序条件在链接中保留 | `q=test_b` 由 4 个收窄为 1 个（test_b.txt） |
| 排序 | 最新（修改时间倒序）/大小（大→小）/名称（升序）三态切换 | `sort=size` 依次输出 test_d.xyz(7B)、test_c.zip(6B)、test_b.txt(5B)、test_a.png(1B) |
| 删除确认 | 删除改为 `act=del&file=...` 二次确认页，展示文件名、大小与风险提示；仍经 `Upload::absPath` 防路径穿越，POST 删除保留 | 确认页显示 test_c.zip / 6 B；提交后文件实际删除，非法路径返回错误提示 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **310 项**（新增图片筛选、搜索、删除确认三条用例） | **310/310 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v30 会员导出增强（按筛选导出 / 字段补全 / 防公式注入）

| 能力 | 实现 | 实测 |
|---|---|---|
| 按当前筛选导出 | [user.php](file:///workspace/php/admin/user.php)：导出按钮改为“导出当前筛选会员CSV”，携带当前 `status`（全部/正常/已锁定）与 `q`（ID/用户名/邮箱/昵称）；导出条件与列表查询完全一致 | `status=locked` 仅导出 user5；`q=user2` 仅导出 user2；全部导出 5 行 |
| 字段补全 | 表头改为中文并补全昵称、分组名称、账号状态、最后登录时间、最后登录 IP，时间戳文件名精确到秒 | 5 列数据完整，已锁定账号显示“已锁定” |
| 安全 | CSV 统一 UTF-8 BOM；对以 `= + - @` 开头的单元格前置单引号，防止 Excel 公式注入 | 导出响应 `text/csv; charset=utf-8`，文件名 `users_20261116_185448.csv` |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **307 项**（新增正常、已锁定、关键词导出三条用例） | **307/307 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v29 举报管理增强（搜索 / 状态筛选 / 处理标记 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 搜索与状态筛选 | [report.php](file:///workspace/php/admin/report.php)：全部/待处理/已处理三态筛选并显示计数（随搜索联动）；关键词 `q` 支持 ID 与帖子 ID 精确、理由/备注模糊；分页保留条件 | 全部(3)/待处理(2)/已处理(1)；`q=广告` 命中 ID:3、ID:1；`status=handled` 仅显示 ID:2 |
| 处理标记 | 待处理记录一键“标记已处理”，已处理记录可“恢复待处理”，状态切换即时生效 | 标记 ID:3 后待处理 2→0、已处理 1→2；恢复 ID:2 后 ID:2 回到待处理 |
| 删除确认 | 删除改为 `act=del&id=N` 二次确认页，展示帖子 ID、举报理由与风险提示；POST `deldone` 校验存在后删除 | 确认页理由显示正确；提交后举报 3→2 |
| 关联信息 | 列表展示举报人（关联 WapUser，缺失回退 #ID）、帖子外链（新窗口打开前台帖子）、备注与时间 | 举报人 user2/user1、帖子 #1 外链正常 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **304 项**（原单条用例替换为搜索、已处理筛选、删除确认三条） | **304/304 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v28 后台首页仪表盘（今日数据 / 最近订单 / 最新会员）

| 能力 | 实现 | 实测 |
|---|---|---|
| 今日经营数据 | [index.php](file:///workspace/php/admin/index.php)：统计卡片扩为 8 项，新增今日注册、今日订单、今日成交额（仅计已付款及之后状态）、待发货订单、库存预警；今日范围按站点本地时区零点起算 | 种子数据下 会员 5 / 今日注册 5 / 今日订单 3 / 今日成交额 ￥99.00 / 待发货 1 / 库存预警 1，均与库内数据一致 |
| 最近订单 | 展示最近 5 笔订单：ID（直达订单查询）、会员名（关联 WapUser，缺失回退 #ID）、金额、中文状态、时间 | 依次显示 #3 user1 ￥19.90 已退款、#2 user2 ￥99.00 待发货、#1 user2 ￥99.00 待付款 |
| 最新会员 | 展示最近 5 名会员：ID、用户名（直达会员搜索）、注册时间 | 显示 user1–user5 及注册时间 |
| 兼容与回归 | 订单/举报等可选表查询全部 try/catch 包裹，缺表时降级为 0，不影响首页打开 | 全量验收 **302/302 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v27 充值卡管理增强（搜索 / 状态筛选 / 作废确认 / 使用信息）

| 能力 | 实现 | 实测 |
|---|---|---|
| 搜索与状态筛选 | [card.php](file:///workspace/php/admin/card.php)：关键词 `q` 支持 ID 精确与卡号/类型模糊；全部/未使用/已使用三态筛选及计数（随搜索联动）；分页与筛选条件联动保留 | 全部(3)/未使用(2)/已使用(1)；`q=123456` 命中 3 张；`status=used` 仅显示已使用卡 |
| 作废二次确认 | 列表作废改为 `act=void&id=N` 确认页，展示卡号/类型/面值；POST `voiddone` 重新校验后删除；已使用卡在确认页与提交时均被拦截 | 未使用卡确认页信息正确，提交后总数 3→2；已使用卡提示“该卡已被使用，不能作废” |
| 使用信息展示 | 已使用卡显示使用人（关联 WapUser，缺失显示 #ID）与使用时间；未使用卡保留作废入口 | 卡号 123456789013 显示“使用人:user2 时间:11-16 16:38” |
| 生成 | 批量生成保留数量/类型/面值校验与卡号查重；新增时间改为站点本地时区写入，避免 SQLite localtime 时区偏差 | 生成结果正常，卡号密码明文仅本次展示 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **302 项**（新增搜索、已使用筛选、作废确认三条用例） | **302/302 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v26 系统公告增强（搜索 / 分页 / 删除确认 / 缓存清理）

| 能力 | 实现 | 实测 |
|---|---|---|
| 搜索与分页 | [sysmsg.php](file:///workspace/php/admin/sysmsg.php)：关键词 `q` 支持公告 ID 精确与标题/内容模糊；列表分页并显示总条数，编辑/删除/翻页均保留搜索条件 | `q=公告测试二` 由 2 条收窄为 1 条；列表显示“共 2 条公告”，链接自动携带搜索参数 |
| 新增与编辑 | 新增/编辑同一表单，标题非空校验；编辑不存在的公告给出提示并回落为新增；新增时间按站点本地时区写入 | 新增公告返回 ID=3 且时间正确；编辑 ID=2 后标题与内容落库 |
| 删除二次确认 | 列表删除改为 `act=del&id=N` 确认页，展示 ID、标题与内容摘要；POST `deldone` 校验存在后删除 | 确认页信息正确；提交后公告 2→1 |
| 缓存清理 | 新增、编辑、删除公告后清站点缓存 | 三类操作均调用缓存清理，前台公告随即更新 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **299 项**（新增搜索、分页、删除确认三条用例） | **299/299 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v25 在线用户增强（搜索 / 类型筛选 / 踢下线 / 超时清理）

| 能力 | 实现 | 实测 |
|---|---|---|
| 搜索与类型筛选 | [online.php](file:///workspace/php/admin/online.php)：全部/会员/游客三态筛选并显示计数（随搜索联动）；关键词 `q` 支持 ID 精确与用户名/IP/页面模糊匹配；分页保留条件 | 登录后 全部(4)/会员(2)/游客(2)；`q=192.168` 仅命中游客记录；`type=member` 仅显示 2 条会员记录 |
| 踢下线 | 列表行“踢下线”进入 `act=kick&id=N` 二次确认页，展示用户、IP、页面；POST `kickdone` 校验记录存在后删除在线记录 | 确认页用户信息正确；提交后该会员记录删除，在线数 4→3 |
| 超时清理 | 一键清理 30 分钟无活动记录（按 PHP 本地时间计算阈值，与前台 60 分钟自动清理互补），返回实际清理条数 | 40 分钟前的游客记录被清理 1 条，在线数 3→2 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **296 项**（新增搜索、会员筛选、踢下线确认三条用例） | **296/296 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v24 评论审核增强（搜索 / 状态筛选 / 编辑 / 批量删除 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 搜索与状态筛选 | [reply.php](file:///workspace/php/admin/reply.php)：内容评论与论坛回复双标签；关键词 `q` 支持 ID 精确与内容/昵称模糊；全部/已显示/已隐藏三态筛选并显示计数（随搜索联动）；分页与筛选参数联动保留 | 内容评论 全部(3)/已显示(2)/已隐藏(1)；`q=隐藏评论` 仅命中 ID:2；论坛回复隐藏筛选仅显示 ID:2 |
| 编辑与显示切换 | 行内“编辑”进入内容编辑区，保存前校验非空；显示/隐藏切换改为链接式操作；两类操作均清站点缓存 | 评论内容修改后落库；隐藏评论切换后 `isshow=1` |
| 单条删除确认 | 列表删除改为 `act=del&id=N` 二次确认页，展示类型、ID、会员、内容摘要与风险提示；POST `deldone` 校验存在后删除并清缓存 | 确认页信息正确；提交后内容评论 3→2 |
| 批量删除 | 列表勾选后一次删除所选评论，未选择时提示；删除后清缓存 | 勾选 2 条提交后内容评论 2→0，论坛回复不受影响 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **293 项**（新增搜索、隐藏筛选、论坛隐藏筛选、删除确认四条用例） | **293/293 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v23 内容管理增强（搜索 / 栏目与状态筛选 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 关键词搜索 | [content.php](file:///workspace/php/admin/content.php)：按表实际列自动选择标题列（title/name/content/data），支持内容 ID 精确与关键词模糊匹配 | `module=shop&q=测试商品` 仅命中 ID:1 |
| 栏目与状态筛选 | 含 `classid` 的表显示当前模块栏目下拉；含 `isshow` 的表显示全部/已显示/已隐藏三态与各态计数（计数随搜索与栏目条件联动）；分页保留全部筛选参数 | 商城列表显示 全部(3)/已显示(2)/已隐藏(1)；`classid=1` 命中 3 条；`status=hidden` 仅显示下架商品 |
| 单条删除确认 | 列表删除改为 `act=del&id=N` 二次确认页，展示模块、ID、标题与风险提示；POST `deldone` 校验存在后删除；显示/隐藏、置顶、单条与批量删除均清站点缓存 | 确认页标题正确；提交后商品删除、总数 3→2；隐藏切换后 `isshow=0` |
| 列表信息 | 每行展示所属栏目名称（未分类/无效栏目有兜底），保留勾选批量删除、显示/隐藏、置顶与附件入口 | 栏目名关联正确，批量与原有操作入口保留 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **289 项**（新增搜索、栏目筛选、隐藏筛选、删除确认四条用例） | **289/289 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v22 栏目管理增强（筛选 / 搜索 / 父栏目校验 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 模块筛选与搜索 | [class.php](file:///workspace/php/admin/class.php)：模块下拉筛选（全部/28 个模块）+ 关键词 `q`（栏目 ID 精确、名称模糊）；列表分页与筛选、搜索条件联动保留 | `module=shop` 仅显示商城栏目；`q=文章` 仅命中“文章测试栏目” |
| 父栏目校验 | 保存前校验父栏目存在、与当前栏目同模块、非自身、非自身下级（沿父链上溯防循环，带 200 层保护）；保存成功后清站点缓存 | `parentid=自身`、父栏目模块不一致、父栏目为当前栏目下级三种情况均被拒绝且数据不变 |
| 删除二次确认 | 列表删除改为 `act=del&id=N` 确认页，展示子栏目数与栏目内容数；存在子栏目或栏目内容时直接拦截且不显示提交按钮；POST `deldone` 再次校验后删除并清缓存 | 商城栏目下 3 条商品被拦截；含 1 个子栏目的栏目被拦截；无子栏目无内容的栏目确认后删除成功 |
| 列表信息 | 每行展示栏目 ID/名称/模块/排序/父栏目名称/内容数量，并提供“内容管理”直达与“删除”确认入口 | 父栏目名称与内容计数正确，直达链接自动带上模块参数 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **285 项**（新增模块筛选、关键词搜索、删除确认三条用例） | **285/285 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v21 会员后台增强（筛选 / 搜索 / 调整流水 / 删除确认）

| 能力 | 实现 | 实测 |
|---|---|---|
| 状态筛选与搜索 | [user.php](file:///workspace/php/admin/user.php)：全部/正常/已锁定三态筛选并显示各态计数；关键词 `q` 支持会员 ID 精确与用户名/邮箱/昵称模糊匹配；分页与筛选、搜索条件联动保留 | `status=locked` 仅显示已锁定会员且计数为 1；`q=user2` 仅命中 ID:2 |
| 余额/积分调整 | 非负整数校验（`^\d+$`）；调整后写入 `WapAccountLog` 余额流水（`type` 按增减为 `in/out`、`num=差额绝对值`、`balance=新余额`）与 `WapPointLog` 积分流水（`num` 带符号），并清站点缓存 | user2 余额 500→650、积分 100→120 落库正确；新增余额流水 1 条 `in/150/650`、积分流水 1 条 `+20/120`；`money=-5` 被拒绝且数据不变 |
| 删除二次确认 | 列表“删除”改为 `act=del&id=N` 确认页，展示会员名、余额、积分与风险提示；存在 `status IN (0,1,2)` 在途订单时直接提示拦截且不显示提交按钮；POST `deldone` 再次校验后删除并清缓存 | user2 有 2 笔在途订单，确认页与提交均被拦截且会员保留；无在途订单的 user3 确认后删除成功 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **282 项**（新增 `q=user2`、`status=locked`、`act=del&id=1` 三条用例）；原有锁定/解锁、重置密码、分组与 CSV 导出保留 | **282/282 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v20 商品后台完整增删改

| 能力 | 实现 | 实测 |
|---|---|---|
| 新增 / 编辑表单 | [goods.php](file:///workspace/php/admin/goods.php)：`act=add` 新增、`act=edit&id=N` 编辑；字段含标题、栏目（仅商城模块下拉）、价格、库存、图片地址、简介、上架开关；校验标题非空且≤255 字、价格非负最多两位小数、库存非负整数、所选栏目必须存在且为商城栏目；新增记录 `userid=0` 入库、编辑执行 UPDATE，保存后清站点缓存并回跳列表 `&ok=` | 新增商品落库 `price=12.34 / stock=7` 字段正确；编辑 #1 改为 `88.50 / 77` 生效；`price=-1` 与 `classid=999` 均被 302 带回表单错误提示，数据不变 |
| 删除商品 | `act=del&id=N` 显示二次确认页；存在 `status IN (0,1,2)` 在途订单时确认页直接提示拦截；POST `act=deldone` 再次校验状态，无在途订单才 DELETE 并清缓存 | 商品 #1 存在待付款/已发货订单，确认页与提交均被拒绝且数据保留；无订单商品 #4 确认后 `count=0` |
| 列表入口 | 列表每行新增“编辑 / 删除”链接，顶部新增“新增商品”入口，均携带当前 `status/low/q` 筛选 | 列表链接参数与当前筛选一致，各入口页面均 200 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **279 项**（新增 `act=add`、`act=edit&id=1`、`act=del&id=1` 三条用例） | **279/279 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v19 运营数据导出（订单 / 商品 CSV）

| 能力 | 实现 | 实测 |
|---|---|---|
| 订单导出 | [export.php](file:///workspace/php/admin/export.php?type=order)：导出当前筛选（七态/关键词）订单，字段含订单号/商品/数量/金额/状态/会员/手机/地址/下单时间 | 全部导出 3 条、`status=1` 仅导出 1 条，与后台筛选一致 |
| 商品导出 | [export.php](file:///workspace/php/admin/export.php?type=goods)：导出当前筛选（在售/下架/预警/关键词）商品，字段含 ID/标题/栏目/价格/库存/点击/状态/上架时间 | 预警导出仅 1 条低库存商品，栏目名正确 |
| 兼容与安全 | UTF-8 BOM + `text/csv` + 附件下载文件名；CSV 公式注入防护（`= + - @` 前缀加 `'`）；未登录 302 跳登录页；类型白名单 | BOM/响应头正确；标题 `=SUM(A1:A9)` 导出为 `'=SUM(A1:A9)`；未登录 302；非法类型走错误提示 |
| 入口 | [shoporder.php](file:///workspace/php/admin/shoporder.php#L94-L95) 与 [goods.php](file:///workspace/php/admin/goods.php#L94-L95) 增加“导出当前筛选 CSV”，携带当前状态/搜索条件 | 入口链接参数与筛选一致 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **276 项** | **276/276 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v18 商品库存后台（列表 / 预警 / 快捷维护）

| 能力 | 实现 | 实测 |
|---|---|---|
| 商品列表 | [goods.php](file:///workspace/php/admin/goods.php)：展示 ID/标题/栏目/价格/库存/点击/状态/时间，分页 20 条 | 后台登录后列表 200，栏目名与商品数据正确 |
| 筛选与搜索 | 全部/在售/已下架三态 + 各态计数；库存预警（stock<5）筛选并按库存升序；商品 ID/标题关键词搜索 | 预警筛选显示计数 1 且命中低库存商品；中文标题搜索与 ID 搜索均命中 |
| 快捷维护 | 行内 POST 改价（非负、最多两位小数）与改库存（非负整数），保存后清缓存并回跳保留筛选；上下架切换 | 改价 88.50、库存 3 落库正确；库存 -5 与价格 abc 均被校验拒绝且数据不变；切换后进入“已下架”列表 |
| 后台首页 | 新增“库存预警”统计卡片与“商品库存（预警 N）”入口 | 卡片与入口数值随库存变化一致 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **272 项** | **272/272 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v17 商城订单闭环（后台管理 + 用户收货 + 订单通知）

| 能力 | 实现 | 实测 |
|---|---|---|
| 后台订单管理 | [shoporder.php](file:///workspace/php/admin/shoporder.php)：全部/待付款/待发货/已发货/已完成/已取消/已退款七态筛选与各态计数、订单号/手机号/用户名搜索、分页；发货(1→2)、完成(2→3)、取消待付款(0→-1)、退款(1或2→-2) | 后台登录后列表与各筛选均 200；发货后订单状态 2 且产生订单通知 |
| 退款闭环 | 事务内订单置 -2 + 余额退回 + `WapAccountLog` 入账流水 + 库存回补 + 站内通知 | 实测订单 ￥10 退款后余额 +10、库存回补 2、流水 1 条、通知 1 条；状态不符时拒绝操作 |
| 用户侧订单状态 | [myorder.php](file:///workspace/php/shop/myorder.php) 状态扩展为 0待付款/1待发货/2已发货/3已完成/-1已取消/-2已退款；已发货订单可“确认收货”(2→3) | 确认收货后订单状态 3，越权/状态不符不生效 |
| 订单通知 | 通知中心新增“订单”类型筛选与标签，订单类通知可跳转对应订单详情 | `type=order` 筛选 200；发货/退款通知标签显示“订单”并回跳订单 |
| 后台首页 | 新增“待发货订单”统计卡片与“商城订单”入口 | 卡片数值与状态 1 订单数一致 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **267 项** | **267/267 PASS**；全部 PHP 文件 `php -l` 零错误；交付库仍为空库 |

## 〇〇〇〇〇〇〇、v16 二次开发增量（积分商城 / 敏感词库 / 社区运营）

| 能力 | 实现 | 实测 |
|---|---|---|
| 积分商城 | `WapPointGoods`/`WapPointOrder`/`WapPointLog` 三表；[point.php](file:///workspace/php/shop/point.php) 商品列表、[point_buy.php](file:///workspace/php/shop/point_buy.php) 事务兑换（条件更新防超卖超扣）、[point_orders.php](file:///workspace/php/shop/point_orders.php) 我的兑换记录；后台 [pointgoods.php](file:///workspace/php/admin/pointgoods.php) 商品增删改/上下架、[pointorder.php](file:///workspace/php/admin/pointorder.php) 发放/取消退分回补库存 | 兑换扣分/减库存/写订单与流水一致；积分不足与库存不足分别拦截；**10 并发兑换仅 1 单成功、未重复扣分**；取消兑换积分退回、库存回补、流水留痕 |
| 自定义敏感词库 | `WapFilter` 表 + [filter.php](file:///workspace/php/admin/filter.php) 批量新增/删除；`kl_has_sensitive` 扩展为内置词 + 站点自定义词双来源，发帖/回复命中即进审核队列 | 新增词"违禁测试"后发帖被隐藏并生成审核记录；删除词后恢复正常；内置词仍生效 |
| 社区基础能力 | `WapFollow`/`WapLike`/`WapNotice` 三表；[follow.php](file:///workspace/php/zone/follow.php)/[following.php](file:///workspace/php/zone/following.php)/[fans.php](file:///workspace/php/zone/fans.php) 关注体系、[like.php](file:///workspace/php/bbs/like.php) 主帖与回复点赞、[notice.php](file:///workspace/php/message/notice.php) 通知中心（回复/@提及/点赞/关注/好友）、[active.php](file:///workspace/php/bbs/active.php) 活跃榜 | 关注/取关与计数联动并产生通知；重复点赞不叠加、取消归零；未读/单条已读/全部已读正确；@提及与回复通知双触发；活跃分=发帖×5+回复×2+获赞×1 |
| 用户等级 | [Level.php](file:///workspace/php/includes/Level.php) 六档积分等级（0/100/300/800/2000/5000） | 11 个边界值断言全对，负分按 Lv1，满级 need=0；空间页/帖子/活跃榜/我的账户展示 |
| 内容审核队列 | [audit.php](file:///workspace/php/admin/audit.php) 待审/已通过/已拒绝/全部四视图 + 分页；单条与批量通过、驳回理由（写 `WapAuditLog`）并通知作者；`WapAudit` 无后台入口的缺口已补齐 | 敏感词发帖后帖子 `isshow=0` 且进入待审；通过后 `status=1` 且 `WapBbs.isshow=1`；拒绝后 `status=2`、内容保持隐藏、理由落日志并产生审核通知；后台首页显示待审角标 |
| 帖子标签话题 | `WapBbsTag` 表；发帖标签输入（逗号/空格/顿号分隔、去 `#`、字符白名单、去重、最多 3 个）；[tag.php](file:///workspace/php/bbs/tag.php) 话题聚合、[tags.php](file:///workspace/php/bbs/tags.php) 热门话题、[view.php](file:///workspace/php/bbs/view.php) 标签展示 | 发帖写入 3 个标签且自动去重；话题页按标签聚合、分页正常；待审（`isshow=0`）帖子不出现在话题与热门列表，无标签泄露 |
| 私信会话 | [sessions.php](file:///workspace/php/message/sessions.php) 按往来用户聚合会话（未读数、最后一条预览）；[chat.php](file:///workspace/php/message/chat.php) 一对一聊天（发送、进入即已读、最近 50 条）；[send.php](file:///workspace/php/message/send.php) 支持 `back=chat` 回跳 | 发送后消息入库并回跳会话；对方来信显示未读，打开会话后 `isread=1`；短信发送成功页正确回跳聊天页 |
| 用户拉黑 | `WapBlock` 表 + [block.php](file:///workspace/php/zone/block.php) 切换拉黑/取消与黑名单列表；[chat.php](file:///workspace/php/message/chat.php)、[send.php](file:///workspace/php/message/send.php)、[follow.php](file:///workspace/php/zone/follow.php) 双向拦截私信与关注，[book_re.php](file:///workspace/php/bbs/book_re.php) 拦截被拉黑者的 `@` 通知 | 拉黑后双方会话/私信/关注均被拦截；黑名单列表可取消拉黑，取消后会话恢复；被拉黑者 `@` 不产生通知 |
| 社区报表 | [community.php](file:///workspace/php/admin/community.php) 近 7 天/近 30 天/全部三档；新增会员、帖子、回复、文章、关注、点赞、私信、待审核、拉黑关系；每日趋势与发帖 Top10、热门标签 Top10 | 三种周期均 200 且数值与库内一致；趋势 7/30 天逐日列出；Top10 用户名与标签正确关联 |
| 通知中心增强 | `kl_notice_label_ext` 补充“审核”标签；[notice.php](file:///workspace/php/message/notice.php) 支持按回复/@提及/点赞/关注/好友/审核筛选（非法 type 兜底为全部），未读条目点击即标记已读并跳转来源（帖子/文章/用户空间/私信会话），分页保留筛选条件 | 类型筛选与非法 type 均 200；审核通知显示“审核”标签；未读点击后回跳对应帖子；仅未读与类型筛选可组合 |
| 作者自助中心 | [my.php](file:///workspace/php/bbs/my.php) 我的帖子/回复列表（待审/已发布/已隐藏状态、计数、分页），作者可自助隐藏（软删除）/恢复帖子、隐藏自己的回复；[admin_WAPmodify.php](file:///workspace/php/bbs/admin_WAPmodify.php) 与 [mod.php](file:///workspace/php/bbs/mod.php) 收紧越权：非作者不可编辑他人帖子，非管理员不可置顶/加精/锁帖；写操作清理站点缓存 | 发帖→我的帖子可见→隐藏后 `isshow=0`→恢复正常；回复隐藏后 `isshow=0`；user2 访问他人帖子返回“无权限修改此帖子”；作者触发置顶返回“仅管理员可执行该操作”；测试数据清理后归零 |
| 标签后台管理 | [tag.php](file:///workspace/php/admin/tag.php) 标签聚合列表（帖子数/可见数/最近时间）、搜索、分页；改名/合并（同帖已有目标标签时去重删除，事务提交）与删除；后台首页入口 | 2 个“临时甲”+1 个“临时乙”改名为“临时乙”后合并为 2 条（迁移 1、去重 1）；删除后 0 条；列表/搜索/分页均 200，测试数据清零 |
| 回归 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) 扩至 **263 项** | **263/263 PASS**，跑批后 WapAudit/WapAuditLog/WapBbsTag/WapBlock/WapMessage/WapFollow/WapLike/WapNotice 均为 0（零副作用）；全部 PHP 文件 `php -l` 零错误；MariaDB 双脚本重建 **79 表零错误** |

## 〇〇〇〇〇〇、v8 交付包完整性与首次安装验收

| 项 | 结果 |
|---|---|
| 交付包独立运行 | zip 解压至全新目录（脱离工作区），内置 tests/acceptance.sh 跑 **231/231 PASS**（脚本已参数化 BASE/PHP_ROOT） |
| 首次安装体验 | 删库模拟全新用户：安装向导(SQLite) → 69 表建成 → 首页/列表 200 → admin 登录后台正常 ✅ |
| 缺陷修复 | 安装向导 MySQL→SQLite 转换器未剥离**列级 COMMENT '...'**（v5 新增表引入）导致建表失败 → install/index.php 增加转换规则，复测通过 |
| 回归确认 | 修复后工作区 SQLite 复跑 **231/231 PASS** |

## 〇〇〇〇〇、v7 多站点端到端与浏览器视觉验收（历史版本，多站点已移除）

| 项 | 结果 |
|---|---|
| 建站申请（wapregsite） | 提交站名+管理员 → 自动创建站点+文章/论坛默认栏目+返回站点ID ✅ |
| 多站点隔离 | 站点4发文仅站点4列表可见；跨站 id 访问返回"不存在"；搜索/sitemap 按站点隔离 ✅ |
| 站点4全链路 | 登录→发文→列表→详情→搜索 ✅（测试数据已清理，恢复单站点交付态） |
| 浏览器视觉验收 | 首页（26 栏目导航+版权）、文章详情（UBB 正文+万能调用链接+评论入口）、后台登录（卡片式表单）3/3 PASS，无裸 PHP/错误文本/白屏 ✅ |

## 〇〇〇〇、v6 安全加固与质量核查

| 项 | 发现 → 处置 |
|---|---|
| 暴力破解 | KL_LoginTime 原声明未实现 → UserSession 登录失败锁定（15 分钟窗口，开关驱动，默认 0 不改变原行为） |
| 会话固定 | 登录成功无会话再生 → login/adminLogin 增加 session_regenerate_id(true)，实测 Cookie ID 变更 |
| 路径穿越 | down.php 本地文件拼接未封禁 → realpath 限制在 uploadfiles 内；上传/文件管理器此前已实测拒绝穿越 |
| SQL 注入 | 全站查询均走 PDO 预处理；动态表名仅来自模块白名单；checkSqlInject 覆盖各投稿/评论入口 |
| 并发压测 | 4 worker × 30 并发 × 300 请求（首页/列表/详情）：**300/300 全 200，日志零 Fatal/Warning/Notice**；基准 76.8 req/s（内置服务器，生产 php-fpm 可线性扩展） |
| 兼容性 | 无 8.1+ 专属语法（PHP 8.0+ 可运行）；GD 缺失时验证码自动降级返回 false；扩展依赖 pdo/gd/mbstring 已在 deploy/README 声明 |

## 〇〇〇、v5 长尾终审与交付验证

| 项 | 结果 |
|---|---|
| 长尾页面终审 | 对原版低频 unique aspx（≤9 次）逐类核查，发现并补齐 6 类缺口：系统设置页(admin/setting)、评论编辑(admin/reply)、会员CSV导出(admin/user)、GD验证码(Captcha.php+captcha_img.php+wapreg 接入)、小说章节增删改+TXT批量分章导入(novel/chapter_*/import)、帖子举报(bbs/report+admin/report+WapReport 表) |
| MySQL 全量复跑 | 重建库执行更新后 schema.sql（68 表含 WapReport/orderindex），144 行迁移，专用账号 PDO 连接，**231/231 PASS** |
| SQLite 复跑 | 回切开箱配置，**231/231 PASS**，250 个 PHP 文件 `php -l` 零错误 |
| 生产交付 | deploy/nginx.conf.example（伪静态/敏感目录封禁/WML 类型）+ deploy/README.md（环境要求/双模式部署/安全建议）+ 整包 zip |

## 〇、v4 页面级终审与模块管理页

| 项 | 结果 |
|---|---|
| 原版 aspx 文件名频次终审 | 逐类核查：admin_wapadd/modify/del/change/advertise、book_list/view/search/re、userlist、guestlist、systemmodel、orderlist、picdiy、share 等高频页面类型全部有 PHP 对应实现 |
| 补齐模块管理页 | 20 模块 × admin_WAPmodify/admin_WAPdel/admin_WAPchange 共 60 页（仿 article 模板，GET确认/POST删除、显隐切换、列表管理员操作链接），4 路并行实测 130+ 断言全过 |
| 全量回归 | 验收矩阵扩至 223 项，SQLite 模式 **223/223 PASS**，php -l 241 文件零错误 |

## 〇〇、v3 交付验证（MySQL 引擎 + 安装向导 + 目录终审）

| 项 | 结果 |
|---|---|
| MySQL 真实环境 | MariaDB 实机：schema.sql 全量 67 表零错误建立（修复 2 处重复定义、1 处 TEXT 索引），145 行数据迁移零错误，全系统跑通 163/163 |
| 安装向导 | MySQL/SQLite 双引擎实测：67/68 表建立+管理员种子；新增 SQLite 方言转换器（AUTO_INCREMENT/KEY/ENGINE/ON DUPLICATE 自动转换） |
| 原版目录终审 | 全部 62 个原版目录逐一对照映射完成；补齐 admin_WAPdelALL 批量删除（admin/content.php 勾选+删除选中，实测通过）；action/class/models/EditTool/HTML5/UBB_API 等目录功能均已有对应实现；PlugIn/Template/djs/NetCSS 等为静态资源目录，由 templates/ 与 assets/ 覆盖 |

## 〇、v2 增量验收（补齐原"待完善"项）

| 增量项 | 实现 | 实测 |
|---|---|---|
| 文件上传管理 | includes/Upload.php + admin/upload.php 文件管理器 + 文章附件/论坛图帖 | 上传/删除/白名单/超限/防穿越全通过 |
| 栏目级权限 | WapPage 基类自动守卫投稿/评论页 + usergroup/postgroup/managegroup 后台配置 | 分组拒绝/放行矩阵 7 场景全对，21 模块零改动 |
| 支付宝/微信支付 | PayGateway.php：RSA2 签名跳转 + APIv3 下单/AES-256-GCM 解密，notify 幂等结算 | 注入测试密钥 24 项签名/验签/解密 CLI 全过 |
| 微信正式对接 | access_token 缓存、菜单创建、关注事件自动注册会员 | 原桩行为零回归，isopen=1 时自动注册验证通过 |
| QQ 登录正式流程 | 真实 OAuth 流程(authorize→token→openid→userinfo)+ 账号绑定 | 开关开 Location 参数正确，绑定/登录链路通过 |
| WAP1.0 WML | ver=1 全链路合法 WML(XML声明/DOCTYPE/转义/标签降级)，UA 自动识别修复 | simplexml 校验通过，ver2/3/4 基线逐字节一致 |

## 一、总体指标

| 指标 | 原版 ASP.NET | PHP 复刻版 | 结果 |
|---|---|---|---|
| 页面/文件 | 1499 个 .aspx（逻辑在 38 个 DLL） | 181 个 .php（逻辑直接实现） | 功能点全覆盖 |
| 数据表 | SQL Server | 68 张表（MySQL/SQLServer/SQLite 三库兼容） | 全覆盖 |
| 站点栏目(模块入口) | 数据驱动 | 32 个栏目/27 个模块 | 全覆盖 |
| 版权信息 | — | 全部为「小宇网络」，无旧品牌残留 | ✅ |
| 语法检查 | — | 181 文件 `php -l` 全通过 | ✅ |
| 自动化验收 | — | 163 项 URL/登录态/WML 检查全通过（tests/acceptance.sh 可复跑） | ✅ |

## 二、模块覆盖矩阵（原版目录 → PHP 实现）

| 原版目录(页数) | PHP 实现 | 状态 |
|---|---|---|
| 21 个内容模块(article/bbs/shop/novel/video/picture/download/ring/link/guessbook/gongqiu/paimai/yuehui/wabao/guangbo/adlink/airplane/album/hotel/rizhi/xinzhang) | 各模块 list/view/book_re/admin_WAPadd(+order等) | ✅ 全通 |
| bbs 高级功能(186页) | mod.php管理/vote.php投票/fav收藏 + 发帖回帖 | ✅ |
| Admin(128) | admin/ 12页：站点/栏目/会员/内容审核/评论/在线/公告/点卡/采集/登录退出 | ✅ |
| games(137) | games/ 列表/排行/成绩 + airplane 模块 | ✅ |
| clan(27) | clan/ 列表/详情/创建/加入/家族发言 | ✅ |
| chinabank(38) | shop/pay.php 余额+点卡支付、myaccount 流水 | ✅(模拟网关) |
| WeiXin(25) | weixin.php 回调验签/消息入库/文本应答 | ✅(对接框架) |
| chat(15) | chat/ 聊天室+发言 | ✅ |
| Signin(8) | signin/ 每日签到+连续奖励 | ✅ |
| SMS(6) | sms.php 发送+记录+限量 | ✅ |
| sysmessage(5) | message/ 收件箱/发件箱/发送/删除/未读数 | ✅ |
| myAccount(10) | myaccount/ 余额/流水/点卡充值 | ✅ |
| card(6) | admin/card.php 批量生成/作废 + 支付核销 | ✅ |
| OAuth(2) | oauth/ QQ登录流程 | ✅(模拟回调) |
| visiteCount(24) | WapOnline/WapVisite 自动记录 + webrank 排行 | ✅ |
| WapStyle(38) | templates/default、touch 两套 + list.php，站点字段切换 | ✅ |
| FormEdit(21) | form/ 动态表单渲染/提交/数据查看 | ✅ |
| PageModel(16) | page.php 自定义页(WapPageTpl) | ✅ |
| NongChang | nongchang/ 播种/生长/收获/金币经验 | ✅ |
| ZhiDao | zhdao/ 提问/回答/采纳/排行 | ✅ |
| DomainName(4) | WapDomain 表 + admin/site.php 域名编辑 | ✅ |
| caiji | admin/caiji.php 采集源管理/触发 | ✅ |
| Utility(30) | utility/ MD5工具/UBB预览/系统信息 + ajax_ubb.php | ✅ |
| WEB(29)/顶层营销页 | index/webad/webreg/webmodel/webcallme/webrank/webhelp/webnews | ✅ |
| WML(19) | WapTool ver=1 WML 渲染分支 | ✅(基础) |
| search(7) | search.php 全站搜索 | ✅ |
| Novel(46) | novel/ 列表/详情/章节阅读(cid) | ✅ |
| User(2)/Zone | zone/ 会员空间+留言、friend/ 好友 | ✅ |
| 原版根页面 | default/wapregsite(建站注册)/wapgetpw(找回密码)/cache/ajax_ubb/t.php(短链)/sitemap/robots | ✅ |

## 三、功能链路实测（登录态 POST 验证）

1. **商城交易**：加购→结算下单(事务)→点卡 XY20260001 充值→余额支付成功(库存/余额/流水一致)→重复支付被拒→直购刷卡→错误密码被拒→取消订单
2. **论坛**：发帖→回帖→作者编辑/置顶/删除→他人越权删除被拒→投票(一人一票)→收藏→加精
3. **社交**：注册登录→签到(+10分,重复拒绝)→站内短信(未读/已读/删除)→加好友(重复拒绝)→空间留言
4. **家族/游戏/问答/农场/表单**：建族→加入→家族发言；成绩提交→排行显示；提问→回答→状态变更；播种→成熟→收获；动态表单→提交→管理员查看
5. **后台**：admin 登录→栏目/公告/会员/内容/评论增删改→点卡生成/作废→退出后越权访问 302
6. **框架能力**：万能调用 `[功能模块=3_3_1_0]` 在文章页渲染出商品列表；在线人数实时统计；模板 CSS 按站点 template 字段加载；短链 t.php 301 跳转；sitemap 输出合法 XML；微信 GET 验签回显/POST 消息入库应答

## 四、上线前待办（代码已就绪，仅差外部凭据）

- 支付宝/微信/QQ 互联：正式商户与开放平台凭据填入 config.php 并将对应 KL_*_Open 置 1 即启用真实通道（签名/验签/回调/幂等已实测；未启用时自动回退余额/点卡/模拟通道）
- 微信公众平台：WapWeixin 表填入 appid/secret 并 isopen=1 即启用 token 缓存/菜单/关注注册

## 五、部署说明

- 开箱即用：`php -S 0.0.0.0:8080`（config.php KL_DB_TYPE=sqlite，数据在 data/sql444001.db）
- MySQL 交付：KL_DB_TYPE 改 'mysql'，执行 install/schema.sql（68 表全量）或访问 install/ 向导
- 账号：站点管理员 admin/admin123（交付库为全新空库，不含测试会员）

Copyright (C) 小宇网络 All Rights Reserved.
