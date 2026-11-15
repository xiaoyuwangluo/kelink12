# 生产部署说明（小宇网络 WAP建站系统 PHP版）

## 环境要求
- PHP >= 8.0，扩展：pdo_sqlite（SQLite 模式）或 pdo_mysql（MySQL 模式）、gd（验证码）、mbstring
- Web 服务器：Apache（用自带 .htaccess）或 Nginx（用 deploy/nginx.conf.example）

## 快速部署（SQLite 开箱即用）
1. 上传整个 `php/` 目录到站点根目录
2. 确认 `config.php` 中 `KL_DB_TYPE` 为 `sqlite`
3. 赋予写权限：`chmod -R 755 data uploadfiles cache`（或 chown 到 www 用户）
4. 访问 `wapindex.php?siteid=1`

## MySQL 部署
1. 建库：`CREATE DATABASE sql444001 DEFAULT CHARSET utf8mb4;`
2. 修改 `config.php`：`KL_DB_TYPE` 改为 `mysql`，填写服务器/用户/密码/库名
3. 访问 `install/index.php` 走安装向导（自动执行 schema.sql 建 67 表）
4. 安装完成后删除或改名 `install/` 目录

## 安全建议
- 生产环境将 `config.php` 的 `KL_ShowAllError` 设为 0
- Nginx 按样例 deny 掉 `data/includes/install/cache/tests`
- 默认账号 `admin/admin123` 部署后立即修改
- SQLite 数据库文件 `data/*.db` 必须禁止 web 访问

## 伪静态
- Apache：`.htaccess` 已内置全部模块规则，需 `AllowOverride All` + `mod_rewrite`
- Nginx：复制 `deploy/nginx.conf.example` 中 rewrite 段

## 目录权限
| 目录 | 要求 |
|---|---|
| data/ | 可写（SQLite） |
| uploadfiles/ | 可写（上传） |
| cache/ | 可写（缓存） |
| config.php | 建议 640 |

## 回归测试
`bash tests/acceptance.sh`（需本机起服务，默认 BASE=http://127.0.0.1:8110，223 项）
