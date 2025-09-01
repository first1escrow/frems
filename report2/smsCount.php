<?php
require_once '../configs/config.class.php';
require_once '../class/SmartyMain.class.php';
require_once '../openadodb.php' ;
require_once '../report/getBranchType.php' ;

// print_r($_POST) ; exit ;
$_POST = escapeStr($_POST) ;
$total = '';
if ($_POST) {
	if (preg_match("/^[0-9]{2,3}$/",$_POST['yearDate'])) $yearDate = $_POST['yearDate'] ;
	else {
		$yearDate = (date("Y", strtotime("-1 month")) - 1911) ;	
	}

	if (preg_match("/^[0-9]{2}$/",$_POST['monthDate'])) $monthDate = $_POST['monthDate'] ;
	else {
		$monthDate = date("m", strtotime("-1 month")) ;
		
	}	
	
	$FDate = ($yearDate + 1911).'-'.$monthDate.'-01' ;
	$TDate = ($yearDate + 1911).'-'.$monthDate.'-31' ;
	// echo $FDate.', '.$TDate."<br>\n" ; exit ;
	##

	$sql = "SELECT COUNT(id) AS smsTotal FROM tSMS_Log WHERE sSend_Time >= '".$FDate." 00:00:00' AND sSend_Time <= '".$TDate." 23:59:59'";
	$rs = $conn->Execute($sql);

	$total = '發送數量：'.$rs->fields['smsTotal'];
}


##
$yearOption = '' ;
for ($i = (date("Y") - 1911) ; $i > 100 ; $i --) {
	$yearOption .= '<option value="'.$i.'"' ;
	if ($i == $yearDate) $yearOption .= ' selected="selected"' ;
	$yearOption .= '>'.$i."</option>\n" ;
}

$monthOption = array() ;
for ($i = 1 ; $i <= 12 ; $i ++) {
	$monthOption .= '<option value="'.str_pad($i,2,'0',STR_PAD_LEFT).'"' ;
	if (str_pad($i,2,'0',STR_PAD_LEFT) == $monthDate) $monthOption .= ' selected="selected"' ;
	$monthOption .= '>'.str_pad($i,2,'0',STR_PAD_LEFT)."</option>\n" ;
}
##

$smarty->assign('total',$total) ;
$smarty->assign('m',preg_replace("/^0/",'',$monthDate)) ;
$smarty->assign('yearOption',$yearOption) ;
$smarty->assign('monthOption',$monthOption) ;
$smarty->display('smsCount.inc.tpl', '', 'report2') ;
?>