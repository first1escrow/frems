<?php
require_once '../Classes/PHPExcel.php' ;
require_once '../Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../../openadodb.php' ;
include_once '../../web_addr.php' ;
include_once '../../session_check.php' ;

$_now = $_REQUEST["now"];

if ($_now == "") { 
	//$_now = date("Y-m-d"); // 當天日期
	$_con = "";
} else {
	$_con = "and C.cApplyDate='$_now'";
}

$sql = "SELECT  C.cApplyDate,A.cCertifiedId,A.cName as owner ,A.cBaseAddr as o_address , A.cIdentifyId as o_id , A.cMobileNum as o_mobile ,B.cName as buyer,B.cBaseAddr as b_address,B.cIdentifyId as b_id , B.cMobileNum as b_mobile, cCaseMoney, C.cEscrowBankAccount as vr_code FROM tContractOwner as A ,tContractBuyer as B , tContractCase as C where A.cCertifiedId = B.cCertifiedId and A.cCertifiedId=C.cCertifiedId $_con and cCaseMoney > 0";
//echo $sql;
$rs=$conn->Execute($sql);
//
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Michael Liu")
							 ->setLastModifiedBy("Michael Liu")
							 ->setTitle("履保業務對帳記錄表")
							 ->setSubject("履保業務對帳記錄表")
							 ->setDescription("履保業務對帳記錄表")
							 ->setKeywords("履保業務對帳記錄表")
							 ->setCategory("履保業務對帳記錄表");

$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '信託履保業務對帳紀錄報表');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);	
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', '序號')
            ->setCellValue('B2', '保證號碼')
            ->setCellValue('C2', '買方姓名')
            ->setCellValue('D2', '身分證號')
			->setCellValue('E2', '手機號')
			->setCellValue('F2', '賣方姓名')
			->setCellValue('G2', '身分證號')
			->setCellValue('H2', '手機號')
			->setCellValue('I2', '餘額');
$j=1;
$k=3;
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
//$objActSheet->setCellValueExplicit('C2', '0987654321',PHPExcel_Cell_DataType:: TYPE_STRING);
while( !$rs->EOF ) {
	//$_vr_code = "60001".$rs->fields["cCertifiedId"];
	$_vr_code = $rs->fields['vr_code'] ;
	
	
	$_sql = 'SELECT cIdentity,COUNT(*) as no FROM tContractOthers WHERE cCertifiedId="'.substr($_vr_code,5).'" GROUP BY cIdentity;' ;
	$_rs = $conn->Execute($_sql) ;
	
	while (!$_rs->EOF) {
		//買方總數
		if (($_rs->fields['cIdentity'] == '1')&&($_rs->fields['no'] != '0')) {
			$rs->fields["buyer"] .= '等'.($_rs->fields['no'] + 1).'人' ;
		}
		##
		
		//賣方總數
		if (($_rs->fields['cIdentity'] == '2')&&($_rs->fields['no'] != '0')) {
			$rs->fields["owner"] .= '等'.($_rs->fields['no'] + 1).'人' ;
		}
		##
		
		$_rs->MoveNext() ;
	}
	##
	
	//賣方總數
	$_sql = 'SELECT COUNT(cIdentity) as no FROM tContractOthers WHERE cIdentity="2" AND cCertifiedId="'.substr($_vr_code,5).'";' ;
	$_rs = $conn->Execute($_sql) ;
	$max = $_rs->fields['no'] ;
	if ($max > 1) {
		$rs->fields["buyer"] .= '等'.($max + 1).'人' ;
	}
	##
	
	$objPHPExcel->getActiveSheet()->setCellValue('A' . $k, $j);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $k, "'".$_vr_code,PHPExcel_Cell_DataType:: TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('C' . $k, $rs->fields["owner"]);
	$objPHPExcel->getActiveSheet()->setCellValue('D' . $k, $rs->fields["o_id"]);
	$objPHPExcel->getActiveSheet()->setCellValue('E' . $k, $rs->fields["o_mobile"]);
	$objPHPExcel->getActiveSheet()->setCellValue('F' . $k, $rs->fields["buyer"]);
	$objPHPExcel->getActiveSheet()->setCellValue('G' . $k, $rs->fields["b_id"]);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $k, "'".$rs->fields["b_mobile"],PHPExcel_Cell_DataType:: TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('I' . $k, $rs->fields["cCaseMoney"]);
	
	$_total = $_total + $rs->fields["cCaseMoney"];
	$rs->MoveNext();
	$j++; $k++;
} 		
//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

//Save Excel 2007 file 保存
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				
//$_file = '/home/httpd/html/'.substr($web_addr,7).'/bank/report/excel/'.date("Y_m_d").'.xlsx' ;
$_file = dirname(__FILE__).'/excel/'.date("Y_m_d").'.xlsx' ;
$objWriter->save($_file	);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EXCEL 對帳單</title>
</head>

<body>
<a href="/bank/report/excel/<?php echo date("Y_m_d");?>.xlsx?ts=<?php echo mktime();?>">下載excel對帳單</a>
</body>
</html>
