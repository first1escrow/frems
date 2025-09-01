<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

if (!empty($_POST['date']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $_POST['date'])) {
    $date = $_POST['date'];

    $conn = new first1DB;

    //取得保證號碼
    $sql       = ' ORDER BY a.cCertifiedId ASC;';
    $member_id = $_SESSION['member_id'];

    if ($_SESSION['member_pDep'] == 5) {
        $sql = ' AND c.sUndertaker1 = ' . $member_id . $sql;
    }

    //特殊專案例外
    if(!in_array($member_id, [1, 3, 12, 13, 36, 84, 90, 6])) {
        $sql .= ' AND a.cCertifiedId != "130119712" ';
    }

    if (in_array($member_id, [1, 6, 12])) {
        $sql = ' ORDER BY a.cCertifiedId ASC;';
    }

    $sql = 'SELECT
                a.cCertifiedId,
                substring(a.cSignDate, 1, 10) AS signDate,
                a.cBankList,
                substring(a.cEndDate, 1, 10) AS endDate,
                b.cScrivener,
                c.sUndertaker1,
                c.sName as scrivener,
                (SELECT sName FROM tStatusCase WHERE sId = a.cCaseStatus) as caseStatus,
                d.cTotalMoney,
                d.cCertifiedMoney,
                CONCAT(
                    (SELECT zCity FROM tZipArea WHERE zZip = e.cZip),
                    (SELECT zArea FROM tZipArea WHERE zZip = e.cZip),
                    e.cAddr
                ) as address
            FROM
                tContractCase AS a
            JOIN
                tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
            JOIN
                tScrivener AS c ON b.cScrivener = c.sId
            JOIN
                tContractIncome AS d ON a.cCertifiedId = d.cCertifiedId
            JOIN
                tContractProperty AS e ON a.cCertifiedId = e.cCertifiedId
            WHERE
                a.cCaseStatus = 3 AND DATE(a.cEndDate) = :date' . $sql . ';';
    $rs           = $conn->all($sql, ['date' => $date]);
    $certifiedIds = array_unique(array_column($rs, 'cCertifiedId'));

    $detail = [];
    if (!empty($rs)) {
        foreach ($rs as $v) {
            $detail[$v['cCertifiedId']] = $v;
        }
    }

    //取得實際點交日期
    // if (!empty($certifiedIds)) {
    //     $sql = 'SELECT tMemo, tBankLoansDate FROM tBankTrans WHERE tMemo IN ("' . implode('","', $certifiedIds) . '") AND tKind = "保證費" AND tPayOk = 1;';
    //     $rs  = $conn->all($sql);

    //     if (!empty($rs)) {
    //         foreach ($rs as $v) {
    //             $detail[$v['tMemo']]['tBankLoansDate'] = $v['tBankLoansDate'];
    //         }
    //     }
    // }

    //取得仲介店資訊
    if (!empty($certifiedIds)) {
        $sql = 'SELECT
                    a.bId,
                    a.bStore,
                    CONCAT(
                        (SELECT bCode FROM tBrand WHERE bId = a.bBrand),
                        LPAD(a.bId, 5, "0")
                    ) as code
                FROM
                    tBranch AS a;';
        $rs = $conn->all($sql);
        foreach ($rs as $v) {
            $branches[$v['bId']] = $v;
        }

        $sql = 'SELECT cCertifyId, cBranchNum, cBranchNum1, cBranchNum2, cBranchNum3 FROM tContractRealestate WHERE cCertifyId IN ("' . implode('","', $certifiedIds) . '");';
        $rs  = $conn->all($sql);

        if (!empty($rs)) {
            foreach ($rs as $v) {
                $realty[]      = $branches[$v['cBranchNum']]['code'];
                $realty_name[] = $branches[$v['cBranchNum']]['bStore'];

                if (!empty($v['cBranchNum1'])) {
                    $realty[]      = $branches[$v['cBranchNum1']]['code'];
                    $realty_name[] = $branches[$v['cBranchNum1']]['bStore'];
                }

                if (!empty($v['cBranchNum2'])) {
                    $realty[]      = $branches[$v['cBranchNum2']]['code'];
                    $realty_name[] = $branches[$v['cBranchNum2']]['bStore'];
                }

                if (!empty($v['cBranchNum3'])) {
                    $realty[]      = $branches[$v['cBranchNum3']]['code'];
                    $realty_name[] = $branches[$v['cBranchNum3']]['bStore'];
                }

                $realty      = empty($realty) ? '' : implode(', ', $realty);
                $realty_name = empty($realty_name) ? '' : implode(', ', $realty_name);

                $detail[$v['cCertifyId']]['Realty'] = [
                    'code' => $realty,
                    'name' => $realty_name,
                ];

                $realty = $realty_name = null;
                unset($realty, $realty_name);
            }
        }
    }

    //利息發票部分
    require_once dirname(__DIR__) . '/includes/inquire/interestInvoiceData.php';
    $detail = array_values($detail);

    //產出excel
    require_once dirname(__DIR__) . '/includes/inquire/interestInvoiceExcel.php';

    exit;
}

$smarty->assign('today', date('Y-m-d'));

$smarty->display('interestInvoiceDownload.inc.tpl', '', 'inquire');