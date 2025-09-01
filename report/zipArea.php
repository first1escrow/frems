<?php
include_once '../openadodb.php' ;


$city = $_REQUEST['c'] ;
$op = $_POST['op'] ;

$v = '<option value="" selected="selected">全區'."</option>\n" ;

$sql = 'SELECT * FROM tZipArea WHERE zCity="'.$city.'" '.$z_str .' ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	if ($op == '1') {
		$v .= '<option value="'.$rs->fields['zZip'].'">'.$rs->fields['zArea']."</option>\n" ;
	}
	else {
		$v .= '<option value="'.$rs->fields['zArea'].'">'.$rs->fields['zArea']."</option>\n" ;
	}
	$rs->MoveNext() ;
}

echo $v ;
?>