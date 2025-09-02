<?php
// 防止任何輸出干擾Excel檔案產生
ob_start();
error_reporting(E_ERROR);

// 載入 PHPExcel 兼容性防護
define('FORCE_PHPSPREADSHEET_ONLY', true);
require_once __DIR__ . '/phpexcel_compatibility_guard.php';

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 定義兼容舊版 PHPExcel 的常量，幫助代碼遷移
if (! class_exists('PHPExcel_Style_Border')) {
    class PHPExcel_Style_Border
    {
        const BORDER_NONE   = Border::BORDER_NONE;
        const BORDER_THIN   = Border::BORDER_THIN;
        const BORDER_DOUBLE = Border::BORDER_DOUBLE;
    }
}

if (! class_exists('PHPExcel_Style_Alignment')) {
    class PHPExcel_Style_Alignment
    {
        const HORIZONTAL_CENTER = Alignment::HORIZONTAL_CENTER;
        const HORIZONTAL_RIGHT  = Alignment::HORIZONTAL_RIGHT;
        const VERTICAL_CENTER   = Alignment::VERTICAL_CENTER;
        const VERTICAL_TOP      = Alignment::VERTICAL_TOP;
        const VERTICAL_BOTTOM   = Alignment::VERTICAL_BOTTOM;
    }
}

if (! class_exists('PHPExcel_Style_Font')) {
    class PHPExcel_Style_Font
    {
        const UNDERLINE_NONE             = Font::UNDERLINE_NONE;
        const UNDERLINE_SINGLE           = Font::UNDERLINE_SINGLE;
        const UNDERLINE_DOUBLE           = Font::UNDERLINE_DOUBLE;
        const UNDERLINE_SINGLEACCOUNTING = Font::UNDERLINE_SINGLEACCOUNTING;
        const UNDERLINE_DOUBLEACCOUNTING = Font::UNDERLINE_DOUBLEACCOUNTING;
    }
}

if (! class_exists('PHPExcel_Cell_DataType')) {
    class PHPExcel_Cell_DataType
    {
        const TYPE_STRING = DataType::TYPE_STRING;
    }
}

$cid = $_POST['cid'];

$sql = "
		SELECT
			cc.cSignDate AS cSignDate,
			cb.cName AS buyer,
			cb.cIdentifyId AS buyerId,
			cb.cMobileNum AS buyerphone,
			cb.sAgentName1 AS buyersale,
			cb.sAgentName2 AS buyersale1,
			cb.sAgentMobile1 AS buyersalephone,
			cb.sAgentMobile2 AS buyersalephone1,
			co.cName AS owner,
			co.cIdentifyId AS ownerId,
			co.cMobileNum AS ownerphone,
			co.sAgentName1 AS ownersale,
			co.sAgentMobile1 AS ownersalephone,
			co.sAgentName2 AS ownersale1,
			co.sAgentMobile2 AS ownersalephone1,
			(SELECT sName FROM tScrivener AS s WHERE s.sId=cs.cScrivener) AS Scrivener,
			(SELECT sName FROM tScrivenerSms AS s WHERE s.sId=cs.cManage2) AS Scrivener2,
			ci.cTotalMoney AS cTotalMoney,
			ci.cSignMoney AS cSignMoney,
			ci.cAffixMoney AS cAffixMoney,
			ci.cDutyMoney AS cDutyMoney,
			ci.cEstimatedMoney AS cEstimatedMoney,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS Brand,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS Brand1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS Brand2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS Store,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS Store1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS Store2,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum) AS StoreName,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum1) AS StoreName1,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum2) AS StoreName2
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractBuyer AS cb ON cb.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractOwner AS co ON co.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE
			cc.cCertifiedId = '" . $cid . "'
	   ";

$rs = $conn->Execute($sql);

$list[] = $rs->fields;

if ($list[0]["cSignDate"] != '') {
    $tmp                  = explode('-', substr($list[0]["cSignDate"], 0, 10));
    $tmp[0]               = $tmp[0] - 1911;
    $list[0]['cSignDate'] = $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";
    unset($tmp);
}

//服務對象：1.買賣方、2.賣方、3.買方
if ($list[0]['cBranchNum'] > 0) {
    if ($list[0]['cServiceTarget'] == 1) {
        $buyerBrand[]  = $list[0]["Brand"];
        $buyerBranch[] = $list[0]["Store"] . "-" . $list[0]["StoreName"];
        $ownerBrand[]  = $list[0]["Brand"];
        $ownerBranch[] = $list[0]["Store"] . "-" . $list[0]["StoreName"];
    } elseif ($list[0]['cServiceTarget'] == 2) {
        $ownerBrand[]  = $list[0]["Brand"];
        $ownerBranch[] = $list[0]["Store"] . "-" . $list[0]["StoreName"];
    } elseif ($list[0]['cServiceTarget'] == 3) {
        $buyerBrand[]  = $list[0]["Brand"];
        $buyerBranch[] = $list[0]["Store"] . "-" . $list[0]["StoreName"];
    }
}

if ($list[0]['cBranchNum1'] > 0) {
    if ($list[0]['cServiceTarget1'] == 1) {
        $buyerBrand[]  = $list[0]["Brand1"];
        $buyerBranch[] = $list[0]["Store1"] . "-" . $list[0]["StoreName1"];
        $ownerBrand[]  = $list[0]["Brand1"];
        $ownerBranch[] = $list[0]["Store1"] . "-" . $list[0]["StoreName1"];
    } elseif ($list[0]['cServiceTarget1'] == 2) {
        $ownerBrand[]  = $list[0]["Brand1"];
        $ownerBranch[] = $list[0]["Store1"] . "-" . $list[0]["StoreName1"];
    } elseif ($list[0]['cServiceTarget1'] == 3) {
        $buyerBrand[]  = $list[0]["Brand1"];
        $buyerBranch[] = $list[0]["Store1"] . "-" . $list[0]["StoreName1"];
    }
}

if ($list[0]['cBranchNum2'] > 0) {
    if ($list[0]['cServiceTarget2'] == 1) {
        $buyerBrand[]  = $list[0]["Brand2"];
        $buyerBranch[] = $list[0]["Store2"] . "-" . $list[0]["StoreName2"];
        $ownerBrand[]  = $list[0]["Brand2"];
        $ownerBranch[] = $list[0]["Store2"];
    } elseif ($list[0]['cServiceTarget2'] == 2) {
        $ownerBrand[]  = $list[0]["Brand2"];
        $ownerBranch[] = $list[0]["Store2"] . "-" . $list[0]["StoreName2"];
    } elseif ($list[0]['cServiceTarget2'] == 3) {
        $buyerBrand[]  = $list[0]["Brand2"];
        $buyerBranch[] = $list[0]["Store2"] . "-" . $list[0]["StoreName2"];
    }
}

$sql = "
			SELECT
				cAddr,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cZip) AS area
			FROM
				 tContractProperty
			WHERE
				cCertifiedId = '" . $cid . "'
				 ORDER BY cItem ASC";
$rs = $conn->Execute($sql);

while (! $rs->EOF) {

    $tmp2[] = $rs->fields['city'] . $rs->fields['area'] . $rs->fields['cAddr'];

    $rs->MoveNext();
}
$list[0]['addr'] = implode(';', $tmp2);

unset($tmp2);
##
$sql                       = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 3 AND cCertifiedId = '" . $cid . "' ORDER BY cId ASC LIMIT 1";
$rs                        = $conn->Execute($sql);
$list[0]['buyersale']      = ($rs && isset($rs->fields['cName'])) ? $rs->fields['cName'] : '';
$list[0]['buyersalephone'] = ($rs && isset($rs->fields['cMobileNum'])) ? $rs->fields['cMobileNum'] : '';

$sql                       = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 4 AND cCertifiedId = '" . $cid . "' ORDER BY cId ASC LIMIT 1";
$rs                        = $conn->Execute($sql);
$list[0]['ownersale']      = ($rs && isset($rs->fields['cName'])) ? $rs->fields['cName'] : '';
$list[0]['ownersalephone'] = ($rs && isset($rs->fields['cMobileNum'])) ? $rs->fields['cMobileNum'] : '';

#################################

$objPHPExcel = new Spreadsheet();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("成交資料暨仲介服務費出款申請單");
$objPHPExcel->getProperties()->setDescription("成交資料暨仲介服務費出款申請單");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('成交資料申請單');
//設定邊界
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0);

##字體大小
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A3:F44')->getFont()->setSize(12);
##對齊
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("A2:E2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
##文字樣式
$objPHPExcel->getActiveSheet()->getStyle('A1:F100')->getFont()->setName('新細明體');
$objPHPExcel->getActiveSheet()->getStyle('A1:A35')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D4:D35')->getFont()->setBold(true);
##
##框
//全部
$styleArray = ['borders' => ['allBorders' => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
$objPHPExcel->getActiveSheet()->getStyle('A3:F14')->applyFromArray($styleArray);

unset($styleArray);

//表頭
$styleArray = ['borders' => ['bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE]]];
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => ['top' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE]]];
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray);
unset($styleArray);

//總價
$styleArray = ['borders' => [
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A18')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A19')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('D15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D18')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A29')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A31')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A32')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A33')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A34')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D29')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('D28')->applyFromArray($styleArray);
//
$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('F15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F18')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('F19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F27')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F29')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F31')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F32')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F33')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F34')->applyFromArray($styleArray);
$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('A28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A30')->applyFromArray($styleArray);
$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('F28')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('F30')->applyFromArray($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE, 'color' => ['rgb' => '000000']],
],
];

// $objPHPExcel->getActiveSheet()->getStyle('A30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E30')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('B28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E28')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('A35')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B35')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C35')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D35')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E35')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F35')->applyFromArray($styleArray);

// $objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('B27')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($styleArray);

// $objPHPExcel->getActiveSheet()->getStyle('E27')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('D27')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('F27')->applyFromArray($styleArray);
unset($styleArray);
//仲介

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('B19:C19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B19:E19')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('A19')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('F19')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('D19')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
],
];

$objPHPExcel->getActiveSheet()->getStyle('A20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A24')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A25')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A26')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($styleArray);

// $objPHPExcel->getActiveSheet()->getStyle('D19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D24')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D25')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D26')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D27')->applyFromArray($styleArray);

unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
],
];
// $objPHPExcel->getActiveSheet()->getStyle('F19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F24')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F25')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F26')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F27')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
],
];
$objPHPExcel->getActiveSheet()->getStyle('F28')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F30')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($styleArray);

// $styleArray = array('borders' => array(
//                                         'top' => array('borderStyle' => PHPExcel_Style_Border::BORDER_NONE),
//                                         'bottom' => array('borderStyle' => PHPExcel_Style_Border::BORDER_NONE),
//                                         'left' => array('borderStyle' => PHPExcel_Style_Border::BORDER_NONE),
//                                         'right' => array('borderStyle' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000'),),
//                                         ),
//                     );
// $objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($styleArray);
##
#############################
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);   //14
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);   //16
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17.5); //16
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);   //14
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);   //15
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);   //15
##

##
$objPHPExcel->getActiveSheet()->setCellValue('A1', '成交資料暨仲介服務費出款申請單');
$objPHPExcel->getActiveSheet()->setCellValue('A2', '簽約日期：' . $list[0]['cSignDate']);
##合併
$objPHPExcel->getActiveSheet()->mergeCells("A1:F1");
$objPHPExcel->getActiveSheet()->mergeCells("A2:F2");

$row = 3;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '保證編號');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $cid, PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, '地政士');
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $list[0]['Scrivener']);

$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '案件連絡人');
$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '');
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;
##
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方姓名");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $list[0]['buyer']);
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方姓名");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $list[0]['owner']);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;
##

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "身分證/統一編號");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, substr_replace($list[0]['buyerId'], '****', 5, 4));
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "身分證/統一編號");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, substr_replace($list[0]['ownerId'], '****', 5, 4));

//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . ($row + 1));
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, "手機號碼：" . substr_replace($list[0]['buyerphone'], '****', 5, 4) . "\n(若不願收受簡訊則請勿填寫)", PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . ($row + 1));
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, "手機號碼：" . substr_replace($list[0]['ownerphone'], '****', 5, 4) . "\n(若不願收受簡訊則請勿填寫)", PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row = $row + 2;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方店長");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '');

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方店長");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '');
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"買方經紀人員");
// $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
// $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$list[0]['buyersale']);

// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"賣方經紀人員");
// $objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$list[0]['ownersale']);

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '');

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '');
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"手機號碼");
// $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
// $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$row, $list[0]['buyersalephone'],PHPExcel_Cell_DataType::TYPE_STRING);
// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"手機號碼");
// $objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
// $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$row, $list[0]['ownersalephone'],PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"買方經紀人員");
// $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
// $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$list[0]['buyersale1']);

// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"賣方經紀人員");
// $objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$list[0]['ownersale1']);

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '');

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '');
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"手機號碼");
// $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
// $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$row, $list[0]['buyersalephone1'],PHPExcel_Cell_DataType::TYPE_STRING);
// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"手機號碼");
// $objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
// $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$row, $list[0]['ownersalephone1'],PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "地址");
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $list[0]['addr']);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "總價：");                                         //$list[0]['cTotalMoney']
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "簽約：" . number_format($list[0]['cSignMoney'])); //
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□無□有設定扺押權_________________萬元");
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$row2 = $row;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, number_format($list[0]['cTotalMoney']));
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "用印：" . number_format($list[0]['cAffixMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□無□有私人設定__________________萬元");
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

//

$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "完稅：" . number_format($list[0]['cDutyMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□無□有解約條款");
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->mergeCells("A" . $row2 . ":A" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "尾款：" . number_format($list[0]['cEstimatedMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□無□有限制登記");
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$styleArray = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
],
];
$styleArray2 = ['borders' => [
    'top'    => ['borderStyle' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
    'bottom' => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'left'   => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
    'right'  => ['borderStyle' => PHPExcel_Style_Border::BORDER_NONE],
],
];
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "【買方】"); //
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "仲介：(如配件拆帳 請詳細填寫！)"); //
$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "【賣方】");
$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, "仲介：(如配件拆帳 請詳細填寫！)");
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);

//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"_______________房屋____________________________店");
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '(1)' . $buyerBrand[0] . $buyerBranch[0]);
// $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"_________________店");
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);

// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"_______________房屋____________________________店");
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '(1)' . $ownerBrand[0] . $ownerBranch[0]);
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,"_________________店");
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方服務費：");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "___________________元");
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方服務費：");
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, "___________________元");
$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

// $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"_______________房屋____________________________店");

if ($buyerBranch[1] != '') {
    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '(2)' . $buyerBrand[1] . $buyerBranch[1]);
} else {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "(2)_______________房屋_______________________店");
}
// $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"_________________店");
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);

// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"_______________房屋____________________________店");
if ($ownerBranch[1] != '') {
    $objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '(2)' . $ownerBrand[1] . $ownerBranch[1]);
} else {
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "(2)_______________房屋_______________________店");
}
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,"_________________店");
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "買方服務費：");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, "___________________元");
$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->mergeCells("B" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "賣方服務費：");
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, "___________________元");
$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("E" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "★【撥款時機】：(須於買方匯入後再為撥付)"); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "★【撥款時機】：");
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "□於簽約後全數撥付 □於交屋時全數撥付"); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□於簽約後全數撥付 □於交屋時全數撥付"); //
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "□簽約後先撥付________________________________元，"); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "□簽約後先撥付________________________________元，"); //
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "餘款交屋時撥付_______________________________元｡\n"); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "餘款交屋時撥付_______________________________元｡\n"); //
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "請蓋仲介公司章"); //
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, "請蓋仲介公司章"); //
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, ""); //
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":C" . $row);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, ""); //
$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("D" . $row . ":F" . $row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(120);

$row++;

$tel = ($undertaker['pExt']) ? $company['tel'] . "(" . $undertaker['undertaker'] . "*" . $undertaker['pExt'] . ")" : $company['tel'];
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, "◆聯絡電話： " . $tel); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);
$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '◆傳真：' . $undertaker['pFaxNum']); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$space = '　　　　　　';
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $space . ''); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(18);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $space . ''); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(18);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $space . ''); //
$objPHPExcel->getActiveSheet()->mergeCells("A" . $row . ":F" . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(18);

$row++;

###############################

$_file = 'service_' . $cid . '.xlsx';

// 清理輸出緩衝區
while (ob_get_level()) {
    ob_end_clean();
}

// 設定適當的 headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $_file . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');

try {
    $objWriter = new Xlsx($objPHPExcel);
    $objWriter->save('php://output');
} catch (Exception $e) {
    error_log('Excel generation error: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Error generating Excel file: ' . $e->getMessage();
}
exit;

exit;
