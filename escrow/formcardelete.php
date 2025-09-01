<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;

$id = empty($_POST["cid"]) 
        ? $_GET["cid"]
        : $_POST["cid"];

$certifiedId = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];
$sql="DELETE FROM tContractParking  WHERE cId ='".$id."'";


$conn->Execute($sql);

header("Location: formcaredit.php?id=".$certifiedId);

?>