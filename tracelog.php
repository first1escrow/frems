<?php
require_once __DIR__ . '/configs/config.class.php';

class TraceLog
{
    protected $dir;
    protected $today;

    public function __construct($logPath = null)
    {
        $this->today = date("Ymd");
        $this->dir   = __DIR__ . '/log/backstage/' . substr($this->today, 0, 6);

        if (! empty($logPath)) {
            $pattern = __DIR__ . '/log';

            $logPath = str_replace($pattern, '', $logPath);
            $logPath = preg_replace('/\/+$/', '', $logPath);
            $logPath = preg_replace('/\\+$/', '', $logPath);

            $this->dir = $pattern . DIRECTORY_SEPARATOR . $logPath;
        }

        if (! is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }

        return $this->dir;
    }

    public function log($usr, $sql, $title, $action)
    {
        $str = "===========================\r\n";
        $str .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $str .= "User: " . $usr . "\r\n";
        $str .= "Action: " . $action . "\r\n";
        $str .= "Title: " . $title . "\r\n";
        $str .= "Text: " . $sql . "\r\n";
        $str .= "==========================\r\n\r\n";

        $fh = $this->dir . '/' . $this->today . '.log';
        file_put_contents($fh, $str, FILE_APPEND);
    }

    //記錄搜尋歷史記錄
    public function selectWrite($usr, $sql, $title = '')
    {
        $this->log($usr, $sql, $title, 'select');
    }
    ##

    //記錄新增歷史記錄
    public function insertWrite($usr, $sql, $title = '')
    {
        $this->log($usr, $sql, $title, 'insert');
    }
    ##

    //記錄修改歷史記錄
    public function updateWrite($usr, $sql, $title = '')
    {
        $this->log($usr, $sql, $title, 'update');
    }
    ##

    //匯出歷史記錄
    public function exportWrite($usr, $sql, $title = '')
    {
        $this->log($usr, $sql, $title, 'export');
    }
    ##

}
