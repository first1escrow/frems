<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';


$_POST = escapeStr($_POST) ;
$sId = trim($_POST['sId']);

$sql = "SELECT DISTINCT f.fCertifiedId 
        FROM `tFeedBackMoneyPayByCase` AS f 
            JOIN `tBankTrans` AS b 
                ON (f.fCertifiedId = b.tMemo and tKind = '保證費' and tObjKind != '履保費先收(結案回饋)') OR (f.fCertifiedId = b.tMemo and tInvoice IS NOT NULL)
        WHERE (tBankLoansDate >= '".date('Y-m-d')."' OR tBankLoansDate = '') AND fTargetId = " .$sId ;
$rs = $conn->Execute($sql);

$cId = '';

while (!$rs->EOF) {
    $cId .= $rs->fields['fCertifiedId'].' ';
    $rs->MoveNext();
}
$total=$rs->RecordCount();
if ($total > 0) {
    echo $cId;
    exit();
}
echo '1';

exit();
?>