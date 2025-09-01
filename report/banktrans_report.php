<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../session_check.php';

$exports = trim($_POST['exp']);
//輸出Excel檔案
if ($exports == 'ok') {

    $people     = trim($_REQUEST['peo']);
    $date_start = trim($_REQUEST['fds']);
    $date_end   = trim($_REQUEST['fde']);

    // $logs->writelog('accChecklistExcel') ;
    include_once 'banktrans_report_excel.php';
}
##

// $_SESSION['member_banktrans']=1;

if ($_SESSION['member_banktrans'] == 2) {

    $list_people[$_SESSION['member_id']] = $_SESSION['member_name'];
    $data_people[0]                      = $_SESSION['member_id'];

} else if ($_SESSION['member_banktrans'] == 1) {
    $sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6)  AND pId!=6 AND pJob = 1 ORDER BY pId ASC ";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $list_people[$rs->fields['pId']] = $rs->fields['pName']; //選項
        // $data_people[]=$rs->fields['pId']; //被選取的

        $rs->MoveNext();
    }
}

##

$year       = date('Y', time()) - 1911;
$date_start = $year . '-' . date('m-d', time());

$date_end = $year . '-' . date('m-d', time());

##
$smarty->assign('date_start', $date_start); //預設時間(今天)
$smarty->assign('date_end', $date_end); //預設時間(今天)
$smarty->assign('list_people', $list_people); //人員選項
$smarty->assign('data_people', $data_people); //人員選項
$smarty->assign('agents', [1, 6, 12]); //允許使用代理人選項人員

$smarty->display('banktrans_report.inc.tpl', '', 'report');
