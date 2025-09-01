<?php
include_once '../session_check.php' ;
// include_once '../opendb.php' ;
include_once '../openadodb.php';

$q = $_GET['q'] ;
if (!$q) return ;

$query = '
SELECT sId,sName,sStatus 
FROM tScrivener 
ORDER BY sId ASC;
' ;
$rs = $conn->Execute($query);
while (!$rs->EOF) {
		
	//1:啟用、2:停用、3:重複建檔、4:未簽約
	if ($rs->fields['sStatus'] == 2) {
		$rs->fields['sStatus'] = '[停用]';
	}elseif ($rs->fields['sStatus'] == 3) {
		$rs->fields['sStatus'] = '[重複建檔]';
	}elseif ($rs->fields['sStatus'] == 4) {
		$rs->fields['sStatus'] = '[未簽約]';
	}else{
		$rs->fields['sStatus'] = '';
	}

	$code = 'SC'.sprintf("%04d",$rs->fields['sId']);

	if (preg_match("/$q/isu",$rs->fields['sName']) || preg_match("/$q/isu",$code)) {

		echo '('.$code.')'.$rs->fields['sName'].$rs->fields['sStatus']."\n" ;
		//echo $tmp['sName']."\n" ;
	}


	$rs->MoveNext();
}
$conn->close()

?>