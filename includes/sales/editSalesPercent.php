<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$_POST = escapeStr($_POST) ;

$sess = ceil($_POST['Month'] / 3);

$season = [
	'1' => [
		's' => 1, 'e' => 3
	],
	'2' => [
		's' => 4, 'e' => 6
	],
	'3' => [
		's' => 7, 'e' => 9
	],
	'4' => [
		's' => 10, 'e' => 12
	],
];

$sql = "
UPDATE 
    `tSalesReportPercent` 
SET 
    pSign = '".$_POST['Sign']."',
 	pGroupTW = '".$_POST['GroupTW']."',
 	pGroupUnTW = '".$_POST['GroupUnTW']."',
 	pEffectiveBaseScore = '".$_POST['EffectiveBaseScore']."',
 	pUpdator = '".$_SESSION['member_id']."',
 	pUpdatedTime = '".date("Y-m-d h:i:s")."'
WHERE 
	pSalesId = '".$_POST['SalesId']."'  
	AND pYear = '".($_POST['Year']+1911)."' 
	AND pMonth >= '".$season[$sess]['s']."' 
	AND pMonth <= '".$season[$sess]['e']."' ";


if ($conn->Execute($sql)) {
	echo 'OK';
}



?>


