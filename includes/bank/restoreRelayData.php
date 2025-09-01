<?php
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

//刪除前台紀錄
function deletePayByCase(&$conn, $uids)
{
    if (empty($uids)) {
        throw new Exception('Empty BankTrans Id');
    }

    $sql = 'SELECT
                a.bCertifiedId,
                a.bDate,
                a.bKind,
                a.bMoney,
                b.fTargetId,
                b.fTax,
                b.fNHI
            FROM
                tBankTransRelay AS a
            JOIN
                tFeedBackMoneyPayByCase AS b ON a.bCertifiedId = b.fCertifiedId AND b.fTarget = "S"
            WHERE
                a.bUid IN ("' . $uids . '")
                AND a.bKind LIKE "地政士回饋金";';
    $rs = $conn->all($sql);

    if (empty($rs)) {
        return true;
    }

    $values = [];
    foreach ($rs as $v) {
        $sql = 'UPDATE
                    tStoreFeedBackMoneyFrom_Record
                SET
                    sDel = 1
                WHERE
                    sType = :type
                    AND sStoreId = :store
                    AND sSeason = :kind
                    AND sMemo = :cid
                    AND sDel = :del;';
        $conn->exeSql($sql, [
            'type'  => 1,
            'store' => $v['fTargetId'],
            'kind'  => $v['bKind'],
            'cid'   => $v['bCertifiedId'],
            'del'   => 0,
        ]);
    }
}

//解鎖案件合約書回饋金資訊
function unlockCaseFeedbackInfo(&$conn, $uids)
{
    if (empty($uids)) {
        throw new Exception('Empty BankTrans Id');
    }

    $sql = 'SELECT bCertifiedId FROM tBankTransRelay WHERE bUid IN ("' . $uids . '") AND bKind = "地政士回饋金";';
    $rs  = $conn->all($sql);

    if (!empty($rs)) {
        $cIds = array_column($rs, 'bCertifiedId');
        $sql  = 'UPDATE tContractCase SET cFeedBackClose = 0 WHERE cCertifiedId IN ("' . implode('","', $cIds) . '");';
        $conn->exeSql($sql);
    }

    return true;
}

$uids = implode('","', $post['uid']);

//恢復中繼銀行出款紀錄為未出款
$sql = 'UPDATE tBankTransRelay SET bExport = 2, bExport_time = NULL, bExport_nu = NULL, bPayOk = 2 WHERE bUid IN ("' . $uids . '");';
$conn->exeSql($sql);

//刪除前台紀錄
deletePayByCase($conn, $uids);

//鎖定案件合約書回饋金資訊
//unlockCaseFeedbackInfo($conn, $uids);
