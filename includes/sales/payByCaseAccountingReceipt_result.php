<?php
$condition = '';

if ($_SESSION['member_pDep'] == 7) {
    $condition = 'fSales = ' . $_SESSION['member_id'];
}

$condition = empty($condition) ? '' : 'WHERE ' . $condition;

$sTable = '(
    SELECT
        IF (a.fReceipt = "Y", "已收", "未收") as receipt,
        a.fCertifiedId AS certifiedId,
        a.fDetail AS cScrivener,
        a.fDetail AS sOffice,
        a.fDetail AS total,
        b.fBankAccountName AS bankAccountName,
        b.fType,
        a.fId,
        a.fExportTime,
        a.fCaseCloseTime,
        a.fSales,
        (SELECT pName FROM tPeopleInfo WHERE pId=a.fSales) as bSalesName
    FROM
        tFeedBackMoneyPayByCase AS a
    JOIN
        tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = "S" AND a.fId = b.fPayByCaseId
    WHERE
        a.fSalesConfirmDate IS NOT NULL
        AND a.fAccountantConfirmDate IS NOT NULL
        AND a.fReceipt = "N"
        AND b.fType = 3
    ) AS tb
    ';

$sql = "
    SELECT
        receipt,
        certifiedId,
        cScrivener,
        sOffice,
        bankAccountName,
        total,
        fType,
        fId,
        fExportTime,
        fCaseCloseTime,
        bSalesName
    FROM
        " . $sTable
    . $condition
;

$rs = $conn->Execute($sql);
