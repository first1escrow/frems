<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
ini_set( 'memory_limit', '128M' );
// // ini_set('max_execution_time', 120);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../configs/config.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

// $tlog = new TraceLog() ;

//預載log物件
// $logs = new Intolog() ;

// $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
// $cacheSettings = array( 'memoryCacheSize' => '128MB');
// PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("報表");
$objPHPExcel->getProperties()->setDescription("報表");

// //指定目前工作頁
// $objPHPExcel->setActiveSheetIndex(0);
// $objPHPExcel->getActiveSheet()->setTitle('數量統計');
// ##

// $c = 66; //B欄開始
// $r =1;
// foreach ($col_date as $k => $v) {
// 	//顏色
// 	$objPHPExcel->getActiveSheet()->getStyle(chr($c).$r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// 	$objPHPExcel->getActiveSheet()->getStyle(chr($c).$r)->getFill()->getStartColor()->setARGB('FFEBEB');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$k);

// }


// if ($row=='branch' || $branch) {
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總計(單件+配件-非本店)');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'單件');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'配件總數');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'非本配件數量');

// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'合約總保證費');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總回饋金金額');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'收入');

	
// }else{
// 	// $objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總計');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'合約總保證費');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總回饋金金額');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'收入');

// }

// $r = 2;
// foreach ($leftColum as $k => $v) {

// 		if ($k != "0") {
// 			// in_array(needle, haystack)
// 			if (in_array($k, $checkColum)) {
// 				$c = 65; //B欄開始
// 				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['name']);
				

// 				foreach ($col_date as $key => $value) {

// 					if ($v['count'][$value] != '') {
// 						$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['count'][$value]);
						
// 					}else{
// 						$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,0);
// 					}	

// 					if ($row == 'branch' || $branch != '') {
// 						if ($v['one'][$value] != '' || $v['pair'][$value] != '') {

// 							$tmp = $v['one'][$value]+$v['pair'][$value]-$v['unpair'][$value];
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$tmp);
// 							unset($tmp);
// 						}else{
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,0);
// 						}	

// 						if ($v['one'][$value] != '') {
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['one'][$value]);
// 						}else{
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,0);
// 						}

// 						if ($v['pair'][$value] != '') {
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['pair'][$value]);
// 						}else{
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,0);
// 						}

// 						if ($v['unpair'][$value] != '') {
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['unpair'][$value]);
								
// 						}else{
// 							$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,0);
// 						}
// 					}
				
// 				}
				
// 				//保證費	
// 				if ($v['certifiedMoney'] == '') { $v['certifiedMoney'] = 0; }
					
				
// 				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['certifiedMoney']);
				
// 				//回饋金			
// 				if ($v['caseFeedBackMoney'] == '') { $v['caseFeedBackMoney'] = 0; }
// 				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$v['caseFeedBackMoney']);
				
// 				//收入
// 				$tmp = $v['certifiedMoney']-$v['caseFeedBackMoney'];
// 				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$tmp);
// 				unset($tmp);
// 				$r++;
// 			}
			
			
			
// 		}
		
	
// 	# code...
// }
// for ($i = 0 ; $i < count($row_title2) ; $i ++) {

// 	$c = 65;//A
	
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$row_title2[$i]['name']);

// 	if (is_array($col_date)) {
// 		foreach ($col_date as $key => $value) {

// 			if ($data[$row_title2[$i]['key']][$value]=='') {
// 				$data[$row_title2[$i]['key']][$value] =0;
// 			}
		
// 			$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$data[$row_title2[$i]['key']][$value]);
			
// 			$total = $total+$data[$row_title2[$i]['key']][$value];
// 		}
// 	}

	
// 	// $objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$total);
	

// 	if ($row=='branch' || $branch) {
// 		if ($count[$row_title2[$i]['key']]['one'] == '') {
// 			$count[$row_title2[$i]['key']]['one'] = 0;
// 		}

// 		if ($count[$row_title2[$i]['key']]['pair'] == '') {
// 			$count[$row_title2[$i]['key']]['pair'] = 0;
// 		}

// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$count[$row_title2[$i]['key']]['one']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$count[$row_title2[$i]['key']]['pair']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$count[$row_title2[$i]['key']]['nopair']);

		
// 	}

// 	// 	$tbl .= "<td>".."</td>";
// 	// 	$tbl .= "<td>".."</td>";
// 	// 	$tbl .= "<td>".."</td>";
		
// 	// //$sheet->getStyle('A1')->getNumberFormat()->setFormatCode('#,##0.00');

// 	$objPHPExcel->getActiveSheet()->getStyle($c.$r)->getNumberFormat()->setFormatCode('#,##0.00');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$cCertifiedMoney[$row_title2[$i]['key']]);

// 	$objPHPExcel->getActiveSheet()->getStyle($c.$r)->getNumberFormat()->setFormatCode('#,##0.00');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$cCaseFeedBackMoney[$row_title2[$i]['key']]);

// 	$objPHPExcel->getActiveSheet()->getStyle($c.$r)->getNumberFormat()->setFormatCode('#,##0.00');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,($cCertifiedMoney[$row_title2[$i]['key']]-$cCaseFeedBackMoney[$row_title2[$i]['key']]));
	
// 	unset($total);
// 	$r++;
// }



$page = 0;
// ################################
// if ($row == 'branch' || $branch != '') {
// 	// 	##
// 	$objPHPExcel->createSheet() ;
// 	$objPHPExcel->setActiveSheetIndex($page) ;
// 	$objPHPExcel->getActiveSheet()->setTitle('單件');

// 	$c=65;
// 	$r = 1;
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'序號');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'保證號碼');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'店家編號');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介店名');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總價金');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'案件狀態日期');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'進案日期');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'地政士姓名');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'標的物座落');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'狀態');
// 	$r++;

// 	for ($i=0; $i < count($showDataOne); $i++) { 
// 		$c =65;
// 		if ($showDataOne[$i]['statusName']=='已結案') { 
// 			$cEndDate = $showDataOne[$i]['cEndDate'];
// 		}
// 		else {
// 			$cEndDate = $showDataOne[$i]['cSignDate'];
// 		}

// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,($i+1));
		
// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $showDataOne[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING);


// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['newCode']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['newbStore']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['cTotalMoney']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$cEndDate);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['cApplyDate']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['scrivenername']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['city'].$showDataOne[$i]['area'].$showDataOne[$i]['cAddr']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataOne[$i]['statusName']);
// 		$r++;
// 	}

// 	// 	##
// 	$page ++ ;
// 	//配件總數

// 	$objPHPExcel->createSheet() ;
// 	$objPHPExcel->setActiveSheetIndex($page) ;
// 	$objPHPExcel->getActiveSheet()->setTitle('配件總數');

// 	$c =65;
// 	$r = 1;
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'序號');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'保證號碼');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'店家編號');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介店名');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總價金');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'案件狀態日期');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'進案日期');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'地政士姓名');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'標的物座落');
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'狀態');
// 	$r++;

// 	for ($i=0; $i < count($showDataPair); $i++) { 
// 		$c =65;
// 		if ($showDataPair[$i]['statusName']=='已結案') { 
// 			$cEndDate = $showDataPair[$i]['cEndDate'];
// 		}
// 		else {
// 			$cEndDate = $showDataPair[$i]['cSignDate'];
// 		}

// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,($i+1));
		
// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $showDataPair[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING);


// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['newCode']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['newbStore']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['cTotalMoney']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$cEndDate);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['cApplyDate']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['scrivenername']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['city'].$showDataPair[$i]['area'].$showDataPair[$i]['cAddr']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showDataPair[$i]['statusName']);
// 		$r++;
// 	}

// 	$page++;
// 	##
// }


$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex($page) ;
$objPHPExcel->getActiveSheet()->setTitle('案件資訊');

$c = 65;//A
$r = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'標的物座落');
$r++;

for ($i=0; $i < count($showData); $i++) { 
	$c = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,($i+1));

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['CertifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $showData[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['brandName']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['bStore']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['buyer']);
	$showData[$i]['cTotalMoney'] = str_replace(',', '', $showData[$i]['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['cTotalMoney']);
	$showData[$i]['cCertifiedMoney'] = str_replace(',', '', $showData[$i]['cCertifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['cCertifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['cSignDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['cApplyDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['scrivenername']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['statusName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$showData[$i]['city'].$showData[$i]['area'].$showData[$i]['cAddr']);

	$r++;
}
$objPHPExcel->setActiveSheetIndex(0) ;
// die('123');
$_file = 'analysiscase.xlsx' ;

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

##

function getStatus($s)
{
	global $conn;

	$sql= "SELECT sName FROM tStatusCase AS sc WHERE sc.sId=".$s;

	$rs = $conn->Execute($sql);

	return $rs->fields['sName'];
}

function dateformate($txt)
{
	$txt = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$txt)) ;
	$tmp = explode('-',$txt) ;
				
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
				
	$txt = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	return $txt;
}
?>