<?php
// include_once '../../configs/config.class.php';
// include_once 'class/SmartyMain.class.php';
// include_once 'class/brand.class.php';
// include_once '../../session_check.php' ;
require_once dirname(dirname(__DIR__)).'/first1DB.php';


Function GetBranchList($brand, $category) {
    $conn = new first1DB;

    $sql = " SELECT *, 
                CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0'), year(Now()) ) bCode2
                FROM  
                `tBranch` b 
                WHERE b.bBrand = '".$brand."' AND  bCategory = '".$category."' AND bStatus  = '1' 
                ORDER BY b.bStore ASC; ";
    return $conn->all($sql);
}
    
Function GetBrand($id) {
    $conn = new first1DB;
    $sql = " SELECT * FROM  `tBrand` Where bId = '".$id."' ;";
    return $conn->one($sql);
}


$result1 = GetBranchList($_POST["id"], $_POST["category"]);
$result2 = GetBrand($_POST["id"]);

$result = array();
$result[0] = $result1;
$result[1] = $result2;

echo json_encode($result);
?>
