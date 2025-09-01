<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;


//起始日期
if ($fds) {
	$tmp = explode('-',$fds) ;
	$tmp[0] += 1911 ;
	$fds = implode('-',$tmp) ;
	unset($tmp) ;

	$str .= ' AND cDateTime >="'.$fds.' 00:00:00"';
}
#

//結束日期
if ($fde) {
	$tmp = explode('-',$fde) ;
	$tmp[0] += 1911 ;
	$fde = implode('-',$tmp) ;
	$str .= ' AND cDateTime <="'.$fde.' 23:59:59"';
	unset($tmp) ;
}
#

#

$sql = 'SELECT 
			cc.cEscrowBankAccount,
			(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=cc.cLastEditor) AS cLastEditor,
			cc.cLastTime,
			(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=s.sUndertaker1) AS sUndertaker1,
			cc.cBankList,
			cq.*
			
		FROM 
			tContractInvoiceQuery AS cq
		LEFT JOIN
			tContractCase AS cc ON cc.cCertifiedId = cq.cCertifiedId
		
		LEFT JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId = cq.cCertifiedId
		LEFT JOIN 
			tScrivener AS s ON s.sId =cs.cScrivener
		WHERE
			cNoSend = 1 AND cObsolete != "Y" '.$str.'
		ORDER BY cInvoiceDate ASC';




$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields;

	$rs->MoveNext();
}

$j = 0;
for ($i=0; $i < count($list); $i++) { 
	
	$arr[$j]['sUndertaker1'] = $list[$i]['sUndertaker1'];//承辦人
	$arr[$j]['CertifyDate'] = CertifyDate($list[$i]['cEscrowBankAccount']);//履保費出款日
	if ($arr[$j]['CertifyDate'] == '') {
		$arr[$j]['CertifyDate'] = str_replace('-', '/', $list[$i]['cBankList']);
	}
	$arr[$j]['cCertifiedId'] = $list[$i]['cCertifiedId'];//保號

	$owner = inv_owner($list[$i]['cTB'],$list[$i]['cTargetId']); // 表對應ID

	$arr[$j]['iden'] = $owner['iden'];//開發票對象
	$arr[$j]['name'] = $owner['name'];//姓名
	$arr[$j]['addr'] = $owner['addr'];//通 訊地址

	//標籤貼【F欄姓名(D欄保證號碼-C欄履保費出款日)】 //第一建築經理股份有限公司(123456789-2015/10/29)
	$arr[$j]['note'] = $arr[$j]['name'].'('.$arr[$j]['cCertifiedId'].'-'.$arr[$j]['CertifyDate'].')';
	$arr[$j]['cLastEditor'] = $list[$i]['cLastEditor'];//最後修改者
	$arr[$j]['cLastTime'] = $list[$i]['cLastTime'];//最後修改時間
	unset($owner);

$j++;

}

unset($list);
function inv_owner($tb,$id)
{
	global $conn;

	if ($tb == 'tContractBuyer') {

		$sql = "SELECT * FROM tContractBuyer WHERE cId = '".$id."'";
		$rs = $conn->Execute($sql);

		$arr['iden'] = '買方';
		$arr['name'] = $rs->fields['cName'];
		$area = city($rs->fields['cBaseZip']);
		$arr['addr'] = $rs->fields['cBaseZip'].$area['city'].$area['area'].$rs->fields['cBaseAddr'];//通 訊地址


	}elseif ($tb == 'tContractOwner') {

		$sql = "SELECT * FROM tContractOwner WHERE cId ='".$id."'";
		$rs = $conn->Execute($sql);

		$arr['iden'] = '賣方';
		$arr['name'] =$rs->fields['cName'];
		$area = city($rs->fields['cBaseZip']);
		$arr['addr'] = $rs->fields['cBaseZip'].$area['city'].$area['area'].$rs->fields['cBaseAddr'];//通 訊地址

	}elseif ($tb == 'tContractOthers_B') { //其他買方

		$sql = "SELECT * FROM tContractOthers WHERE cId ='".$id."' AND cIdentity = 1";
		$rs = $conn->Execute($sql);
		
		$arr['iden'] = '買方';
		$arr['name'] = $rs->fields['cName'];
		$area = city($rs->fields['cBaseZip']);
		$arr['addr'] = $rs->fields['cBaseZip'].$area['city'].$area['area'].$rs->fields['cBaseAddr'];//通 訊地址

	}elseif ($tb == 'tContractOthers_O') { //其他賣方
		$sql = "SELECT * FROM tContractOthers WHERE cId ='".$id."' AND cIdentity = 2";
		$rs = $conn->Execute($sql);
		
		$arr['iden'] = '賣方';
		$arr['name'] = $rs->fields['cName'];
		$area = city($rs->fields['cBaseZip']);
		$arr['addr'] = $rs->fields['cBaseZip'].$area['city'].$area['area'].$rs->fields['cBaseAddr'];//通 訊地址

	}elseif (preg_match("/tContractRealestate/",$tb)) {
		
		$sql = "SELECT * FROM tBranch WHERE bId = '".$id."'";
		$rs = $conn->Execute($sql);

		$arr['iden'] = '仲介';
		$arr['name'] = $rs->fields['bName'];
		$area = city($rs->fields['bZip']);
		$arr['addr'] = $rs->fields['bZip'].$area['city'].$area['area'].$rs->fields['bAddress'];//通 訊地址
		# code...
		//preg_match_all("/<span class=\"totalPage\">.* 共(.*)筆<\/span>/U", $data,$tmp);
	}elseif ($tb == 'tContractScrivener') {
		

		$sql = "SELECT * FROM tScrivener WHERE sId = '".$id."'";
		$rs = $conn->Execute($sql);

		$arr['iden'] = '地政士';
		$arr['name'] = $rs->fields['sName'];
		$area = city($rs->fields['sZip1']);
		$arr['addr'] = $rs->fields['sZip1'].$area['city'].$area['area'].$rs->fields['sAddress'];//通訊地址

	}elseif (preg_match("/tContractInvoiceExt/", $tb)) {
		
		$sql = "SELECT * FROM tContractInvoiceExt WHERE cId = '".$id."'";
		$rs = $conn->Execute($sql);

		if ($rs->fields['cDBName'] == 'tContractBuyer') {

			$arr['iden'] = '買方';
			
			
		}elseif ($rs->fields['cDBName'] == 'tContractOwner') {

			$arr['iden'] = '賣方';
			
		}elseif ($rs->fields['cDBName'] =='tContractOthersO') {
			$arr['iden'] = '賣方';
			
		}elseif ($rs->fields['cDBName'] =='tContractOthersB') {
			$arr['iden'] = '買方';
			
		}elseif (preg_match("/tContractRealestate/", $rs->fields['cDBName'])) {
			$arr['iden'] = '仲介';
		}elseif ($rs->fields['cDBName'] =='tContractScrivener') {
			$arr['iden'] = '地政士';
		}

		$arr['name'] = $rs->fields['cName'];
		$area = city($rs->fields['cInvoiceZip']);
		$arr['addr'] = $rs->fields['cInvoiceZip'].$area['city'].$area['area'].$rs->fields['cInvoiceAddr'];//通訊地址
	}


	return $arr;
}

function branch($bid)
{
	global $conn;

	$sql = "SELECT 
				(SELECT bCode FROM tBrand AS ba WHERE ba.bId=b.bBrand) AS code,
				bStore,
				bZip,
				bAddress,
				bName 
			FROM 
				tBranch AS b
			WHERE b.bId ='".$bid."'" ;

	$rs = $conn->Execute($sql);

	$branch = $rs->fields;

	return $branch;
}

function city($zip)
{
	global $conn;

	$sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip='".$zip."'";

	$rs = $conn->Execute($sql);

	$area['city'] = $rs->fields['zCity'];
	$area['area'] = $rs->fields['zArea'];

	
	return $area;
	

}
function CertifyDate($ea)
{
	
	global $conn;

	$sql_tra = '
		SELECT 
			tBankLoansDate as tExport_time, 
			tObjKind,
			tKind, 
			tMoney, 
			tTxt 
		FROM 
			tBankTrans 
		WHERE 
			tVR_Code="'.$ea.'" 
		ORDER BY 
			tExport_time 
		ASC ;
		' ;

	$rs= $conn->Execute($sql_tra);
	
	while (!$rs->EOF) {
			

		if ($rs->fields['tKind']=='保證費') {
				
			$cCertifyDate = str_replace('-','/',substr($rs->fields['tExport_time'],0,10)) ;
		}

		$rs->MoveNext();
	}

	return $cCertifyDate;
}
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die();
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("未寄送發票");
$objPHPExcel->getProperties()->setDescription("第一建經未寄送發票");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);



//設定總表文字置中
$objPHPExcel->getActiveSheet()->getStyle('A:k')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A:k')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F1:P1')->getAlignment()->setWrapText(true);


//設定總表所有案件金額千分位符號
//$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//設定字型大小
$objPHPExcel->getActiveSheet()->getStyle('A:P')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setSize(12);
//$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setSize(10);


//寫入表頭資料


$objPHPExcel->getActiveSheet()->setCellValue('A1','承辦人');
$objPHPExcel->getActiveSheet()->setCellValue('B1','履保費出款日');
$objPHPExcel->getActiveSheet()->setCellValue('C1','保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('D1','開發票資訊對象');
$objPHPExcel->getActiveSheet()->setCellValue('E1','姓名');
$objPHPExcel->getActiveSheet()->setCellValue('F1','通訊地址');
$objPHPExcel->getActiveSheet()->setCellValue('G1','標籤貼【F欄姓名(D欄保證號碼-C欄履保費出款日)】');
$objPHPExcel->getActiveSheet()->setCellValue('H1','最後修改者');
$objPHPExcel->getActiveSheet()->setCellValue('I1','最後修改時間');


##

$row = 2;
$total = 0;
$col = 65;

for ($i=0; $i < count($arr); $i++) { 
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$arr[$i]['sUndertaker1']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$arr[$i]['CertifyDate']);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cCertifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$row, $arr[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$arr[$i]['iden']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$arr[$i]['name']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,$arr[$i]['addr']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,$arr[$i]['note']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$arr[$i]['cLastEditor']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row,$arr[$i]['cLastTime']);
	$row++;
}
##
//
// die();
//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('AIG');

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//Save Excel 2007 file 保存
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

//$file_name = date("Y_m_d").'.xlsx' ;
//$file_name = '銀行點交結算統計表.xlsx' ;

//$file_path = '/home/httpd/html/'.substr($web_addr,7).'/accounting/excel/' ;

//$_file = $file_path.$file_name ;
//$objWriter->save($_file);

$_file = 'NoSend.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit ;

?>