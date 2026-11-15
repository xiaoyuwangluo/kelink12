<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 数据库操作类 (对应原 KenLin_ClassManager 数据库操作)
 * 支持 MySQL / SQL Server，PDO 驱动
 */
if (!defined('KL_ROOT')) exit;

class Database {
    private static $instance = null;
    private $pdo = null;
    private $dbType = '';

    public function __construct() {
        $this->dbType = KL_DB_TYPE;
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            if ($this->dbType === 'sqlite') {
                $dbFile = KL_ROOT . '/data/' . KL_DatabaseName . '.db';
                if (!is_dir(dirname($dbFile))) @mkdir(dirname($dbFile), 0755, true);
                $this->pdo = new PDO('sqlite:' . $dbFile, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $this->pdo->exec('PRAGMA journal_mode=WAL');
                $this->pdo->exec('PRAGMA foreign_keys=ON');
                // 注册 MySQL 兼容函数
                @$this->pdo->sqliteCreateFunction('NOW', function() { return date('Y-m-d H:i:s'); });
                @$this->pdo->sqliteCreateFunction('CURDATE', function() { return date('Y-m-d'); });
                @$this->pdo->sqliteCreateFunction('IF', function($cond, $true, $false) { return $cond ? $true : $false; });
                @$this->pdo->sqliteCreateFunction('MD5', 'md5');
            } elseif ($this->dbType === 'mysql') {
                $dsn = 'mysql:host=' . KL_SQL_SERVERIP . ';dbname=' . KL_DatabaseName . ';charset=utf8';
                $this->pdo = new PDO($dsn, KL_SQL_UserName, KL_SQL_PassWord, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } else {
                // SQL Server (sqlsrv)
                $dsn = 'sqlsrv:Server=' . KL_SQL_SERVERIP . ';Database=' . KL_DatabaseName;
                $this->pdo = new PDO($dsn, KL_SQL_UserName, KL_SQL_PassWord, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            }
        } catch (PDOException $e) {
            if (KL_ShowAllError) {
                die('数据库连接失败: ' . $e->getMessage());
            }
            die('数据库连接失败，请检查配置。');
        }
    }

    /** 执行查询，返回 PDOStatement */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** 获取单行 */
    public function getOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /** 获取多行 */
    public function getAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /** 获取单个标量值 */
    public function getScalar($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $val = $stmt->fetchColumn();
        return $val === false ? null : $val;
    }

    /** 执行 INSERT/UPDATE/DELETE，返回影响行数 */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /** 插入并返回自增ID */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return (int)$this->pdo->lastInsertId();
    }

    /** 开始事务 */
    public function beginTransaction() { return $this->pdo->beginTransaction(); }
    public function commit() { return $this->pdo->commit(); }
    public function rollBack() { return $this->pdo->rollBack(); }

    /** 获取 PDO 实例 (直接使用) */
    public function getPdo() { return $this->pdo; }

    /** 获取数据库类型 */
    public function getDbType() { return $this->dbType; }

    /**
     * 通用分页查询
     * @param string $table 表名
     * @param string $fields 字段
     * @param string $where 条件 (不含WHERE)
     * @param string $order 排序
     * @param int $page 页码
     * @param int $pageSize 每页条数
     * @return array [list, total, pageCount]
     */
    public function getPage($table, $fields = '*', $where = '', $order = '', $page = 1, $pageSize = 20, $params = []) {
        $page = max(1, (int)$page);
        $whereSql = $where ? " WHERE $where " : '';
        $orderSql = $order ? " ORDER BY $order " : '';

        $total = (int)$this->getScalar("SELECT COUNT(*) FROM $table $whereSql", $params);
        $pageCount = ceil($total / $pageSize);

        if (in_array($this->dbType, ['mysql', 'sqlite'])) {
            $offset = ($page - 1) * $pageSize;
            $sql = "SELECT $fields FROM $table $whereSql $orderSql LIMIT $pageSize OFFSET $offset";
        } else {
            $offset = ($page - 1) * $pageSize;
            $sql = "SELECT $fields FROM $table $whereSql $orderSql OFFSET $offset ROWS FETCH NEXT $pageSize ROWS ONLY";
        }
        $list = $this->getAll($sql, $params);
        return ['list' => $list, 'total' => $total, 'pageCount' => $pageCount];
    }
}
