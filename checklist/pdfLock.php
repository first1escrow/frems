<?php
require_once 'fpdf/chinese-unicode.php' ;
include_once '../opendb.php' ;
include_once '../openadodb.php' ;

//加密PDF檔案
Function pdfEncrypt ($origFile, $password, $destFile){
	//include the FPDI protection http://www.setasign.de/products/pdf-php-solutions/fpdi-protection-128/
	//require_once('fpdi/FPDI_Protection.php');
	require_once 'FPDI-1.4.4/FPDI_Protection.php' ;
	
	$_pdf =& new FPDI_Protection();
	// set the format of the destinaton file
	$_pdf->FPDF('P', 'mm', 'A4');
	
	//calculate the number of pages from the original document
	$pagecount = $_pdf->setSourceFile($origFile);
	
	//set pdf file page margin
	$_pdf->SetMargins(10,5,10) ;
	
	// copy all pages from the old unprotected pdf in the new one
	for ($loop = 1; $loop <= $pagecount; $loop++) {
		$tplidx = $_pdf->importPage($loop);
		$_pdf->addPage();
		$_pdf->useTemplate($tplidx);
	}
	
	// protect the new pdf file, and allow no printing, copy etc and leave only reading allowed
	//$_pdf->SetProtection(array(),'password');
	$_pdf->SetProtection(array(),$password);
	$_pdf->Output($destFile, 'F');
	
	return $destFile;
}
##

$uid = uniqid() ;								//PDF檔案命名用(重要)
$cell_y1 = 4.5 ;															// 內容用
$cell_y4 = 5 ;																// 內容用
$cell_y2 = 5 ;																// 標題用
$cell_y3 = 1 ;																// 手動跳行調行距用
$cell_y5 = 8 ;																// 銀行框框加大
$cell_y6 = 4 ;
$line_gap = 0.4 ;

$fromYear = (int)trim(addslashes($_POST['fromYear'])) ;
if (!$fromYear) {
	$fromYear = (int)date("Y",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$fromMonth = (int)trim(addslashes($_POST['fromMonth'])) ;
if (!$fromMonth) {
	$fromMonth = (int)date("m",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$toYear = (int)trim(addslashes($_POST['toYear'])) ;
if (!$toYear) {
	$toYear = (int)date("Y") ;
}

$toMonth = (int)trim(addslashes($_POST['toMonth'])) ;
if (!$toMonth) {
	$toMonth = (int)date("m") ;
}

if ($fromYear && $fromMonth && $toYear && $toMonth) {
	$totalMonths = ($toYear - $fromYear) * 12 + $toMonth - $fromMonth + 1 ;		//計算期間總月份
	
	$date_array = array() ;
	for ($i = 0 ; $i < $totalMonths ; $i ++) {
		$mm = date("Y.m",mktime(0,0,0,($fromMonth + $i),1,$fromYear)) ;
		$date_array[$i] = $mm ;
	}
}

//取得時間範圍內之資料
$sql = 'SELECT * FROM tStatusCase ORDER BY sId ASC;' ;
$rs = $conn->Execute($sql) ;

$i = 0 ;
$totalNo = 0 ;
while (!$rs->EOF) {
	$list[$i] = $rs->fields ;
	
	for ($j = 0 ; $j < count($date_array) ; $j ++) {
		$tmp = array() ;
		$tmp = explode('.',$date_array[$j]) ;
		
		$sql = '
			SELECT 
				COUNT(cCertifiedId) as total 
			FROM 
				tContractCase 
			WHERE 
				cCaseStatus="'.$rs->fields['sId'].'" 
				AND cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
				AND cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
		' ;
		
		$rs1 = $conn->Execute($sql) ;
		$list[$i]['total'] .= $rs1->fields['total'].',' ;
		unset($tmp) ;
	}
	$tmp = preg_replace("/,$/","",$list[$i]['total']) ;
	$list[$i]['total'] = $tmp ;
	
	unset($tmp) ;
	
	$i ++ ;
	$rs->MoveNext() ;
}
##

Function cell_height($str,$len=10) {										// 計算欄位高度
	$str_len = mb_strlen($str) ;

	if ($str_len > $len) {
		$cell_h = intval($str_len / $len) ;
		if ($str_len % $len > 0) { $cell_h += 1 ; }
		$cell_h *= 4.5 ;
	}
	else {
		$cell_h = 4.5 ;
	}
	
	return $cell_h ;
}

Function newName($nameStr) {
	for ($i = 0 ; $i < mb_strlen($nameStr,'UTF-8') ; $i ++) {
		$arrName[$i] = mb_substr($nameStr,$i,1,'UTF-8') ;
		if (($i > 0) && ($i < (mb_strlen($nameStr,'UTF-8') - 1))) {
			$arrName[$i] = 'Ｏ' ;
		}
	}
	return implode('',$arrName) ;
}

//取得金流系統類別與百分比
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;

$i = 0 ;
$totalNo = 0 ;
while (!$rs->EOF) {
	$list[$i] = $rs->fields ;
	
	//本月保證號碼統計
	$sql = '
		SELECT 
			COUNT(cBank) as total 
		FROM 
			tContractCase 
		WHERE 
			cBank="'.$rs->fields['cBankCode'].'" 
			AND cApplyDate>="'.date("Y-m").'-01 00:00:00" 
			AND cApplyDate<="'.date("Y-m").'-31 23:59:59"
	;' ;
	$rsThis = $conn->Execute($sql) ;
	$list[$i]['ThisMonth'] = $rsThis->fields['total'] ;
	$thisMonth += $list[$i]['ThisMonth'] + 1 - 1 ;
	##
	
	$i ++ ;
	$rs->MoveNext() ;
}

for ($i = 0 ; $i < count($list) ; $i ++) {
	$list[$i]['percentThis'] = round(($list[$i]['ThisMonth'] / $thisMonth) * 100, 2) ;
}
##

$con2 = '
{
        chart: {
			//type: "column"
        },
        title: {
            text: "第一建經狀態案件數統計"
        },
		subtitle: {
			text: "('.$fromYear.'/'.$fromMonth.'~'.$toYear.'/'.$toMonth.')"
		},
		xAxis: {
			categories: [
' ;

$str = '' ;
for ($i = 0 ; $i < count($date_array) ; $i ++) {
	$str .= "\t\t\t".$date_array[$i].",\n" ;
}
echo preg_replace("/,$/","",$str) ;

$con2 .= '
			]
		},
		yAxis: {
			min: 0,
			title: {
				text: "案件數"
			}
		},
        tooltip: {
			headerFormat: "<table>",
			pointFormat: "<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>" +
				"<td style=\"padding:0\"><b>{point.y:.0f} 件</b></td></tr>",
			footerFormat: "</table>",
			shared: true,
			useHTML: true
        },
        plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
        },
        series: [{
' ;

$arr = array() ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$arr[$i] = 'name: "'.$list[$i]['sName'].'",'."\n" ;
	$arr[$i] .= 'data: ['.$list[$i]['total'].']'."\n" ;
}

$con2 .= implode('}, {',$arr) ;
$con2 .= '
        }]
}
' ;

//HighCharts 轉圖片檔
$contents = "
{
        chart: {
			plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '第一建經銀行系統使用比例'
        },
        subtitle: {
            text: '(本月份)'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
" ;

$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percentThis']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
$contents .= $str ;

$contents .= "
            ]
        }]
};
" ;
##

//Server 端產生圖片檔案(一)
$url = 'http://export.highcharts.com/';
$data = array('filename' => 'chart' , 'scale' => 2 , 'sourceHeight' => 1200 ,'sourceWidth' => 800 , 'type' => 'image/jpeg' , 'options' => $contents) ;
$options = array(
      'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
));

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

//var_dump($result);
$fp = fopen('pdfChart_'.$uid.'.jpg', 'w');
fwrite($fp, $result);
fclose($fp);
##

//Server 端產生圖片檔案(二)
$url = 'http://export.highcharts.com/';
$data = array('filename' => 'chart2' , 'scale' => 2 , 'sourceHeight' => 1200 ,'sourceWidth' => 800 , 'type' => 'image/jpeg' , 'options' => $con2) ;
$options = array(
      'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
));

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

//var_dump($result);
$fp = fopen('pdfChart2_'.$uid.'.jpg', 'w');
fwrite($fp, $result);
fclose($fp);
##

$pdf = new PDF_Unicode() ;													// 建立 FPDF

$pdf->Open() ;																// 開啟建立新的 PDF 檔案
$pdf->SetAuthor('Jason Chen') ; 											// 設定作者
/*
$pdf->SetAutoPageBreak(1,2) ;												// 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10,5,10) ;													// 設定顯示邊界 (左、上、右)
*/
$pdf->AddPage() ;															// 新增一業
$pdf->AddUniCNShwFont('uni'); 												// 設定為 UTF-8 顯示輸出

//////////////////////// 買方 ///////////////////////////

$pdf->SetFont('uni','',6); 
$pdf->Cell(190,$cell_y1,$detail['last_modify'],0,1,'R') ;
$pdf->SetFont('uni','',14); 
$pdf->Cell(190,$cell_y1,'第一建築經理(股)公司',0,1,'C') ;					// 寫入文字
	
$pdf->SetFontSize(8) ;														// 設定字體大小
$pdf->Cell(190,$cell_y1,'履保專戶收支明細表暨點交表確認書(買方)',0,1,'C') ;

$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

$pdf->Ln() ;																// 換行
$pdf->Ln() ;																// 換行

$pdf->SetFontSize(10) ;	
$pdf->Cell(190,$cell_y6,'其他注意事項',0,1) ;
$pdf->Cell(5,$cell_y6,'1.') ;
$pdf->MultiCell(185,$cell_y6,'簽名蓋章前請確認已完成點交手續且上列金額及帳號內容無誤，若上列資料有誤須變更，請在有誤須變更處接修正及簽名蓋章',0,1) ;
$pdf->Cell(5,$cell_y6,'2.') ;
$pdf->Cell(190,$cell_y6,'本案業由買方已取回權狀及隨案謄本並結案。',0,1) ;
$pdf->Cell(5,$cell_y6,'3.') ;
$pdf->Cell(190,$cell_y6,'此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據',0,1) ;
$pdf->Cell(5,$cell_y6,'4.') ;
$pdf->Cell(75,$cell_y6,'履約保證費發票郵寄地址') ;
$pdf->Cell(120,$cell_y6,'□同合約書所留地址',0,2) ;
$pdf->Cell(120,$cell_y6,'□捐贈創世基金會',0,2) ;
$pdf->Cell(20,$cell_y6,'□如下地址：') ;
$x = $pdf->GetX() ;
$y = $pdf->GetY() + $cell_y6 ;												// 畫線
$pdf->Line($x,$y,($x+50),$y) ;

$pdf->Ln() ;

$pdf->Cell(5,$cell_y6,'5.') ;
$pdf->Cell(190,$cell_y6,'上述事項確認無誤後請於下方簽章處簽名蓋章：',0,1) ;
$pdf->Ln() ;
$pdf->Cell(63,$cell_y6,'買方簽章：') ;
$pdf->Cell(64,$cell_y6,'仲介方簽章：') ;
$pdf->Cell(63,$cell_y6,'地政士簽章：',0,1) ;

//插入圖片
$img = 'pdfChart_'.$uid.'.jpg' ;		//600px * 25.4 / 72dpi = 211.67mm(寬度)、400px * 25.4 / 72dpi = 141.12(長度)
$pdf->Image($img,$pdf->GetX(),$pdf->GetY(),211.67,141.12,'','http://tw.yahoo.com') ;
##

//Test
$pdf->SetXY(200,250) ;
$pdf->Ln() ;
//$s = '一二三四五' ;
//$s = '中' ;

for ($t = 0 ; $t < 44 ; $t ++) {
	$s .= 'Ａ' ;
}


$pdf->SetFontSize(12) ;	
//echo $pdf->GetStringWidth($s) ;
$pdf->Ln() ;
//$pdf->Cell(0,5,$s,1,1,'C') ;
$pdf->MultiCell(0,5,$s,1,'C') ;

//$pdf->SetLink($lnk) ;
$pdf->Cell(0,5,'文字1','B',1,'C',0,'http://tw.yahoo.com') ;
//$pdf->Text(105,200,'手動放字區域') ;
$pdf->Cell(160,5,'',1) ;
$pdf->Write(5,'ＡＡＡＡＡＡＡＡＡＡＡ') ;
$pdf->Ln() ;
##

//////////////////////// 賣方 ///////////////////////////
$pdf->AddPage() ;

$pdf->SetFont('uni','',6); 
$pdf->Cell(190,$cell_y1,$detail['last_modify'],0,1,'R') ;

$pdf->SetFont('uni','',14); 
$pdf->Cell(190,$cell_y1,'第一建築經理(股)公司',0,1,'C') ;					// 寫入文字
	
$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y1,'履保專戶收支明細表暨點交表確認書(賣方)',0,1,'C') ;

$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

$pdf->SetFontSize(10) ;	
$pdf->Cell(190,$cell_y6,'應注意事項',0,1) ;
$pdf->Cell(5,$cell_y6,'1.') ;
$pdf->MultiCell(185,$cell_y6,'簽名蓋章前請確認已完成點交手續且上列金額及帳號內容無誤，若上列資料有誤須變更，請在有誤須變更處接修正及簽名蓋章',0,1) ;
$pdf->Cell(5,$cell_y6,'2.') ;
$pdf->Cell(185,$cell_y6,'此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據',0,1) ;
$pdf->Cell(5,$cell_y6,'3.') ;
$pdf->MultiCell(185,$cell_y6,'年度給付利息累計超過壹仟元,將依法開立扣繳憑單;該所得非「儲蓄投資特別扣除額」之27萬免扣繳範圍',0,1) ;
$pdf->Cell(5,$cell_y6,'4.') ;
$pdf->Cell(70,$cell_y6,'履約保證費發票及扣繳憑單郵寄地址') ;
$pdf->Cell(120,$cell_y6,'□同合約書所留地址',0,2) ;
$pdf->Cell(120,$cell_y6,'□捐贈創世基金會',0,2) ;
$pdf->Cell(20,$cell_y6,'□如下地址：') ;
$x = $pdf->GetX() ;
$y = $pdf->GetY() + $cell_y6 ;												// 畫線
$pdf->Line($x,$y,($x+50),$y) ;

$pdf->Ln() ;

$pdf->Cell(5,$cell_y6,'5.') ;
$pdf->Cell(185,$cell_y6,'上述事項確認無誤後請於下方簽章處簽名蓋章：',0,1) ;
$pdf->Cell(63,$cell_y6,'賣方簽章：') ;
$pdf->Cell(64,$cell_y6,'仲介方簽章：') ;
$pdf->Cell(63,$cell_y6,'地政士簽章：',0,1) ;
/*
for ($i = 0 ; $i < 6 ; $i ++) {
	$pdf->Cell(190,$cell_y2,'',0,1) ;
}
*/
$pdf->SetFontSize(10) ;

$pdf->Cell(74,$cell_y1,'中華民國 ________ 年 ________ 月 ________ 日') ;
$pdf->Cell(63,$cell_y1,'聯絡電話：02-2363-6611 Ext.'.$undertaker['Ext'],0,0,'R') ;
$pdf->Cell(63,$cell_y1,'傳真電話：'.$undertaker['FaxNum'],0,0,'C') ;
$pdf->Ln() ;
//插入圖片
$img2 = 'pdfChart2_'.$uid.'.jpg' ;		//600px * 25.4 / 72dpi = 211.67mm(寬度)、400px * 25.4 / 72dpi = 141.12(長度)
$pdf->Image($img2,($pdf->GetX()+50),($pdf->GetY()+20),100,70) ;
##

// 產生輸出
$pdf->Output('_tmp'.$uid.'.pdf','F') ;
//$pdf->Output() ;

//echo $cCertifiedId."點交表已輸出" ;

//設定PDF密碼
//password for the pdf file
$password = 'abcd' ;
##

//name of the original file (unprotected)
$origFile = '_tmp'.$uid.'.pdf' ;

//name of the destination file (password protected and printing rights removed)
$destFile = 'plan_'.$uid.'.pdf' ;

//encrypt the book and create the protected file
pdfEncrypt($origFile, $password, $destFile ) ;

unlink($origFile) ;
unlink($img) ;
unlink($img2) ;

//echo "<br>\nDone!!<br><br>\n" ;
//echo '<a href="'.$destFile.'">下載行銷企劃書</a>' ;

/*
header('Pragma: public') ;
header('Expires: 0') ;
header('Cache-Control: must-revalidate, post-check=0, pre-check=0') ;
*/
header('Content-type: application/force-download') ;
//header('Cache-Control: private', false) ;
//header('Content-type: application/pdf') ;
//header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($destFile)) . ' GMT') ;
header('Content-Transfer-Encoding: Binary') ;
header('Content-Disposition: attachment; filename="'.$destFile.'"') ;
//header('Content-length:'.filesize($destFile)) ;
readfile($destFile) ;
?>
