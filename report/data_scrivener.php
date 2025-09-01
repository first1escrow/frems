<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

 $_GET = escapeStr($_GET) ;

$q = $_GET['q'] ;
if (!$q) return ;

$query = 'SELECT sId,sName FROM tScrivener GROUP BY sName ASC;' ;
$rs = $conn->Execute($query);
while (!$rs->EOF) {
	if (preg_match("/".$q."/",$rs->fields['sName'])) {
		echo '(SC'.sprintf("%04d",$rs->fields['sId']).')'.$rs->fields['sName']."\n" ;
		//echo $tmp['sName']."\n" ;
	}

	$rs->MoveNext();
}

$conn->close();
?>