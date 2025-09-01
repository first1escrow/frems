<?php
include_once('../openadodb.php') ;
include_once '../session_check.php' ;

$bank = $_POST['bk'] ;

$sql = 'SELECT * FROM tBank WHERE bBank3="'.$bank.'" AND bBank4<>"";' ;
$rs1 = $conn->CacheExecute($sql) ;
$_return = "\t\t\t\t\t\t\t".'<option value="">請選擇...</option>'."\n" ;

while (!$rs1->EOF) {
	$_return .= "\t\t\t\t\t\t\t".'<option value="'.$rs1->fields['bBank4'].'">'.$rs1->fields['bBank4_name'].'('.$rs1->fields['bBank4'].')</option>'."\n" ;
	$rs1->MoveNext() ;
}

echo $_return ;
//echo $sql ;
?>