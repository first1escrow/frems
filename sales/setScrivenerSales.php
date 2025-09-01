<?php
include_once '../openadodb.php' ;
include_once '../includes/sales/getSalesArea.php';

$_POST = escapeStr($_POST) ;

$scrivener = $_POST['branch'];

$sales = $_POST['sales'];


$date = $_POST['date'];

$date = (substr($date, 0,3)+1911).substr($date, 3);

// $salesArr = array($sales);

$check = false;

if (empty($sales)) {
	die("請選擇業務");
}
sort($sales);


$check = 0;
foreach ($scrivener as $k => $v) {

	//設定公司會以如果店家有簽約業務，就歸給簽約業務，沒有就公司

	if (in_array(66,$sales)) {
		$signSales = getSignSales(1,$v,66);
		$salesArr = ($signSales)?$signSales:$sales;
		
		setScrivenerSales($v,$sales);
		

		setSalesRegionalAttribution($v,$sales,$date);
		$check++;

	}else{

		setScrivenerSales($v,$sales);
		setSalesRegionalAttribution($v,$sales,$date);
		$check++;		
	}

	

}

##
if ($check == count($scrivener)) {
	echo '成功';
}else{
	echo '失敗';
}
function setSalesRegionalAttribution($scrivener,$sales,$date){
	global $conn;
	$zip = getScrivenerzip($scrivener);

	//檢查是否有設定過同時間業務，如果有就刪除
	$sql = "SELECT sId FROM tSalesRegionalAttribution WHERE sType = '1' AND sStoreId = '".$scrivener."' AND sDate = '".$date."'";
	$rs = $conn->Execute($sql);
	// echo $sql."\r\n";

	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$sql = "DELETE FROM tSalesRegionalAttribution WHERE sId = '".$rs->fields['sId']."'";
			// echo $sql."\r\n";
			$conn->Execute($sql);
			$rs->MoveNext();
		}
			
	}

	// print_r($zip);
	foreach ($sales as  $value) {

		$sql = "INSERT INTO tSalesRegionalAttribution(sType,sZip,sStoreId,sSales,sDate,sCreatTime) VALUES('1','".$zip['sCpZip1']."','".$scrivener."','".$value."','".$date."','".date('Y-m-d H:i:s')."')";

		// echo $sql."\r\n";
		$conn->Execute($sql);
	}
	
}




exit;






?>