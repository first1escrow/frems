<?php
include_once '../openadodb.php' ;

$sql = '
	SELECT
		sId,
		sName
	FROM
		tScrivener
	WHERE
		sName LIKE "%'.$_REQUEST['term'].'%"
	ORDER BY
		sName
	ASC;
' ;

$rs = $conn->Execute($sql) ;

$list = array() ;
$i = 0 ;

while (!$rs->EOF) {
	$list[] = $rs->fields['sName'] ;
	$rs->MoveNext() ;
}

$str = implode('","',$list) ;
if ($str) $str = '["'.$str.'"]' ;

echo $str ;
?>