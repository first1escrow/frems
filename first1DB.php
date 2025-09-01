<?php
//version 1.02
ini_set('date.timezone', 'Asia/Taipei');

require_once __DIR__ . '/.env.php';

class first1DB extends Database
{
    protected $dbname;
    protected $driver;
    protected $host;
    protected $user;
    protected $pass;
    protected $port;

    public function __construct($str = 'SET CHARACTER SET utf8')
    {
        global $env;

        $this->dbname = $env['db']['197']['database'];
        $this->driver = $env['db']['197']['driver'];
        $this->host   = $env['db']['197']['host'];
        $this->user   = $env['db']['197']['username'];
        $this->pass   = $env['db']['197']['password'];
        $this->port   = $env['db']['197']['port'];

        parent::__construct($str);
    }
}

class first1TestDB extends Database
{
    protected $dbname;
    protected $driver;
    protected $host;
    protected $user;
    protected $pass;
    protected $port;

    public function __construct($str = 'SET CHARACTER SET utf8')
    {
        global $env;

        $this->dbname = $env['db']['195']['database'];
        $this->driver = $env['db']['197']['driver'];
        $this->host   = $env['db']['195']['host'];
        $this->user   = $env['db']['195']['username'];
        $this->pass   = $env['db']['195']['password'];
        $this->port   = $env['db']['195']['port'];

        parent::__construct($str);
    }
}

/*******類別定義*****/
class Database
{
    private $dbh;
    private $stmt;
    private $debug_sql;

    public function __construct($str = 'SET NAMES utf8')
    {
        // 設定 DSN
        $dsn = $this->driver . ':host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname;
        ##

        // 設定 options 遇到big5時 初始化物件加上big5字串
        if (strtolower($str) == 'big5') {
            $options = array();
        } else {
            $options = array(
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => $str,
                // PDO::MYSQL_ATTR_INIT_COMMAND=>'SET CHARACTER SET utf8',
                PDO::ATTR_EMULATE_PREPARES   => true,
            );
        }
        ##

        // 新增 PDO instanace
        try
        {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

            // $this->dbh->exec('SET GLOBAL interactive_timeout=120');
            // $this->dbh->exec('SET GLOBAL wait_timeout=120');

            if (preg_match("/utf8/iu", $str)) {
                $this->dbh->exec('SET CHARACTER_SET_CLIENT=utf8');
                $this->dbh->exec('SET CHARACTER_SET_RESULTS=utf8');
            }
        } catch (PDOException $e) {
            $message = print_r($e->getMessage(), true) . "\ndsn：" . print_r($dsn, true) . "\nuser：" . $this->user . ' / ' . $this->pass . "\n\n";
            $this->logError($message, 'conn_error');
        }
        ##
    }

    //bindValue
    public function bind($param, $value, $type = null)
    {
        if (empty($type)) {
            $type = PDO::PARAM_STR;

            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            }

            if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            }

            if (is_null($value)) {
                $type = PDO::PARAM_NULL;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    //prepare
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }
    ##

    //預估可查詢到的筆數
    public function found_rows()
    {
        return $this->dbh->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    //execute
    public function go($data = array())
    {
        if (empty($data)) {
            try {
                return $this->stmt->execute();
            } catch (PDOException $e) {
                $message = print_r($e->getMessage(), true) . "\nsql：" . print_r($this->debug(), true) . "\n\n";
                $this->logError($message);
                throw new Exception($message);
            }
        } else {
            foreach ($data as $param => $value) {
                $this->bind($param, $value);
                // $this->bind($param, $value, PDO::PARAM_STR);
            }

            try {
                return $this->stmt->execute($data);
            } catch (PDOException $e) {
                $message = print_r($e->getMessage(), true) . "\nsql：" . print_r($this->debug(), true) . "\n\n";
                $this->logError($message);
                throw new Exception($message);
            }
        }
    }
    ##

    //prepare + execute
    public function getPrepare($query, $data = array())
    {
        $this->debug_params($query, $data);
        $this->query($query);
        return $this->go($data);
    }
    ##

    //prepare + execute
    public function exeSql($query, $data = array())
    {
        return $this->getPrepare($query, $data);
    }
    ##

    //使用fetchALL時
    public function all($query, $data = array())
    {
        $this->getPrepare($query, $data);
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    ##

    //使用fetch時
    public function one($query, $data = array())
    {
        $this->getPrepare($query, $data);
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    ##

    //傳回被影響的行數
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
    ##

    //返回最後插入資料的id
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
    ##

    //To begin a transaction:
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }
    ##

    //To end a transaction and commit your changes:
    public function endTransaction()
    {
        return $this->dbh->commit();
    }
    ##

    //To cancel a transaction and roll back your changes:
    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }
    ##

    //印出執行的 sql 語法
    public function debugDump()
    {
        return $this->stmt->debugDumpParams();
    }
    ##

    //列印可執行 sql
    private function debug_params($query, array $data = null)
    {
        $temp_sql = $query;

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $temp_sql = preg_replace('/:' . $k . '/', "'" . $v . "'", $temp_sql);
            }
        }

        $this->debug_sql = $temp_sql;
    }

    public function debug()
    {
        $this->debug_sql = str_replace(array("\r", "\n", "\r\n", "\n\r"), ' ', $this->debug_sql);
        return $this->debug_sql;
    }
    ##

    //錯誤資訊紀錄
    private function logError($message, $filename = 'error')
    {
        // set error log
        $log = __DIR__ . '/log/db';

        if (!is_dir($log)) {
            mkdir($log, 0777, true);
        }
        ##

        file_put_contents(
            $log . '/' . $filename . '_' . date("Ymd") . '.log',
            date("Y-m-d H:i:s") . "\n" . print_r($message, true) . "\n\n",
            FILE_APPEND
        );
    }
    ##

    //確認資料庫連線是否 alive
    public function ping()
    {
        try {
            $this->dbh->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                return false;
            }
        }
        return true;
    }
    ##
}
##