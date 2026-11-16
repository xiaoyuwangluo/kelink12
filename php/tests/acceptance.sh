#!/bin/bash
# 小宇网络 WAP建站系统 PHP版 全量验收测试
BASE=${BASE:-http://127.0.0.1:8110}
JAR=/tmp/acc_cookie.txt
CJ=/tmp/acc_admin.txt
PASS=0; FAIL=0; FAILED_LIST=""

check() { # check NAME URL
  body=$(curl -s -b "$JAR" -c "$JAR" -L "$2")
  code=$(curl -s -o /dev/null -w '%{http_code}' -b "$JAR" -c "$JAR" -L "$2")
  err=$(echo "$body" | grep -c 'Fatal error\|Parse error\|Uncaught')
  if [ "$code" = "200" ] && [ "$err" = "0" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL $1 [$code err=$err] $2"; fi
}
check_admin() {
  body=$(curl -s -b "$CJ" -c "$CJ" -L "$2")
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

# 商城/支付/账户
for p in "shop/cart.php" "shop/checkout.php?shopid=1" "shop/pay.php?orderid=1" "shop/myorder.php" "shop/myorder.php?orderid=1" "paimai/bid.php?id=1" "myaccount/index.php"; do
  check "shop:$p" "$BASE/$p"
done

# 后台
for p in "admin/index.php" "admin/class.php" "admin/user.php" "admin/content.php?module=article" "admin/content.php?module=bbs" "admin/reply.php" "admin/online.php" "admin/sysmsg.php" "admin/card.php" "admin/logout.php"; do
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
check_admin "admin:setting" "$BASE/admin/setting.php"
check_admin "admin:report" "$BASE/admin/report.php"
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
check_admin "admin:caiji" "$BASE/admin/caiji.php"
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

echo "=============================="
echo "PASS=$PASS FAIL=$FAIL"
[ -n "$FAILED_LIST" ] && echo -e "失败明细:$FAILED_LIST"
