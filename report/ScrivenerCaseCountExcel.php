<?php
$sales = $_POST['sales'];
$bBrand = $_POST['bBrand'];
$scrivener = $_POST['scrivener'];

$exceptbId = array(632, 575,552,620,411,224) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳)
$sales = $_POST['sales'];
$brand = $_POST['bBrand'];
$str = '';
if ($brand) {
	$str .= " AND bBrand = '".$brand."'";
}

if ($scrivener) {
	$str .= " AND bSID = '".$scrivener."'";
}

$sql = "SELECT
			bAccount,
			bSID,
			(SELECT sName FROM tScrivener WHERE sId = bSID) AS Name,
			bCreateDate,
			(SELECT (SELECT pName FROM tPeopleInfo WHERE  pId= sUndertaker1) FROM tScrivener WHERE sId = bSID) AS Undertaker,
			(SELECT bName FROM tBrand WHERE bId = bBrand) brand,
			CASE  
		    WHEN bCategory=1 THEN '加盟'
		    WHEN bCategory=2 THEN '直營' 
		    WHEN bCategory=3 THEN '非仲介成交' 
		    ELSE '非仲介成交' 
		    END bCategory, 
		    CASE
			WHEN bApplication=1 THEN '建物'
		    WHEN bApplication=2 THEN '土地' 
		    WHEN bApplication=3 THEN '預售屋' 
		    ELSE '建物' 
		    END bApplication 
		FROM
			tBankCode
		WHERE
			bDel = 'n'
			AND bUsed = 0
			AND bCreateDate >='".$_POST['date_start_y']."-01-01 00:00:00' AND bCreateDate <='".$_POST['date_end_y']."-12-31 00:00:00'
			AND bSID NOT IN(".implode(',', $exceptbId).")
			".$str."
		ORDER BY bSID";
// 	header("Content-Type:text/html; charset=utf-8"); 
// echo $sql;
// die;
$rs = $conn->Execute($sql);
$i = 0;

while (!$rs->EOF) {
	$check = 0;

	if ($sales != 0 && $sales != '') {
		if (!checkScrivenerSales($rs->fields['bSID'],$sales)) {
			$check = 1;
		
		}
	}
	
	
	if ($check == 0) {
		$rs->fields['bCreateDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',str_replace(' ', '', $rs->fields['bCreateDate']));
		$key = 'SC'.str_pad($rs->fields['bSID'], 4,0,STR_PAD_LEFT).str_replace('-', '', $rs->fields['bCreateDate']);
		
		$data[$key]['code'] = 'SC'.str_pad($rs->fields['bSID'], 4,0,STR_PAD_LEFT);
		$data[$key]['Name'] = $rs->fields['Name'];
		$data[$key]['bCreateDate'] = $rs->fields['bCreateDate'];
		$data[$key]['Undertaker'] = $rs->fields['Undertaker'];
		$data[$key]['sales'] = getScrivenerSales($rs->fields['bSID']);
		$data[$key]['total']++;
		$data[$key]['CertifiedId'][$i] = $rs->fields;
		$i++;

	// 	$data[$i]= $rs->fields;
	// 	$data[$i]['sales'] = getScrivenerSales($rs->fields['bSID']);
	// 	$data[$i]['CertifiedId'] = getCertifiedId($rs->fields['bSID'],$rs->fields['bCreateDate']);
	// 	$i++;
	}
	

	$rs->MoveNext();
}
// echo "<pre>";
// print_r($data);
// echo "</pre>";

// die;

function checkScrivenerSales($id,$sales){
	global $conn;

	$sql = "SELECT * FROM tScrivenerSales WHERE sScrivener = '".$id."' AND sSales ='".$sales."'";
	
	$rs = $conn->Execute($sql);
	$total = $rs->RecordCount();

	if ($total > 0) {
		return true;
	}else{
		return false;
	}
	
}
function getScrivenerSales($id){
	global $conn;
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '".$id."'";

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$sales[] = $rs->fields['Name'];

		$rs->MoveNext();
	}


	return @implode(',',  $sales);
}




$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("代書庫存有效合約書");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('代書庫存有效合約書');

//寫入清單標題列資料
//代書姓名/合約份數/申請日期/負責業務/經辦

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代書姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約份數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'負責業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'經辦');
$row++;

foreach ($data as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['code']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Name']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['total']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bCreateDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sales']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Undertaker']);
	// print_r($v);
	// die;
	// preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
	$row++;
}


$objPHPExcel->createSheet(1) ;
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('有效保證號碼');
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代書姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'版本品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'版本品牌類型');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'版本類別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'負責業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'經辦');
$row++;


foreach ($data as $k => $v) {

	foreach ($v['CertifiedId'] as $key => $value) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['code']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Name']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['bAccount'],PHPExcel_Cell_DataType::TYPE_STRING); 

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['brand']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['bCategory']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['bApplication']);
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bCreateDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sales']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Undertaker']);

		$row++;
	}
	
	
	
	
}

$objPHPExcel->setActiveSheetIndex(0);
// for ($i = 0 ; $i < count($data) ; $i ++) {
// 	$col = 65;

// 	$tmp = explode(' ', $data[$i]['bCreateDate']);
// 	$tmp2 = explode('-', $tmp[0]);
// 	$data[$i]['bCreateDate'] = ($tmp2[0]-1911)."-".$tmp2[1]."-".$tmp2[2];
// 	$code = 'SC'.str_pad($data[$i]['bSID'], 4,0,STR_PAD_LEFT);

// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['Name']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['total']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['bCreateDate']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['sales']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['Undertaker']);

// 	unset($tmp);
// 	unset($tmp2);
// 	$row++;
// }



$_file = iconv('UTF-8', 'BIG5', '代書庫存有效合約書') ;
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file.'.xlsx');
			
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
	
exit ;
?>
