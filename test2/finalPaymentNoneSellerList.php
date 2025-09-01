<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
header("Content-Type:text/html; charset=utf-8");
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
// require dirname(dirname(__FILE__)).'/vendor/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
require dirname(dirname(__FILE__)).'/vendor/autoload.php';

// require_once '../bank/Classes/PHPExcel.php' ;
// require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;


use phpoffice\phpspreadsheet\Spreadsheet;
use \phpoffice\phpspreadsheet\Style\NumberFormat;
use phpoffice\phpspreadsheet\Writer\Xlsx;
use \phpoffice\phpspreadsheet\Style\Fill;

$objPHPExcel = new Spreadsheet();
die;
$_POST = escapeStr($_POST) ;

##
if ($_POST['year']) {
	$sDate = $_POST['year']."-".$_POST['month']."-01";
	$eDate = $_POST['year']."-".$_POST['month']."-31";

	//賣方備註選單	
	// $Item = array(1 => '賣方匯第三人',2 => '多數賣方指定匯其中一人或數人',3 =>'代理人受領',4 =>'代理人指定匯第三人帳戶',5=>'其他:' );////1.賣方親自點交,指定滙給第三人 2.賣方等親自點交,同意全部滙給其中一人 3.有授權書(代理人可收受價金) 4.有授權書(代理人可指定滙給第三人) 5.其他:
	$sql = "SELECT * FROM tCategorySellerNote  ORDER BY cOrder ASC ";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$Item[$rs->fields['cId']] = $rs->fields['cName'];

		$rs->MoveNext();
	}
	##
	//取得結案號碼
	$sql = "SELECT tMemo,tBankLoansDate,tAccountName FROM tBankTrans WHERE tBankLoansDate BETWEEN '".$sDate."' AND '".$eDate."' AND tPayOk = 1 AND tBank_kind = '一銀' AND tObjKind IN ('解除契約','點交(結案)','建經發函終止') AND tKind ='賣方'";
	
	$rs = $conn->Execute($sql);
	$list_check = array();
	$checkOwnerNote = true;//確認是否符合顯示賣方備註 false:要顯示填寫
	
	while (!$rs->EOF) {
		$data = array();
		$data = $rs->fields;
		

		if (empty($list_check[$data['tMemo']])) { $list_check[$data['tMemo']] = array(); }
		if (empty($list_check[$data['tMemo']]['tAccountName'])) { $list_check[$data['tMemo']]['tAccountName'] = array(); }
			
		
		$list_check[$data['tMemo']]['tMemo'] = $data['tMemo'];
		$list_check[$data['tMemo']]['tBankLoansDate'] = $data['tBankLoansDate'];
		$list_check[$data['tMemo']]['tAccountName'][] = $data['tAccountName'];
	
		
		unset($owner);unset($ownerArr);unset($data);
		$rs->MoveNext();
	}


	$list = array();
	foreach ($list_check as $key => $val) {
		$check = true;
		//賣方備註(沒賣方不用填寫;有非賣方帳戶要填寫;//結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2))
		$owner = getOwner($val['tMemo'],'cName');
		$ownerArr = explode('_',$owner)	;
		$list_check[$key]['owner'] = $ownerArr;
		
		//帳戶名跟賣方姓名對不起來
		foreach ($val['tAccountName'] as $acc_name) {
			if (!in_array($acc_name, $ownerArr)) {
				$list_check[$key]['check'] = 0;
				$check = false;
			}
		}

		//結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2)
		foreach ($ownerArr as $name) {
			if (!in_array($name, $val['tAccountName'])) {
				$list_check[$key]['check'] = 0;
				$check = false;
			}
		}

		if (!$check) {
			array_push($list, $list_check[$key]);
		}

		
		unset($owner);unset($ownerArr);
	}

	foreach ($list as $key => $val) {
		$sql = "SELECT * FROM tBankTransSellerNote WHERE tCertifiedId = '".$val['tMemo']."'";
		$rs = $conn->Execute($sql);

		$list[$key]['tAnother'] = $rs->fields['tAnother'];
		$list[$key]['tAnotherNote'] = $rs->fields['tAnotherNote'];
	}
	
	

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("第一建經");
	$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
	$objPHPExcel->getProperties()->setTitle("第一建經");
	$objPHPExcel->getProperties()->setSubject("");
	$objPHPExcel->getProperties()->setDescription("");

	$objPHPExcel->setActiveSheetIndex(0);

	$col = 65;
	$row = 1;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'結案日期');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方匯出帳號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方匯出帳號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'原因');

	$row++;
	for ($i=0; $i < count($list); $i++) { 
		
		$col = 65;

		$buyer = getBuyer($list[$i]['tMemo'],'cName');
		$owner = getOwner($list[$i]['tMemo'],'cName');

		$buyerAcc = array();
		$sql = "SELECT tAccountName FROM tBankTrans WHERE tBankLoansDate BETWEEN '".$sDate."' AND '".$eDate."' AND tMemo = '".$list[$i]['tMemo']."' AND tPayOk = 1 AND tBank_kind = '一銀' AND tObjKind IN ('解除契約','點交(結案)','建經發函終止') AND tKind ='買方'";
		$rs = $conn->Execute($sql);
	    while (!$rs->EOF) {
	    	 array_push($buyerAcc, $rs->fields['tAccountName']);
	    	$rs->MoveNext();
	    }
		

		$tmp = explode(',', $list[$i]['tAnother']);
		for ($j=0; $j < count($tmp); $j++) { 
			if ($tmp[$i] == 5) {
				$noteArr[]= $list[$i]['tAnotherNote'];
			}else{
				$noteArr[]= $Item[$tmp[$j]];
			}
			
		}
		$note = @implode(';', $noteArr);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $list[$i]['tMemo'],PHPExcel_Cell_DataType::TYPE_STRING); 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['tBankLoansDate']);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$buyer);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$owner);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode('_',$buyerAcc));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode('_',$list[$i]['tAccountName']));

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$note);
		unset($tmp);unset($noteArr);
		$row++;

		$str = $rs->fields['tMemo'].",".$rs->fields['tBankLoansDate'].",".$buyer.",".$owner.",".iconv("utf-8","big5",$note)."\n";
	}

	// die;
	$_file = 'seller.xlsx' ;

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

}	


function getBuyer($id,$col){
	global $conn;
	$sql = "SELECT cName,cIdentifyId FROM tContractBuyer WHERE cCertifiedId = '".$id."'";
	$rs = $conn->Execute($sql);
	$bueyr = array();
	if (!empty($rs->fields[$col])) {
		$buyer[] = $rs->fields[$col];
	}
	
	$sql = "SELECT cName,cIdentifyId FROM tContractOthers WHERE cCertifiedId = '".$id."' AND cIdentity =1";
	$rs = $conn->Execute($sql);
	
	while (!$rs->EOF) {

		
		if (!empty($rs->fields[$col])) {
			$buyer[]  = $rs->fields[$col];
		}
		

		
	
		$rs->MoveNext();
	}

	return @implode('_', $buyer);

}

function getOwner($id,$col){
	global $conn;
	$sql = "SELECT cName,cIdentifyId FROM tContractOwner WHERE cCertifiedId = '".$id."'";
	$rs = $conn->Execute($sql);
	$owner = array();
	if (!empty($rs->fields[$col])) {
		$owner[] = $rs->fields[$col];
	}

	$sql = "SELECT cName,cIdentifyId FROM tContractOthers WHERE cCertifiedId = '".$id."' AND cIdentity = 2";
	$rs = $conn->Execute($sql);
	
	while (!$rs->EOF) {
		if (!empty($rs->fields[$col])) {
			$owner[]  = $rs->fields[$col];
		}
		
		// $i++;
		$rs->MoveNext();
	}


	return @implode('_', $owner);

}





##
$smarty->assign('list',$list);
$smarty->display('finalPaymentNoneSellerList.inc.tpl', '', 'report2');
?>