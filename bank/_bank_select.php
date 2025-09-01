<?php
include_once '../session_check.php' ;
include_once '../openadodb.php';

$bank3 = $_REQUEST["bank3"];

$bank3 = $_REQUEST["bank3"];
$bank4 = $_REQUEST["b4"];

// if ($bank4) {
// 	$str = "AND bBank4 = '".$bank4."'";
// }

$sql = "select * from tBank where bBank3='$bank3' and bBank4 <> '' AND bOK = 0 order by bBank4 asc";
//echo $sql;
$rs = $conn->Execute($sql);	
	$_str = '<option value="">請選擇分行</option>';
while( !$rs->EOF ) {
	$_bank_name = str_replace("　","",trim($rs->fields["bBank4_name"]));
	
	$selected = ($bank4 == $rs->fields["bBank4"])?'selected=selected':'';

	$_str .= '<option value="'. $rs->fields["bBank4"].'" '.$selected.'>'. "(".$rs->fields["bBank4"].")".$_bank_name.'</option>';
	$rs->MoveNext();
}
echo $_str;
?>