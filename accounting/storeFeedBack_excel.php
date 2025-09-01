<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
##

$_POST = escapeStr($_POST) ;


if ($_POST['allForm']) {

	$qstr = "sId IN(".@implode(',', $_POST['allForm']).")";
}

$sql = "SELECT * FROM tStoreFeedBackMoneyFrom WHERE ".$qstr." ORDER BY sSeason DESC,sType  DESC,sStoreId ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$list[] = $rs->fields;


	$rs->MoveNext();
}

$exportTime = date("YmdHis");

##
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("回饋金-台新整批匯款");
$objPHPExcel->getProperties()->setDescription("回饋金-台新整批匯款");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

$i = 0;
foreach ($list as $k => $v) {
	##回饋案件資料
	$dataCase = array();
	$sql = "SELECT SUM(sFeedBackMoney)AS sFeedBackMoney FROM tStoreFeedBackMoneyFrom_Case WHERE sFromId = '".$v['sId']."'";
	
	$rs = $conn->Execute($sql); 
	$feedbackmoneyTotal = $rs->fields['sFeedBackMoney'] ;
	$code = ($v['sStoreCode'] == 'SC')?$v['sStoreCode'].str_pad($v['sStoreId'], 4,'0',STR_PAD_LEFT):$v['sStoreCode'].str_pad($v['sStoreId'], 5,'0',STR_PAD_LEFT);

	
	// echo $feedbackmoneyTotal."_";
	##店回饋銀行資料
	$dataAccount = array();
	$sql = "SELECT
				sBankMain,
				sBankBranch,
	            sBankAccountNo,
	            sBankAccountName,
	            sBankMoney
	        FROM
	            tStoreFeedBackMoneyFrom_Account
	        WHERE
	            sFromId = '".$v['sId']."'";
	            // echo $sql;
	
	$rs = $conn->Execute($sql);
	$total = $rs->RecordCount();
	while (!$rs->EOF) {
		// echo $i."<br>";
	    $FeedBackAccount[$i] = $rs->fields;
	    // $FeedBackAccount[$i]['money'] = (($rs->fields['sBankMoney'] != 0 && $rs->fields['sBankMoney'] != '' && $total > 1))?$rs->fields['sBankMoney']:$feedbackmoneyTotal;
	   	$FeedBackAccount[$i]['money'] = ($total > 1)?$rs->fields['sBankMoney']:$feedbackmoneyTotal;
	   	$FeedBackAccount[$i]['formId'] = $v['sId'];
	   	$FeedBackAccount[$i]['sStoreCode'] = $v['sStoreCode'];
	    $FeedBackAccount[$i]['sStoreId'] = $v['sStoreId'];
	    $FeedBackAccount[$i]['storeId'] = $code;
	     $FeedBackAccount[$i]['season'] = $v['sSeason'];

	    $i++;
	    $rs->MoveNext();
	}
	##

	// 鍾岳和地政士事務所

	unset($feedbackmoneyTotal);
}


$row = 1;

foreach ($FeedBackAccount as $k => $v) {
	$col = 65;

	$bankcode = $v['sBankMain'].substr($v['sBankBranch'], 0,3);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['storeId'],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['season'],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, '',PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, '20680100151828',PHPExcel_Cell_DataType::TYPE_STRING); //付款帳號
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row, date('Ymd'),PHPExcel_Cell_DataType::TYPE_STRING);  //付款日期
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['sBankMain'],PHPExcel_Cell_DataType::TYPE_STRING); //收款帳號
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['sBankBranch'],PHPExcel_Cell_DataType::TYPE_STRING); //收款帳號
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['sBankAccountNo'],PHPExcel_Cell_DataType::TYPE_STRING); //收款帳號
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, '',PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sBankAccountName']) ;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['money']) ;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,15) ;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'第一建築經理股份有限公司');

	$sql = "UPDATE tStoreFeedBackMoneyFrom SET sCaseCloseTime = '".date('Y-m-d')."',sExportTime = '".$exportTime."' WHERE sId = '".$v['formId']."'";

	$conn->Execute($sql);
	$row++;
}


$_file = 'feedbackmoney_taishin.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;


?>