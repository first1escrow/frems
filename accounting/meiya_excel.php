<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow=1';
$rs=$conn->Execute($sql);

while (!$rs->EOF) {
	
	$tmp[]="'".$rs->fields['cBankAccount']."'";

	$rs->MoveNext();
}
$bank_acc =implode(',', $tmp);

unset($tmp);
//起始日期
if ($fds) {
	$tmp = explode('-',$fds) ;
	$tmp[0] += 1911 ;
	$fds = implode('-',$tmp) ;
	unset($tmp) ;
}
#

//結束日期
if ($fde) {
	$tmp = explode('-',$fde) ;
	$tmp[0] += 1911 ;
	$fde = implode('-',$tmp) ;
	unset($tmp) ;
}
#

#

$sql = '
SELECT 
		cas.cCertifiedId AS cCertifiedId,
		cas.cEndDate AS cEndDate,
		cas.cCaseStatus AS cCaseStatus,
		(SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
		tra.tBankLoansDate as tDate,
		invo.cInvoiceBuyer AS cInvoiceBuyer,
		invo.cInvoiceOwner AS cInvoiceOwner,
		invo.cInvoiceRealestate AS cInvoiceRealestate,
		invo.cInvoiceScrivener AS cInvoiceScrivener,
		invo.cInvoiceOther AS cInvoiceOther,
		rea.cInvoiceMoney AS cInvoiceMoney,
		rea.cInvoiceMoney1 AS cInvoiceMoney1,
		rea.cInvoiceMoney2 AS cInvoiceMoney2,
		cas.cSignDate AS cSignDate
FROM 
	tBankTrans AS tra 
JOIN
	tContractRealestate AS rea ON rea.cCertifyId=tra.tMemo
JOIN 
	tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
JOIN
	tContractInvoice AS invo ON cas.cCertifiedId=invo.cCertifiedId

WHERE 
	tra.tExport="1"
	AND tra.tPayOk="1"
	AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
	AND tra.tObjKind IN ("點交(結案)","解除契約") 
	AND tra.tAccount IN ('.$bank_acc.') 
	AND  cas.cEndDate>="'.$fds.' 00:00:00" 
	AND cas.cEndDate<="'.$fde.' 23:59:59" 

	
GROUP BY cas.cCertifiedId
ORDER BY
	tra.tExport_time,tra.tMemo 
ASC ;
' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields;



	$rs->MoveNext();
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
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經案件資料查詢明細結果");

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


$objPHPExcel->getActiveSheet()->setCellValue('A1','交易日期');
$objPHPExcel->getActiveSheet()->setCellValue('B1','序號');
$objPHPExcel->getActiveSheet()->setCellValue('C1','保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('D1','每一個案號的身份別');
$objPHPExcel->getActiveSheet()->setCellValue('E1','開發票對象(非買賣方姓名須註記)');
$objPHPExcel->getActiveSheet()->setCellValue('F1','買/賣方姓名');
$objPHPExcel->getActiveSheet()->setCellValue('G1','郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue('H1','通訊(寄送)地址');
$objPHPExcel->getActiveSheet()->setCellValue('I1','簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue('J1','案件狀態');
$objPHPExcel->getActiveSheet()->setCellValue('K1','承辦人');


//寫入查詢資料

$j = 2 ;	// 起始位置
for ($i = 0 ; $i < count($list) ; $i ++) {
	

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$list[$i]['tDate']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$j,($i+1));

	// $objPHPExcel->getActiveSheet()->getCell('C'.$j)->setValueExplicit($list[$i]['tCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

	##簽約日期
	
		preg_match("/(.*)-(.*)-(.*) (.*):(.*):(.*)/i",$list[$i]['cSignDate'],$tmp);

		//$tmp[1] = $tmp[1]-1911;
		
		
		//$list[$i]['cSignDate'] = $tmp[2].'/'.$tmp[3];
		
			// $list[$i]['cSignDate']='';
		$list[$i]['cSignDate'] = $tmp[1].'-'.$tmp[2].'-'.$tmp[3] ;

		unset($tmp);
	##


	##	案件狀態
		if ($list[$i]['cCaseStatus']==2) {
			
			$list[$i]['cCaseStatus'] = '進行中';

		}elseif ($list[$i]['cCaseStatus']==3) {

			$list[$i]['cCaseStatus'] = '已結案';

		}elseif ($list[$i]['cCaseStatus']==4) {

			$list[$i]['cCaseStatus']='解除契約';

		}elseif ($list[$i]['cCaseStatus']==6) {

			$list[$i]['cCaseStatus']='異常';

		}elseif ($list[$i]['cCaseStatus']==8) {
			
			$list[$i]['cCaseStatus']='作廢';
		}
	##
	##買方
		// $list[$i]['cCertifiedId']= '003059502';
	$sql="SELECT cName,cCertifiedId,cBaseZip,cBaseAddr,cInvoiceMoney FROM  tContractBuyer WHERE cCertifiedId='".$list[$i]['cCertifiedId']."'";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

	$tmp[]=$rs->fields;
	$tmp[0]['cIdentity']=1;
	unset($rs);

	##其他買(buy)

	$sql="SELECT cIdentity,cName,cCertifiedId,cBaseZip,cBaseAddr,cInvoiceMoney  FROM  tContractOthers WHERE cCertifiedId='".$list[$i]['cCertifiedId']."' AND cIdentity='1'";
	// echo $sql."</br>";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[]=$rs->fields;

		$rs->MoveNext();
	}
		// $c=count($rs->fields);
	unset($rs);
	##
	## 賣方
	
	$sql="SELECT cName,cCertifiedId,cBaseZip,cBaseAddr,cInvoiceMoney FROM  tContractOwner WHERE cCertifiedId='".$list[$i]['cCertifiedId']."'";

	$rs = $conn->Execute($sql);

	$tmp[]=$rs->fields;
	$c=count($tmp)-1;
	$tmp[$c]['cIdentity']=2;
	unset($rs);
	##
	##其他賣(sell)

	$sql="SELECT cIdentity,cName,cCertifiedId,cBaseZip,cBaseAddr,cInvoiceMoney FROM  tContractOthers WHERE cCertifiedId='".$list[$i]['cCertifiedId']."' AND cIdentity='2'";
	// echo $sql."</br>";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[]=$rs->fields;

		$rs->MoveNext();
	}
		
	unset($rs);
	##
	
	
	$b=1;//買
	 $o=1;//賣

	 $tmp_j=$j;
	
	for ($k=0; $k <count($tmp) ; $k++) { 
		
		$objPHPExcel->getActiveSheet()->getCell('C'.$j)->setValueExplicit($list[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

		if ($tmp[$k]['cIdentity']==1) {


			$type='買'.$b;
			$b++;
		}else if ($tmp[$k]['cIdentity']==2) {
			
			$type='賣'.$o;
			$o++;
		}

		##發票分配(買賣)
		if ($list[$i]['cInvoiceBuyer']!=0&&$tmp[$k]['cIdentity']==1) {
			
			if ($tmp[$k]['cInvoiceMoney']!=0) {
				$invoice[] =$type;
			}
		}

		if ($list[$i]['cInvoiceOwner']!=0&&$tmp[$k]['cIdentity']==2) {

			if ($tmp[$k]['cInvoiceMoney']!=0) {
				$invoice[] =$type;
			}
		}

		
		##
		
			
		##地址
		$sql = 'SELECT zCity,zArea FROM tZipArea WHERE zZip="'.$tmp[$k]['cBaseZip'].'";' ;

		$area=$conn->Execute($sql);
		##
		
		
		// $objPHPExcel->getActiveSheet()->setCellValue('E'.$j,$invoice);
		##
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,$type);

		
		

		$objPHPExcel->getActiveSheet()->setCellValue('F'.$j,$tmp[$k]['cName']);

		$objPHPExcel->getActiveSheet()->setCellValue('G'.$j,$tmp[$k]['cBaseZip']);

		$objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$area->fields['zCity'].$area->fields['zArea'].$tmp[$k]['cBaseAddr']);




		$objPHPExcel->getActiveSheet()->setCellValue('I'.$j,$list[$i]['cSignDate']);



		$objPHPExcel->getActiveSheet()->setCellValue('J'.$j,$list[$i]['cCaseStatus']);

		##
		//最後修改人
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$j,$list[$i]['lastmodify']);
		##

		
		$j++;
		
	}

	
	##發票分配(其他的)
	if ($list[$i]['cInvoiceRealestate']!=0) {
		if ($list[$i]['cInvoiceMoney']!=0) {
			
			$invoice[]='仲介1';
		}

		if ($list[$i]['cInvoiceMoney1']!=0) {
			$invoice[]='仲介2';
		}

		if ($list[$i]['cInvoiceMoney2']!=0) {
			$invoice[]='仲介3';
		}
	}

	if ($list[$i]['cInvoiceScrivener']!=0) {
		$invoice[]='地政士';
	}

	if ($list[$i]['cInvoiceOther']!=0) {
		
		$invoice[]='創世基金會';
	}
    ##
   
    ##發票欄位
	$tmp_invoice= implode('/', $invoice);
	$s=$tmp_j;
	$e=$j-1;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$tmp_j,$tmp_invoice);
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$s.':E'.$e);//合併
	##

	
	unset($tmp);unset($invoice);unset($area);
	


	

}
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

$_file = 'aig.xlsx' ;

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