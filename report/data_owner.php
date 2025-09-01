<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

 $_GET = escapeStr($_GET) ;

$q = $_GET['q'] ;
if (!$q) return ;

$query = 'SELECT cId,cCertifiedId,cName,cIdentifyId FROM tContractOwner GROUP BY cName ASC;' ;
$rs = $conn->Execute($query);

while (!$rs->EOF) {
	if (preg_match("/".$q."/", $rs->fields['cName'])) {
		echo '('.$rs->fields['cIdentifyId'].')'.$rs->fields['cName']."\n" ;
	}

	$rs->MoveNext();
}

$conn->close();
?>