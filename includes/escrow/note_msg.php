<?php
include_once '../../openadodb.php';
include_once '../../session_check.php';

$_POST = escapeStr($_POST);

// 安全地取得 POST 參數
$cId  = $_POST['cId'] ?? '';
$note = $_POST['note'] ?? '';
$id   = $_POST['id'] ?? '';

// 初始化日期和姓名變數
$date = '';
$name = '';
if (isset($_POST['date']) && isset($_POST['hour']) && isset($_POST['min'])) {
    $date = dateChange($_POST['date']) . " " . $_POST['hour'] . ":" . $_POST['min'] . ":00";
}
if (isset($_POST['man'])) {
    $name = $_POST['man'];
}

switch ($_POST['type']) {
    case 'add':
        Add_Msg($cId, $date, $name, $note);
        Show_Msg($cId);
        break;
    case 'del':
        Del_Msg($id);
        Show_Msg($cId);
        break;
    default:
        # code...
        break;
}

function Del_Msg($id)
{
    global $conn;
    $sql = "UPDATE tContractNote SET cDel = 1,cDelName = '" . $_SESSION['member_id'] . "' WHERE cId ='" . $id . "'";

    $conn->Execute($sql);
}

function Add_Msg($cid, $date, $name, $note)
{
    global $conn;
    $sql = "INSERT INTO
			tContractNote
		(
			cCertifiedId,
			cCategory,
			cNote,
			cCreator,
			cCreatTime
		)VALUES(
			'" . $cid . "',
			'4',
			'" . $note . "',
			'" . $_SESSION['member_id'] . "',
			'" . date('Y-m-d H:i:s') . "'
		)";

    $conn->Execute($sql);

}

function Show_Msg($cid)
{
    global $conn;
    $sql          = "SELECT * FROM tContractNote WHERE cCertifiedId ='" . $cid . "' AND cCategory = 4 AND cDel = 0 ";
    $rs           = $conn->Execute($sql);
    $data_service = []; // 初始化陣列
    while (! $rs->EOF) {
        $data_service[] = $rs->fields;
        $rs->MoveNext();
    }
    $table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
    $table .= '<tr >';
    // $table .= '<td width="5%" align="left" class="tb-title2">序號</td>';
    $table .= '<td width="20%" align="left" class="tb-title2">日期/時間</td>';
    $table .= '<td width="10%" align="left" class="tb-title2">承辦</td>';
    $table .= '<td width="60%"align="left" class="tb-title2">內容</td>';
    $table .= '<td width="5%"align="center" class="tb-title2">刪除</td>';

    $table .= '</tr>';

    for ($i = 0; $i < count($data_service); $i++) {

        $table .= '<tr>';
        // $table .='<td width="5%" align="center">'.($i+1).'</td>';
        $table .= '<td width="20%">' . dateChange2($data_service[$i]['cCreatTime'] ?? '') . '</td>';
        $table .= '<td width="10%">' . ($data_service[$i]['cName'] ?? $data_service[$i]['cStaffName'] ?? '未知') . '</td>';
        $table .= '<td width="60%">' . nl2br($data_service[$i]['cNote'] ?? '') . '</td>';
        $table .= '<td width="5%" align="center"><a href="javascript:void(0)" onclick="DelNoteMsg(' . ($data_service[$i]['cId'] ?? 0) . ')">刪除</a></td>';
        $table .= '</tr>';

    }

    echo $table;
}

function dateChange($val)
{
    // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
    $tmp = explode('-', $val);

    if (preg_match("/000/", $tmp[0])) {$tmp[0] = '0000';} else { $tmp[0] += 1911;}

    $val = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);

    return $val;
}

function dateChange2($val)
{
    // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
    $tmp2 = explode(' ', $val);
    $tmp  = explode('-', $tmp2[0]);

    if (preg_match("/0000/", $tmp[0])) {$tmp[0] = '000';} else { $tmp[0] -= 1911;}

    $val = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2] . " " . $tmp2[1];
    unset($tmp);

    return $val;
}
