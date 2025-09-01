<?php
include_once '../openadodb.php' ;
include_once '../includes/sales/getSalesArea.php';

$_POST = escapeStr($_POST) ;

$branch = $_POST['branch'];

$sales = $_POST['sales']; //array

$cat = $_POST['cat'];

$date = $_POST['date'];

$date = (substr($date, 0,3)+1911).substr($date, 3);



if (empty($sales)) {
	die("請選擇業務");
}

sort($sales);
$check = 0;
foreach ($branch as $k => $v) {

	//設定公司會以如果店家有簽約業務，就歸給簽約業務，沒有就公司

	if (in_array(66,$sales)) {
		$signSales = getSignSales(2,$v,66);
		$salesArr = ($signSales)?$signSales:$sales;
		
		setBranchSales($v,$sales);
		

		setSalesRegionalAttribution($v,$sales,$date);
		$check++;

	}else{

		setBranchSales($v,$sales);
		setSalesRegionalAttribution($v,$sales,$date);
		$check++;		
	}

	

}

##

function setSalesRegionalAttribution($branch,$sales,$date){
	global $conn;
	$zip = getBranchzip($branch);

	//檢查是否有設定過同時間業務，如果有就刪除
	$sql = "SELECT sId FROM tSalesRegionalAttribution WHERE sType = '2' AND sStoreId = '".$branch."' AND sDate = '".$date."'";
	$rs = $conn->Execute($sql);

	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$sql = "DELETE FROM tSalesRegionalAttribution WHERE sId = '".$rs->fields['sId']."'";
			$conn->Execute($sql);
			$rs->MoveNext();
		}
			
	}

	// print_r($zip);
	foreach ($sales as  $value) {



		$sql = "INSERT INTO tSalesRegionalAttribution(sType,sZip,sStoreId,sSales,sDate,sCreatTime) VALUES('2','".$zip['bZip']."','".$branch."','".$value."','".$date."','".date('Y-m-d H:i:s')."')";

		// echo $sql."\r\n";
		$conn->Execute($sql);
	}
	
}

if ($check == count($branch)) {
	echo '成功';
}else{
	echo '';
}

exit;

// function setBranchSales2($bid,$sales){
// 	global $conn;
// 	$sql = "DELETE FROM tBranchSales WHERE bBranch = '".$bid."'";

// 	// echo $sql."\r\n";
// 	$conn->Execute($sql);

	
// 	foreach ($sales as  $value) {
// 		$sql = "INSERT INTO tBranchSales(bSales,bBranch,bStage) VALUES('".$value."','".$bid."','1')";
// 		// echo $sql."\r\n";
// 		$conn->Execute($sql);
// 	}
	
	
// }
?>