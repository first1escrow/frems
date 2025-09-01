<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
include_once dirname(dirname(__DIR__)) . '/session_check.php';


$certifiedIdSn = $_REQUEST["sn"];
$sn = substr($certifiedIdSn, 11);

$sql    = "
        SELECT 
            tAccount 
        FROM 
            tBankTrans 
        WHERE 
            tMemo='" . $sn . "' 
            AND 
                tObjKind IN ('代墊利息') 
            AND 
                tPayOk ='2' 
            AND 
                tObjKind2 != '01' AND tObjKind2 != '02'
            AND 
            tOwner = '" . $_SESSION['member_name'] . "'
        ";

$rs = $conn->Execute($sql);

$res['status'] = false;
$account = [];

if($rs->RecordCount() > 1) {
    while (!$rs->EOF) {
        $account[] =  $rs->fields["tAccount"];
        $rs->MoveNext();
    }
    $res['status'] = true;
    $res['account'] = $account;
}

echo json_encode($res);
exit();
