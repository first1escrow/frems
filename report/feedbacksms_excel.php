<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

// die("建置中");

$sql = "
	SELECT
		br.bCode,
		br.bName AS brand, 
		bf.bName,
		bf.bMobile,
		b.bId,
		b.bName AS branch,
		(SELECT tTitle  FROM tTitle_SMS AS t WHERE t.id=bf.bNID) AS title,
		b.bStore
	FROM 
		tBranchFeedback AS bf
	LEFT JOIN 
		tBranch AS b ON b.bId=bf.bBranch
	LEFT JOIN 
		tBrand AS br ON br.bId = b.bBrand
	WHERE bf.bBranch !=0 AND b.bStatus = 1
	ORDER BY b.bId
";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$list[] = $rs->fields;

	$rs->MoveNext();
}

$sql = "SELECT 
			s.sId,
			s.sName AS boss,
			sf.sName AS mName,
			sf.sMobile  AS mMobile,
			(SELECT tTitle  FROM tTitle_SMS AS t WHERE t.id=sf.sNID) AS title,
			s.sOffice AS bStore,
			CONCAT('SC',LPAD(s.sId,4,'0')) as sCode
	 	FROM 
	 	 tScrivenerFeedSms AS sf
	 	LEFT JOIN
	 	 tScrivener AS s ON s.sId=sf.sScrivener
	 	ORDER BY s.sId
	 	";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list2[] = $rs->fields;

	$rs->MoveNext();
}
##
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("回饋金簡訊名單");
$objPHPExcel->getProperties()->setDescription("第一建經 回饋金簡訊名單");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('仲介');

//寫入表頭資料
// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$objPHPExcel->getActiveSheet()->setCellValue('A1','編號');
$objPHPExcel->getActiveSheet()->setCellValue('B1','品牌');
$objPHPExcel->getActiveSheet()->setCellValue('C1','仲介店名稱');
$objPHPExcel->getActiveSheet()->setCellValue('D1','公司名稱');
$objPHPExcel->getActiveSheet()->setCellValue('E1','職稱');
$objPHPExcel->getActiveSheet()->setCellValue('F1','姓名');
$objPHPExcel->getActiveSheet()->setCellValue('G1','電話');
##
$row = 2;
for ($i=0; $i < count($list); $i++) { 

	$code = $list[$i]['bCode'].str_pad($list[$i]['bId'],5,'0',STR_PAD_LEFT);



	$objPHPExcel->getActiveSheet()->setCellValue('A'.($row),$code);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($row),$list[$i]['brand']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($row),$list[$i]['bStore']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($row),$list[$i]['branch']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($row),$list[$i]['title']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($row),$list[$i]['bName']);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$row, $list[$i]['bMobile'],PHPExcel_Cell_DataType::TYPE_STRING);

	$row++;
}

#新增並指定工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
$objPHPExcel->getActiveSheet()->setTitle('地政士');

//寫入表頭資料
// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$objPHPExcel->getActiveSheet()->setCellValue('A1','編號');

$objPHPExcel->getActiveSheet()->setCellValue('B1','地政士');
$objPHPExcel->getActiveSheet()->setCellValue('C1','事務所名稱');
$objPHPExcel->getActiveSheet()->setCellValue('D1','職稱');
$objPHPExcel->getActiveSheet()->setCellValue('E1','姓名');
$objPHPExcel->getActiveSheet()->setCellValue('F1','電話');
##
$row = 2;
for ($i=0; $i < count($list2); $i++) { 

	$objPHPExcel->getActiveSheet()->setCellValue('A'.($row),$list2[$i]['sCode']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($row),$list2[$i]['boss']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($row),$list2[$i]['bStore']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($row),$list2[$i]['title']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($row),$list2[$i]['mName']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$row, $list2[$i]['mMobile'],PHPExcel_Cell_DataType::TYPE_STRING);

	$row++;
}

$objPHPExcel->setActiveSheetIndex(0);
$_file = 'feefback_sms.xlsx' ;

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