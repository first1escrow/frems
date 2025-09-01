<?php
include_once '../openadodb.php' ;

$cid = trim($_POST['id']);
$item = trim($_POST['ditem']);


$sql="DELETE FROM tContractProperty WHERE cCertifiedId='".$cid."' AND cItem ='".$item."'";


$conn->Execute($sql);

$sql = "DELETE FROM tContractPropertyObject WHERE cCertifiedId = '".$cid."' AND cBuildItem='".$item."'";
$conn->Execute($sql);

header("Location:formbuyowneredit.php?id=".$cid ."");




?>