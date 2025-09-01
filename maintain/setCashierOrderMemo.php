<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

switch ($_POST['category']) {
    case 'add':
        addNote($_POST);
        showNote($_POST);
        break;
    case 'del':
        deleteNote($_POST['id']);
        showNote($_POST);
        break;
    case 'status':
        setStatus($_POST['id']);
        showNote($_POST);
        break;
    default:
        # code...
        break;
}

function addNote($form)
{
    global $conn;

    $sql = "INSERT INTO
 				tBranchNote
 			SET
 				bStore = '" . $form['bId'] . "',
				bNote = '" . addslashes($form['memo']) . "',
 				bCreator = '" . addslashes($_SESSION['member_name']) . "',
 				bCreatTime = '" . date("Y-m-d H:i:s") . "'";

    $conn->Execute($sql);
}

function setStatus($id)
{
    global $conn;

    $sql = "SELECT bStatus FROM tBranchNote WHERE bId = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    $status = ($rs->fields['bStatus'] == 0) ? 1 : 0;

    $sql = "UPDATE tBranchNote SET bStatus = '" . $status . "' WHERE bId = '" . $id . "'";
    // echo $sql;

    $conn->Execute($sql);
}

function deleteNote($id)
{
    global $conn;

    $sql = "UPDATE tBranchNote SET bDel = '1' WHERE bId = '" . $id . "'";
    $conn->Execute($sql);
}

function showNote($form)
{
    global $conn;

    $table = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb">';
    $table .= '<tr>';
    // $table .= '<th width="5%" align="left">序號</th>';
    // $table .= '<th width="10%" align="left">日期/時間</th>';
    // $table .= '<th width="10%" align="left">建立者</th>';
    // $table .= '<th width="65%"align="left">內容</th>';
    // $table .= '<th width="10%"align="center">功能</th>';

    $table .= '<td width="12%" align="left" class="tb-title">建立時間</td>';
    $table .= '<td width="10%" align="left" class="tb-title">建立者</td>';
    $table .= '<td width="60%"align="left" class="tb-title">內容</td>';
    $table .= '<td width="8%"align="left" class="tb-title">狀態</td>';
    $table .= '<td width="10%"align="center" class="tb-title">功能</td>';

    $table .= '</tr>';

    $sql = "SELECT * FROM tBranchNote WHERE bStore = '" . $form['bId'] . "' AND bDel = 0 ORDER BY bId ASC";
    $rs  = $conn->Execute($sql);
    $i   = 0;
    while (!$rs->EOF) {

        $rs->fields['bStatusName'] = ($rs->fields['bStatus'] == 0) ? '使用中' : '停用';
        $rs->fields['bNote']       = nl2br($rs->fields['bNote']);
        $table .= '<tr>';
        // $table .='<td align="center">'.($i+1).'</td>';
        $table .= '<td>' . $rs->fields['bCreatTime'] . '</td>';
        $table .= '<td align="center">' . $rs->fields['bCreator'] . '</td>';
        $table .= '<td>' . $rs->fields['bNote'] . '</td>';
        $table .= '<td align="center"><a href="javascript:void(0)" onclick="setCashierOrderMemo(\'status\',' . $rs->fields['bId'] . ')">' . $rs->fields['bStatusName'] . '</a></td>';
        $table .= '<td align="center"><a href="javascript:void(0)" onclick="setCashierOrderMemo(\'del\',' . $rs->fields['bId'] . ')">刪除</a></td>';

        $table .= '</tr>';
        $i++;

        $rs->MoveNext();
    }

    echo $table;
}

// funciton showNote($form){
//     global $conn;

// $list = array();
// $table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
// $table .= '<tr>';
// $table .= '<td width="5%" align="left" class="tb-title2">序號</td>';
// $table .= '<td width="20%" align="left" class="tb-title2">日期/時間</td>';
// $table .= '<td width="10%" align="left" class="tb-title2">建立者</td>';
// $table .= '<td width="60%"align="left" class="tb-title2">內容</td>';
// $table .= '<td width="5%"align="center" class="tb-title2">功能</td>';
// $table .= '</tr>';

// $sql = "SELECT * FROM tBranchNote WHERE bStore = '".$form['bId']."' ORDER BY bId ASC";
// $rs = $conn->Execute($sql);

// $i = 0;
//     while (!$rs->EOF) {

//         $table .='<tr>';
//         $table .='<td width="5%" align="center">'.($i+1).'</td>';
//         $table .='<td width="20%">'.dateChange($rs->fields['bCreatTime']).'</td>';
//         $table .='<td width="10%">'.$rs->fields['cName'].'</td>';
//         $table .='<td width="60%">'.$rs->fields['cNote'].'</td>';
//         $table .='<td width="5%" align="center"><a href="javascript:void(0)" onclick="setCashierOrderMemo(\'del\','.$rs->fields['bId'].')">刪除</a></td>';
//         $table .='</tr>';
//         $i++;
//         $rs->MoveNext();
//     }

//     echo $table;
// }

// function dateChange($val){
//     // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
//     $tmp2 = explode(' ',$val) ;
//     $tmp = explode('-',$tmp2[0]) ;

//     if (preg_match("/0000/",$tmp[0])) {    $tmp[0] = '000' ; }
//     else { $tmp[0] -= 1911 ; }

//     $val = $tmp[0].'-'.$tmp[1].'-'.$tmp[2]." ".$tmp2[1] ;
//     unset($tmp) ;

//     return $val;
// }
