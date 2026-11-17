#!/bin/bash
# 小宇网络 WAP建站系统 PHP版 全量验收测试
BASE=${BASE:-http://127.0.0.1:8110}
JAR=/tmp/acc_cookie.txt
CJ=/tmp/acc_admin.txt
PASS=0; FAIL=0; FAILED_LIST=""

check() { # check NAME URL
  body=$(curl -s -b "$JAR" -c "$JAR" -L "$2" | tr -d '\0')
  code=$(curl -s -o /dev/null -w '%{http_code}' -b "$JAR" -c "$JAR" -L "$2")
  err=$(echo "$body" | grep -c 'Fatal error\|Parse error\|Uncaught')
  if [ "$code" = "200" ] && [ "$err" = "0" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL $1 [$code err=$err] $2"; fi
}
check_admin() {
  body=$(curl -s -b "$CJ" -c "$CJ" -L "$2" | tr -d '\0')
  code=$(curl -s -o /dev/null -w '%{http_code}' -b "$CJ" -c "$CJ" -L "$2")
  err=$(echo "$body" | grep -c 'Fatal error\|Parse error\|Uncaught')
  if [ "$code" = "200" ] && [ "$err" = "0" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL $1 [$code err=$err] $2"; fi
}

echo "== 登录会员 user2 =="
curl -s -c "$JAR" -b "$JAR" -o /dev/null "$BASE/waplogin.php"
curl -s -c "$JAR" -b "$JAR" -o /dev/null -X POST -d 'username=user2&password=123456' "$BASE/waplogin.php"
echo "== 登录管理员 =="
curl -s -c "$CJ" -b "$CJ" -o /dev/null -X POST -d 'username=admin&password=admin123' "$BASE/admin/login.php"

# 根页面
for p in index.php wapindex.php waplogin.php wapreg.php myfile.php search.php version.php webad.php webmodel.php webcallme.php default.php wapgetpw.php webhelp.php webnews.php cache.php weixin.php sms.php page.php sitemap.php; do
  check "root:$p" "$BASE/$p"
done
# t.php 短链：有效code应301/302跳转
tcode=$(python3 -c 'import urllib.parse;print(urllib.parse.quote("网址导航测试内容"))')
tcode1=$(curl -s -o /dev/null -w '%{http_code}' "$BASE/t.php?code=$tcode")
if [ "$tcode1" = "301" ] || [ "$tcode1" = "302" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL t.php短链 [$tcode1]"; fi
check "ajax_ubb" "$BASE/ajax_ubb.php"

# 21+ 内容模块 list/view
for m in article bbs shop novel video picture download ring link guessbook gongqiu paimai yuehui wabao guangbo adlink airplane album hotel rizhi xinzhang; do
  check "$m/list" "$BASE/$m/list.php?classid=1&id=1"
  check "$m/view" "$BASE/$m/view.php?id=1"
  check "$m/book_re" "$BASE/$m/book_re.php?id=1"
  check "$m/admin_WAPadd" "$BASE/$m/admin_WAPadd.php"
done
check "novel/chapter" "$BASE/novel/view.php?id=1&cid=1"
check "article/index" "$BASE/article/index.php"

# 新模块
for m in chat clan games zhdao nongchang signin form; do
  check "$m/index" "$BASE/$m/index.php"
done
for p in "clan/view.php?id=1" "clan/chat.php?id=1" "games/rank.php?gameid=1" "zhdao/view.php?id=1" "zhdao/ask.php" "form/view.php?id=1" "nongchang/plant.php?id=1" "nongchang/harvest.php?id=1"; do
  check "newmod:$p" "$BASE/$p"
done

# 用户深度功能
for p in "bbs/mod.php?id=1" "bbs/vote.php?id=1" "bbs/fav.php?module=bbs&contentid=1" "bbs/favlist.php" "bbs/my.php" "bbs/my.php?type=replies" "message/index.php" "message/send.php" "message/view.php?id=1" "friend/index.php" "friend/add.php?fid=1" "zone/index.php?uid=1" "zone/msg.php?uid=1"; do
  check "user:$p" "$BASE/$p"
done
# 站内消息增强：状态筛选 / 关键词搜索 / 非法状态回落
check "msg:inbox unread" "$BASE/message/index.php?box=in&status=unread"
check "msg:inbox search" "$BASE/message/index.php?box=in&q=%E7%A7%81%E4%BF%A1"
check "msg:outbox read" "$BASE/message/index.php?box=out&status=read"
check "msg:bad status" "$BASE/message/index.php?box=in&status=bad&q=%E7%A7%81%E4%BF%A1"
# 好友列表增强：用户名/昵称搜索 / 无结果
check "friend:search username" "$BASE/friend/index.php?q=user1"
check "friend:search nickname" "$BASE/friend/index.php?q=%E4%BC%9A%E5%91%98%E4%B8%89"
check "friend:search none" "$BASE/friend/index.php?q=zzz"

# 商城/支付/账户
check "shop:view stock" "$BASE/shop/view.php?id=2"
check "shop:view offshelf" "$BASE/shop/view.php?id=3"
check "shop:list sort" "$BASE/shop/list.php?classid=1&sort=price_asc"
check "shop:list search" "$BASE/shop/list.php?classid=1&q=%E6%B5%8B%E8%AF%95"
check "shop:list bad price" "$BASE/shop/list.php?classid=1&minprice=abc"
check "shop:cart msg" "$BASE/shop/cart.php?msg=ok"
check "shop:checkout no item" "$BASE/shop/checkout.php?shopid=999"
check "shop:checkout bad ids" "$BASE/shop/checkout.php?ids=99999"
for p in "shop/cart.php" "shop/checkout.php?shopid=1" "shop/pay.php?orderid=1" "shop/myorder.php" "shop/myorder.php?status=0" "shop/myorder.php?status=1" "shop/myorder.php?orderid=1" "shop/myorder.php?orderid=2" "paimai/bid.php?id=1" "myaccount/index.php" "myaccount/index.php?type=in" "myaccount/index.php?type=out"; do
  check "shop:$p" "$BASE/$p"
done
# 订单列表增强：搜索 / 排序
check "shop:myorder search" "$BASE/shop/myorder.php?q=%E6%B5%8B%E8%AF%95"
check "shop:myorder search id" "$BASE/shop/myorder.php?q=2"
check "shop:myorder sort amount_desc" "$BASE/shop/myorder.php?sort=amount_desc"
check "shop:myorder status+sort" "$BASE/shop/myorder.php?status=1&sort=amount_asc"
# 直购页增强：在售/库存校验
check "shop:order normal" "$BASE/shop/order.php?id=1"
check "shop:order offshelf" "$BASE/shop/order.php?id=3"
check "shop:order missing" "$BASE/shop/order.php?id=999"
check "shop:order num over stock" "$BASE/shop/order.php?id=2&num=5"
check "shop:order num bad" "$BASE/shop/order.php?id=1&num=-3"

# 后台
for p in "admin/index.php" "admin/class.php" "admin/user.php" "admin/content.php?module=article" "admin/content.php?module=bbs" "admin/reply.php" "admin/online.php" "admin/sysmsg.php" "admin/card.php"; do
  check_admin "admin:$p" "$BASE/$p"
done
# 商城订单后台
check_admin "admin:shoporder" "$BASE/admin/shoporder.php"
check_admin "admin:shoporder status=0" "$BASE/admin/shoporder.php?status=0"
check_admin "admin:shoporder status=1" "$BASE/admin/shoporder.php?status=1"
check_admin "admin:shoporder status=2" "$BASE/admin/shoporder.php?status=2"
check_admin "admin:shoporder status=-2" "$BASE/admin/shoporder.php?status=-2"
check_admin "admin:shoporder search" "$BASE/admin/shoporder.php?q=13800001111"
# 商品库存后台
check_admin "admin:goods" "$BASE/admin/goods.php"
check_admin "admin:goods low" "$BASE/admin/goods.php?low=1"
check_admin "admin:goods offshelf" "$BASE/admin/goods.php?status=offshelf"
check_admin "admin:goods search" "$BASE/admin/goods.php?q=test"
check_admin "admin:goods page" "$BASE/admin/goods.php?page=2"
check_admin "admin:goods add form" "$BASE/admin/goods.php?act=add"
check_admin "admin:goods edit form" "$BASE/admin/goods.php?act=edit&id=1"
check_admin "admin:goods del confirm" "$BASE/admin/goods.php?act=del&id=1"
# 会员管理增强
check_admin "admin:user search" "$BASE/admin/user.php?q=user2"
check_admin "admin:user locked" "$BASE/admin/user.php?status=locked"
check_admin "admin:user del confirm" "$BASE/admin/user.php?act=del&id=1"
# 栏目管理增强
check_admin "admin:class module filter" "$BASE/admin/class.php?module=shop"
check_admin "admin:class search" "$BASE/admin/class.php?q=%E6%96%87%E7%AB%A0"
check_admin "admin:class del confirm" "$BASE/admin/class.php?act=del&id=2"
# 内容管理增强
check_admin "admin:content search" "$BASE/admin/content.php?module=shop&q=%E6%B5%8B%E8%AF%95%E5%95%86%E5%93%81"
check_admin "admin:content class" "$BASE/admin/content.php?module=shop&classid=1"
check_admin "admin:content hidden" "$BASE/admin/content.php?module=shop&status=hidden"
check_admin "admin:content del confirm" "$BASE/admin/content.php?module=shop&act=del&id=1"
# 评论审核增强
check_admin "admin:reply search" "$BASE/admin/reply.php?q=%E8%AF%84%E8%AE%BA"
check_admin "admin:reply hidden" "$BASE/admin/reply.php?status=hidden"
check_admin "admin:reply bbs hidden" "$BASE/admin/reply.php?tab=bbs&status=hidden"
check_admin "admin:reply del confirm" "$BASE/admin/reply.php?tab=wap&act=del&id=1"
# 充值卡管理增强
check_admin "admin:card search" "$BASE/admin/card.php?q=123456"
check_admin "admin:card used" "$BASE/admin/card.php?status=used"
check_admin "admin:card void confirm" "$BASE/admin/card.php?act=void&id=1"
# 在线用户增强
check_admin "admin:online search" "$BASE/admin/online.php?q=127.0.0.1"
check_admin "admin:online member" "$BASE/admin/online.php?type=member"
check_admin "admin:online kick confirm" "$BASE/admin/online.php?act=kick&id=1"
# 系统公告增强
check_admin "admin:sysmsg search" "$BASE/admin/sysmsg.php?q=%E5%85%AC%E5%91%8A"
check_admin "admin:sysmsg page" "$BASE/admin/sysmsg.php?page=1"
check_admin "admin:sysmsg del confirm" "$BASE/admin/sysmsg.php?act=del&id=1"
# 运营数据导出 CSV
check_admin "admin:export orders" "$BASE/admin/export.php?type=order"
check_admin "admin:export orders status" "$BASE/admin/export.php?type=order&status=1"
check_admin "admin:export goods" "$BASE/admin/export.php?type=goods"
check_admin "admin:export goods low" "$BASE/admin/export.php?type=goods&low=1"

# OAuth / 模板
check "oauth" "$BASE/oauth/index.php"
check "templates" "$BASE/templates/list.php"

# v4 每模块管理页 (modify/del/change)
cd "${PHP_ROOT:-/workspace/php}"
for m in bbs shop novel video picture download ring link guessbook gongqiu paimai yuehui wabao guangbo adlink airplane album hotel rizhi xinzhang; do
  for p in admin_WAPmodify admin_WAPdel admin_WAPchange; do
    if [ -f "$m/$p.php" ]; then
      check_admin "$m/$p" "$BASE/$m/$p.php?id=1"
    fi
  done
done

# v5 长尾补齐页面
check_admin "admin:user export normal" "$BASE/admin/user.php?status=normal&action=export"
check_admin "admin:user export locked" "$BASE/admin/user.php?status=locked&action=export"
check_admin "admin:user export search" "$BASE/admin/user.php?action=export&q=user2"
check_admin "admin:setting group" "$BASE/admin/setting.php?group=%E5%AE%89%E5%85%A8"
check_admin "admin:setting search" "$BASE/admin/setting.php?q=KL_OpenCache"
check_admin "admin:setting" "$BASE/admin/setting.php"
check_admin "admin:backup" "$BASE/admin/backup.php"
check_admin "admin:backup check" "$BASE/admin/backup.php?action=check"
check_admin "admin:backup download" "$BASE/admin/backup.php?action=download"
check_admin "admin:report search" "$BASE/admin/report.php?q=%E5%B9%BF%E5%91%8A"
check_admin "admin:report handled" "$BASE/admin/report.php?status=handled"
check_admin "admin:report del confirm" "$BASE/admin/report.php?act=del&id=1"
check_admin "novel:chapter_add" "$BASE/novel/chapter_add.php?novelid=1"
check_admin "novel:import" "$BASE/novel/import.php?novelid=1"
check "novel:chapter_mod" "$BASE/novel/chapter_mod.php?id=1"
check "novel:chapter_del" "$BASE/novel/chapter_del.php?id=1"
check "bbs:report" "$BASE/bbs/report.php?id=1"
code=$(curl -s -o /dev/null -w '%{http_code}' "$BASE/captcha_img.php")
ctype=$(curl -s -I "$BASE/captcha_img.php" | grep -ci 'image/png')
if [ "$code" = "200" ] && [ "$ctype" -ge 1 ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL captcha_img [$code]"; fi

# v2 新增页面
check_admin "admin:upload" "$BASE/admin/upload.php"
check_admin "admin:upload image" "$BASE/admin/upload.php?type=image"
check_admin "admin:upload search" "$BASE/admin/upload.php?q=test_b"
check_admin "admin:upload del confirm" "$BASE/admin/upload.php?act=del&file=test_c.zip"
check_admin "admin:caiji" "$BASE/admin/caiji.php"
check_admin "admin:caiji search" "$BASE/admin/caiji.php?q=%E6%B5%8B%E8%AF%95"
check_admin "admin:caiji del confirm" "$BASE/admin/caiji.php?act=del&id=1"
check "utility" "$BASE/utility/index.php"
for n in notify_ali notify_wx; do
  b=$(curl -s "$BASE/shop/$n.php"); e=$(echo "$b" | grep -c 'Fatal error\|Warning:\|Notice:')
  if [ "$e" = "0" ] && echo "$b" | grep -qi 'FAIL\|SUCCESS\|fail'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL pay:$n body=$b"; fi
done
check "weixin:get" "$BASE/weixin.php?echostr=hello123&signature=x&timestamp=1&nonce=1"
# WAP1.0 WML 校验
wml=$(curl -s -A 'Mozilla/5.0 (SAMSUNG; series60)' "$BASE/wapindex.php")
if echo "$wml" | head -1 | grep -q '<?xml'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL wml:wapindex"; fi
wml2=$(curl -s "$BASE/article/list.php?classid=1&ver=1")
if echo "$wml2" | head -1 | grep -q '<?xml'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL wml:article_list"; fi

# v6 积分商城 / 敏感词后台（只读检查）
check "shop:point" "$BASE/shop/point.php"
check "shop:point search" "$BASE/shop/point.php?q=%E7%A7%AF%E5%88%86"
check "shop:point sort points_asc" "$BASE/shop/point.php?sort=points_asc"
check "shop:point points range" "$BASE/shop/point.php?minpoints=10&maxpoints=100"
check "shop:point bad range" "$BASE/shop/point.php?minpoints=abc&maxpoints=xyz"
check "shop:point_orders" "$BASE/shop/point_orders.php"
check "shop:point_orders status=0" "$BASE/shop/point_orders.php?status=0"
check "shop:point_orders status=1" "$BASE/shop/point_orders.php?status=1"
check "shop:point_orders status=2" "$BASE/shop/point_orders.php?status=2"
check_admin "admin:pointgoods" "$BASE/admin/pointgoods.php"
check_admin "admin:pointorder" "$BASE/admin/pointorder.php"
check_admin "admin:filter" "$BASE/admin/filter.php"
check_admin "admin:audit-pending" "$BASE/admin/audit.php?status=pending"
check_admin "admin:audit-passed" "$BASE/admin/audit.php?status=passed"
check_admin "admin:audit-rejected" "$BASE/admin/audit.php?status=rejected"
check_admin "admin:audit-all" "$BASE/admin/audit.php?status=all"
check_admin "admin:community7" "$BASE/admin/community.php?days=7"
check_admin "admin:community30" "$BASE/admin/community.php?days=30"
check_admin "admin:communityAll" "$BASE/admin/community.php?days=all"
check_admin "admin:tag" "$BASE/admin/tag.php"
check_admin "admin:tag search" "$BASE/admin/tag.php?q=test"
check_admin "admin:tag page" "$BASE/admin/tag.php?page=2"

# v7 社区基础能力（只读检查，不写数据）
check "zone:following" "$BASE/zone/following.php"
check "zone:fans" "$BASE/zone/fans.php?uid=1"
check "message:notice" "$BASE/message/notice.php"
check "message:notice unread" "$BASE/message/notice.php?only=unread"
check "message:notice type reply" "$BASE/message/notice.php?type=reply"
check "message:notice type audit" "$BASE/message/notice.php?type=audit"
check "message:notice type guard" "$BASE/message/notice.php?type=bad%20type"
check "message:notice type order" "$BASE/message/notice.php?type=order"
check "message:sessions" "$BASE/message/sessions.php"
check "message:chat" "$BASE/message/chat.php?uid=1"
check "bbs:active" "$BASE/bbs/active.php"
check "bbs:tags" "$BASE/bbs/tags.php"
check "bbs:tag guard" "$BASE/bbs/tag.php?name=bad%20name"
check "bbs:like guard" "$BASE/bbs/like.php?id=999999"
check "zone:follow guard" "$BASE/zone/follow.php?uid=2"
check "zone:block list" "$BASE/zone/block.php"
check "zone:block guard" "$BASE/zone/block.php?uid=2"

# v48 UI 设计底座与后台外壳（结构断言）
ui_admin=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/index.php")
if echo "$ui_admin" | grep -q 'class="admin-shell"' && echo "$ui_admin" | grep -q 'assets/css/admin.css'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v48:admin shell"
fi
ui_front=$(curl -s -b "$JAR" -c "$JAR" -L "$BASE/friend/index.php")
if echo "$ui_front" | grep -q 'assets/css/tokens.css' && echo "$ui_front" | grep -q 'assets/css/components.css'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v48:front tokens"
fi
# 管理员退出放在最后，避免提前销毁后台会话
ui_user=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/user.php")
if echo "$ui_user" | grep -q 'class="admin-shell"' && echo "$ui_user" | grep -q '会员管理'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v49:admin user shell"
fi
ui_goods=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/goods.php")
if echo "$ui_goods" | grep -q 'class="admin-shell"' && echo "$ui_goods" | grep -q 'class="subtitle">商品库存'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v50:admin goods shell"
fi
ui_so=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/shoporder.php")
if echo "$ui_so" | grep -q 'class="admin-shell"' && echo "$ui_so" | grep -q 'class="subtitle">商城订单'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v51:admin shoporder shell"
fi
ui_reply=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/reply.php")
if echo "$ui_reply" | grep -q 'class="admin-shell"' && echo "$ui_reply" | grep -q 'class="subtitle">评论审核'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v52:admin reply shell"
fi
ui_content=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/content.php?module=article")
if echo "$ui_content" | grep -q 'class="admin-shell"' && echo "$ui_content" | grep -q 'class="subtitle">内容管理'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v53:admin content shell"
fi
ui_class=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/class.php")
if echo "$ui_class" | grep -q 'class="admin-shell"' && echo "$ui_class" | grep -q '>栏目管理<'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v54:admin class shell"
fi
ui_setting=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/setting.php")
if echo "$ui_setting" | grep -q 'class="admin-shell"' && echo "$ui_setting" | grep -q 'class="subtitle">系统设置'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v54:admin setting shell"
fi
ui_audit=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/audit.php")
if echo "$ui_audit" | grep -q 'class="admin-shell"' && echo "$ui_audit" | grep -q 'class="subtitle">内容审核'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v55:admin audit shell"
fi
ui_tag=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/tag.php")
if echo "$ui_tag" | grep -q 'class="admin-shell"' && echo "$ui_tag" | grep -q 'class="subtitle">帖子标签管理'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v55:admin tag shell"
fi
ui_filter=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/filter.php")
if echo "$ui_filter" | grep -q 'class="admin-shell"' && echo "$ui_filter" | grep -q 'class="subtitle">敏感词库'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v56:admin filter shell"
fi
ui_online=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/online.php")
if echo "$ui_online" | grep -q 'class="admin-shell"' && echo "$ui_online" | grep -q 'class="subtitle">在线用户'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v56:admin online shell"
fi
ui_comm=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/community.php")
if echo "$ui_comm" | grep -q 'class="admin-shell"' && echo "$ui_comm" | grep -q '社区运营报表'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v57:admin community shell"
fi
ui_sysmsg=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/sysmsg.php")
if echo "$ui_sysmsg" | grep -q 'class="admin-shell"' && echo "$ui_sysmsg" | grep -q 'class="subtitle">系统公告'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v57:admin sysmsg shell"
fi
ui_pg=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/pointgoods.php")
if echo "$ui_pg" | grep -q 'class="admin-shell"' && echo "$ui_pg" | grep -q 'class="subtitle">积分商品管理'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v58:admin pointgoods shell"
fi
ui_po=$(curl -s -b "$CJ" -c "$CJ" -L "$BASE/admin/pointorder.php")
if echo "$ui_po" | grep -q 'class="admin-shell"' && echo "$ui_po" | grep -q 'class="subtitle">积分兑换订单'; then
  PASS=$((PASS+1))
else
  FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL v58:admin pointorder shell"
fi
check_admin "admin:logout" "$BASE/admin/logout.php"

echo "=============================="
echo "PASS=$PASS FAIL=$FAIL"
[ -n "$FAILED_LIST" ] && echo -e "失败明细:$FAILED_LIST"
