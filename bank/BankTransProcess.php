<?php
error_reporting(E_ALL & ~E_WARNING);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';

$_POST = escapeStr($_POST);

##
$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '" . $_SESSION['member_id'] . "'";

$rs = $conn->Execute($sql);

$name = $rs->fields['pName'];

if ($_SESSION['member_id'] == 6) {
    // $name = '蘇嘉穎';
}

if ($_SESSION['member_id'] == 6) {
    $str = "1=1";
} else {
    if ($_POST['cId']) {
        $str = "1=1";
    } else {
        // if($_SESSION['member_id'] == 45){
        //     $str = "(tOwner ='".$name."' OR tOwner ='劉展宏')" ;
        // }else{

        // }

        $str = "tOwner ='" . $name . "'";

    }

}

##

##
// $day = date('Y-m-d',strtotime("- 1day"))." 00:00:00";
$day = date('Y-m-d');

if ($_POST) {
    if ($_POST['Date']) {

        $tmp           = explode('-', $_POST['Date']);
        $_POST['Date'] = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];

        $str .= " AND tDate >= '" . $_POST['Date'] . " 00:00:00' AND tDate <= '" . $_POST['Date'] . " 23:59:59'";
    }

    if ($_POST['cId']) {
        $str .= " AND tMemo = '" . $_POST['cId'] . "'";
    }

    if ($_POST['process'] != '0') {

        $process = $_POST['process'];

        if ($_POST['process'] == '已出款') {
            $str .= " AND tPayOk = '1'";
            $limit = 'LIMIT 500';
        } elseif ($_POST['process'] == '銀行出款中') {
            $str .= " AND tExport = 1 AND tPayOk = 2";
        } elseif ($_POST['process'] == '已審核') {
            $str .= " AND tOk = 1 AND tExport = 2 AND tPayOk = 2";
        } else {
            $str .= " AND tOk = 2 AND tExport = 2 AND tPayOk = 2";
        }

    }

    if ($_POST['process'] == '0' && empty($_POST['cId']) && empty($_POST['Date'])) {

        $str .= " AND (tDate >= '" . $day . " 00:00:00' AND tDate <= '" . $day . " 23:59:59')";
    }
} else {

    $str .= " AND (tDate >= '" . $day . "  00:00:00' AND tDate <= '" . $day . " 23:59:59')";
}

$sql = "SELECT * ,tMoney AS total FROM tBankTrans WHERE " . $str . " AND tObjKind NOT IN('點交(結案)','解除契約','建經發函終止','保留款撥付')  ORDER BY tDate DESC " . $limit;
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    $list[$rs->fields['tDate']][] = $rs->fields;

    $rs->MoveNext();
}

// BankTransProcess
$sql = "SELECT * ,SUM(tMoney) AS total FROM tBankTrans WHERE " . $str . " AND tObjKind IN('點交(結案)','解除契約','建經發函終止','保留款撥付') GROUP bY tExport_nu,`tObjKind`,`tVR_Code` ORDER BY tDate DESC " . $limit;

// echo $sql;
$rs = $conn->Execute($sql);
$i  = 0;
while (! $rs->EOF) {
    $list[$rs->fields['tDate']][] = $rs->fields;
    // $list[$i] = $rs->fields;
    // $list[$i]['tDate'] =  substr($list[$i]['tDate'], 0,10);

    unset($branch);
    $rs->MoveNext();
}

if (is_array($list)) {
    krsort($list);

    $i = 0;
    foreach ($list as $key => $value) {

        foreach ($value as $k => $v) {
            $arr[$i] = $v;
            if ($arr[$i]['tPayOk'] == 1) {
                $arr[$i]['status'] = '已出款';
            } elseif ($arr[$i]['tExport'] == 1) {
                $arr[$i]['status'] = '銀行出款中';
            } elseif ($arr[$i]['tOk'] == 1) {
                $arr[$i]['status'] = '已審核';
            } else {
                $arr[$i]['status'] = '未審核';
            }

            if (substr($arr[$i]['tVR_Code'], 0, 5) == '99986') {
                $arr[$i]['branch'] = '城中';
            } elseif (substr($arr[$i]['tVR_Code'], 0, 5) == '99985') {
                $arr[$i]['branch'] = '西門';
            }

            if (($i % 2) == 0) {
                $arr[$i]['color'] = "#FFF";
            } else {
                $arr[$i]['color'] = "#F8ECE9";
            }

            $arr[$i]['total'] = number_format($arr[$i]['total']);

            //地政士
            $arr[$i]['scrivener'] = getScrivener($arr[$i]['tMemo']);
            $i++;
        }

    }
}

function getScrivener($cId)
{
    global $conn;

    $sql = "SELECT (SELECT sName FROM tScrivener WHERE sId =cScrivener) scrivener FROM tContractScrivener WHERE cCertifiedId = '" . $cId . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields['scrivener'];
}

// for ($i=0; $i < count($arr); $i++) {

//     //     echo "<pre>";
//     // print_r($list);
//     // echo "</pre>";

//     $i++;
// }
unset($list);
$list = $arr;
##
$menuStatus = ['0' => '全部', '未審核' => '未審核', '已審核' => '已審核', '銀行出款中' => '銀行出款中', '已出款' => '已出款'];
$smarty->assign('menuStatus', $menuStatus);
$smarty->assign('list', $arr);
$smarty->display('BankTransProcess.inc.tpl', '', 'bank');
