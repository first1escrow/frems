<?php
include_once '../session_check.php' ;
// include_once '../opendb.php' ;
include_once '../openadodb.php' ;

$q = $_GET['q'] ;
if (!$q) return ;

$query = '
SELECT cName,cIdentifyId 
FROM tContractOwner 
WHERE 
(cName LIKE "%'.$q.'%" OR cIdentifyId LIKE "%'.$q.'%" )
GROUP BY
 cIdentifyId,cName
ORDER BY cName ASC;
' ;
$rs = $conn->Execute($query);
while (!$rs->EOF) {

	echo '('.$rs->fields['cIdentifyId'].')'.$rs->fields['cName']."\n" ;

	$rs->MoveNext();
}

//其他賣方
$sql="SELECT cIdentifyId,cName FROM tContractOthers WHERE cIdentity = 2 AND (cName LIKE '%".$q."%' OR cIdentifyId LIKE '%".$q."%' ) GROUP BY cIdentifyId,cName ORDER BY cName ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	echo '('.$rs->fields['cIdentifyId'].')'.$rs->fields['cName']."\n" ;


	$rs->MoveNext();
}
$conn->close();

?>