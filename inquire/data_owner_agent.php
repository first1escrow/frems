<?php
include_once '../session_check.php' ;
// include_once '../opendb.php' ;
include_once '../openadodb.php';

$q = $_GET['q'] ;
if (!$q) return ;

$query = '
SELECT 
	cId,
	cCertifiedId,
	cContactName 
FROM 
	tContractOwner 
GROUP BY 
	cContactName
ASC;
' ;
$rs = $conn->Execute($query);
while (!$rs->EOF) {
	echo '*'.$rs->fields['cContactName']."\n" ;


	$rs->MoveNext();
}
$conn->close();
?>