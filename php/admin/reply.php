<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 评论审核 (对应原 admin/reply 管理)
 * WapReply 与 WapBbsReply 两类评论：显示/隐藏、删除
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminReplyPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $tab = ($_GET['tab'] ?? 'wap') === 'bbs' ? 'bbs' : 'wap';
        $back = 'reply.php?siteid=' . $this->siteid . '&tab=' . $tab;

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $fromBbs = ($_POST['from'] ?? 'wap') === 'bbs';
            $table = $fromBbs ? 'WapBbsReply' : 'WapReply';
            if ($action === 'toggle_show') {
                $db->execute("UPDATE " . $table . " SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=? AND siteid=?", [$id, $this->siteid]);
                $this->showSuccess('审核状态已切换', $back);
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM " . $table . " WHERE id=? AND siteid=?", [$id, $this->siteid]);
                $this->showSuccess('评论已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '评论审核';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">评论审核</div><div><a href="index.php">返回后台首页</a></div>';

        // 切换标签
        echo '<div class="content">'
            . '<a href="reply.php?siteid=' . (int)$this->siteid . '&tab=wap">内容评论' . ($tab === 'wap' ? '√' : '') . '</a> | '
            . '<a href="reply.php?siteid=' . (int)$this->siteid . '&tab=bbs">论坛回复' . ($tab === 'bbs' ? '√' : '') . '</a></div>';

        // 分页列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        if ($tab === 'bbs') {
            $result = $db->getPage('WapBbsReply', '*', 'siteid=?', 'id DESC', $page, 20, [$this->siteid]);
        } else {
            $result = $db->getPage('WapReply', '*', 'siteid=?', 'id DESC', $page, 20, [$this->siteid]);
        }
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            $rid = (int)$row['id'];
            $hide = empty($row['isshow']) ? '<span style="color:red">[隐藏]</span>' : '';
            $src = $tab === 'bbs' ? ('帖' . (int)($row['bbsid'] ?? 0))
                                  : ($this->ToHtm($row['module'] ?? '') . '#' . (int)($row['contentid'] ?? 0));
            $time = !empty($row['addtime']) ? date('m-d H:i', strtotime($row['addtime'])) : '';
            $content = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($row['content'] ?? ''))), 0, 50);
            echo '<li>' . $hide . 'ID:' . $rid . ' [' . $src . '] ' . $this->ToHtm($row['nickname'] ?? '游客')
                . ': ' . $this->ToHtm($content) . ' <span class="time">' . $time . '</span><br/>';
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="toggle_show"/><input type="hidden" name="id" value="' . $rid . '"/>'
                . '<input type="hidden" name="from" value="' . $tab . '"/>'
                . '<input type="submit" value="' . (empty($row['isshow']) ? '显示' : '隐藏') . '"/></form>';
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/><input type="hidden" name="id" value="' . $rid . '"/>'
                . '<input type="hidden" name="from" value="' . $tab . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        if (empty($result['list'])) echo '<li>暂无评论</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }
}

$page = new AdminReplyPage();
$page->run();
