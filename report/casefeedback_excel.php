<?php
ini_set("memory_limit","2048M") ;
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}
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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'結算明細暨付款通知書') ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第一段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(36);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '一、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':L'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '依  台端與本公司於日前所簽立之合作契約書所約定事項，雙方合作辦理價金信託履約保證作業，本公司即定期結算居間勞務報酬予  台端。' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第二段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(66);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '二、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':L'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '本次 民國'.($sales_year-1911)."年".$sales_season1.' 之結算明細如下列所示，應給付予  台端之金額為新台幣 '.@number_format($total_money).' 元整，如經  如經  台端確認無誤後請依下方所列之作業辦法完成請款作業，本公司即按貴我雙方間之合作契約書上  台端所指定之帳戶逕行匯款。' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//第三段條文
$objPHPExcel->getActiveSheet()->mergeCells('B'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B'.$cell_no)->getAlignment()->setWrapText(true);
$str = '三、' ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,$str) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':L'.$cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$str = '本次結算明細：（明細製作日期：民國 '.(date("Y")-1911).' 年 '.date('m').' 月 '.date('d').' 日）' ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,$str) ;
$cell_no += 1 ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':L'.$cell_no);
$objPHPExcel->getActiveSheet()->getRowDimension($cell_no)->setRowHeight(10);

$cell_no += 1 ;
//清單標題列填色
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':AA'.$cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':AA'.$cell_no)->getFill()->getStartColor()->setARGB('00A6A6A6');

//繪製清單外框
draw_border($objPHPExcel,'A'.$cell_no.':AA'.$cell_no) ;

//寫入清單標題列資料
$objPHPExcel->getActiveSheet()->mergeCells('A'.$cell_no.':B'.$cell_no);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':AA'.$cell_no)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$cell_no.':AA'.$cell_no)->getFont()->getColor()->setARGB('00FFFFFF');
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$cell_no.':AA'.$cell_no)->getFont()->setBold(true);



$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'日期') ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'銀行別') ;
$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell_no,'保證號碼') ;
$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell_no,'店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell_no,'店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell_no,'身份別') ;
$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'買方') ;
$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell_no,'賣方') ;
$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,'買賣總價金') ;
$objPHPExcel->getActiveSheet()->setCellValue('K'.$cell_no,'保證號碼') ;
$objPHPExcel->getActiveSheet()->setCellValue('L'.$cell_no,'回饋金額') ;
$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'是否回饋') ;
$objPHPExcel->getActiveSheet()->setCellValue('N'.$cell_no,'仲介類型') ;
$objPHPExcel->getActiveSheet()->setCellValue('O'.$cell_no,'回饋對象') ;
$objPHPExcel->getActiveSheet()->setCellValue('P'.$cell_no,'地政士姓名') ;
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$cell_no,'回饋數量') ;
$objPHPExcel->getActiveSheet()->setCellValue('R'.$cell_no,'簽約日期') ;
$objPHPExcel->getActiveSheet()->setCellValue('S'.$cell_no,'保證費') ; //
$objPHPExcel->getActiveSheet()->setCellValue('T'.$cell_no,'店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('U'.$cell_no,'與履保實收比對是否收足');
$objPHPExcel->getActiveSheet()->setCellValue('V'.$cell_no,'合理');
$objPHPExcel->getActiveSheet()->setCellValue('W'.$cell_no,'案件類型') ;
$objPHPExcel->getActiveSheet()->setCellValue('X'.$cell_no,'回饋金額') ;
$objPHPExcel->getActiveSheet()->setCellValue('Y'.$cell_no,'案件回饋比率');
$objPHPExcel->getActiveSheet()->setCellValue('Z'.$cell_no,'案件回饋金總額');
$objPHPExcel->getActiveSheet()->setCellValue('AA'.$cell_no,'隨案結出款日');
$cell_no += 1 ;	//愈填寫查詢結果起始的儲存格位置


//寫入查詢結果
for ($i = 0 ; $i < $max ; $i ++) {
	
	$objPHPExcel->getActiveSheet()->mergeCells('A'.($cell_no).':B'.($cell_no));
	
	//繪製外框
	//draw_border($objPHPExcel,'A'.($i+$cell_no).':N'.($i+$cell_no)) ;
	
	//設定字體大小
	$objPHPExcel->getActiveSheet()->getStyle('A'.($cell_no).':Y'.($cell_no))->getFont()->setSize(9);
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
	
	if ($list[$i]['cSpCaseFeedBackMoney']!='') { //地政士特殊回饋
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),' SC'.str_pad($list[$i]['sId'],4,'0',STR_PAD_LEFT));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$list[$i]['sOffice']);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.($cell_no),$list[$i]['cSpCaseFeedBackMoney']);		
		$objPHPExcel->getActiveSheet()->setCellValue('N'.($cell_no),$list[$i]['bCategory2']);
		$objPHPExcel->getActiveSheet()->setCellValue('X'.($cell_no),$list[$i]['cSpCaseFeedBackMoney']);
	}else{
		if ($list[$i]['bBrand'] == 'SC') {
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$list[$i]['bBrand'].str_pad($list[$i]['bId'],4,'0',STR_PAD_LEFT));			
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($cell_no),$list[$i]['bBrand'].str_pad($list[$i]['bId'],5,'0',STR_PAD_LEFT));	
		}
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($cell_no),$list[$i]['bStore']);	
		$objPHPExcel->getActiveSheet()->setCellValue('L'.($cell_no),$list[$i]['cCaseFeedBackMoney']);
		if ($list[$i]['bCategory2']) {$list[$i]['bCategory'] = $list[$i]['bCategory2'];}
		$objPHPExcel->getActiveSheet()->setCellValue('N'.($cell_no),$list[$i]['bCategory']);	
		$objPHPExcel->getActiveSheet()->setCellValue('X'.($cell_no),$list[$i]['cCaseFeedBackMoney']);
	}

	$objPHPExcel->getActiveSheet()->setCellValue('A'.($cell_no),$_date);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($cell_no),$list[$i]['cBank']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($cell_no),($list[$i]['cCertifiedId'].' '));

	$objPHPExcel->getActiveSheet()->setCellValue('G'.($cell_no),$list[$i]['bStoreClass']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($cell_no),$list[$i]['buyer']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.($cell_no),$list[$i]['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.($cell_no),$list[$i]['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.($cell_no),($list[$i]['cCertifiedId'].' '));
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($cell_no),$list[$i]['bFeedback']);

	$objPHPExcel->getActiveSheet()->setCellValue('O'.($cell_no),$list[$i]['bFBTarget']);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.($cell_no),$list[$i]['cScrivener']);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.($cell_no),$count[$list[$i]['cCertifiedId']]);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.($cell_no),$_cSignDate);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.($cell_no),$list[$i]['cCertifiedMoney']);//保證費
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$cell_no,'') ;
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$cell_no,'');
	$objPHPExcel->getActiveSheet()->setCellValue('V'.$cell_no,'');

	##
	$caseCat = '';

	if ($list[$i]['bApplication'] == 3) {
		$caseCat = '預售屋';
	}

	if ($list[$i]['sFeedDateCat'] == 1) {
		$caseCat .= '(月結)';
	} elseif ($list[$i]['bFeedDateCat'] == 1) {
		$caseCat .= '(月結)';
	}

    if ($list[$i]['sFeedDateCat'] == 2) {
        $caseCat .= '(隨案結)';
    }
    if ($list[$i]['bFeedDateCat'] == 2) {
        $caseCat .= '(隨案結)';
    }
	
	$objPHPExcel->getActiveSheet()->setCellValue('W'.($cell_no),$caseCat);

	##回饋比率
    if($CaseFeedTotal[$list[$i]['cCertifiedId']] == 0) {
        $recall = "0%";
    } else {
        $recall = round(($CaseFeedTotal[$list[$i]['cCertifiedId']]/$list[$i]['cCertifiedMoney'])*100,2)."%";
    }

	$objPHPExcel->getActiveSheet()->setCellValue('Y'.($cell_no),$recall);
	$objPHPExcel->getActiveSheet()->setCellValue('Z'.($cell_no),$CaseFeedTotal[$list[$i]['cCertifiedId']]);
	$objPHPExcel->getActiveSheet()->getStyle('Z'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    $objPHPExcel->getActiveSheet()->setCellValue('AA'.($cell_no),$list[$i]['exportTime']);
	unset($recall);
	//$totalMoney += $list[$i]['bRecall'] ;
		
	//設定案件金額千分位符號
	$objPHPExcel->getActiveSheet()->getStyle('J'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('S'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('X'.($cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
	$cell_no++;
	
		
	
}

// if ($_SESSION['member_id'] == 6) {
// 			// echo "<pre>";
// 			// print_r($list);
// 			// echo $list[$i]['bFeedDateCat'].'_'.$list[$i]['cCertifiedId'];
// 			// echo $caseCat;
// 			die;
// }
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
$objPHPExcel->getActiveSheet()->getStyle('J'.$cell_no.':L'.$cell_no)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,'合計'.$max.'筆');
$objPHPExcel->getActiveSheet()->setCellValue('L'.$cell_no,$total_money);
$objPHPExcel->getActiveSheet()->getStyle('L'.$cell_no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
$objPHPExcel->getActiveSheet()->setTitle('保證費統計報表(1)');
############################
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)).'/includes/company.json'),true) ;

//////// 新增第二頁 ////////
$objPHPExcel->createSheet(1) ;

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(1);

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
$cacheSettings = array( ' memoryCacheSize ' => '80MB');
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