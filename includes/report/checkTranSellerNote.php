<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once  dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$_POST = escapeStr($_POST) ;
$cId = $_POST['cId'];

include_once '/home/httpd/html/first.twhg.com.tw/includes/report/sellerNoteReportApi.php';

$check = false;
for ($i=0; $i < count($sellerNote); $i++) { 
	
	if ($sellerNote[$i]['cCertifiedId'] == $cId) { //未填寫保證號碼
		$check = true;
	}
}


if ($check) {
	echo 'error';
}
?>