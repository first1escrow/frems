<?php
$item = [];

$sql = 'SELECT
            *
        FROM
           tBuyerOwnerWebShow
        WHERE
            bCertifiedId = "' . $cCertifiedId . '" AND bTarget = "' . $_target . '" ORDER BY bDate ASC;';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $item[] = [
        'date'    => $rs->fields['bDate'],
        'kind'    => $rs->fields['bDetail'],
        'income'  => $rs->fields['bIncome'],
        'expense' => $rs->fields['bOutgoing'],
        'remark'  => $rs->fields['bDetail'],
    ];

    $rs->MoveNext();
}