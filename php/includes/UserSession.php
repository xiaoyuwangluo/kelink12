<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会话与用户管理类 (对应原 DLL 中 Session/用户鉴权逻辑)
 */
if (!defined('KL_ROOT')) exit;

class UserSession {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
        // 启动 session
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(KL_SessionTimeOut * 60);
            session_start();
        }
    }

    /** 检查是否已登录 */
    public function isLogin() {
        return isset($_SESSION['kl_userid']) && $_SESSION['kl_userid'] > 0;
    }

    /** 获取当前登录用户ID */
    public function getUserId() {
        return $_SESSION['kl_userid'] ?? 0;
    }

    /** 获取当前登录用户名 */
    public function getUserName() {
        return $_SESSION['kl_username'] ?? '';
    }

    /** 获取用户信息对象 */
    public function getUserVo() {
        if (!$this->isLogin()) return null;
        $uid = $this->getUserId();
        return $this->db->getOne("SELECT * FROM WapUser WHERE id=?", [$uid]);
    }

    /**
     * 用户登录验证
     * @return array [success, msg, userVo]
     */
    public function login($username, $password) {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return [false, '用户名或密码不能为空', null];
        }
        // SQL注入检查
        if (WapTool::checkSqlInject($username) || WapTool::checkSqlInject($password)) {
            return [false, '包含非法字符', null];
        }
        if ($this->loginLocked()) {
            return [false, '登录失败次数过多，请15分钟后再试', null];
        }
        $row = $this->db->getOne("SELECT * FROM WapUser WHERE username=? AND password=?", [$username, md5($password)]);
        if (!$row) {
            $this->loginFailRecord();
            return [false, '用户名或密码错误', null];
        }
        if (isset($row['islock']) && $row['islock'] == 1) {
            return [false, '账号已被锁定', null];
        }
        $this->loginFailReset();
        session_regenerate_id(true); // 防会话固定
        $_SESSION['kl_userid'] = $row['id'];
        $_SESSION['kl_username'] = $row['username'];
        $_SESSION['kl_nickname'] = $row['nickname'] ?? '';
        // 更新最后登录
        $this->db->execute("UPDATE WapUser SET lastlogintime=NOW(), lastloginip=? WHERE id=?", [self::getClientIp(), $row['id']]);
        return [true, '登录成功', $row];
    }

    /** 退出登录 */
    public function logout() {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * 注册用户
     */
    public function register($username, $password, $nickname = '', $email = '') {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return [false, '用户名或密码不能为空'];
        }
        if (WapTool::checkSqlInject($username)) {
            return [false, '用户名包含非法字符'];
        }
        // 检查重名
        $exists = $this->db->getScalar("SELECT COUNT(*) FROM WapUser WHERE username=?", [$username]);
        if ($exists > 0) {
            return [false, '用户名已存在'];
        }
        $uid = $this->db->insert(
            "INSERT INTO WapUser (username, password, nickname, email, regtime, regip, islock, money) VALUES (?,?,?,?,NOW(),?,0,0)",
            [$username, md5($password), $nickname, $email, self::getClientIp()]
        );
        return [true, '注册成功', $uid];
    }

    /** 检查管理员登录 */
    public function isAdminLogin() {
        return isset($_SESSION['kl_adminid']) && $_SESSION['kl_adminid'] > 0;
    }

    /** 管理员登录 */
    public function adminLogin($username, $password) {
        if ($this->loginLocked()) return [false, '登录失败次数过多，请15分钟后再试', null];
        $row = $this->db->getOne("SELECT * FROM WapAdmin WHERE username=? AND password=?", [$username, md5($password)]);
        if (!$row) { $this->loginFailRecord(); return [false, '账号或密码错误', null]; }
        $this->loginFailReset();
        session_regenerate_id(true); // 防会话固定
        $_SESSION['kl_adminid'] = $row['id'];
        $_SESSION['kl_adminname'] = $row['username'];
        return [true, '登录成功', $row];
    }

    /** KL_LoginTime>0 时启用登录失败锁定（15分钟窗口） */
    private function loginLocked() {
        if (KL_LoginTime <= 0) return false;
        $f = $_SESSION['kl_loginfail'] ?? null;
        if (!$f) return false;
        if (time() - $f['t'] > 900) { unset($_SESSION['kl_loginfail']); return false; }
        return $f['n'] >= KL_LoginTime;
    }
    private function loginFailRecord() {
        if (KL_LoginTime <= 0) return;
        $f = $_SESSION['kl_loginfail'] ?? ['n' => 0, 't' => time()];
        if (time() - $f['t'] > 900) $f = ['n' => 0, 't' => time()];
        $f['n']++;
        $_SESSION['kl_loginfail'] = $f;
    }
    private function loginFailReset() { unset($_SESSION['kl_loginfail']); }

    /** 获取客户端真实IP */
    public static function getClientIp() {
        if (KL_GetIP_FromCDN == 1 && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return trim($ip);
    }

    /** 防刷新检查 (对应 KL_CheckIPTime) */
    public function checkSubmitLimit() {
        $ip = self::getClientIp();
        $key = 'kl_submit_' . $ip;
        $last = $_SESSION[$key] ?? 0;
        $now = time();
        if ($now - $last < KL_CheckIPTime) {
            return false;
        }
        $_SESSION[$key] = $now;
        return true;
    }
}
