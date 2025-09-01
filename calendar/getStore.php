<?php
include_once '../openadodb.php' ;

$sql = '
	SELECT
		bId,
		bStore
	FROM
		tBranch
	WHERE
		bBrand="'.$_REQUEST['b'].'"
		AND bCategory="'.$_REQUEST['c'].'" 
		AND bStore LIKE "'.$_REQUEST['term'].'%"
	ORDER BY
		bStore
	ASC;
' ;

$rs = $conn->Execute($sql) ;

$list = array() ;
$i = 0 ;

while (!$rs->EOF) {
	$list[] = $rs->fields['bStore'] ;
	$rs->MoveNext() ;
}

$str = implode('","',$list) ;
if ($str) $str = '["'.$str.'"]' ;

echo $str ;
?>