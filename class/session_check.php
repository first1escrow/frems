<?php
if (session_status() != 2) {
    session_start();
}

if ($_SESSION['member_job'] != '1') {
    //header('Location: http://' . $GLOBALS['DOMAIN']);
    //    header('Location: http://first.nhg.tw') ;
    // exit ;
}

//確認 URL 來源為IP或Domain
$url = $_SERVER['HTTP_HOST'];
if ((!preg_match("/^first[2]?.twhg.com.tw$/", $url)) && (!preg_match("/^first[2]?.nhg.tw$/", $url))) { //非 first & first2 後台網址
    //if (!preg_match("/^first[2]?.twhg.com.tw$/",$url)) {        //非 first & first2 後台網址
    //if (!preg_match("/^first.twhg.com.tw$/",$url)) {            //非 first 後台網址
    // header('Location: http://www.first1.com.tw') ;
    // exit ;
}
##
