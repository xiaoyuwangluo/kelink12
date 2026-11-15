<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 问答模块 - 问题列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZhdaoIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        $this->strTitle = $this->classVo->classname ?? '问答';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';
        echo '<div class="postbar"><a href="ask.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">我要提问</a></div>';

        $result = $db->getPage('WapZhiDao', 'id,title,nickname,status,hits,addtime',
            "siteid=?", 'id DESC', $page, $pageSize, [$this->siteid]);

        echo '<div class="content"><ul class="list">';
        if (!$result['list']) echo '<li>暂无问题</li>';
        foreach ($result['list'] as $r) {
            $flag = (int)$r['status'] === 1 ? '[已答]' : '[待答]';
            echo '<li>' . $this->ToHtm($flag);
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . (int)$r['id'] . '">'
                . $this->ToHtm($r['title']) . '</a>';
            echo ' <span class="info">阅' . (int)$r['hits'] . '</span>';
            if (!empty($r['addtime'])) {
                echo ' <span class="time">' . date('m-d', strtotime($r['addtime'])) . '</span>';
            }
            echo '</li>';
        }
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="actionbar"><a href="../wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div>';
        $this->showDown();
    }
}

$page = new ZhdaoIndexPage();
$page->run();
