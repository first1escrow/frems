<?php

// 2025-04-11 電子合約書加權分數
$from_date = ($yr + 1911) . '-' . str_pad(($sess * 3 - 2), 2, 0, STR_PAD_LEFT) . '-01';
$to_date   = date('Y-m-t', strtotime('+2 months', strtotime($from_date)));

$from_date         = new DateTime($from_date);
$roc_from_date     = ($from_date->format('Y') - 1911) . $from_date->format('-m-d');
$expense_from_date = str_replace('-', '', $roc_from_date);

$to_date         = new DateTime($to_date);
$roc_to_date     = ($to_date->format('Y') - 1911) . $to_date->format('-m-d');
$expense_to_date = str_replace('-', '', $roc_to_date);

//取得時間範圍內入款的電子合約書保證號碼
$sql = 'SELECT
            SUBSTRING(a.eDepAccount, 3) as eDepAccount,
            b.bSID,
            c.sName
        FROM
            tExpense AS a
        JOIN
            tBankCode AS b ON SUBSTRING(a.eDepAccount, 3) = b.bAccount
        JOIN
            tScrivener AS c ON b.bSID = c.sId
        WHERE
            a.eTradeDate >= "' . $expense_from_date . '"
            AND a.eTradeDate <= "' . $expense_to_date . '"
            AND a.eDebit = "000000000000000"
            AND a.eLender != "000000000000000"
            AND a.eTradeStatus = 0
            AND b.bFrom = 2
        GROUP BY
            a.eDepAccount;';
$rs = $conn->Execute($sql);

$econtract_list = [];
while (! $rs->EOF) {
    $econtract_list[$rs->fields['eDepAccount']] = $rs->fields;
    $rs->MoveNext();
}

$econtract = [
    'score'  => 0,
    'detail' => [],
];

$cIds = array_keys($econtract_list);
if (! empty($cIds)) {
    foreach ($cIds as $cId) {
        $sql = 'SELECT
                    eDepAccount,
                    eTradeDate
                FROM
                    tExpense
                WHERE
                    eDepAccount = "00' . $cId . '"
                ORDER BY
                    eTradeDate ASC LIMIT 1;';
        $rs = $conn->Execute($sql);

        $found = (! $rs->EOF && ($rs->fields['eTradeDate'] >= $expense_from_date && $rs->fields['eTradeDate'] <= $expense_to_date)) ? true : false;

        //是否第一筆入帳時間正確且案件屬於 sales
        if ($found && belongTSales($conn, $econtract_list[$cId]['bSID'], $rs->fields['eTradeDate'], $sales)) {
            $_eTradeDate = substr($rs->fields['eTradeDate'], 0, 3) . '-' . substr($rs->fields['eTradeDate'], 3, 2) . '-' . substr($rs->fields['eTradeDate'], 5, 2);

            $econtract['score'] += 0.5;
            $econtract['detail'][$cId] = [
                'eDepAccount' => $econtract_list[$cId]['eDepAccount'],
                'eTradeDate'  => $_eTradeDate,
                'bSID'        => $econtract_list[$cId]['bSID'],
                'sName'       => $econtract_list[$cId]['sName'],
                'sSales'      => $econtract_list[$cId]['sSales'],
            ];

            $_eTradeDate = $_date = null;
            unset($_eTradeDate, $_date);
        }
    }
}

$from_date         = null;
$to_date           = null;
$roc_from_date     = null;
$roc_to_date       = null;
$expense_from_date = null;
$expense_to_date   = null;
$econtract_list    = null;
$cIds              = null;
$cId               = null;
$found             = null;

unset($from_date, $to_date, $roc_from_date, $roc_to_date, $expense_from_date, $expense_to_date, $econtract_list, $cIds, $cId, $found);

/** 確認入帳時間是否為時間內的業務 */
function belongTSales(&$conn, $sId, $eTradeDate, $sales)
{
    $date = (substr($eTradeDate, 0, 3) + 1911) . '-' . substr($eTradeDate, 3, 2) . '-' . substr($eTradeDate, 5, 2);

    $sql = 'SELECT
                sDate, sSales
            FROM
                tSalesRegionalAttributionForPerformance AS a WHERE sType = 1 AND sStoreId = "' . $sId . '" AND sDate <= "' . $date . '" ORDER BY sDate DESC LIMIT 1;';
    $rs = $conn->Execute($sql);

    if ($rs->EOF) {
        return false;
    }

    if ($rs->fields['sSales'] == $sales) {
        return true;
    }

    return false;
}
