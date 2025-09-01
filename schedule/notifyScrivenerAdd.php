<?php
// require_once dirname(dirname(__FILE__)).'/openadodb.php' ;
include_once '/home/httpd/html/first.twhg.com.tw/openadodb.php' ;

$today = date('Y-m-d');
// $sql = "SELECT lLineId,lpId  FROM tLineAccount WHERE lIdentity = 'O' AND lStatus = 'Y'";

// $rs = $conn->Execute($sql);
// $sales = $rs->fields;

// $sql = "SELECT * FROM  tZipArea WHERE zZip";


$sql = 'SELECT sAddId,sAdd,sDeleteId,sDelete,sBrand FROM tSalesGetScrivenerCount WHERE sDate>="'.$today.'" AND sDate<="'.$today.'" ORDER BY sId ASC;' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields;

	$rs->MoveNext();
}

for ($i=0; $i < count($list); $i++) { 
	if ($list[$i]['sAdd'] > 0) {
	    $addId = implode(',', json_decode($list[$i]['sAddId']));
	    // print_r($addId);

	    $sql = "SELECT *,(SELECT zSales FROM tZipArea WHERE zZip = sZip) AS sales FROM tSalesGetScrivener WHERE sId IN(".$addId.")";
	    $rs = $conn->Execute($sql);
	    while (!$rs->EOF) {
	        $adding[$rs->fields['sales']][$rs->fields['sBrand']][] = "姓名:".$rs->fields['sName']."\r\n事務所名稱:".$rs->fields['sCompanyName']."\r\n電話:".$rs->fields['sTel']."\r\n地址:".$rs->fields['sAddress'] ;
	        $rs->MoveNext();
	    }
	}
}


foreach ($adding as $k => $v) {
	$msg = '';
	$sql = "SELECT lLineId FROM tLineAccount WHERE lIdentity = 'O' AND lStatus = 'Y' AND lpId = '".$k."'";
	// echo $sql."\r\n";
	$rs = $conn->Execute($sql);


	
	foreach ($v as $key => $value) {
		// print_r($value);
		$msg .= $key."新增地政士\r\n";
		$msg .= date('Y年m月d日')."\r\n";
		$msg .= implode("\r\n\r\n", $value)."\r\n";
	}
	// $userId = 'U4b14569b842b0d5d4613b77b94af02b6';
	// $userId = $rs->fields['lLineId'];
	$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$userId."&txt=".urlencode($msg);
	echo $url."\r\n";
	file_get_contents($url);

	// $url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=U4b14569b842b0d5d4613b77b94af02b6&txt=".urlencode($msg);
	// echo $url."\r\n";
	// file_get_contents($url);
	// echo $msg;
	// $msg .= $v['']

}

// $sql = "SELECT lLineId,lpId FROM tLineAccount WHERE lIdentity = 'O' AND lStatus = 'Y' AND lpId = '".."'";
// print_r($adding);


?>