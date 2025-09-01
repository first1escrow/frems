<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
// include_once '../opendb.php' ;
include_once '../openadodb.php';
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;

if ($_REQUEST['xls'] == 'ok') {
	$tlog->exportWrite($_SESSION['member_id'], json_encode($_REQUEST), '實價登錄excel匯出') ;
	
	include_once 'excel.php' ;
	exit ;
}

$sql = 'SELECT DISTINCT zCity FROM tZipArea ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql);

$str = '' ;
while (!$rs->EOF) {
	$tmp = $rs->fields;
	
	$str .= '<option value="'.$tmp['zCity'].'">'.$tmp['zCity']."</option>\n" ;
	unset($tmp) ;

	$rs->MoveNext();
}

$yearMenu = '' ;
for ($i = 103 ; $i <= (date("Y") - 1911) ; $i ++) {
	$yearMenu .= '<option value="'.$i.'"' ;
	if ((date("Y") - 1911) == $i) $yearMenu .= ' selected="selected"' ;
	$yearMenu .= '>'.$i."年度</option>\n" ;
}

$monthMenu = '' ;
for ($i = 1 ; $i <= 12 ; $i ++) {
	$monthMenu .= '<option value="'.str_pad($i,2,'0',STR_PAD_LEFT).'"' ;
	if ((date("n") - 1) == $i) $monthMenu .= ' selected="selected"' ;
	$monthMenu .= '>'.str_pad($i,2,' ',STR_PAD_LEFT)."月份</option>\n" ;
}

$smarty->assign('cityMenu', $str) ;
$smarty->assign('monthMenu', $monthMenu) ;
$smarty->assign('yearMenu', $yearMenu) ;
$smarty->assign('web_addr', $web_addr) ;

$smarty->display('realPrice2.inc.tpl', '', 'sales');
?> 
