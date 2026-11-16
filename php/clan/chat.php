<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 家族模块 - 家族内部聊天 (仅成员可发言)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ClanChatPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $clan = $db->getOne("SELECT * FROM WapClan WHERE id=?",[$id]);
        if (!$clan) $this->showError('家族不存在');

        $uid = (int)$this->userSession->getUserId();
        if (!$db->getScalar("SELECT COUNT(*) FROM WapClanMember WHERE clanid=? AND userid=?", [$id, $uid])) {
            $this->showError('只有家族成员才能进入家族聊天', 'view.php?classid=' . $this->classid . '&id=' . $id);
        }

        // 处理发言 (头部输出前完成，成功则跳转)
        $err = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if ($content === '' || mb_strlen($content) > 200) {
                $err = '发言不能为空且不超过200字';
            } elseif (WapTool::checkSqlInject($content)) {
                $err = '包含非法字符';
            } elseif (!$this->userSession->checkSubmitLimit()) {
                $err = '操作过于频繁，请稍后再试';
            } else {
                $nick = $this->userVo['nickname'] ?? '';
                if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';
                $db->insert(
                    "INSERT INTO WapClanChat (clanid,userid,nickname,content,addtime) VALUES (?,?,?,?,NOW())",
                    [$id, $uid, $nick, $content]
                );
                header('Location: chat.php?classid=' . $this->classid . '&id=' . $id);
                exit;
            }
        }

        $this->strTitle = $clan['name'] . ' 家族聊天';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';

        // 最近50条家族发言
        $rows = $db->getAll(
            "SELECT * FROM (SELECT id,nickname,content,addtime FROM WapClanChat WHERE clanid=? ORDER BY id DESC LIMIT 50) t ORDER BY t.id ASC",
            [$id]
        );
        echo '<div class="content"><ul class="list">';
        if (!$rows) echo '<li>家族里还没有人说话</li>';
        foreach ($rows as $r) {
            $time = !empty($r['addtime']) ? date('m-d H:i', strtotime($r['addtime'])) : '';
            echo '<li><b>' . $this->ToHtm($r['nickname'] !== '' && $r['nickname'] !== null ? $r['nickname'] : '成员') . '</b>：'
                . $this->ToHtm($r['content']) . ' <span class="time">' . $time . '</span></li>';
        }
        echo '</ul></div>';

        echo '<div class="content"><form method="post" action="chat.php?classid=' . $this->classid . '&id=' . $id . '">';
        echo '<input type="text" name="content" maxlength="200" required/>';
        echo '<input type="submit" value="发言"/></form></div>';

        echo '<div class="actionbar"><a href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回家族</a></div>';
        $this->showDown();
    }
}

$page = new ClanChatPage();
$page->run();
