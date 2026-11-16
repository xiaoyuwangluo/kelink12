<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 数据库维护 (SQLite)
 * 备份下载 / 完整性检查 / VACUUM 优化 / 运行信息展示
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminBackupPage extends WapPage {
    /** 当前 SQLite 数据库文件路径 */
    private function dbPath() {
        return KL_ROOT . '/data/' . KL_DatabaseName . '.db';
    }

    /** 独立连接（用于维护命令，避免影响主连接） */
    private function rawPdo() {
        $pdo = new PDO('sqlite:' . $this->dbPath(), null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    /** 文件大小格式化 */
    private function sizeText($bytes) {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return (int)$bytes . ' B';
    }

    public function run() {
        $this->checkAdminLogin();
        $isSqlite = (KL_DB_TYPE === 'sqlite');
        $dbFile = $this->dbPath();
        $back = 'backup.php';

        // 备份下载（必须在任何输出之前）
        if (($_GET['action'] ?? '') === 'download') {
            if (!$isSqlite || !is_file($dbFile)) {
                $this->showError('当前数据库不是 SQLite 或数据库文件不存在，无法下载', $back);
            }
            // 将 WAL 日志合并回主库，确保下载内容完整
            try {
                $pdo = $this->rawPdo();
                $pdo->query('PRAGMA wal_checkpoint(TRUNCATE)')->fetchAll();
                $pdo = null;
            } catch (Exception $e) {
                // 检查点失败不阻断下载
            }
            $name = KL_DatabaseName . '_' . date('Ymd_His') . '.db';
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($dbFile));
            readfile($dbFile);
            exit;
        }

        $msg = '';
        $msgType = 'success';
        if (($action = $_GET['action'] ?? '') === 'check') {
            if (!$isSqlite) {
                $msg = '完整性检查仅支持 SQLite'; $msgType = 'error';
            } else {
                try {
                    $pdo = $this->rawPdo();
                    $rows = $pdo->query('PRAGMA integrity_check')->fetchAll(PDO::FETCH_COLUMN);
                    $pdo = null;
                    $result = implode('; ', $rows);
                    if ($result === 'ok') {
                        $msg = '完整性检查通过（integrity_check = ok）';
                    } else {
                        $msg = '完整性检查发现问题：' . $result; $msgType = 'error';
                    }
                } catch (Exception $e) {
                    $msg = '完整性检查失败：' . $e->getMessage(); $msgType = 'error';
                }
            }
        } elseif ($action === 'vacuum') {
            if (!$isSqlite) {
                $msg = '空间优化仅支持 SQLite'; $msgType = 'error';
            } else {
                $before = is_file($dbFile) ? filesize($dbFile) : 0;
                try {
                    $pdo = $this->rawPdo();
                    $pdo->exec('VACUUM');
                    $pdo = null;
                    clearstatcache(true, $dbFile);
                    $after = is_file($dbFile) ? filesize($dbFile) : 0;
                    $msg = '数据库优化完成，当前大小 ' . $this->sizeText($after) . '（优化前 ' . $this->sizeText($before) . '）';
                } catch (Exception $e) {
                    $msg = '数据库优化失败：' . $e->getMessage(); $msgType = 'error';
                }
            }
        }

        clearstatcache(true, $dbFile);
        $exists = is_file($dbFile);
        $size = $exists ? filesize($dbFile) : 0;
        $wal = is_file($dbFile . '-wal') ? filesize($dbFile . '-wal') : 0;
        $mtime = $exists ? date('Y-m-d H:i:s', filemtime($dbFile)) : '—';
        $tableCount = 0;
        if ($isSqlite) {
            try {
                $tableCount = (int)$this->db->getScalar("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            } catch (Exception $e) {
                $tableCount = 0;
            }
        }

        $this->strTitle = '数据库维护';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">数据库维护</div><div><a href="index.php">返回后台首页</a></div>';
        if ($msg !== '') echo '<div class="' . ($msgType === 'error' ? 'error' : 'success') . '">' . $this->ToHtm($msg) . '</div>';

        echo '<div class="content"><ul class="list">';
        echo '<li>数据库类型：' . $this->ToHtm(strtoupper(KL_DB_TYPE)) . '</li>';
        echo '<li>数据库文件：' . $this->ToHtm($isSqlite ? basename($dbFile) : KL_DatabaseName) . '</li>';
        echo '<li>主库大小：' . $this->sizeText($size) . '　WAL 日志：' . $this->sizeText($wal) . '</li>';
        echo '<li>最后修改：' . $this->ToHtm($mtime) . '</li>';
        echo '<li>数据表数量：' . $tableCount . '</li>';
        echo '</ul></div>';

        echo '<div class="content"><div class="subtitle">维护操作</div>';
        if ($isSqlite) {
            echo '<a href="backup.php?action=download">下载备份</a> | '
                . '<a href="backup.php?action=check">完整性检查</a> | '
                . '<a href="backup.php?action=vacuum">空间优化</a>';
            echo '<div class="time">下载前会自动合并 WAL 日志；建议在低峰期执行空间优化。备份文件请妥善保管。</div>';
        } else {
            echo '<div class="error">当前数据库类型为 ' . $this->ToHtm(strtoupper(KL_DB_TYPE)) . '，备份/检查/优化功能仅支持 SQLite。</div>';
        }
        echo '</div>';

        $this->showDown();
    }
}

$page = new AdminBackupPage();
$page->run();
