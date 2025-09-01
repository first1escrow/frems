<?php
include_once '../../openadodb.php' ;

$scrivener = array(1961,1795,1363,1362,1361,1360,1351,1349,632);
$sql = "SELECT bAccount,bSID FROM tBankCode WHERE bUsed = 0 AND bDel = 'n' AND bSID IN (".implode(',', $scrivener).") AND bCreateDate <= '2019-12-31'";
// echo $sql;
// die;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	// echo "#".$rs->fields['bSID']."<br>";
	echo "UPDATE tBankCode SET bDel = 'y' WHERE bAccount = '".$rs->fields['bAccount']."';<br>";

	$rs->MoveNext();
}


?>