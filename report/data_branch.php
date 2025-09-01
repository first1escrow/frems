<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

 $_GET = escapeStr($_GET) ;

$q = $_GET['q'] ;
if (!$q) return ;

$query = 'SELECT bId,bName,bStore,bBrand,(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode FROM tBranch b GROUP BY bStore ASC;' ;
$rs = $conn->Execute($query);
while (!$rs->EOF) {
	if (preg_match("/".$q."/", $rs->fields['bStore'])) {
		echo '('.$rs->fields['bCode'].sprintf("%05d",$rs->fields['bId']).')'.$rs->fields['bStore']."\n" ;
	}


	$rs->MoveNext();
}

$conn->close();
?>