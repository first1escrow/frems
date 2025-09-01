<?php
$condition = 'WHERE ';

if(1 == $banktranStatus) {
    $condition .= 'fExportTime is NULL ';
}
if(2 == $banktranStatus) {
    $condition .= 'fExportTime is not NULL ';
}
if(0 != $exp) {
    $condition .= 'AND fExportTime ="'.$exp.'"';
}
if($exportStart != '') {
    $condition .= 'AND fCaseCloseTime >= "'.$exportStart.'"';
}
if($exportEnd != '') {
    $condition .= 'AND fCaseCloseTime <= "'.$exportEnd.'"';
}
if($_SESSION['member_pDep'] == 7) {
    $condition .= 'AND fSales = ' . $_SESSION['member_id'];
}

$sTable = '(
    SELECT
        IF (a.fReceipt = "Y", "已收", "---") as receipt,
        a.fCertifiedId AS certifiedId,
        a.fDetail,
        b.fBankAccountName AS bankAccountName,
        b.fType,
        a.fId,
        a.fExportTime,
        a.fCaseCloseTime,
        a.fSales,
        (SELECT pName FROM tPeopleInfo WHERE pId=a.fSales) as bSalesName
    FROM
        tFeedBackMoneyPayByCase AS a
    LEFT JOIN
        tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = "S" AND a.fId = b.fPayByCaseId 
    
    ) AS tb
    ';


$sql = "
    SELECT 
        receipt, 
        certifiedId,
        bankAccountName,
        fType,
        fId,
        fExportTime,
        fCaseCloseTime,
        bSalesName,
        fDetail
    FROM 
        " . $sTable
    . $condition
;

$rs   = $conn->Execute($sql);