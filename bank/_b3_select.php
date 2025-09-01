<?php

include_once '../session_check.php' ;
include_once '../openadodb.php' ;
$bank3 = $_REQUEST["bank3"];
$sql = "select * from tBank where bCode not in ('1','7','4') and bBank4 = '' order by bCodeTitle , bBank3 asc  ";
//echo $sql;
$rs = $conn->Execute($sql);	
	$_str = '<option value="">請選擇銀行</option>';
while( !$rs->EOF ) {
	$_str .= '<option value="'. $rs->fields["bBank3"].'">'. trim($rs->fields["bBank4_name"]).'</option>';
	$rs->MoveNext();
}
echo $_str;
?>