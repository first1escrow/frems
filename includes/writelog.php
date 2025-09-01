<?php

function write_log($txt, $filename, $path = null)
{
    $path = empty($path) ? dirname(__DIR__) . '/log2' : $path;

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $usrName    = $_SESSION['member_name'];
    $usrAccount = $_SESSION['member_acc'];
    $usrIP      = '';
    $usrDate    = date("Y-m-d H:i:s");

    //IP
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $usrIP = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $usrIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $usrIP = $_SERVER['REMOTE_ADDR'];
    }

    $line = $usrName . ',' . $usrAccount . ',' . $usrIP . ',' . $usrDate;

    if ($txt) {
        $line .= ',' . $txt;
    }

    // $fw = fopen($GLOBALS['FILE_PATH'] . 'log2/' . $filename . '.log', 'a+');
    $fw = fopen($path . '/' . $filename . '.log', 'a+');
    fwrite($fw, $line . "\r\n");
    fclose($fw);
}

function checklist_log($txt, $name)
{
    write_log($txt, $name, dirname(__DIR__) . '/log');
}
