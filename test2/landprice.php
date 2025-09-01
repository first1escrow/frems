<?php
require_once '../openadodb.php' ;



$sql = "SELECT cc.cCertifiedId,cl.cItem FROM tContractLand AS cl LEFT JOIN tContractCase AS cc ON cc.cCertifiedId =cl.cCertifiedId WHERE cItem = 0 ";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
   
   
   $sql = "SELECT * FROM tContractLandPrice WHERE cLandItem = '".$rs->fields['cItem']."' AND cCertifiedId = '".$rs->fields['cCertifiedId']."'";
   // echo $sql."\r\n";
   $rs2 = $conn->Execute($sql);
   $total=$rs2->RecordCount();

    if ( $total  == 0) {
        $sql = "INSERT INTO tContractLandPrice SET
                cCertifiedId ='".$rs->fields['cCertifiedId']."',
                cLandItem = '".$rs->fields['cItem']."',
                cItem = 0,
                cMoveDate = '',
                cLandPrice = ''";

                echo $sql."\r\n";
                $conn->Execute($sql);

                  $sql = "INSERT INTO tContractLandPrice SET
                cCertifiedId ='".$rs->fields['cCertifiedId']."',
                cLandItem = '".$rs->fields['cItem']."',
                cItem = 1,
                cMoveDate = '',
                cLandPrice = ''";

                echo $sql."\r\n";
                $conn->Execute($sql);
    }elseif ($total == 1) {
        $sql = "INSERT INTO tContractLandPrice SET
                cCertifiedId ='".$rs->fields['cCertifiedId']."',
                cLandItem = '".$rs->fields['cItem']."',
                cItem = 1,
                cMoveDate = '',
                cLandPrice = ''";

                echo $sql."\r\n";
                $conn->Execute($sql);


    }


    
    


    $rs->MoveNext();
}

// $sql = "SELECT cMoveDate,cLandPrice,cCertifiedId,cItem,cPower1,cPower2 FROM tContractLand WHERE cMeasure != ''";

// $rs = $conn->Execute($sql);
// $list = array();
// while (!$rs->EOF) {
//     array_push($list, $rs->fields);

//     $rs->MoveNext();
// }

// foreach ($list as  $value) {
  
//     $sql = "SELECT * FROM tContractLandPrice WHERE cCertifiedId = '".$value['cCertifiedId']."' AND cLandItem = '".$value['cItem']."'";

//     $rs = $conn->Execute($sql);

//     if ($rs->EOF) {
//         $sql = "INSERT INTO tContractLandPrice SET
//                 cCertifiedId ='".$value['cCertifiedId']."',
//                 cLandItem = '".$value['cItem']."',
//                 cItem = 0,
//                 cMoveDate = '".$value['cMoveDate']."',
//                 cLandPrice = '".$value['cLandPrice']."'";

      
       
//     }else{
//         $sql = "UPDATE tContractLandPrice SET
//                 cCertifiedId ='".$value['cCertifiedId']."',
//                 cLandItem = '".$value['cItem']."',
//                 cItem = 0,
//                 cMoveDate = '".$value['cMoveDate']."',
//                 cLandPrice = '".$value['cLandPrice']."' WHERE cId = '".$rs->fields['cId']."'";
        
//     }
//     echo $sql."\r\n";
//     $conn->Execute($sql);
//       // print_r($value);
   
// }
?>