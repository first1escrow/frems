<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../configs/config.class.php';
include_once 'class/getAddress.php' ;

$_POST = escapeStr($_POST) ;

##
$sql = "SELECT sScrivener FROM tScrivenerLevel WHERE sId = '".$_POST['sId']."'";
$rs = $conn->Execute($sql);
$scrivener = $rs->fields['sScrivener'];
##

$sql = "SELECT *,(SELECT zCity FROM tZipArea WHERE zZip = fZipR) AS city FROM tFeedBackData WHERE fType = 1 AND fStatus = 0 AND  fStoreId = '".$scrivener."'";
$rs = $conn->Execute($sql);

$feed = $rs->fields;

$feed['AreaOption'] = listArea($conn,$feed['fZipR']);

echo json_encode($feed);

?>