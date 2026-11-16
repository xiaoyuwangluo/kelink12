<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - TXT 批量导入章节 (管理员或小说作者可操作)
 * 按"第X章/第X节"等标题行正则分章，逐章写入 WapNovelChapter
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Upload.php';

class NovelImportPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $this->showTop('导入章节');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doImport();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    /** 当前用户是否可管理该小说：管理员或作者(author 匹配用户名/昵称) */
    private function canManage($novel) {
        if ($this->userSession->isAdminLogin()) return true;
        $author = trim((string)($novel['author'] ?? ''));
        $name = $this->userSession->getUserName();
        $nick = (string)($_SESSION['kl_nickname'] ?? '');
        return $author !== '' && ($author === $name || ($nick !== '' && $author === $nick));
    }

    private function showForm() {
        $db = $this->db;
        $novelid = (int)($_GET['novelid'] ?? 0);
        // 管理员可选全站小说，会员仅可选自己名下的
        if ($this->userSession->isAdminLogin()) {
            $novels = $db->getAll("SELECT id,title FROM WapNovel ORDER BY id DESC",[]);
        } else {
            $name = $this->userSession->getUserName();
            $nick = (string)($_SESSION['kl_nickname'] ?? '');
            if ($nick !== '') {
                $novels = $db->getAll("SELECT id,title FROM WapNovel WHERE (author=? OR author=?) ORDER BY id DESC",[$name,$nick]);
            } else {
                $novels = $db->getAll("SELECT id,title FROM WapNovel WHERE author=? ORDER BY id DESC",[$name]);
            }
        }
        echo '<div class="subtitle">TXT 批量导入章节</div>';
        echo '<div class="content">正文需以"第X章 / 第X节"等标题单独成行分章，编码支持 UTF-8 / GBK。</div>';
        echo '<form method="post" enctype="multipart/form-data" action="import.php?classid=' . $this->classid . '">';
        echo '选择小说:<select name="novelid">';
        if (empty($novels)) echo '<option value="0">-- 无可导入的小说 --</option>';
        foreach ($novels as $n) {
            echo '<option value="' . (int)$n['id'] . '"' . ($novelid === (int)$n['id'] ? ' selected' : '') . '>' . $this->ToHtm($n['title']) . '</option>';
        }
        echo '</select><br/>';
        echo 'TXT文件:<input type="file" name="txtfile" accept=".txt"/><br/>';
        echo '<div class="time">单文件最大 ' . Upload::formatSize(KL_UpMaxSize * 1024) . '，仅允许 .txt</div>';
        echo '<input type="submit" value="开始导入"/></form>';
    }

    private function doImport() {
        $db = $this->db;
        $novelid = (int)($_POST['novelid'] ?? 0);
        if ($novelid <= 0) { echo '<div class="error">请选择小说</div>'; $this->showForm(); return; }
        $novel = $db->getOne("SELECT * FROM WapNovel WHERE id=?", [$novelid]);
        if (!$novel) { echo '<div class="error">小说不存在</div>'; return; }
        if (!$this->canManage($novel)) {
            $this->showError('无权限：仅管理员或小说作者可导入该小说',
                'view.php?classid=' . (int)$novel['classid'] . '&id=' . $novelid);
        }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        // 仅允许 txt，其余校验复用 Upload 通用逻辑（大小/黑名单/随机化保存）
        $file = $_FILES['txtfile'] ?? null;
        if (!is_array($file) || empty($file['name'])) { echo '<div class="error">请选择TXT文件</div>'; $this->showForm(); return; }
        $ext = strtolower(pathinfo(basename(str_replace('\\', '/', $file['name'])), PATHINFO_EXTENSION));
        if ($ext !== 'txt') { echo '<div class="error">仅允许上传 .txt 文件</div>'; return; }
        list($ok, $msg, $info) = Upload::save($file);
        if (!$ok) { echo '<div class="error">上传失败：' . $this->ToHtm($msg) . '</div>'; return; }

        $raw = @file_get_contents($info['abspath']);
        @unlink($info['abspath']); // 导入用临时文件，读取后即删除
        if ($raw === false || trim($raw) === '') { echo '<div class="error">文件内容为空</div>'; return; }

        // 编码统一转 UTF-8 (非 UTF-8 视为 GBK)
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $conv = @iconv('GBK', 'UTF-8//IGNORE', $raw);
            if ($conv !== false) $raw = $conv;
        }

        $chapters = $this->splitChapters($raw);
        if (empty($chapters)) {
            echo '<div class="error">未识别到章节，请确保正文以"第X章/第X节"标题单独成行分章</div>';
            return;
        }

        $base = (int)$db->getScalar("SELECT COALESCE(MAX(orderindex),0) FROM WapNovelChapter WHERE novelid=?", [$novelid]);
        $count = 0;
        foreach ($chapters as $c) {
            $count++;
            $db->insert("INSERT INTO WapNovelChapter (novelid,title,content,orderindex,addtime) VALUES (?,?,?,?,NOW())",
                [$novelid, $c['title'], $c['content'], $base + $count]);
        }
        echo '<div class="success">成功导入 ' . $count . ' 章 <a href="view.php?classid=' . (int)$novel['classid'] . '&id=' . $novelid . '">查看目录</a></div>';
    }

    /** 按"第X章/第X节/回/篇/卷"标题行拆分章节 */
    private function splitChapters($text) {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        $pattern = '/^\s*第\s*[0-9０-９零〇一二三四五六七八九十百千万两]+\s*[章节回篇卷]/u';
        $chapters = [];
        $cur = null;
        foreach ($lines as $line) {
            if (preg_match($pattern, $line)) {
                if ($cur !== null) $chapters[] = $cur;
                $cur = ['title' => trim($line), 'body' => []];
            } elseif ($cur !== null) {
                $cur['body'][] = $line;
            }
        }
        if ($cur !== null) $chapters[] = $cur;
        foreach ($chapters as &$c) {
            $c['content'] = trim(implode("\n", $c['body']), "\n");
            unset($c['body']);
        }
        return $chapters;
    }
}

$page = new NovelImportPage();
$page->run();
