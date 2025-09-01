<?php
#顯示錯誤
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;
header("Content-Type:text/html; charset=utf-8"); 
$sql = "SELECT CONCAT('SC',LPAD(sId,4,'0')) as Code,sName,sOffice,sMobileNum,sFaxArea,sFaxMain FROM tScrivener WHERE sStatus = 1 AND sId NOT IN(620,224)";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if (!preg_match("/業務/", $rs->fields['sName'])) {
		$list[] = $rs->fields;
	}
	

	$rs->MoveNext();
}

$fw = fopen('log/unApplyLine.log', 'a+');
$txt = '';
foreach ($list as $k => $v) {
	$sql = "SELECT * FROM tLineAccount WHERE lTargetCode = '".$v['Code']."'";
	$rs = $conn->Execute($sql);

	if ($rs->EOF) {
		echo $v['Code']."_".$v['sName']."_".$v['sOffice']."_".$v['sFaxArea']."-".$v['sFaxMain']."\r\n";
		$txt .= $v['Code']."_".$v['sName']."_".$v['sOffice']."_".$v['sFaxArea']."-".$v['sFaxMain']."\r\n";
		
	}
}


fwrite($fw, $txt);
fclose($fw);

?>