<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 表单模块 - 按 fields JSON 动态渲染表单并提交 (WapFormData)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class FormViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if ($id <= 0) $this->showError('参数错误');

        $form = $db->getOne("SELECT * FROM WapForm WHERE id=?",[$id]);
        if (!$form) $this->showError('表单不存在');

        $fields = json_decode($form['fields'] ?? '[]', true);
        if (!is_array($fields)) $fields = [];

        // 处理提交
        $err = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $data = [];
            foreach ($fields as $f) {
                $name = trim((string)($f['name'] ?? ''));
                if ($name === '') continue;
                $val = trim((string)($_POST['field_' . $name] ?? ''));
                if ($val === '') { $err = '请填写：' . $name; break; }
                if (mb_strlen($val) > 200) { $err = '「' . $name . '」内容过长'; break; }
                if (WapTool::checkSqlInject($val)) { $err = '「' . $name . '」包含非法字符'; break; }
                $data[$name] = $val;
            }
            if ($err === '') {
                if (!$this->userSession->checkSubmitLimit()) {
                    $err = '操作过于频繁，请稍后再试';
                } else {
                    $uid = $this->userSession->isLogin() ? (int)$this->userSession->getUserId() : 0;
                    $db->insert("INSERT INTO WapFormData (formid,userid,data,addtime) VALUES (?,?,?,NOW())",[$id,$uid,json_encode($data, JSON_UNESCAPED_UNICODE)]);
                    $this->showSuccess('提交成功', 'view.php?classid=' . $this->classid . '&id=' . $id);
                }
            }
        }

        $this->strTitle = $form['name'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($form['name']) . '</div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';

        // 动态渲染字段
        echo '<div class="content"><form method="post" action="view.php?classid=' . $this->classid . '">';
        echo '<input type="hidden" name="id" value="' . $id . '"/>';
        if (!$fields) echo '<div>该表单未配置字段</div>';
        foreach ($fields as $f) {
            $name = (string)($f['name'] ?? '');
            if ($name === '') continue;
            $type = (string)($f['type'] ?? 'text');
            echo $this->ToHtm($name) . ':';
            if ($type === 'textarea') {
                echo '<textarea name="field_' . $this->ToHtm($name) . '" rows="4"></textarea><br/>';
            } elseif ($type === 'select' && !empty($f['options']) && is_array($f['options'])) {
                echo '<select name="field_' . $this->ToHtm($name) . '">';
                foreach ($f['options'] as $opt) {
                    echo '<option value="' . $this->ToHtm((string)$opt) . '">' . $this->ToHtm((string)$opt) . '</option>';
                }
                echo '</select><br/>';
            } elseif ($type === 'number') {
                echo '<input type="number" name="field_' . $this->ToHtm($name) . '" required/><br/>';
            } else {
                echo '<input type="text" name="field_' . $this->ToHtm($name) . '" maxlength="200" required/><br/>';
            }
        }
        echo '<input type="submit" value="提交"/>';
        echo '</form></div>';

        echo '<div class="actionbar"><a href="index.php?classid=' . $this->classid . '">返回表单列表</a></div>';
        $this->showDown();
    }
}

$page = new FormViewPage();
$page->run();
