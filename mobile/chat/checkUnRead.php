<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
// require_once 'snoopy/Snoopy.class.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;
 $_POST = escapeStr($_POST) ;

$undertaker = $_SESSION['member_id'];

##test value##
if ($undertaker == 6) {
	$undertaker = 22 ;
}

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

$rs = $conn->Execute($sql);
$count = 0;
while (!$rs->EOF) {
	
	$count += getUnRead($rs->fields['aId']);	
	$rs->MoveNext();
}

echo $count;

function getUnRead($id){
	global $conn;
	$sql = "SELECT COUNT(id) AS unRead FROM tAppMessages WHERE aFlow = 1 AND aRead = 'N' AND aAccount ='".$id."' ";
	
	$rs = $conn->Execute($sql);

	return $rs->fields['unRead'];
}
// $undertaker = 19;
#####
?>