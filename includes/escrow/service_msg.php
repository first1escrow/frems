<?php
include_once '../../openadodb.php';
include_once '../../session_check.php';

if (session_status() != 2) {
    session_start();
}

$_POST = escapeStr($_POST);

$id        = isset($_POST['id']) ? $_POST['id'] : '';
$cid       = isset($_POST['cid']) ? $_POST['cid'] : '';
$date_part = isset($_POST['date']) ? dateChange($_POST['date']) : '';
$hour_part = isset($_POST['hour']) ? $_POST['hour'] : '00';
$min_part  = isset($_POST['min']) ? $_POST['min'] : '00';
$date      = $date_part . " " . $hour_part . ":" . $min_part . ":00";
$name      = isset($_POST['man']) ? $_POST['man'] : '';
$note      = isset($_POST['note']) ? $_POST['note'] : '';

switch (isset($_POST['type']) ? $_POST['type'] : '') {
    case 'add':
        Add_Msg($cid, $date, $name, $note);
        Show_Msg($cid);
        break;
    case 'del':
        Del_Msg($id);
        Show_Msg($cid);
        break;
    default:
        # code...
        break;
}

function Del_Msg($id)
{
    global $conn;
    $sql = "UPDATE tContractService SET cDel = 1 WHERE cId ='" . $id . "'";

    $conn->Execute($sql);
}

function Add_Msg($cid, $date, $name, $note)
{
    global $conn;
    $sql = "INSERT INTO
			tContractService
		(
			cCertifiedId,
			cDateTime,
			cName,
			cNote
		)VALUES(
			'" . $cid . "',
			'" . date('Y-m-d H:i:s') . "',
			'" . $_SESSION['member_name'] . "',
			'" . $note . "'
		)";
    $conn->Execute($sql);

}

function Show_Msg($cid)
{
    global $conn;
    $data_service = []; // 初始化陣列
    $sql          = "SELECT * FROM tContractService WHERE cCertifiedId ='" . $cid . "' AND cDel = 0 ";
    $rs           = $conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        while (! $rs->EOF) {
            $data_service[] = $rs->fields;
            $rs->MoveNext();
        }
    }
    $table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
    $table .= '<tr >';
    $table .= '<td width="5%" align="left" class="tb-title2">序號</td>';
    $table .= '<td width="20%" align="left" class="tb-title2">日期/時間</td>';
    $table .= '<td width="10%" align="left" class="tb-title2">承辦</td>';
    $table .= '<td width="60%"align="left" class="tb-title2">內容</td>';
    $table .= '<td width="5%"align="center" class="tb-title2">刪除</td>';

    $table .= '</tr>';

    for ($i = 0; $i < count($data_service); $i++) {

        $table .= '<tr>';
        $table .= '<td width="5%" align="center">' . ($i + 1) . '</td>';
        $table .= '<td width="20%">' . dateChange2($data_service[$i]['cDateTime']) . '</td>';
        $table .= '<td width="10%">' . $data_service[$i]['cName'] . '</td>';
        $table .= '<td width="60%">' . $data_service[$i]['cNote'] . '</td>';
        $table .= '<td width="5%" align="center"><a href="javascript:void(0)" onclick="DelServiceMsg(' . $data_service[$i]['cId'] . ')">刪除</a></td>';
        $table .= '</tr>';

    }

    echo $table;
}

function dateChange($val)
{
    // 檢查輸入值是否為空或 null
    if (empty($val) || $val === null) {
        return '';
    }

    // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
    $tmp = explode('-', $val);

    // 確保有足夠的陣列元素
    if (count($tmp) < 3) {
        return '';
    }

    if (preg_match("/000/", $tmp[0])) {$tmp[0] = '0000';} else {
        // 確保 $tmp[0] 是數值
        if (is_numeric($tmp[0])) {
            $tmp[0] = (int) $tmp[0] + 1911;
        } else {
            $tmp[0] = '0000';
        }
    }

    $val = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);

    return $val;
}

function dateChange2($val)
{
    // 檢查輸入值是否為空或 null
    if (empty($val) || $val === null) {
        return '';
    }

    // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
    $tmp2 = explode(' ', $val);
    $tmp  = explode('-', $tmp2[0]);

    // 確保有足夠的陣列元素
    if (count($tmp) < 3) {
        return '';
    }

    if (preg_match("/0000/", $tmp[0])) {$tmp[0] = '000';} else {
        // 確保 $tmp[0] 是數值
        if (is_numeric($tmp[0])) {
            $tmp[0] = (int) $tmp[0] - 1911;
        } else {
            $tmp[0] = '000';
        }
    }

    // 確保 $tmp2[1] 存在
    $time_part = isset($tmp2[1]) ? ' ' . $tmp2[1] : '';

    $val = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2] . $time_part;
    unset($tmp);

    return $val;
}
