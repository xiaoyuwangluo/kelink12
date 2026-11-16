# 小宇网络 WAP建站系统 PHP版 — 复刻完成度验收报告

验收日期：2026-11-16　　验收方式：自动化全量 URL 矩阵测试 + 功能链路实测 + 双数据库引擎验证 + 安全/压测/兼容性核查
验收结论：**通过（独立建站版 SQLite 289/289；全部 PHP 文件 php -l 零错误；SQLite 新库建表零错误）**
交付包：`/workspace/xiaoyu-wap-php-v1.0.zip`

## 独立建站版改造（当前交付）

| 项 | 结果 |
|---|---|
| 站群功能移除 | 删除 `admin/site.php`（站点管理）、`wapregsite.php`（建站申请）、`webreg.php`（注册站长）、`webrank.php`（站点PV排行）及相关导航/统计入口 |
| 数据模型 | 全库移除 `siteid` 列与含 `siteid` 的索引/唯一键；`WapSite` 保留为单行站点配置表（id=1） |
| URL / SQL | 全站移除 `siteid` 查询参数、SQL 条件、INSERT 列与绑定参数；`WapPage` / `WapTool` / `Community` 全部单站化 |
| 在线与流量统计 | `WapOnline` / `WapVisite` / `WapVisiteLog` 去掉 siteid，按全站统计 |
| 验收 | [acceptance.sh](file:///workspace/php/tests/acceptance.sh) **289/289 PASS**（移除 4 个已删除页面用例，新增商城订单、商品库存、数据导出、会员管理、栏目管理与内容管理用例）；SQLite 新库 `schema.sql` + `schema_community.sql` 共 80+12 表导入零错误 |
| 交付数据库 | `data/sql444001.db` 为全新空库：仅 `admin/admin123` 与默认站点配置，无测试会员、无内容数据 |

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
