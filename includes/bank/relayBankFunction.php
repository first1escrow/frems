<?php

//第一建經各合約銀行活儲帳號
function getFirstBank(&$conn, $prefix)
{
    $prefix = ($prefix == 60001) ? 55006 : $prefix;
    $sql    = 'SELECT cBankMain, cBankBranch, cBankAccount, cAccountName FROM tContractBank WHERE cBankVR LIKE "' . $prefix . '%";';
    return $conn->one($sql);
}

//儲存資料
function saveDB(&$conn, $data)
{
    $sql = 'INSERT INTO
                tBankTransRelay
            (
                bUid,
                bCertifiedId,
                bVR_Code,
                bDate,
                bKind,
                bBankCode,
                bAccount,
                bAccountName,
                bMoney,
                bIncomingMoney,
                bTxt,
                bConfirmOk,
                bOrderTime,
                bCreated_at
            ) VALUES (
                UUID(),
                "' . $data['cId'] . '",
                "' . $data['vr_code'] . '",
                "' . $data['date'] . '",
                "' . $data['kind'] . '",
                "' . $data['bank_code'] . '",
                "' . $data['bank_account'] . '",
                "' . $data['bank_account_name'] . '",
                "' . $data['money'] . '",
                "' . $data['incoming_money'] . '",
                "' . $data['txt'] . '",
                1,
                "' . $data['order_time'] . '",
                NOW()
            );';
    return $conn->exeSql($sql);
}
