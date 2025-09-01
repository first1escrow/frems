<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$export = $_REQUEST['export'] ;

if ($export == 'ok') {
	
	

	//
	$city = $_REQUEST['city'] ;
	$area = $_REQUEST['area'] ;
	
	$Af_year = $_REQUEST['Af_year'] ;
	$Af_month = $_REQUEST['Af_month'] ;
	$At_year = $_REQUEST['At_year'] ;
	$At_month = $_REQUEST['At_month'] ;
	
	$Bf_year = $_REQUEST['Bf_year'] ;
	$Bf_month = $_REQUEST['Bf_month'] ;
	$Bt_year = $_REQUEST['Bt_year'] ;
	$Bt_month = $_REQUEST['Bt_month'] ;
	
	$incoming = $_REQUEST['incoming'] ;
	$numbering = $_REQUEST['numbering'] ;
	$HDnumbering = $_REQUEST['HDnumbering'] ;
	
	$brA = $_REQUEST['branchA'] ;
	$brB = $_REQUEST['branchB'] ;
	
	$twhgBranch = $_REQUEST['twhgBranch'] ;
	$twhgBranch2 = $_REQUEST['twhgBranch2'] ;
	$statusOff = $_REQUEST['statusOff'] ;
	##

	//
	include_once 'realtyCompScrivenerExcel.php' ;
	##

}

//
$y = '' ;
$yr = date("Y") - 1911 ;

for ($i = 0 ; $i < $yr ; $i ++) {
	$y .= '<option value="'.($yr - $i).'"' ;
	if ($i == 0) {
		$y .= ' selected="selected"' ;
	}
	$y .= '>'.($yr - $i)."</option>\n" ;
}
##

//
$m = '' ;
$mn = date("n") ;

for ($i = 1 ; $i <= 12 ; $i ++) {
	$m .= '<option value="'.$i.'"' ;
	if ($i == $mn) {
		$m .= ' selected="selected"' ;
	}
	$m .= '>'.$i."</option>\n" ;
}
##

//
$a = '' ;

$sql = 'SELECT * FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$a .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//
// $b = '' ;

// $sql = 'SELECT * FROM tBranch WHERE bId<>"0" AND bCategory<>"3" ORDER BY bId ASC;' ;
// $rs = $conn->Execute($sql) ;
// while (!$rs->EOF) {
// 	$b .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bStore']."</option>\n" ;
	
// 	$rs->MoveNext() ;
// }
$sql = "SELECT * FROM tScrivener WHERE sStatus != 3 ORDER BY sId ASC"; //3¬O­«½ÆÁäÀÉ

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$b .= '<option value="'.$rs->fields['sId'].'">'.$rs->fields['sOffice']."</option>\n" ;
	$rs->MoveNext();
}
##

$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign("a",$a) ;
$smarty->assign("b",$b) ;

$smarty->display('realtycompScrivener.inc.tpl', '', 'report');
?>
