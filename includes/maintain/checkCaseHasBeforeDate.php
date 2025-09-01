<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

// 檢查案件是否有在指定日期之前的出款履保費或代墊利息
function checkCaseHasBeforeDate($sId, $date)
{
    if (!preg_match("/^[0-9]{4}\-[0-9]{2}$/", $date)) {
        throw new Exception('日期格式錯誤');
    }

    $date .= '-01';
    $from = date('Y-m-01', strtotime($date));
    $to   = date('Y-m-t', strtotime($date));

    $conn = new first1DB;

    //出款履保費
    // $sql  = 'SELECT tMemo as cId FROM tBankTrans WHERE tKind = "保證費" AND tBankLoansDate >= :from AND tBankLoansDate <= :to AND tPayOk = 1;';
    $sql = 'SELECT
                b.cCertifiedId as cId, b.cScrivener, c.tBankLoansDate
            FROM
                tContractCase AS a
            JOIN
                tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
            JOIN
                tBankTrans AS c ON a.cCertifiedId = c.tMemo
            WHERE
                c.tBankLoansDate >= :from
                AND c.tBankLoansDate <= :to
                AND c.tPayOk = 1
                AND c.tKind = "保證費"
                AND b.cScrivener = :sId;';
    $rs   = $conn->all($sql, ['from' => $from, 'to' => $to, 'sId' => $sId]);
    $cIds = empty($rs) ? [] : array_column($rs, 'cId');

    //代墊利息
    // $sql = 'SELECT cCertifiedId as cId FROM tContractCase WHERE cBankList >= :from AND cBankList <= :to;';
    $sql = 'SELECT
                a.cCertifiedId as cId, b.cScrivener, a.cBankList
            FROM
                tContractCase AS a
            JOIN
                tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
            WHERE
                a.cBankList >= :from
                AND a.cBankList <= :to
                AND b.cScrivener = :sId;';
    $rs = $conn->all($sql, ['from' => $from, 'to' => $to, 'sId' => $sId]);
    if (!empty($rs)) {
        $cIds = array_merge($cIds, array_column($rs, 'cId'));
    }

    $cIds = array_unique($cIds);

    return empty($cIds) ? false : true;
}

//是否有進行中的案件
function workingCaseHas($sId) {
    $conn = new first1DB;

    $sql = 'SELECT a.cCertifiedId, b.cCaseStatus FROM tContractScrivener AS a JOIN tContractCase AS b ON a.cCertifiedId = b.cCertifiedId WHERE a.cScrivener = :sId AND b.cCaseStatus = 2;';
    $rs = $conn->all($sql, ['sId' => $sId]);

    return empty($rs) ? false : true;
}