<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 表单模块 - 提交记录列表 (仅管理员 kl_adminid 会话可见)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class FormDataPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $form = $db->getOne("SELECT * FROM WapForm WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$form) $this->showError('表单不存在');

        // 仅管理员可查看
        if (!$this->userSession->isAdminLogin()) {
            $this->showError('仅管理员可查看提交记录', 'index.php?siteid=' . $this->siteid . '&classid=' . $this->classid);
        }

        $this->strTitle = $form['name'] . ' - 提交记录';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        $rows = $db->getAll("SELECT * FROM WapFormData WHERE formid=? AND siteid=? ORDER BY id DESC LIMIT 200",
            [$id, $this->siteid]);
        if (!$rows) {
            echo '<div class="content">暂无提交记录</div>';
        }
        foreach ($rows as $row) {
            $data = json_decode($row['data'] ?? '{}', true);
            if (!is_array($data)) $data = [];
            echo '<div class="post">';
            $who = (int)$row['userid'] > 0 ? '会员ID ' . (int)$row['userid'] : '游客';
            $time = !empty($row['addtime']) ? date('Y-m-d H:i', strtotime($row['addtime'])) : '';
            echo '<div class="poster">' . $who . ' ' . $time . '</div>';
            echo '<div class="content">';
            foreach ($data as $k => $v) {
                echo $this->ToHtm((string)$k) . '：' . $this->ToHtm((string)$v) . '<br/>';
            }
            echo '</div></div>';
        }

        echo '<div class="actionbar"><a href="index.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回表单列表</a></div>';
        $this->showDown();
    }
}

$page = new FormDataPage();
$page->run();
