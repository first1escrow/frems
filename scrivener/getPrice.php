<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;
$scrivener = $_POST['sId'];

$sql = "SELECT
			sl.sLevel,
			s.sCategory
		FROM
			tScrivenerLevel AS sl
		LEFT JOIN
			tScrivener AS s ON s.sId = sl.sScrivener
		WHERE
			sl.sId = '".$scrivener."'";
$rs = $conn->Execute($sql);
// $data = $rs->fields;



$gift = getGift2($rs->fields['sLevel']);
if ($rs->fields['sCategory'] == 2) {
	$gift['gMoney'] = 2000;
}
echo json_encode($gift);

// if ($rs->fields['sCategory'] != 2) {
// 	$gift = getGift2($rs->fields['sLevel']);
// 	// print_r($gift);
	


// 	echo json_encode($gift);

// }else{
// 	$gift = getGift($rs->fields['sLevel']);
// 	//直營固定兩千
// 	if ($rs->fields['sCategory'] == 2) {
// 		$gift['gMoney'] = 2000;
// 	}

// 	$gift['Category'] = $rs->fields['sCategory'];
// 	$gift['count'] = 1;
// 	echo json_encode($gift);
// }


function getGift($level){
	global $conn;

	$sql = "SELECT gMoney,gCode,gName,gId FROM tGift WHERE sLevel = '".$level."'";
	$rs = $conn->Execute($sql);

	return $rs->fields;
}

function getGift2($level){
	global $conn;

	$sql = "SELECT gMoney,gCode,gName,gId FROM tGift WHERE (sLevel = '".$level."' OR sTop = 1)";
	$rs = $conn->Execute($sql);
	$i= 0;
	while (!$rs->EOF) {
		$gift['data'][$i] = $rs->fields;

		if ($i == 0) {
			$gift['gMoney'] = $rs->fields['gMoney'];
		}
		$i++;
		$rs->MoveNext();
	}

	$gift['count'] = count($gift['data']);
	return $gift;
}
?>