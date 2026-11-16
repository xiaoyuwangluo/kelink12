<?php
/**
 * 验收种子数据脚本（仅测试环境使用，不参与前台运行）
 * 用法: php tests/seed.php data/sql444001.db
 */
$dbFile = $argv[1] ?? (__DIR__ . '/../data/sql444001.db');
date_default_timezone_set('Asia/Shanghai');
$d = new PDO('sqlite:' . $dbFile);
$d->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$d->sqliteCreateFunction('NOW', function () { return date('Y-m-d H:i:s'); });
$d->sqliteCreateFunction('CURDATE', function () { return date('Y-m-d'); });
$d->sqliteCreateFunction('MD5', 'md5');

$pwd = md5('123456');
$users = [
    [1, 'user1', '会员一', 1000], [2, 'user2', '会员二', 500], [3, 'user3', '会员三', 0],
    [4, 'user4', '会员四', 0], [5, 'user5', '会员五', 0],
];
foreach ($users as $u) {
    $d->prepare("INSERT INTO WapUser (id,username,password,nickname,email,money,point,islock,regtime,regip,usergroup) VALUES (?,?,?,?,?,?,?,0,NOW(),'127.0.0.1',1)")
      ->execute([$u[0], $u[1], $pwd, $u[2], $u[1] . '@test.com', $u[3], 100]);
}
$d->exec("UPDATE WapUser SET islock=1 WHERE id=5");

$d->exec("INSERT INTO WapClass (id,parentid,classname,module,orderid,isshow) VALUES (1,0,'商城测试栏目','shop',0,1)");
$d->exec("INSERT INTO WapClass (id,parentid,classname,module,orderid,isshow) VALUES (2,0,'文章测试栏目','article',0,1)");

$shops = [
    [1, 1, '测试商品', '测试商品简介', 99.00, 94, 1],
    [2, 1, '低库存商品', '库存预警测试', 19.90, 2, 1],
    [3, 1, '下架商品', '下架筛选测试', 5.00, 10, 0],
];
foreach ($shops as $s) {
    $d->prepare("INSERT INTO WapShop (id,classid,userid,title,content,picurl,price,stock,isshow,hits,addtime) VALUES (?,?,0,?,?,'',?,?,?,10,NOW())")
      ->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6]]);
}

$orders = [
    [1, 2, 1, 1, 99.00, 0, '北京市测试路1号', '13800001111'],
    [2, 2, 1, 1, 99.00, 1, '上海市测试路2号', '13800002222'],
    [3, 1, 2, 1, 19.90, -2, '广州市测试路3号', '13800003333'],
];
foreach ($orders as $o) {
    $d->prepare("INSERT INTO WapShopOrder (id,userid,shopid,num,totalprice,status,address,mobile,addtime) VALUES (?,?,?,?,?,?,?,?,NOW())")
      ->execute([$o[0], $o[1], $o[2], $o[3], $o[4], $o[5], $o[6], $o[7]]);
}

$d->exec("INSERT INTO WapLink (id,classid,userid,title,content,picurl,downurl,istop,isshow,hits,addtime) VALUES (1,1,0,'网址导航测试内容','','','http://example.com/',0,1,0,NOW())");

$replies = [
    [1, 2, 'user2', '正常评论内容', 1],
    [2, 2, 'user2', '隐藏评论内容', 0],
    [3, 1, 'user1', '另一条评论', 1],
];
foreach ($replies as $r) {
    $d->prepare("INSERT INTO WapReply (id,module,contentid,classid,userid,nickname,content,isshow,addtime) VALUES (?,?,?,0,?,?,?,?,NOW())")
      ->execute([$r[0], 'article', 1, $r[1], $r[2], $r[3], $r[4]]);
}
$bbsReplies = [
    [1, 1, 2, 'user1', '论坛正常回复', 1],
    [2, 1, 2, 'user2', '论坛隐藏回复', 0],
];
foreach ($bbsReplies as $r) {
    $d->prepare("INSERT INTO WapBbsReply (id,bbsid,classid,userid,nickname,content,isshow,addtime) VALUES (?,?,0,?,?,?,?,NOW())")
      ->execute([$r[0], $r[1], $r[2], $r[3], $r[4], $r[5]]);
}

$online = [
    [1, 2, 'user2', '127.0.0.1', '/index.php', date('Y-m-d H:i:s')],
    [2, 1, 'user1', '10.0.0.1', '/bbs/list.php', date('Y-m-d H:i:s')],
    [3, 0, '', '192.168.1.9', '/wapindex.php', date('Y-m-d H:i:s', time() - 2400)],
];
foreach ($online as $o) {
    $d->prepare("INSERT INTO WapOnline (id,userid,username,ip,page,lasttime) VALUES (?,?,?,?,?,?)")
      ->execute($o);
}

$msgs = [
    [1, '公告测试一', '这是第一条公告内容'],
    [2, '公告测试二', '这是第二条公告内容'],
];
foreach ($msgs as $m) {
    $d->prepare("INSERT INTO WapSysMsg (id,title,content,addtime) VALUES (?,?,?,?)")
      ->execute([$m[0], $m[1], $m[2], date('Y-m-d H:i:s')]);
}

$cards = [
    [1, '123456789012', 'money', 100, 0, 0, null],
    [2, '123456789013', 'money', 50, 1, 2, date('Y-m-d H:i:s', time() - 3600)],
    [3, '123456789014', 'money', 20, 0, 0, null],
];
foreach ($cards as $c) {
    $d->prepare("INSERT INTO WapCard (id,cardno,cardpwd,cardtype,money,isused,userid,usetime,addtime) VALUES (?,?,?,?,?,?,?,?,NOW())")
      ->execute([$c[0], $c[1], md5('pass123'), $c[2], $c[3], $c[4], $c[5], $c[6]]);
}

$reports = [
    [1, 1, 2, '广告刷屏', '第一次举报', 0],
    [2, 1, 1, '恶意灌水', '已核实', 1],
    [3, 1, 2, '广告内容', '', 0],
];
foreach ($reports as $r) {
    $d->prepare("INSERT INTO WapReport (id,bbsid,userid,reason,note,status,addtime) VALUES (?,?,?,?,?,?,NOW())")
      ->execute($r);
}

$caijis = [
    [1, '测试采集源', 'http://example.com/feed', 2],
    [2, '备用采集', 'http://example.org/rss', 2],
];
foreach ($caijis as $c) {
    $d->prepare("INSERT INTO WapCaiji (id,name,url,classid,lastrun,addtime) VALUES (?,?,?,?,NULL,NOW())")
      ->execute($c);
}

echo "seeded: users=" . $d->query("SELECT COUNT(*) FROM WapUser")->fetchColumn()
   . " shops=" . $d->query("SELECT COUNT(*) FROM WapShop")->fetchColumn()
   . " orders=" . $d->query("SELECT COUNT(*) FROM WapShopOrder")->fetchColumn()
   . " classes=" . $d->query("SELECT COUNT(*) FROM WapClass")->fetchColumn()
   . " links=" . $d->query("SELECT COUNT(*) FROM WapLink")->fetchColumn()
   . " replies=" . $d->query("SELECT COUNT(*) FROM WapReply")->fetchColumn()
   . " bbsreplies=" . $d->query("SELECT COUNT(*) FROM WapBbsReply")->fetchColumn()
   . " online=" . $d->query("SELECT COUNT(*) FROM WapOnline")->fetchColumn()
   . " msgs=" . $d->query("SELECT COUNT(*) FROM WapSysMsg")->fetchColumn()
   . " cards=" . $d->query("SELECT COUNT(*) FROM WapCard")->fetchColumn()
   . " reports=" . $d->query("SELECT COUNT(*) FROM WapReport")->fetchColumn() . "\n";
