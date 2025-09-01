<?php
include_once '../../openadodb.php';

$id = $_POST['id'];
$val = $_POST['val'];
// 顯示填寫欄位
// 1.多筆賣方一定顯示
// 2.只有一個賣方有多帳戶也顯示(戶名有非他本人名子)
// 3.賣方姓名不等於戶名(不包含多筆賣方情況)

if ($val) {
    //多筆賣方一定顯示
    $sql = "SELECT cName FROM  tContractOthers WHERE cCertifiedId = '".$id."' AND cIdentity = 2";
    $rs = $conn->Execute($sql);
    $total = $rs->RecordCount();
    // echo $sql."\r\n";
    if ($total > 0) {
        echo 'fail';

        die;
    }

    //只有一個賣方有多帳戶也顯示(戶名有非他本人名子)
    $sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId='".$id."' AND cChecklistBank = 0 ORDER BY cIdentity ASC";
    // echo $sql;
    $rs = $conn->Execute($sql);
    $total = $rs->RecordCount();
    //$ownerBankNameArr

    while (!$rs->EOF) {
        $ownerBankNameArr[] = $rs->fields['cBankAccountName'];

        $rs->MoveNext();
    }
    for ($i=0; $i < count($ownerBankNameArr); $i++) { 
            if ($val != $ownerBankNameArr[$i]) {
               echo 'fail';
               die;
            }
    }
    

    $sql = "SELECT cName FROM  tContractOwner WHERE cName = '".$val."' AND cCertifiedId = '".$id."'";   
    $rs = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        echo 'ok';

        die;
    }



   
}else{
    echo 'fail';
}


// 
exit;
?>