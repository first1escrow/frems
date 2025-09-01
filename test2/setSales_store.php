<?php
include_once '/var/www/html/first.twhg.com.tw/openadodb.php';


$sql = "SELECT * FROM tZipArea WHERE zCity IN('宜蘭縣')";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields['zZip'];
	$rs->MoveNext();
}

$date = "2020-08-13";
$str = "sZip IN (".@implode(',', $list).")";

unset($list);
$sql = "SELECT * FROM tSalesRegionalAttribution WHERE ".$str." GROUP BY sType,sStoreId " ;
// echo $sql."<bR>";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields;

	
	$rs->moveNext();
}

foreach ($list as $k => $v) {
	$sql = "SELECT * FROM tSalesRegionalAttribution WHERE sType = '".$v['sType']."' AND sStoreId = '".$v['sStoreId']."' AND sDate = '".$date."' " ;
	// echo $sql."<br><br>";
	$rs = $conn->Execute($sql);

	if ($rs->EOF) {
		// echo $k;
		// echo $v['sId'];
		// print_r($v);
		$sql = "INSERT INTO
					tSalesRegionalAttribution
				SET
					sType = '".$v['sType']."',
					sDate = '".$date."',
					sZip = '".$v['sZip']."',
					sStoreId = '".$v['sStoreId']."',
					sSales = 65";
					echo $sql.";<bR>";
		// $conn->Execute($sql);
	}

}
?>