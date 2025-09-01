<?php

include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$undertaker = $_SESSION['member_id'];
$_POST = escapeStr($_POST) ;
// $_GET = escapeStr($_GET) ;
##test value##

if ($undertaker == 6) {
	$undertaker = 12 ;
}


// $undertaker = 19;
#####
$acc = $_POST['acc'] ;

$sql = "SELECT
			aa.aId,
			aa.aAccount,
			aa.aName,
			aa.aParentId,
			s.sOffice
		FROM
			tAppAccount AS aa,
			tScrivener AS s
		WHERE
			SUBSTR(aa.aParentId,3)=s.sId  
			AND sUndertaker1 = '".$undertaker."'
			AND s.sStatus = '1'
			AND aa.aStatus = 1
			AND aa.aIdentity = 1
			AND aa.aOK = 1
		ORDER BY aa.aParentId ASC

		";
// echo $sql;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$list[$rs->fields['aId']] = getUnRead($rs->fields['aId']);	
	$rs->MoveNext();
}

echo json_encode($list);
###
function getUnRead($id){
	global $conn;
	$sql = "SELECT COUNT(id) AS unRead FROM tAppMessages WHERE aFlow = 1 AND aRead = 'N' AND aAccount ='".$id."' ";

	$rs = $conn->Execute($sql);

	return $rs->fields['unRead'];
}


?>



