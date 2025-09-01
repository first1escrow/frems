<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$conn = new first1DB;

//export_status
$sql = '(b.bPayOk = ' . $export_status . ' AND b.bKind = "地政士回饋金")';

if ($export_status == 2) {
    $sql = '(b.bUid IS NULL OR ' . $sql . ')';
}

//from_date
if (!empty($from_date)) {
    $sql .= empty($sql) ? '' : ' AND ';
    $sql .= 'DATE(b.bExport_time) >= "' . $from_date . '"';
}

//to_date
if (!empty($to_date)) {
    $sql .= empty($sql) ? '' : ' AND ';
    $sql .= 'DATE(b.bExport_time) <= "' . $to_date . '"';
}

//scriveners
if (!empty($scriveners)) {
    $sql .= empty($sql) ? '' : ' AND ';
    $sql .= 'd.cScrivener IN (' . $scriveners . ')';

}

//sales
if ($_SESSION['member_pDep'] == 7) {
    $sql .= empty($sql) ? '' : ' AND ';
    $sql .= 'a.fSales = ' . $_SESSION['member_id'];
}

$sql = 'SELECT
            a.fCertifiedId,
            a.fTargetId,
            a.fSales,
            a.fTax,
            a.fNHI,
            a.fDetail,
            b.bExport,
            b.bExport_nu,
            b.bPayOk,
            c.sDate,
            SUBSTRING(b.bExport_time, 1, 10) as exportDate,
            c.sAmountReceived,
            d.cScrivener,
            (SELECT sName FROM tScrivener WHERE sId = a.fTargetId) as scrivener
        FROM
            tFeedBackMoneyPayByCase AS a
        LEFT JOIN 
            tFeedBackMoneyPayByCaseAccount AS acc ON a.fId = acc.fPayByCaseId
        LEFT JOIN
            tBankTransRelay AS b ON a.fCertifiedId = b.bCertifiedId AND acc.fBankAccount = b.bAccount
        LEFT JOIN
            tStoreFeedBackMoneyFrom_Record AS c ON a.fCertifiedId = c.sMemo AND c.sSeason = "地政士回饋金" AND c.sDel = 0 AND b.bMoney = c.sAmountReceived AND c.sStoreId = a.fTargetId
        JOIN
            tContractScrivener AS d ON a.fCertifiedId = d.cCertifiedId
        WHERE
            a.fSalesConfirmDate IS NOT NULL
            AND fHidden = 0 
            AND ' . $sql;
$rs = $conn->all($sql);

$list = [];
if (!empty($rs)) {
    foreach ($rs as $k => $v) {
        $v['detail'] = json_decode($v['fDetail']);
        $_money      = empty($v['sAmountReceived']) ? null : $v['sAmountReceived'];

        if (is_null($_money)) {
            $_money = isset($v['detail']->total) ? $v['detail']->total : '-';
            if ($_money != '-') {
                $_money = (int) $_money - (int) $v['fTax'] - (int) $v['fNHI'];
            }
        }

        $v['status']    = empty($v['bPayOk']) ? '未匯款' : '已匯款';
        $v['money']     = number_format($_money);
        $v['scrivener'] = 'SC' . str_pad($v['fTargetId'], 4, '0', STR_PAD_LEFT) . '/' . $v['scrivener'];

        $list[] = $v;

        $_money = null;unset($_money);
    }
}
