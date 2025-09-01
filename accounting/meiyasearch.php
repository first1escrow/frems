<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$exports = trim(addslashes($_POST['exp'])) ;

//預載log物件
// $logs = new Intolog() ;
##

//輸出Excel檔案
if ($exports == 'ok') {
	$bank_option = trim(addslashes($_REQUEST['bke'])) ;
	$startDate = $fds = trim(addslashes($_REQUEST['fds'])) ;
	$endDate = $fde = trim(addslashes($_REQUEST['fde'])) ;
	
	// $logs->writelog('accChecklistExcel') ;
	include_once 'meiya_excel.php' ;
}
##



$smarty->display('meiyasearch.inc.tpl', '', 'accounting');
?>
