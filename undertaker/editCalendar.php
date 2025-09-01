<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);
$date  = ($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$cat   = ($_GET['cat']) ? $_GET['cat'] : $_POST['cat'];
$id    = ($_GET['id']) ? $_GET['id'] : $_POST['id'];

if ($_POST) {
    $sDate = (substr($_POST['DateStart'], 0, 3) + 1911) . "-" . substr($_POST['DateStart'], 4) . " " . $_POST['DateStartTime'];
    $eDate = (substr($_POST['DateEnd'], 0, 3) + 1911) . "-" . substr($_POST['DateEnd'], 4) . " " . $_POST['DateEndTime'];

    if ($cat == 'add') {
        $sql = "INSERT INTO
                    tUndertakerCalendar
                SET
                    uDateTime = '" . $sDate . "',
                    uDateTime2 = '" . $eDate . "',
                    uStaff = '" . $_POST['undertaker'] . "',
                    uSubstituteStaff = '" . $_POST['substituteStaff'] . "',
                    uNote = '" . $_POST['Note'] . "',
                    uCreator = '" . $_SESSION['member_id'] . "',
                    uCreatTime = '" . date('Y-m-d H:i:s') . "',
                    uEditor = '" . $_SESSION['member_id'] . "',
                    uEditeTime = '" . date('Y-m-d H:i:s') . "'";
        $conn->Execute($sql);
    } else if ($cat == 'edit') {
        $sql = "UPDATE
                    tUndertakerCalendar
                SET
                    uDateTime = '" . $sDate . "',
                    uDateTime2 = '" . $eDate . "',
                    uStaff = '" . $_POST['undertaker'] . "',
                    uSubstituteStaff = '" . $_POST['substituteStaff'] . "',
                    uNote = '" . $_POST['Note'] . "',
                    uEditor = '" . $_SESSION['member_id'] . "',
                    uEditeTime = '" . date('Y-m-d H:i:s') . "'
                WHERE
                    uId ='" . $id . "'
                ";
        $conn->Execute($sql);
    }
}

##
$sql = "SELECT
            uId,
            uDateTime,
            uDateTime2,
            uCreatTime,
            uEditeTime,
            uStaff,
            uSubstituteStaff,
            uNote,
            (SELECT pName FROM tPeopleInfo WHERE pId=uStaff) as Staff,
            (SELECT pName FROM tPeopleInfo WHERE pId=uSubstituteStaff) as SubstituteStaff,
            (SELECT pName FROM tPeopleInfo WHERE pId=uCreator) as Creator,
            (SELECT pName FROM tPeopleInfo WHERE pId=uEditor) as uEditor
        FROM
            tUndertakerCalendar WHERE uId = '" . $id . "'";
$rs   = $conn->Execute($sql);
$data = $rs->fields;

$data['sDate'] = ($rs->fields['uDateTime']) ? substr($rs->fields['uDateTime'], 0, 10) : $date;
$data['sTime'] = ($rs->fields['uDateTime']) ? substr($rs->fields['uDateTime'], 11, 5) . ":00" : ':00:00:00';

$data['eDate'] = ($rs->fields['uDateTime2']) ? substr($rs->fields['uDateTime2'], 0, 10) : $date;
$data['eTime'] = ($rs->fields['uDateTime2']) ? substr($rs->fields['uDateTime2'], 11, 5) . ":00" : ':00:00:00';

$data['sDate'] = (substr($data['sDate'], 0, 4) - 1911) . substr($data['sDate'], 4);
$data['eDate'] = (substr($data['eDate'], 0, 4) - 1911) . substr($data['eDate'], 4);

$data['sTimeMenu'] = timeMenu($data['sTime']);
$data['eTimeMenu'] = timeMenu($data['eTime']);
##

$menuUndertaker[0] = '請選擇';

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN (5,6) AND pJob = 1";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menuUndertaker[$rs->fields['pId']] = $rs->fields['pName'];
    $rs->MoveNext();
}

$menuTime = timeMenu();
##

//產製時間選單
function timeMenu($patt = '')
{
    $ampm = '';
    $val  = '<option value="">請選擇</option>' . "\n";

    for ($i = 8; $i < 24; $i++) {
        $hrVal = str_pad($i, 2, '0', STR_PAD_LEFT);

        if ($i < 12) {
            $hr   = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ampm = 'AM';
        } else if ($i == 12) {
            $hr   = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ampm = 'PM';
        } else {
            $hr   = str_pad(($i - 12), 2, '0', STR_PAD_LEFT);
            $ampm = 'PM';
        }

        //整點
        $val .= '<option value="' . $hrVal . ':00:00"';
        $val .= ($patt == $hrVal . ':00:00') ? ' selected="selected"' : '';
        $val .= '>' . $hr . ':00 ' . $ampm . "</option>\n";
        ##

        //半點
        $val .= '<option value="' . $hrVal . ':30:00"';
        $val .= ($patt == $hrVal . ':30:00') ? ' selected="selected"' : '';
        $val .= '>' . $hr . ':30 ' . $ampm . "</option>\n";
        ##
    }

    return $val;
}
##

$smarty->assign('cat', $cat);
$smarty->assign('menuUndertaker', $menuUndertaker);
$smarty->assign('data', $data); //預設時間(今天)
$smarty->display('editCalendar.inc.tpl', '', 'undertaker');