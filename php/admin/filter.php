<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 敏感词库管理 (自定义词库)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminFilterPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'filter.php?siteid=' . $this->siteid;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            if ($act === 'add') {
                $this->doAdd($back);
            } elseif ($act === 'del') {
                $id = (int)($_POST['id'] ?? 0);
                $db->execute("DELETE FROM WapFilter WHERE id=? AND siteid=?", [$id, $this->siteid]);
                kl_cache_clear_site($this->siteid);
                $this->showSuccess('敏感词已删除', $back);
            }
            $this->showError('非法操作', $back);
        }

        $rows = $db->getAll("SELECT * FROM WapFilter WHERE siteid=? ORDER BY id DESC", [$this->siteid]);

        $this->strTitle = '敏感词库';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">敏感词库</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';
        echo '<div class="content">内置词来自 config.php 的 KL_Filter_All，此处为额外自定义词库。</div>';
        echo '<div class="content">内置词：' . $this->ToHtm(implode('、', kl_filter_words())) . '</div>';

        echo '<div class="content"><form method="post" action="' . $back . '">';
        echo '<input type="hidden" name="act" value="add"/>';
        echo '新增敏感词（每行一个，也可用中英文逗号或空格分隔）：<br/>';
        echo '<textarea name="words" rows="5" cols="40"></textarea><br/>';
        echo '<input type="submit" value="批量添加"/></form></div>';

        echo '<div class="content"><ul class="list">';
        foreach ($rows as $r) {
            echo '<li>ID:' . (int)$r['id'] . ' ' . $this->ToHtm($r['word'])
                . ' <span class="time">' . (!empty($r['addtime']) ? date('m-d H:i', strtotime($r['addtime'])) : '') . '</span> ';
            echo '<form method="post" action="' . $back . '" style="display:inline" onsubmit="return confirm(\'删除该敏感词?\')">';
            echo '<input type="hidden" name="act" value="del"/><input type="hidden" name="id" value="' . (int)$r['id'] . '"/>';
            echo '<input type="submit" value="删除"/></form>';
            echo '</li>';
        }
        if (empty($rows)) echo '<li>暂无自定义敏感词</li>';
        echo '</ul></div>';
        $this->showDown();
    }

    /** 批量添加：按行/中英文逗号/空白分隔，去重、长度 1-50 校验，先查后插 */
    private function doAdd($back) {
        $db = $this->db;
        $raw = (string)($_POST['words'] ?? '');
        $parts = preg_split('/[\r\n,，\s]+/u', $raw);
        $words = [];
        foreach ($parts as $w) {
            $w = trim($w);
            if ($w === '') continue;
            if (mb_strlen($w) < 1 || mb_strlen($w) > 50) continue;
            $words[$w] = true;
        }
        if (!$words) $this->showError('请输入有效敏感词（单个长度 1-50）', $back);

        $existing = [];
        foreach ($db->getAll("SELECT word FROM WapFilter WHERE siteid=?", [$this->siteid]) as $r) {
            $existing[(string)$r['word']] = true;
        }

        $added = 0;
        $skipped = 0;
        foreach (array_keys($words) as $word) {
            if (isset($existing[$word])) { $skipped++; continue; }
            try {
                $db->insert("INSERT INTO WapFilter (siteid,word,addtime) VALUES (?,?,NOW())", [$this->siteid, $word]);
                $existing[$word] = true;
                $added++;
            } catch (Exception $e) {
                $skipped++;
            }
        }
        kl_cache_clear_site($this->siteid);
        $this->showSuccess('已添加 ' . $added . ' 个敏感词' . ($skipped > 0 ? '，跳过重复/无效 ' . $skipped . ' 个' : ''), $back);
    }
}

$page = new AdminFilterPage();
$page->run();
