<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;

Function draw_border($objPHPExcel,$cells) {
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getTop()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getBottom()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getLeft()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getRight()->getColor()->setARGB('00000000');
}

Function draw_color($objPHPExcel,$cells,$color) {
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->getStartColor()->setARGB($color);
}

if ($bck == 1) { //多店顯示地址
	$ckb = explode(',', $branch);
	$cks = explode(',', $scrivener);
	$ckc = count($ckb)+count($cks);
}

##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經保證費統計表明細");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);

$cell_no = 1 ;
//主標題
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'結算明細暨付款通知書') ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第一段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '一、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '依  台端與本公司於日前所簽立之合作契約書所約定事項，雙方合作辦理價金信託履約保證作業，本公司即定期結算居間勞務報酬予  台端。' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第二段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(66);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '二、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '本次 民國'.($sales_year-1911)."年".$sales_season1.' 之結算明細如下列所示，應給付予  台端之金額為新台幣 '.@number_format($total_money).' 元整，如經  台端確認無誤後請依下方所列之作業辦法完成請款作業，本公司即按貴我雙方間之合作契約書上  台端所指定之帳戶逕行匯款。' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第三段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '三、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '本次結算明細：（明細製作日期：民國 '.(date("Y")-1911).' 年 '.date('m').' 月 '.date('d').' 日）' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//清單標題列填色
// $objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':L'.$cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':L'.$cell_no)->getFill()->getStartColor()->setARGB('00A6A6A6');

//繪製清單外框
draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;

//寫入清單標題列資料
// $objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':B'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getFont()->setSize(10);
// $objPHPExcel->getActiveSheet(0)->getStyle('A'.$cell_no.':L'.$cell_no)->getFont()->getColor()->setARGB('00FFFFFF'); 
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$cell_no.':M'.$cell_no)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,'日期') ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'保證號碼') ;
$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell_no,'店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell_no,'店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell_no,'買方') ;
$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell_no,'賣方') ;
$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'買賣總價金') ;
$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell_no,'保證費') ; //K
$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,'回饋金額') ;
$objPHPExcel->getActiveSheet()->setCellValue('K'.$cell_no,'簽約日期') ;
$objPHPExcel->getActiveSheet()->setCellValue('L'.$cell_no,'案件類型') ;
$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'備註') ;
// echo count($list);
for ($i = 0 ; $i < count($list); $i ++) {
	
	$listMonthCase[$list[$i]['cCertifiedId']][] =  $list[$i];
}

unset($list);


$i = 0;
foreach ($listMonthCase as $k => $v) {
	

	foreach ($v as $key => $value) {


		$list[$i] = $value;

		##分店頁籤資料寫入陣列##
		if ($list[$i]['cSpCaseFeedBackMoney']!='') {
				 $tmp_code = 'SC'.str_pad($list[$i]['sId'],4,'0',STR_PAD_LEFT);
		}else{
			if ($list[$i]['bBrand'] == 'SC') {
				$tmp_code = $list[$i]['bBrand'].str_pad($list[$i]['bId'],4,'0',STR_PAD_LEFT);
						
			}elseif($list[$i]['bBrand'] == 'NG'){
				$tmp_code = $list[$i]['bFBTarget'];
			}else{
				$tmp_code = $list[$i]['bBrand'].str_pad($list[$i]['bId'],5,'0',STR_PAD_LEFT);
						
			}
		}
		$storeData[$tmp_code][] = $list[$i];
		unset($tmp_code);
		##
		
		// die;
		$i++;
	}
}

$cell_no += 1 ;	//愈填寫查詢結果起始的儲存格位置
//寫入查詢結果
for ($i = 0 ; $i < $max ; $i ++) {
	draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;
	// $objPHPExcel->getActiveSheet()->mergeCells('A'.($cell_no).':B'.($cell_no));
	
	//繪製外框
	//draw_border($objPHPExcel,'A'.($i+$cell_no).':N'.($i+$cell_no)) ;
	
	//設定字體大小
	$objPHPExcel->getActiveSheet()->getStyle('A'.($cell_no).':S'.($cell_no))->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('M'.($cell_no))->getFont()->setSize(9);
	
	//寫入資料
	$_date = trim(substr($list[$i]['cEndDate'],0,10)) ;
	if (preg_match("/0000-00-00/",$_date)) {
		$_date = '-' ;
	}
	else {
		$_tmp = explode('-',$_date) ;
		$_date = ($_tmp[0]-1911).'-'.$_tmp[1].'-'.$_tmp[2] ;
		unset($_tmp) ;
	}
	##簽約日期
	$_cSignDate = trim(substr($list[$i]['cSignDate'],0,10)) ;
	if (preg_match("/0000-00-00/",$_cSignDate)) {
		$_cSignDate = '-' ;
	}
	else {
		$_tmp = explode('-',$_cSignDate) ;
		$_cSignDate = ($_tmp[0]-1911).'-'.$_tmp[1].'-'.$_tmp[2] ;
		unset($_tmp) ;
	}
	##
	if ($list[$i]['cSpCaseFeedBackMoney']!='') {

		$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($cell_no),($list[$i]['cCertifiedId'].' '));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),' SC'.str_pad($list[$i]['sId'],4,'0',STR_PAD_LEFT));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$list[$i]['sOffice']);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$list[$i]['buyer']);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($cell_no),$list[$i]['owner']);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($cell_no),$list[$i]['cTotalMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($cell_no),$list[$i]['cCertifiedMoney']);//保證費
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($cell_no),$list[$i]['cSpCaseFeedBackMoney']);

	}else{
		
	
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$_date);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($cell_no),($list[$i]['cCertifiedId'].' '));
			if ($list[$i]['bBrand'] == 'SC') {
				$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),$list[$i]['bBrand'].str_pad($list[$i]['bId'],4,'0',STR_PAD_LEFT));
			
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),$list[$i]['bBrand'].str_pad($list[$i]['bId'],5,'0',STR_PAD_LEFT));
			
			}
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$list[$i]['bStore']);
	
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$list[$i]['buyer']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($cell_no),$list[$i]['owner']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($cell_no),$list[$i]['cTotalMoney']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($cell_no),$list[$i]['cCertifiedMoney']);//保證費
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($cell_no),$list[$i]['cCaseFeedBackMoney']);
		
			
	}


		$objPHPExcel->getActiveSheet()->setCellValue('K'.($cell_no),$_cSignDate);

		$caseCat = '';

		if ($list[$i]['bApplication'] == 3) {
			$caseCat = '預售屋';
		}

		if ($list[$i]['bFeedDateCat'] == 1) { //1:月
			$caseCat .= '(月結)';
		}elseif ($list[$i]['bFBTarget'] != '' && $list[$i]['sFeedDateCat'] == 1) { //回饋地政士
			$caseCat .= '(月結)';
			// echo $caseCat."-".$list[$i]['cCertifiedId'];
			// die;
		}

        if ($list[$i]['sFeedDateCat'] == 2) {
            $caseCat .= '(隨案結)';
        }
        if ($list[$i]['bFeedDateCat'] == 2) {
            $caseCat .= '(隨案結)';
        }

		$objPHPExcel->getActiveSheet()->setCellValue('L'.($cell_no),$caseCat);
		//$totalMoney += $list[$i]['bRecall'] ;
		//cTotalMoney<100萬; ((cTotalMoney*0.0006)-cFirstMoney) < cCertifiedMoney
		if ($list[$i]['cTotalMoney'] < 1000000) {
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'總價小於100萬') ;
		}elseif((($list[$i]['cTotalMoney']*0.0006)-$list[$i]['cFirstMoney']) > ($list[$i]['cCertifiedMoney']+10)){ //(cer_real + 10) < cer_title
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'未收足保證費') ;
		}

		
		//設定案件金額千分位符號
		$objPHPExcel->getActiveSheet()->getStyle('H'.($cell_no).':J'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$cell_no++;
	
		
	// if ($bck == 1) { //多店顯示地址
		
		// if ($ckc > 1) { //再次檢查是否為多店
			// echo $tmp_Cid." != ".$list[($i+1)]['cCertifiedId']."<br>";
			$tmp_Cid = $list[($i+1)]['cCertifiedId'];
			if ($tmp_Cid != $list[$i]['cCertifiedId']) {
			
					draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($cell_no).':M'.($cell_no));
				
					$addr = getAddress($list[$i]['cCertifiedId']);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$addr);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no.':M'.$cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no.':M'.$cell_no)->getFill()->getStartColor()->setARGB('C6E0B4');

					$cell_no++;

			}
		// }
		
		
	// }
}
// die;
function getAddress($cid){
	global $conn;

	$tmp = array();
	$sql = "SELECT (SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip=cZip) AS city,cAddr FROM tContractProperty WHERE cCertifiedId ='".$cid."'";

	$rs= $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['city'].$rs->fields['cAddr'];

		$rs->MoveNext();
	}

	return @implode(',', $tmp);
}
// die;
// $cell_no = $i + $cell_no ;
$objPHPExcel->getActiveSheet()->getStyle('H'.$cell_no.':J'.$cell_no)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'合計'.$max.'筆');
$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,$total_money);
$objPHPExcel->getActiveSheet()->getStyle('J'.$cell_no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$cell_no += 15 ;
//簽章處
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':K'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setWrapText(true);
$str = '確認人簽章：' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':K'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('全部');
############################
##############各店分頁##############
// $sheet_index  = count($storeData);

$sheet_index = 1;

foreach ($storeData as $k => $v) {
	$money = 0; 
	$objPHPExcel->createSheet($sheet_index) ;

	//指定目前工作頁
	$objPHPExcel->setActiveSheetIndex($sheet_index);
	$objPHPExcel->getActiveSheet()->setTitle($k);
	##
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);

	$cell_no = 10 ;
	// echo "##".$sheet_index."##<br>";
	foreach ($v as $key => $value) {
		draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;
	
		//設定字體大小
		$objPHPExcel->getActiveSheet()->getStyle('A'.($cell_no).':S'.($cell_no))->getFont()->setSize(9);
		$objPHPExcel->getActiveSheet()->getStyle('M'.($cell_no))->getFont()->setSize(9);
	
		//寫入資料
		$_date = trim(substr($value['cEndDate'],0,10)) ;
		if (preg_match("/0000-00-00/",$_date)) {
			$_date = '-' ;
		}else {
			$_tmp = explode('-',$_date) ;
			$_date = ($_tmp[0]-1911).'-'.$_tmp[1].'-'.$_tmp[2] ;
			unset($_tmp) ;
		}
		##簽約日期
		$_cSignDate = trim(substr($value['cSignDate'],0,10)) ;
		if (preg_match("/0000-00-00/",$_cSignDate)) {
			$_cSignDate = '-' ;
		}
		else {
			$_tmp = explode('-',$_cSignDate) ;
			$_cSignDate = ($_tmp[0]-1911).'-'.$_tmp[1].'-'.$_tmp[2] ;
			unset($_tmp) ;
		}
		##
		if ($value['cSpCaseFeedBackMoney']!='') {

			$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$_date);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($cell_no),($value['cCertifiedId'].' '));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),' SC'.str_pad($value['sId'],4,'0',STR_PAD_LEFT));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$value['sOffice']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$value['buyer']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($cell_no),$value['owner']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($cell_no),$value['cTotalMoney']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($cell_no),$value['cCertifiedMoney']);//保證費
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($cell_no),$value['cSpCaseFeedBackMoney']);
			$money += $value['cSpCaseFeedBackMoney'];

		}else{
			
		
				$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$_date);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($cell_no),($value['cCertifiedId'].' '));
				if ($value['bBrand'] == 'SC') {
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),$value['bBrand'].str_pad($value['bId'],4,'0',STR_PAD_LEFT));
				
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),$value['bBrand'].str_pad($value['bId'],5,'0',STR_PAD_LEFT));
				
				}
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$value['bStore']);
		
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$value['buyer']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.($cell_no),$value['owner']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.($cell_no),$value['cTotalMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.($cell_no),$value['cCertifiedMoney']);//保證費
				$objPHPExcel->getActiveSheet()->setCellValue('J'.($cell_no),$value['cCaseFeedBackMoney']);
				$money += $value['cCaseFeedBackMoney'];
				
		}


		$objPHPExcel->getActiveSheet()->setCellValue('K'.($cell_no),$_cSignDate);

		$caseCat = '';

		if ($value['bApplication'] == 3) {
			$caseCat = '預售屋';
		}

		// if ($list[$i]['bFBTarget'] != '' && $list[$i]['sFeedDateCat'] == 1) { //回饋地政士
		// 	$caseCat .= '(月結)';
		// 	// echo $caseCat."-".$list[$i]['cCertifiedId'];
		// 	// die;
		// }elseif ($list[$i]['bFeedDateCat'] == 1 && $list[$i]['bFBTarget'] == '') { //1:月
		// 	$caseCat .= '(月結)';
		// }

		if ($value['bFeedDateCat'] == 1) { //1:月
			$caseCat .= '(月結)';
		}elseif ($value['bFBTarget'] != '' && $value['sFeedDateCat'] == 1) { //回饋地政士
			$caseCat .= '(月結)';
			// echo $caseCat."-".$list[$i]['cCertifiedId'];
			// die;
		}
        if ($value['sFeedDateCat'] == 2) {
            $caseCat .= '(隨案結)';
        }
        if ($list[$i]['bFeedDateCat'] == 2) {
            $caseCat .= '(隨案結)';
        }
		
		

		$objPHPExcel->getActiveSheet()->setCellValue('L'.($cell_no),$caseCat);

		if ($value['cTotalMoney'] < 1000000) {
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'總價小於100萬') ;
		}elseif((($value['cTotalMoney']*0.0006)-$value['cFirstMoney']) > ($value['cCertifiedMoney']+10)){ //(cer_real + 10) < cer_title
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'未收足保證費') ;
		}
		//$totalMoney += $list[$i]['bRecall'] ;
		
		//設定案件金額千分位符號
		$objPHPExcel->getActiveSheet()->getStyle('H'.($cell_no).':J'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		// echo $cell_no."<br>";
		$cell_no++;
	
		
	
			// echo $cell_no."-<br>";	
		if ($tmp_Cid != $value['cCertifiedId']) {
				
				draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;
				$objPHPExcel->getActiveSheet()->mergeCells('B'.($cell_no).':M'.($cell_no));
				
				$addr = getAddress($value['cCertifiedId']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.($cell_no),$addr);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no.':M'.$cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no.':M'.$cell_no)->getFill()->getStartColor()->setARGB('C6E0B4');
					
				$cell_no++;
					// echo $cell_no."*<br>";
					

		}



		


		
		
	}

	$tmp_Cid = $value['cCertifiedId'];

	$objPHPExcel->getActiveSheet()->getStyle('H'.$cell_no.':J'.$cell_no)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'合計'.count($v).'筆');
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,$money);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$cell_no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$cell_no = $cell_no+15;
	//簽章處
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':K'.$cell_no);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setWrapText(true);
	$str = '確認人簽章：' ;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
	$cell_no += 1 ;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':K'.$cell_no);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

	$cell_no = 1 ;
	//主標題
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(20);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'結算明細暨付款通知書') ;
	$cell_no += 1 ;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

	$cell_no += 1 ;
	//第一段條文
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
	$str = '一、' ;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$str = '依  台端與本公司於日前所簽立之合作契約書所約定事項，雙方合作辦理價金信託履約保證作業，本公司即定期結算居間勞務報酬予  台端。' ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
	$cell_no += 1 ;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

	$cell_no += 1 ;
	//第二段條文
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(66);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
	$str = '二、' ;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$str = '本次 民國'.($sales_year-1911)."年".$sales_season1.' 之結算明細如下列所示，應給付予  台端之金額為新台幣 '.@number_format($money).' 元整，如經  台端確認無誤後請依下方所列之作業辦法完成請款作業，本公司即按貴我雙方間之合作契約書上  台端所指定之帳戶逕行匯款。' ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
	$cell_no += 1 ;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
	$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

		$cell_no += 1 ;
		//第三段條文
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':M'.$cell_no);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
		$str = '三、' ;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$str = '本次結算明細：（明細製作日期：民國 '.(date("Y")-1911).' 年 '.date('m').' 月 '.date('d').' 日）' ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
		$cell_no += 1 ;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':M'.$cell_no);
		$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

		$cell_no += 1 ;
		
		//繪製清單外框
		draw_border($objPHPExcel,'B'.$cell_no.':M'.$cell_no) ;

		//寫入清單標題列資料
		$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':O'.$cell_no)->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':M'.$cell_no)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,'日期') ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'保證號碼') ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell_no,'店編號') ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell_no,'店名') ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell_no,'買方') ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell_no,'賣方') ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'買賣總價金') ;
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell_no,'保證費') ; //K
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,'回饋金額') ;
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$cell_no,'簽約日期') ;
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$cell_no,'案件類型') ;
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'備註') ;



	$sheet_index++;
}

// die;

// die;
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)).'/includes/company.json'),true) ;
//////// 新增頁 ////////
$objPHPExcel->createSheet($sheet_index) ;

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex($sheet_index);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);

$cell_no = 1 ;
//第四段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '四、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':J'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '作業辦法：   台端與第一建經間所簽立之合作契約書之指定帳戶戶名為公司者，請選擇下列 "辦法一" 辦理，若指定帳戶戶名為個人者，請選擇以下 "辦法二" 辦理。' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//辦法一
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':B'.$cell_no);
$objPHPExcel->getActiveSheet()->mergeCells('C'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getAlignment()->setWrapText(true);
$str = '辦法一' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
draw_border($objPHPExcel,'A'.$cell_no.':B'.$cell_no) ;
$str = ' 開立三聯式發票（詳附件明細表所載之回饋金額（含稅））' ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

//辦法內容
$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '發票抬頭：第一建築經理股份有限公司' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '統一編號：５３５４９９２０' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '地　　址：'.$company['Addr2'] ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '品　　名：佣金收入' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '●直接匯入該發票章上載明之公司帳戶（同合作契約書上載明之指定帳戶戶名）' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '●請將發票正本＋用印後之結算明細暨付款通知書寄至第一建經蕭家津經理，以利儘速撥款。' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setBold(true);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);


$cell_no += 1 ;
//辦法二
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':B'.$cell_no);
$objPHPExcel->getActiveSheet()->mergeCells('C'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getAlignment()->setWrapText(true);
$str = '辦法二' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
draw_border($objPHPExcel,'A'.$cell_no.':B'.$cell_no) ;
$str = ' 開立收據' ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

//辦法內容
$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '請填妥"收據"表單（隨函檢附制式收據表單備用）' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '回饋金額超過2萬者，依法匯款時先預扣10%，年底寄所得扣繳憑單給立據人報稅用。' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '●直接匯入該收據上載明之個人帳戶（同合作契約書上載明之指定帳戶戶名）' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '●請將收據正本＋立據人之身份證影本＋親簽、用印後之結算明細暨付款通知書寄至第一建經蕭家津經理，以利儘速撥款。' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setBold(true);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no += 5 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '聯絡人電話：'.$company['tel'].'#分機 101 吳小姐　　　分機 888 蕭小姐' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(30);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

$cell_no ++ ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$str = '注意事項：上述載明應寄回之文件請備齊完整，以便順利撥款。' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(12);
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':J'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(12);

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('保證費統計報表(2)');
############################

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
$cacheSettings = array( ' memoryCacheSize ' => '12MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
// die('***');
$_file = 'CaseFeedbackList.xlsx' ;

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