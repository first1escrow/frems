<?php

include_once '../session_check.php' ;
// include('../opendb.php') ;
include_once '../openadodb.php';

$q = $_GET['q'] ;

if (!$q) return ;

$sql = '
SELECT
	bId,bName,bStore,bBrand,bStatus,
	(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode 
FROM
	tBranch AS b
';
if(isset($_GET['brand'])) {
    $sql.= ' WHERE bBrand = '.$_GET['brand'];
}
$branch = array();
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	if ($rs->fields['bStatus'] == 2) {
		$rs->fields['bStatus'] = '[停用]';
	}elseif ($rs->fields['bStatus'] == 3) {
		$rs->fields['bStatus'] = '[暫停]';
	}else{
		$rs->fields['bStatus'] = '';
	}
	$code = $rs->fields['bCode'].sprintf("%05d",$rs->fields['bId']);

	if (preg_match("/$q/isu",$rs->fields['bStore']) || preg_match("/$q/isu",$code)) {
		echo '('.$code.')'.$rs->fields['bStore'].$rs->fields['bStatus']."\n" ;
	}
	

	$rs->MoveNext();
}

$conn->close();
?>