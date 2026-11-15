#!/bin/bash
# 小宇网络 WAP建站系统 PHP版 全量验收测试
BASE=http://127.0.0.1:8110
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
curl -s -c "$JAR" -b "$JAR" -o /dev/null "$BASE/waplogin.php?siteid=1"
curl -s -c "$JAR" -b "$JAR" -o /dev/null -X POST -d 'username=user2&password=123456&siteid=1' "$BASE/waplogin.php"
echo "== 登录管理员 =="
curl -s -c "$CJ" -b "$CJ" -o /dev/null -X POST -d 'username=admin&password=admin123' "$BASE/admin/login.php"

# 根页面
for p in index.php wapindex.php waplogin.php wapreg.php myfile.php search.php version.php webad.php webreg.php webmodel.php webcallme.php default.php wapregsite.php wapgetpw.php webhelp.php webnews.php webrank.php cache.php weixin.php sms.php page.php sitemap.php; do
  check "root:$p" "$BASE/$p?siteid=1"
done
# t.php 短链：有效code应301/302跳转
tcode=$(python3 -c 'import urllib.parse;print(urllib.parse.quote("网址导航测试内容"))')
tcode1=$(curl -s -o /dev/null -w '%{http_code}' "$BASE/t.php?code=$tcode")
if [ "$tcode1" = "301" ] || [ "$tcode1" = "302" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL t.php短链 [$tcode1]"; fi
check "ajax_ubb" "$BASE/ajax_ubb.php"

# 21+ 内容模块 list/view
for m in article bbs shop novel video picture download ring link guessbook gongqiu paimai yuehui wabao guangbo adlink airplane album hotel rizhi xinzhang; do
  check "$m/list" "$BASE/$m/list.php?siteid=1&classid=1&id=1"
  check "$m/view" "$BASE/$m/view.php?siteid=1&id=1"
  check "$m/book_re" "$BASE/$m/book_re.php?siteid=1&id=1"
  check "$m/admin_WAPadd" "$BASE/$m/admin_WAPadd.php?siteid=1"
done
check "novel/chapter" "$BASE/novel/view.php?siteid=1&id=1&cid=1"
check "article/index" "$BASE/article/index.php?siteid=1"

# 新模块
for m in chat clan games zhdao nongchang signin form; do
  check "$m/index" "$BASE/$m/index.php?siteid=1"
done
for p in "clan/view.php?siteid=1&id=1" "clan/chat.php?siteid=1&id=1" "games/rank.php?siteid=1&gameid=1" "zhdao/view.php?siteid=1&id=1" "zhdao/ask.php?siteid=1" "form/view.php?siteid=1&id=1" "nongchang/plant.php?siteid=1&id=1" "nongchang/harvest.php?siteid=1&id=1"; do
  check "newmod:$p" "$BASE/$p"
done

# 用户深度功能
for p in "bbs/mod.php?siteid=1&id=1" "bbs/vote.php?siteid=1&id=1" "bbs/fav.php?siteid=1&module=bbs&contentid=1" "bbs/favlist.php?siteid=1" "message/index.php?siteid=1" "message/send.php?siteid=1" "message/view.php?siteid=1&id=1" "friend/index.php?siteid=1" "friend/add.php?siteid=1&fid=1" "zone/index.php?siteid=1&uid=1" "zone/msg.php?siteid=1&uid=1"; do
  check "user:$p" "$BASE/$p"
done

# 商城/支付/账户
for p in "shop/cart.php?siteid=1" "shop/checkout.php?siteid=1&shopid=1" "shop/pay.php?siteid=1&orderid=1" "shop/myorder.php?siteid=1" "paimai/bid.php?siteid=1&id=1" "myaccount/index.php?siteid=1"; do
  check "shop:$p" "$BASE/$p"
done

# 后台
for p in "admin/index.php" "admin/site.php" "admin/class.php" "admin/user.php" "admin/content.php?module=article" "admin/content.php?module=bbs" "admin/reply.php" "admin/online.php" "admin/sysmsg.php" "admin/card.php" "admin/logout.php"; do
  check_admin "admin:$p" "$BASE/$p"
done

# OAuth / 模板
check "oauth" "$BASE/oauth/index.php?siteid=1"
check "templates" "$BASE/templates/list.php?siteid=1"

# v4 每模块管理页 (modify/del/change)
cd /workspace/php
for m in bbs shop novel video picture download ring link guessbook gongqiu paimai yuehui wabao guangbo adlink airplane album hotel rizhi xinzhang; do
  for p in admin_WAPmodify admin_WAPdel admin_WAPchange; do
    if [ -f "$m/$p.php" ]; then
      check_admin "$m/$p" "$BASE/$m/$p.php?siteid=1&id=1"
    fi
  done
done

# v2 新增页面
check_admin "admin:upload" "$BASE/admin/upload.php"
check_admin "admin:caiji" "$BASE/admin/caiji.php"
check "utility" "$BASE/utility/index.php?siteid=1"
for n in notify_ali notify_wx; do
  b=$(curl -s "$BASE/shop/$n.php"); e=$(echo "$b" | grep -c 'Fatal error\|Warning:\|Notice:')
  if [ "$e" = "0" ] && echo "$b" | grep -qi 'FAIL\|SUCCESS\|fail'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL pay:$n body=$b"; fi
done
check "weixin:get" "$BASE/weixin.php?siteid=1&echostr=hello123&signature=x&timestamp=1&nonce=1"
# WAP1.0 WML 校验
wml=$(curl -s -A 'Mozilla/5.0 (SAMSUNG; series60)' "$BASE/wapindex.php?siteid=1")
if echo "$wml" | head -1 | grep -q '<?xml'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL wml:wapindex"; fi
wml2=$(curl -s "$BASE/article/list.php?siteid=1&classid=1&ver=1")
if echo "$wml2" | head -1 | grep -q '<?xml'; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n  FAIL wml:article_list"; fi

echo "=============================="
echo "PASS=$PASS FAIL=$FAIL"
[ -n "$FAILED_LIST" ] && echo -e "失败明细:$FAILED_LIST"
