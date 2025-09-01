<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../session_check.php';
include_once '../web_addr.php';
require_once '../bank/Classes/PHPExcel.php';
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php';
include_once '../class/getAddress.php';
include_once '../class/getBank.php';
include_once '../includes/maintain/feedBackData.php';
include_once 'certifiedFeeAudit.php';
include_once '../tracelog.php';
// include_once 'getBranchType.php';
// include_once '../report/getBranchType.php';
$brand         = '';
$status        = '';
$category      = '';
$contract_bank = '';

$_POST = escapeStr($_POST);
$sDate = $_POST['sDate'];
$eDate = $_POST['eDate'];
$cat   = $_POST['cat'];
$sales = empty($_POST["sales"])
? $_SESSION['member_id']
: $_POST["sales"];
$windowOpen = $_GET['s'];

//預設狀態
$caseStatus = preg_match("/^[1|2]{1}$/", $_POST['caseStatus']) ? $_POST['caseStatus'] : 2;

if ($_POST['review'] != '') {
    $review = $_POST['review'];
} else {
    if ($_SESSION['member_pDep'] == 7) {
        $review = 1;
    } else if ($_SESSION['member_pDep'] == 4) {
        $review = 2;
    } else {
        $review = 2;
    }
}

$cCertifiedId = $_POST['cCertifiedId'];

if ($cat == 'sign' || $cat == '') {
    $checked1 = 'checked=checked';
    $checked2 = '';

} elseif ($cat == 'end') {
    $checked1 = '';
    $checked2 = 'checked=checked';

} elseif ($cat == 'check_date') {
    $checked1 = '';
    $checked2 = '';
    $checked3 = 'checked=checked';
}

// print_r($_POST);
if ($_POST['ok'] == 'ok' || $windowOpen == 1) {

    // die;
    if (is_array($_POST['cId'])) {
        $tlog = new TraceLog();
        $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '履保未收足審核案件');
        Audit($_SESSION['member_id'], $_POST['cId']);

    }

    $tlog = new TraceLog();
    $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '履保未收足審核查詢案件');
    include_once 'certifiedFeeResult.php';

}
##

$reviewMenu = array(0 => '全部', 1 => '業務未審核', 2 => '業務已審核', 3 => '主管已審核');

$statusMenu = [1 => '全部', 2 => '進行中'];

// echo $sDate;
if ($sDate) {
    $tmp   = explode('-', $sDate);
    $sDate = ($tmp[0] - 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

if ($eDate) {
    $tmp   = explode('-', $eDate);
    $eDate = ($tmp[0] - 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}
##
//地政士選單
$sql = 'SELECT sId,sName FROM tScrivener ';
$rs  = $conn->Execute($sql);

$scrivener_search = '';
while (!$rs->EOF) {
    $scrivener_search .= "<option value='" . $rs->fields['sId'] . "'>" . $rs->fields['sName'] . "(SC" . str_pad($rs->fields['sId'], 4, 0, STR_PAD_LEFT) . ")</option>\n";

    $rs->MoveNext();
}
##
// $sDate = '107-11-01';
// $eDate = '107-11-31';
##
$smarty->assign('scrivener_search', $scrivener_search);
$smarty->assign('reviewMenu', $reviewMenu);
$smarty->assign('review', $review);

$smarty->assign('statusMenu', $statusMenu);
$smarty->assign('caseStatus', $caseStatus);

$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('list', $list);
$smarty->assign('salesList', $salesList);
$smarty->assign('sDate', $sDate);
$smarty->assign('eDate', $eDate);
$smarty->assign('checked1', $checked1);
$smarty->assign('checked2', $checked2);
$smarty->assign('checked3', $checked3);
$smarty->display('certifiedFee.inc.tpl', '', 'sales');
