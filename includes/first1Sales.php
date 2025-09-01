<?php
include_once '../openadodb.php' ;

//取得業務人員清單
$menu_sales = array(0=>'全部') ;
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN ("4","7","8")  ORDER BY pId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$menu_sales[$rs->fields['pId']] = $rs->fields['pName'] ;

	if ($rs->fields['pJob'] == 2) {
		$menu_sales[$rs->fields['pId']] .='(離)';
	}
	$rs->MoveNext() ;
}
##

?>