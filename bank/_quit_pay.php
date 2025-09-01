<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$sn = $_REQUEST["sn"]; //媒體檔序號
$p  = $_REQUEST["p"]; // p=ok 執行退回

if ($p == 'ok') {
    $conn = new first1DB;

    $export_nu = ['export_nu' => $sn];

    $sql = 'SELECT tPayOk FROM tBankTrans WHERE tExport_nu = :export_nu;';
    $rs  = $conn->all($sql, $export_nu);

    $_error = 0;
    if (!empty($rs)) {
        foreach ($rs as $v) {
            $_error += ($v["tPayOk"] == "1") ? 1 : 0;
        }
    }

    if ($_error == 0) {
        //確認要不要清空結案日期
        $sql = 'SELECT tMemo, tKind, tObjKind, tInvoice  FROM tBankTrans WHERE tExport_nu = :export_nu and ((tKind ="保證費" and tObjKind != "履保費先收(結案回饋)") or (tInvoice IS NOT null));';
        $tBankTrans  = $conn->all($sql, $export_nu);

        if (!empty($tBankTrans)) {
            foreach ($tBankTrans as $tran) {
                $sql = 'SELECT 
                            tMemo, tKind, tObjKind, tInvoice 
                        FROM 
                            tBankTrans 
                        WHERE 
                            tMemo = :tMemo 
                          and ((tKind ="保證費" and tObjKind != "履保費先收(結案回饋)") or (tInvoice IS NOT null)) 
                          and tBankLoansDate !="" 
                          and tExport_nu != :export_nu;';

                $caseTrans  = $conn->all($sql, ['tMemo' => $tran["tMemo"], 'export_nu' => $tran["tExport_nu"]]);
                if (empty($caseTrans)) {
                    $sql = 'SELECT bCertifiedId FROM tBankTransRelay WHERE bCertifiedId = :cCertifiedId ';
                    $tBankTransRelayList = $conn->all($sql, ['cCertifiedId' => $tran["tMemo"]]);
                    //沒有出過回饋金才可以清除結案日期
                    if(empty($tBankTransRelayList)) {
                        $update = 'UPDATE tContractCase SET cFeedbackDate = null WHERE cCertifiedId = :cCertifiedId;';
                        $conn->exeSql($update, ['cCertifiedId' => $tran["tMemo"]]);

                        $update = 'UPDATE tContractCase SET cBankList = "" WHERE cBankRelay = "Y" AND cCertifiedId = :cCertifiedId;';
                        $conn->exeSql($update, ['cCertifiedId' => $tran["tMemo"]]);
                    }

                }
            }
        }

        // 媒體檔可退回
        $update = 'UPDATE tBankTrans SET tExport = "2", tBankLoansDate = "",  tExport_nu = "" WHERE tExport_nu = :export_nu;';
        $conn->exeSql($update, $export_nu);

        //20240516 因為媒體檔序號被清除，所以要把指示書(tBankTrankBook、bExport_nu)也作廢
        $sql = 'UPDATE tBankTrankBook SET bDel = 1 WHERE bExport_nu = :export_nu;';
        $conn->exeSql($sql, $export_nu);



        header("Location: redirect.php?fn=quit_pay");
        exit;
    }

    exit('此媒體檔無法退回! 因為已經在銀行做過媒體檔匯入!!');
}
