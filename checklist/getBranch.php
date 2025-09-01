<?php
include_once('../openadodb.php') ;

$sql = '
	SELECT
		*
	FROM
		tBank
	WHERE
		bBank3="'.$_REQUEST['m'].'"
		AND bBank4<>""
	ORDER BY
		bBank4
	ASC;
' ;
$rs = $conn->Execute($sql) ;
$i = 0 ;
while (!$rs->EOF) {
	$chk = '' ;
	if ($i == 0) { $chk = ' selected="selected"' ; }
	echo '<option value="'.$rs->fields['bBank4'].'"'.$chk.'>'.$rs->fields['bBank4_name'].'('.$rs->fields['bBank4'].')</option>'."\n" ;
	$rs->MoveNext() ;
	$i ++ ;
}

?>