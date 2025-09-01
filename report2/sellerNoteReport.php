<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$today = ($_POST['today'])? $_POST['today']:(date('Y')-1911).'-'.date('m-d');

if ($_SESSION['member_id'] == 6) {
	include_once dirname(dirname(__FILE__)).'/includes/report/sellerNoteReportApi.php';
}else{
	include_once dirname(dirname(__FILE__)).'/includes/report/sellerNoteReportApi.php';
}



$smarty->assign('today',$today);
$smarty->assign('sellerNote', $sellerNote) ;

$smarty->display('sellerNoteReport.inc.tpl', '', 'report2') ;
?>