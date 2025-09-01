<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/class/intolog.php' ;
include_once dirname(__DIR__) . '/openadodb.php' ;
include_once dirname(__DIR__) . '/openpdodb.php' ;
include_once dirname(__DIR__) . '/opendb.php';
include_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
// include_once 'feedBackData.php';
include_once dirname(__DIR__) . '/session_check.php' ;

if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}
$oldflag = 1;

include_once 'casefeedbackPDF2_resultPDF.php';

// $link3
// $link2
# 搜尋資訊
$smarty->assign('link',$link) ;
$smarty->assign('cat',$cat) ;
$smarty->assign('link2',$link2) ;
$smarty->assign('link3',$link3) ;
$smarty->display('casefeedbackPDF_result.inc.tpl', '', 'report');
?>