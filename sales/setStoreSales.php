<?php
include_once dirname(dirname(__FILE__)).'/includes/openadodb.php' ;

$today = date('Y-m-d H:i:s');

$sql = "SELECT * FROM tBranch WHERE bCreat_time <= '".$today."'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$store[] = $rs->fields;

	$rs->MoveNext();
}

foreach ($store as $k => $v) {
	$sql = "SELECT * FROM tSalesRegionalAttribution WHERE sType = 2 AND sStoreId = '".$v['bId']."'";
	$rs = $conn->Execute($sql);

	$total=$rs->RecordCount();

	if ($total == 0) {
		$sales = getSales(2,$v['bId']);
		foreach ($sales as $key => $value) {
			$sql = "INSERT INTO	
					tSalesRegionalAttribution
				SET
					sType = 2,
					sDate = '0000-00-00',
					sZip = '".$v['bZip']."',
					sStoreId = '".$v['bId']."',
					sSales = '".$value['bSales']."',
					sCreatTime = '".date('Y-m-d H:i:s')."'";
			$conn->Execute($sql);
			// echo $sql."\r\n";
			echo 'Branch:'.$v['bId']."_".$value['bSales']."\r\n";
		}

		unset($sales);
		
	}

}
unset($store);
##
$sql = "SELECT * FROM tScrivener WHERE sCreat_time <= '".$today."'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$store[]= $rs->fields;

	$rs->MoveNext();
}

foreach ($store as $k => $v) {
	$sql = "SELECT * FROM tSalesRegionalAttribution WHERE sType = 1 AND sStoreId = '".$v['sId']."'";
	$rs = $conn->Execute($sql);

	$total = $rs->RecordCount();

	if ($total == 0) {
		$sales = getSales(1,$v['sId']);

		foreach ($sales as $key => $value) {
			$sql = "INSERT INTO	
					tSalesRegionalAttribution
				SET
					sType = 1,
					sDate = '0000-00-00',
					sZip = '".$v['sCpZip1']."',
					sStoreId = '".$v['sId']."',
					sSales = '".$value['sSales']."',
					sCreatTime = '".date('Y-m-d H:i:s')."'";
				echo 'Branch:'.$v['sId']."_".$value['sSales']."\r\n";
				$conn->Execute($sql);
		}

		unset($sales);
	}
}

##
function getSales($type,$storeId){
	global $conn;

	if ($type == 2) {
		$sql = "SELECT * FROM tBranchSales WHERE bBranch = '".$storeId."'";
		$rs = $conn->Execute($sql);
	}else{
		$sql = "SELECT * FROM tScrivenerSales WHERE sScrivener = '".$storeId."'";
		$rs = $conn->Execute($sql);
	}

	// echo $sql;
	// die;

	while (!$rs->EOF) {
		$sales[] = $rs->fields;

		$rs->MoveNext();
	}


	return $sales;
	
}
?>