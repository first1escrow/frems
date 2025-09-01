<?php
//濾除sql injection字串
function escapeStrOut($v = '')
{
    $v = preg_replace("/create/i", "", $v);
    $v = preg_replace("/modify/i", "", $v);
    $v = preg_replace("/rename/i", "", $v);
    $v = preg_replace("/alter/i", "", $v);
    $v = preg_replace("/drop/i", "", $v);
    $v = preg_replace("/commit/i", "", $v);
    $v = preg_replace("/grant/i", "", $v);
    $v = preg_replace("/cast/i", "", $v);
    $v = preg_replace("/select/i", "", $v);
    $v = preg_replace("/insert/i", "", $v);
    $v = preg_replace("/update/i", "", $v);
    $v = preg_replace("/replace/i", "", $v);
    $v = preg_replace("/delete/i", "", $v);

    $v = preg_replace("/[\s+\"\'\`]?from[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?or[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?and[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?xor[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?not[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?like[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?join[\s+\"\'\`]?/i", "", $v);

    $v = preg_replace("/user/i", "", $v);
    $v = preg_replace("/union/i", "", $v);
    $v = preg_replace("/ where /i", "", $v);
    $v = preg_replace("/concat/i", "", $v);
    $v = preg_replace("/sub\_str/i", "", $v);
    $v = preg_replace("/chr\(\d+\)/i", "", $v);
    $v = preg_replace("/char\(\d+\)/i", "", $v);
    $v = preg_replace("/ascii/i", "", $v);

    $v = preg_replace("/\%[0-9a-zA-Z]{2}/", "", $v);
    $v = preg_replace("/\!+/", "", $v);
    $v = preg_replace("/\|+/", "", $v);
    $v = preg_replace("/\'+/", "", $v);
    $v = preg_replace("/\"+/", "", $v);
    $v = preg_replace("/\+{2,}/", "", $v);
    $v = preg_replace("/\&+/", "", $v);
    $v = preg_replace("/\*+/", "", $v);
    $v = preg_replace("/\\{2,}+/", "", $v);
    $v = preg_replace("/\/{2,}/", "", $v);
    $v = preg_replace("/\?+/", "", $v);
    $v = preg_replace("/\-{2,}/", "", $v);
    $v = preg_replace("/\#+/", "", $v);
    $v = preg_replace("/\=+/", "", $v);

    $v = preg_replace("/^\s+/", "", $v);
    $v = preg_replace("/\s+&/", "", $v);

    return $v;
}
##

//主程式
function escapeStr($str)
{
    if (is_array($str)) {
        return recursiveCheck($str);
    }
    //傳入變數為陣列矩陣
    else {
        return escapeStrOut($str, $ajax);
    }
    //字串檢核
}
##

//決定是否陣列遞迴
function recursiveCheck($arr = [])
{
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            $arr[$k] = recursiveCheck($v);
        }
        //陣列、繼續遞迴
        else {
            $arr[$k] = escapeStrOut($v);
        }
        //字串檢核
    }

    return $arr;
}
##

//取得全部縣市
function getCity()
{
    global $conn;
    $qCity = [];

    $sql = 'SELECT * FROM tZipArea WHERE 1 GROUP BY zCity ORDER BY zZip ASC;';
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $qCity[] = $rs->fields['zCity'];
        $rs->MoveNext();
    }

    return $qCity;
}
##

//取得指定縣市行政區與郵遞區號
function getDistinct($zCity)
{
    global $conn;
    $qDist = [];

    $sql = 'SELECT * FROM tZipArea WHERE zCity="' . $zCity . '" ORDER BY zZip ASC;';
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $qDist[$rs->fields['zZip']] = $rs->fields['zArea'];
        $rs->MoveNext();
    }

    return $qDist;
}
##

//計算差異時間
function countDiffDate($f, $t, $ch = '')
{
    $arr = [];
    $arr = countMonths($f, $t);

    return $arr;
}
##

//計算差異年度月份
function countMonths($f, $t)
{
    $arr = [];

    $ff = strtotime($f . '-01');
    $tt = strtotime($t . '-01');

    while ($ff <= $tt) {
        $arr[] = date("Y-m", $ff);
        $ff    = strtotime("+1month", $ff);
    }

    return $arr;
}
##

function date_convert($date_str)
{
    if (empty($date_str) || ! preg_match('/^\d{1,4}-\d{1,2}-\d{1,2}$/', $date_str)) {
        return '';
    }
    $tmp      = explode('-', $date_str);
    $tmp[0]   = (int) $tmp[0] + 1911;
    $date_str = join('-', $tmp);
    unset($tmp);
    return $date_str;
}

function date_convert_month($date_str)
{
    $tmp = explode('-', $date_str);
    $tmp[0] += 1911;
    $date_str = $tmp[0] . '-' . $tmp[1];
    unset($tmp);
    return $date_str;
}

//解編碼
function CheckDecode($arr, $cat) //array 一筆資料 //解編碼及空值判斷 (要欄位名稱一樣才能用) $cat=資料表名稱
{
    global $code_arr;

    $rc = new crypt();
    foreach ($code_arr[$cat] as $k => $v) {
        if ($arr[$v] != '') {
            $arr[$v] = $rc->deCode($arr[$v]);
        }
    }

    return $arr;
}

//編碼
function CheckEncode($arr, $cat) //array 一筆資料 //資料編碼及空值判斷 (要欄位名稱一樣才能用)
{
    global $code_arr;

    $rc = new crypt();
    foreach ($code_arr[$cat] as $k => $v) {
        if ($arr[$v] != '') {
            $arr[$v] = $rc->enCode($arr[$v]);
        }
    }

    return $arr;
}

//單一個
function CheckDecodeOne($val) //值 //解編碼及空值判斷 //單一個
{
    $rc = new crypt();
    if ($val != '') {
        $val = $rc->deCode($val);
    }

    return $val;
}

function CheckEncodeOne($val) //值 //資料編碼及空值判斷 //單一個
{
    $rc = new crypt();
    if ($val != '') {
        $val = $rc->enCode($val);
    }

    return $val;
}
