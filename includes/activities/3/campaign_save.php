<?php

$_identity         = $_POST['act_identity_' . $v];
$_idNo             = $_POST['act_idNo_' . $v];
$_mailingAddrZip   = $_POST['act_mailingAddrZip_' . $v];
$_mailingAddr      = $_POST['act_mailingAddr_' . $v];
$_residenceAddrZip = $_POST['act_residenceAddrZip_' . $v];
$_residenceAddr    = $_POST['act_residenceAddr_' . $v];
$_bankMain         = $_POST['act_bankMain_' . $v];
$_bankBranch       = $_POST['act_bankBranch_' . $v];
$_bankAccount      = $_POST['act_bankAccount_' . $v];
$_bankAccountName  = $_POST['act_bankAccountName_' . $v];

$_extRecord = [];
foreach ($_identity as $_k => $_v) {
    $_extRecord[] = [
        "idNo"             => $_idNo[$_k],
        "bankMain"         => $_bankMain[$_k],
        "identity"         => $_identity[$_k],
        "bankBranch"       => $_bankBranch[$_k],
        "bankAccount"      => $_bankAccount[$_k],
        "mailingAddr"      => $_mailingAddr[$_k],
        "residenceAddr"    => $_residenceAddr[$_k],
        "mailingAddrZip"   => $_mailingAddrZip[$_k],
        "bankAccountName"  => $_bankAccountName[$_k],
        "residenceAddrZip" => $_residenceAddrZip[$_k],
    ];
}

$json = json_encode($_extRecord, JSON_UNESCAPED_UNICODE);

$sql = 'INSERT INTO tActivityRecordsExt
            (aActivityId, aIdentity, aStoreId, aContent)
        VALUES
            (' . $v . ', "' . $act_identity . '", ' . $store_save . ', "' . addslashes($json) . '")
        ON DUPLICATE KEY UPDATE
            aContent = "' . addslashes($json) . '";';
$conn->Execute($sql);
