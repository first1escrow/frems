<?php
/**
 * Logger
 * param $body 要記錄的內容
 * param $path 記錄的路徑與檔名
 */
trait Logger
{
    public static function info($body)
    {
        return self::log($body, createDirectory('info'));
    }

    public static function error($body)
    {
        return self::log($body, createDirectory('error'));
    }

    public static function debug($body)
    {
        return self::log($body, createDirectory('debug'));
    }

    public static function log($body, $path = null)
    {
        $path = empty($path) ? createDirectory('') : $path;
        self::makeDirectory(dirname($path));

        $body = date('Y-m-d H:i:s') . ' ' . print_r($body, true) . PHP_EOL;
        file_put_contents($path, $body, FILE_APPEND);

        return true;
    }

    public static function createDirectory($type)
    {
        $path = dirname(dirname(__DIR__)) . '/log/';
        self::makeDirectory($path);

        $path .= empty($type) ? '' : $type . '_';
        $path .= date('Y-m-d') . '.log';

        return $path;
    }

    public static function makeDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return true;
    }

}