<?php
//取得縣市列表 SELECT
function listCity($_conn, $str = '')
{
    $val = '<option value="0"';
    if ($str == '') {
        $val .= ' selected="selected"';
    } else {
        $sql = 'SELECT * FROM tZipArea WHERE zZip="' . $str . '";';
        $rs  = $_conn->Execute($sql);
        $str = $rs->fields['zCity'];
    }
    $val .= ">縣市</option>\n";

    $sql = 'SELECT * FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;';
    $rs  = $_conn->Execute($sql);
    while (! $rs->EOF) {
        $val .= '<option value="' . $rs->fields['zCity'] . '"';
        if ($str == $rs->fields['zCity']) {
            $val .= ' selected="selected"';
        }
        $val .= '>' . $rs->fields['zCity'] . "</option>\n";

        $rs->MoveNext();
    }
    return $val;
}
##

//取得縣市所屬鄉鎮市區列表
function listArea($_conn, $str = '')
{
    $val  = '<option value="0"';
    $city = ''; // 初始化 city 變數
    if ($str == '') {
        $val .= ' selected="selected"';
    } else {
        $sql = 'SELECT zCity FROM tZipArea WHERE zZip="' . $str . '";';
        $rs  = $_conn->Execute($sql);
        if ($rs && ! $rs->EOF) {
            $city = $rs->fields['zCity'];
        }
    }
    $val .= ">鄉鎮市區</option>\n";

    if ($city != '') {
        $sql = 'SELECT * FROM tZipArea WHERE zCity="' . $city . '" ORDER BY zZip ASC;';
        $rs  = $_conn->Execute($sql);
        while (! $rs->EOF) {
            $val .= '<option value="' . $rs->fields['zZip'] . '"';
            if ($str == $rs->fields['zZip']) {
                $val .= ' selected="selected"';
            }
            $val .= '>' . $rs->fields['zArea'] . "</option>\n";

            $rs->MoveNext();
        }
    }
    return $val;
}
##

//取得縣市所屬鄉鎮市區列表(SELECTED)
function getArea($_conn, $str = '')
{
    // @session_start();
    $val = '<option value="0" selected="selected"' . ">鄉鎮市區</option>\n";

    $sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="' . $str . '"   ORDER BY zZip ASC;';

    $rs = $_conn->Execute($sql);
    while (! $rs->EOF) {
        $val .= '<option value="' . $rs->fields['zZip'] . '">' . $rs->fields['zArea'] . "</option>\n";

        $rs->MoveNext();
    }
    return $val;
}
##

//經由郵遞區號取得縣市
function filterCityAreaName($_conn, $zips = '', $addr = '')
{
    $sql = 'SELECT zCity,zArea FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->Execute($sql);

    $city = $rs->fields['zCity'];
    $area = $rs->fields['zArea'];

    $addr = preg_replace("/$city/", "", $addr);
    $addr = preg_replace("/$area/", "", $addr);

    return $addr;
}
##

//經由郵遞區號取得縣市
function getCityName($_conn, $zips = '')
{
    $sql = 'SELECT zCity FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->Execute($sql);

    return $rs->fields['zCity'];
}
##

//經由郵遞區號取得鄉鎮市區
function getAreaName($_conn, $zips = '', $addr = '')
{
    $sql = 'SELECT zArea FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->Execute($sql);

    return $rs->fields['zArea'];
}
##
