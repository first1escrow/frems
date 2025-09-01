<?php
include_once '../../configs/config.class.php';
include_once 'class/advance.class.php';
include_once '../../session_check.php' ;

echo "<pre>";

$advance = new Advance();
$sql = "Select * from tBranch; ";
$branch = $advance->GetSql($sql);
$sql = " SELECT * FROM  `tContractRealestate`;  "; 
$rl = $advance->GetSql($sql);

foreach ($rl as $k => $v) {
    foreach ($branch as $k2 => $v2) {
         if ($v['cTelMain'] == $v2['bTelMain'] && !empty($v['cTelMain']) && !empty($v2['bTelMain'])) {
             $sql = "update tContractRealestate Set 
                            cBranchNum = '".$v2['bId']."'
                        Where cCertifyId = '".$v['cCertifyId']."';";
             echo $sql;
             break;
        }
    }
}



?>
