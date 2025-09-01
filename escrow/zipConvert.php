<?php
include_once('../openadodb.php') ;
include_once '../session_check.php' ;

$city = $_POST['ct'] ;

$sql = 'SELECT * FROM tZipArea WHERE zCity LIKE "'.$city.'";' ;
$rs1 = $conn->CacheExecute($sql) ;
$_return = "\t\t\t\t\t\t\t".'<option value="">區域</option>'."\n" ;

while (!$rs1->EOF) {
	$_return .= "\t\t\t\t\t\t\t".'<option value="'.$rs1->fields['zZip'].'">'.$rs1->fields['zArea'].'</option>'."\n" ;
	$rs1->MoveNext() ;
}

echo $_return ;
//echo $sql ;
?>