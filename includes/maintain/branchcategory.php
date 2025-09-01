<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once 'class/getAddress.php' ;
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

$brand = new brand();
$result = $brand->GetCategoryBranch($_POST["id"]);

$result['bAddress'] = filterCityAreaName($conn,$result['bZip'],$result['bAddress']) ;
$result['bCity'] = getCityName($conn,$result['bZip']) ;
$result['bArea'] = getAreaName($conn,$result['bZip']) ;

echo json_encode($result);
?>