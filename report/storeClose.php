<?php
require_once dirname(dirname(__FILE__)).'/configs/config.class.php';
require_once dirname(dirname(__FILE__)).'/class/SmartyMain.class.php';
require_once dirname(dirname(__FILE__)).'/openadodb.php' ;
require_once dirname(dirname(__FILE__)).'/session_check.php' ;
require_once dirname(dirname(__FILE__)).'/includes/lib.php' ;

if ($_POST['xls'] == 'ok') {
	//print_r ($_POST) ;
	require_once 'storeCloseExcel.php' ;
	exit ;
}

$qCity = '' ;
// $arr = array() ;
// // $arr = getCity() ;
// foreach ($arr as $k => $v) {
// 	$qCity .= '<option value="'.$v.'">'.$v."</option>\n" ;
// }



$z_str = '';
if($_SESSION['member_test'] != 0){
	$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
	
		
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}
		$z_str = " AND zZip IN(".implode(',', $test_tmp).")";
		unset($test_tmp);
    
}else if ($_SESSION['member_pDep'] == 7) {
	$z_str = 'AND FIND_IN_SET('.$_SESSION['member_id'].',zSales)';
}

//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1 '.$z_str.'  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);

$qCity = '' ;
while (!$rs->EOF) {
	$qCity .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;

	$rs->MoveNext();

}

unset($arr) ;

$smarty->assign('qCity', $qCity) ;

$smarty->display('storeClose.inc.tpl', '', 'report') ;
?> 
