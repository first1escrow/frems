<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;

$sql = "SELECT
			z.zCity,
			s.sName
		FROM
			tSalesSign AS ss
		LEFT JOIN
			tScrivener AS s ON s.sId = ss.sStore
		LEFT JOIN
			tZipArea AS z ON z.zZip=s.sCpZip1
		WHERE
			ss.sType = 1 AND z.zCity IN('新北市','台北市','基隆市') AND ss.sSales != 0";
$rs = $conn->Execute($sql);
$list = array();
while (!$rs->EOF) {
	$list[$rs->fields['zCity']]['count']++;
	// $list[$rs->fields['zCity']]['data'][] = $rs->fields;

	$rs->MoveNext();
}

echo "scrivener\r\n";
print_r($list);


$sql = "SELECT
			z.zCity,
			b.bStore
		FROM
			tSalesSign AS ss
		LEFT JOIN
			tBranch AS b ON b.bId = ss.sStore
		LEFT JOIN
			tZipArea AS z ON z.zZip=b.bZip
		WHERE
			ss.sType = 2 AND zCity IN('新北市','台北市','基隆市') AND ss.sSales != 0";
$rs = $conn->Execute($sql);
$list2 = array();
while (!$rs->EOF) {
	$list2[$rs->fields['zCity']]['count']++;
	// $list[$rs->fields['zCity']]['data'][] = $rs->fields;

	$rs->MoveNext();
}

echo "branch\r\n";
print_r($list2);
?>