<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$export = $_REQUEST['export'] ;

if ($export == 'ok') {
	
	//��X Excel ��
	include_once 'NoCaseReportExcel.php' ;
	##

}

//�]�w��ܦ~��
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

//�]�w��ܤ��
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

//�]�w��ܰϰ�
$a = '' ;

$sql = 'SELECT * FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$a .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//�]�w��ܩҦ��򤶩�
$b = '' ;

$sql = 'SELECT * FROM tBranch WHERE bId<>"0" AND bCategory<>"3" ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$b .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bStore']."</option>\n" ;
	
	$rs->MoveNext() ;
}
##

$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign("a",$a) ;
$smarty->assign("b",$b) ;

$smarty->display('NoCaseReport.inc.tpl', '', 'report');
?>
