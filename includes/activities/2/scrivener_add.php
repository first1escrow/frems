<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

//取得郵遞區號
$sql = 'SELECT zZip, zCity FROM tZipArea WHERE zCity IN ("台北市", "新北市", "桃園市", "屏東縣", "澎湖縣", "雲林縣", "嘉義市", "嘉義縣", "台南市", "台中市", "彰化縣", "南投縣", "新竹市", "新竹縣", "苗栗縣", "台東縣", "高雄市");';
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $zips[] = $rs->fields['zZip'];
    $rs->MoveNext();
}
// print_r($zips);exit;
##

/**
 * $_POST：相關資料
 * $newid：地政士代號
 */

if (in_array($_POST['zip2'], $zips)) { //符合郵遞區號區域，自動加入參加
    $v = 2; //活動代碼

    $sql = 'INSERT INTO
                tActivityRecords
            (
                aActivityId,
                aIdentity,
                aStoreId,
                aRule,
                aGift,
                aPriority
            ) VALUES (
                "' . $v . '",
                "S",
                "' . $newid . '",
                "2",
                "0",
                "N"
            );';
    $conn->Execute($sql);

    $_POST['id'] = $newid; //地政士號碼

    $_POST['act_identity_' . $v]         = $_POST['newIdentity'];
    $_POST['act_idNo_' . $v]             = $_POST['newIdentityNumber'];
    $_POST['act_mailingAddrZip_' . $v]   = $_POST['newzipC'];
    $_POST['act_mailingAddr_' . $v]      = $_POST['newaddrC'];
    $_POST['act_residenceAddrZip_' . $v] = $_POST['newzipR'];
    $_POST['act_residenceAddr_' . $v]    = $_POST['newaddrR'];
    $_POST['act_bankMain_' . $v]         = $_POST['newAccountNum'];
    $_POST['act_bankBranch_' . $v]       = $_POST['newAccountNumB'];
    $_POST['act_bankAccount_' . $v]      = $_POST['newAccount'];
    $_POST['act_bankAccountName_' . $v]  = $_POST['newAccountName'];

    require_once __DIR__ . '/scrivener_save.php';

    $act_identity = $store_save = null;
    unset($act_identity, $store_save);
}

$zips = $data = null;
unset($zips, $data);
