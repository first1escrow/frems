<?php
require_once '../bank/Classes/PHPExcel.php';
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php';
include_once '../session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$real_year  = $_REQUEST['real_year'] + 1911;
$real_month = $_REQUEST['real_month'];

$sql = '
SELECT
	tBankTrans.tVR_Code,
	tBankTrans.tExport_time,
	tBankTrans.tMoney,
	b.cName AS owner,
	c.cName AS buyer,
	tBranch.bName,
	tBranch.bStore,
	tBankTrans.tTxt
FROM
	tBankTrans
INNER JOIN
	tContractRealestate AS a ON tBankTrans.tMemo = a.cCertifyId
INNER JOIN
	tContractOwner AS b ON tBankTrans.tMemo = b.cCertifiedId
INNER JOIN
	tContractBuyer AS c ON tBankTrans.tMemo = c.cCertifiedId
INNER JOIN
	tBranch ON a.cBranchNum = tBranch.bId
WHERE
	tBankTrans.tObjKind = "仲介服務費" AND
	tBankTrans.tExport_time >= "' . $real_year . '-' . str_pad($real_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00" AND
	tBankTrans.tExport_time <= "' . $real_year . '-' . str_pad($real_month, 2, '0', STR_PAD_LEFT) . '-31 23:59:59" AND
	tBranch.bCategory="2"
';

$conn = new first1DB();
$list = $conn->all($sql);

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經直營案件服務費資料查詢明細結果");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);

//設定總表所有案件金額千分位符號
$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//寫入總表資料
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '出款日期');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '出款金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '賣方');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '買方');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '仲介公司');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '店名稱');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '備註');

$cell_no = 2; //愈填寫查詢結果起始的儲存格位置
//寫入查詢結果
for ($i = 0; $i < count($list); $i++) {
    //設定文字格式
    $objPHPExcel->getActiveSheet()->getCell('A' . ($i + $cell_no))->setValueExplicit($list[$i]['tVR_Code'], PHPExcel_Cell_DataType::TYPE_STRING);

    //寫入資料
    //$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+$cell_no),$list[$i]['tVR_Code']);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + $cell_no), $list[$i]['tExport_time']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + $cell_no), $list[$i]['tMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i + $cell_no), $list[$i]['owner']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + $cell_no), $list[$i]['buyer']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + $cell_no), $list[$i]['bName']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . ($i + $cell_no), $list[$i]['bStore']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . ($i + $cell_no), $list[$i]['tTxt']);

    //設定案件金額千分位符號
    //$objPHPExcel->getActiveSheet()->getStyle('G'.($i+$cell_no).':I'.($i+$cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    //設定保證號碼置中
    //$objPHPExcel->getActiveSheet()->getStyle('B'.($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('直營仲介服務費報表');

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$_file = 'realty_service_charge.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
/*
header("Pragma: public") ;
header("Expire: 0") ;
header("Cache-Control: must-revalidate, post-check=0, pre-check=0") ;
header("Content-Type: application/force-download") ;
header("Content-Type: application/vnd.ms-excel") ;
header("Content-Type: application/octet-stream") ;
header("Content-Type: application/download") ;
header("Content-Disposition: attachment; filename=$file_name") ;
header("Content-Transfer-Encoding: binary") ;
$objWriter->save('php://output') ;
 */
/*
<html>
<head>
<title>產出、下載報表</title>
</head>
<body>
<div id="msg">

檔案產生完成!!開始下載...
</div>
<script type="text/javascript">
//document.getElementById('msg').value = '檔案產生完成!!開始下載...' ;
var url = "<?='/report/excel/'.$file_name?>" ;
location = url ;
setTimeout("window.close()",2000) ;
</script>
</body>
</html>
 */
