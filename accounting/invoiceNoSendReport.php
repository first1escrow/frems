<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$fds = trim($_POST['start']);
$fde = trim($_POST['end']);
$ex = trim($_POST['ex']);
if ($ex ==1) {
	
	
	include_once 'invoiceNoSendExcel.php';
}


$option = '<option value="">請選擇</option>';
$today_m = date('m');

for ($i=1; $i <= 12 ; $i++) { 
	
	if ($i < 10) {
		$m = '0'.$i;
	}else{
		$m = $i;
	}

	$option = "<option value='".$i."'>".$m."</option>";

	

}



$smarty->assign('option', $option) ;
$smarty->display('invoiceNoSendReport.inc.tpl', '', 'accounting');
?>