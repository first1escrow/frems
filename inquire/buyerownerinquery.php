<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/openadodb.php';

$contract = new Contract();

$brand         = '';
$status        = '';
$contract_bank = '';

$sql = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;';
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    $brand .= "<option value='" . $rs->fields['bId'] . "'>" . $rs->fields['bName'] . "</option>\n";
    $rs->MoveNext();
}

$sql = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;';
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    $status .= "<option value='" . $rs->fields['sId'] . "'>" . $rs->fields['sName'] . "</option>\n";
    $rs->MoveNext();
}

// 簽約銀行
$list_categorybank = $contract->GetContractBank();
foreach ($list_categorybank as $val) {
    $contract_bank .= "<option value='" . $val['cBankCode'] . "'>" . $val['cBankFullName'] . "(" . $val['cBranchFullName'] . ")</option>\n";
}
##

// 經辦人員篩選
$undertaker = '';

$str = '';
if (! in_array($_SESSION['member_id'], [6, 1, 39])) {
    $_sales = in_array($_SESSION['member_id'], [38, 72]) ? '38,72' : $_SESSION['member_id'];

    // $str = "AND b.pId = '" . $_SESSION['member_id'] . "'";
    $str = "AND b.pId IN (" . $_sales . ")";

    $_sales = null;unset($_sales);
}

$sql = 'SELECT
            b.pName as cUndertaker,
            b.pId as cUndertakerId
        FROM
            tContractCase AS a
        JOIN
            tPeopleInfo AS b ON b.pId=a.cUndertakerId
        WHERE
            b.pJob="1"
            AND b.pId<>"6"
            ' . $str . '
        GROUP BY
            b.pId;';
$rs = $conn->Execute($sql);

while (! $rs->EOF) {
    $undertaker .= "<option value='" . $rs->fields['cUndertakerId'] . "'>" . $rs->fields['cUndertaker'] . "</option>\n";
    $rs->MoveNext();
}
##

//是否顯示資訊視窗
$sms_window = '';
$sql        = 'SELECT * FROM tSmsSystem WHERE sUsed="1";';
$rs         = $conn->Execute($sql);

if (! $rs->EOF) {
    if ((isset($_SESSION['sms_window']) && $_SESSION['sms_window'] != '1') && (isset($_SESSION['pSMSInfoWindow']) && $_SESSION['pSMSInfoWindow'] == '1')) {
        $sms_window = 'window.open("../sms/sms_summary.php","sms_summary","height=60px,width=300px,status=no") ;';

        if ($_SESSION['member_pDep'] == 5) {
            $sms_window .= 'window.open("../report/transNoEnd.php?s=1","UnEnd","status=no,scrollbars=1") ;';
        }

        $_SESSION['sms_window'] = '1';
    }
}

if ((isset($_SESSION['certifiedFee_window']) && $_SESSION['certifiedFee_window'] != '1') && ($_SESSION['member_pDep'] == 4)) {
    $sms_window .= 'window.open("../sales/certifiedFee.php?s=1","cf","status=no") ;';
    $_SESSION['certifiedFee_window'] = '1';
}

if ((isset($_SESSION['legalNotifyInfo_window']) && $_SESSION['legalNotifyInfo_window'] != '1') && (isset($_SESSION['pLegalCaseNotify']) && $_SESSION['pLegalCaseNotify'] == '1')) {
    $sms_window .= 'window.open("../legal/legalNotifyInfo.php","legalNotifyInfo","height=600px,width=750px,status=no") ;';
    $_SESSION['legalNotifyInfo_window'] = '1';
}

$z_str = '';
if ($_SESSION['member_test'] != 0) {
    $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $test_tmp[] = "'" . $rs->fields['zZip'] . "'";
        $rs->MoveNext();
    }

    $z_str = " AND zZip IN(" . implode(',', $test_tmp) . ")";

    $test_tmp = null;unset($test_tmp);
} else if ($_SESSION['member_pDep'] == 7) {
    $_sales = $_SESSION['member_id'];

    $z_str  = 'AND FIND_IN_SET(zSales, ' . $_sales . ')';
    $_sales = null;unset($_sales);
}

//縣市
$sql = "SELECT zZip,zCity FROM tZipArea WHERE 1=1 " . $z_str . " GROUP BY zCity ORDER BY zZip ASC;";
$rs  = $conn->Execute($sql);

$listCity = '';
while (! $rs->EOF) {
    $listCity .= "<option value='" . $rs->fields['zCity'] . "'>" . $rs->fields['zCity'] . "</option>";
    $rs->MoveNext();
}

$conn->close();

$smarty->assign('Y', date('Y'));
$smarty->assign('country', $listCity); //縣市
$smarty->assign('sms_window', $sms_window);
$smarty->assign('brand', $brand);
$smarty->assign('status', $status);
$smarty->assign('contract_bank', $contract_bank);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('web_addr', $web_addr);

$smarty->display('buyerownerinquery.inc.tpl', '', 'inquire');
