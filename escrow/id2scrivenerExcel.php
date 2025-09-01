<?php
include_once "../openadodb.php";
include_once '../session_check.php';
require_once '../bank/Classes/PHPExcel.php';
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php';

$_POST = escapeStr($_POST);

$scr  = $_POST['scr_total'];
$bank = $_POST['bank_total'];
$ver  = $_POST['ver_total'];
$date = $_POST['date'];
##
$str = '';

if (!empty($bank)) {
    $str .= ' AND bAccount LIKE "' . $bank . '%"';
}

if (!empty($ver)) {
    $str .= ' AND bBrand LIKE "' . $ver . '%"';
}

if (!empty($_REQUEST['date'])) {
    $tmp     = explode('-', $_REQUEST['date']);
    $sDate   = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2] . " 00:00:00";
    $eDate   = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2] . " 23:59:59";
    $dateStr = ' AND bCreateDate >="' . $sDate . '" AND bCreateDate <="' . $eDate . '"';
}

//剩餘保證號碼總數 AND bUsed="0"
$sql = '
	SELECT
		bAccount,
		bSID,
		bCategory,
		(SELECT sName FROM tScrivener WHERE sId = bSID) AS Name,
		(SELECT bName FROM tBrand WHERE bId =bBrand) AS bBrand,
		bCreateDate,
		bApplication,
		(SELECT (SELECT pName FROM tPeopleInfo WHERE  pId= sUndertaker1) FROM tScrivener WHERE sId = bSID) AS Undertaker,
		bUsed
	FROM
		tBankCode
	WHERE
		bSID="' . $scr . '"
		AND bDel="n"
		' . $str . $dateStr . '
	ORDER BY bCategory,bApplication';

$rs = $conn->Execute($sql);
$i  = 0;
while (!$rs->EOF) {

    if ($rs->fields['bUsed'] == 0) {
        $rs->fields['bCreateDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/", '', str_replace(' ', '', $rs->fields['bCreateDate']));

        $key = ($rs->fields['bCategory'] . $rs->fields['bApplication'] == '') ? '0' : $rs->fields['bCategory'] . $rs->fields['bApplication'];

        $dataCount[$key]++;

        $data[$i]['code']        = 'SC' . str_pad($rs->fields['bSID'], 4, 0, STR_PAD_LEFT);
        $data[$i]['Name']        = $rs->fields['Name'];
        $data[$i]['bCreateDate'] = $rs->fields['bCreateDate'];
        $data[$i]['Undertaker']  = $rs->fields['Undertaker'];
        $data[$i]['sales']       = getScrivenerSales($rs->fields['bSID']);
        $data[$i]['CertifiedId'] = $rs->fields['bAccount'];
        $data[$i]['Category']    = getCategory($rs->fields['bCategory']) . getApplication($rs->fields['bApplication']);

        $i++;
    }

    if (preg_match("/^60001/", $rs->fields['bAccount'])) {
        $codeBank = '一銀桃園';
    } elseif (preg_match("/^99985/", $rs->fields['bAccount'])) { //永豐
        $codeBank = '永豐西門';
    } elseif (preg_match("/^99986/", $rs->fields['bAccount'])) { //永豐
        $codeBank = '永豐城中';
    } elseif (preg_match("/^96988/", $rs->fields['bAccount'])) {
        $codeBank = '台新';
    } elseif (preg_match("/^55006/", $rs->fields['bAccount'])) {
        $codeBank = '一銀城東';
    }

    $key                                                                   = ($rs->fields['bCategory'] . $rs->fields['bApplication'] == '') ? '0' : $rs->fields['bCategory'] . $rs->fields['bApplication'];
    $used                                                                  = ($rs->fields['bUsed'] == 0) ? '未使用' : '已使用';
    $verC                                                                  = $codeBank . "_" . $rs->fields['bBrand'] . getCategory($rs->fields['bCategory']) . "_" . getApplication($rs->fields['bApplication']);
    $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC]['bank']  = $codeBank;
    $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC]['brand'] = $rs->fields['bBrand'];

    if ($rs->fields['bBrand'] == '台灣房屋') {
        $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC]['brand'] .= getCategory($rs->fields['bCategory']);
    }

    $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC]['app'] = getApplication($rs->fields['bApplication']);

    $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC][$used]++;
    $dataCount2[substr($rs->fields['bCreateDate'], 0, 10)][$verC]['total']++;
    // $dataCount2[$key]['total']++;

    $rs->MoveNext();
}

// //舊版無法辨識版本保證號碼餘額
// $sql = '
//     SELECT
//         COUNT(bAccount) as unknow_no
//     FROM
//         tBankCode
//     WHERE
//         bSID="'.$scr.'"
//         AND bBrand=""
//         AND bCategory=""
//         AND bApplication=""
//         AND bDel="n"
//         AND bUsed="0"
//         AND bAccount LIKE "'.$bank.'%"
//         '.$dateStr.'
//         ;' ;
// $rs = $conn->Execute($sql) ;
// // $unknow_no = $rs->fields['unknow_no'] + 1 - 1 ;
// $unknow_no = 0;
// while (!$rs->EOF) {
//     $unknow_no++;
//     $data[$i]['code'] = 'SC'.str_pad($rs->fields['bSID'], 4,0,STR_PAD_LEFT);
//     $data[$i]['Name'] = $rs->fields['Name'];
//     $data[$i]['bCreateDate'] = $rs->fields['bCreateDate'];
//     $data[$i]['Undertaker'] = $rs->fields['Undertaker'];
//     $data[$i]['sales'] = getScrivenerSales($rs->fields['bSID']);
//     $data[$i]['CertifiedId'] = $rs->fields['bAccount'];
//     $data[$i]['Category'] = $rs->fields['bCategory'];
//     $data[$i]['Application'] = $rs->fields['bApplication'];

//     $rs->MoveNext() ;
// }

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("代書申請保證號碼");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('代書申請保證號碼數量');

//寫入清單標題列資料
//代書姓名/合約份數/申請日期/負責業務/經辦

$col = 65;
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col) . $row, getScrivener($scr));
$row++;

$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col) . $row, '加盟');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '土地');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[11]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '建物');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[12]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '預售屋');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[13]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col) . $row, '直營');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '土地');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[21]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '建物');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[22]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '預售屋');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[23]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col) . $row, '非仲介成交');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '土地');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[31]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '建物');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[32]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '預售屋');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[33]);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col) . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col) . $row, '未知');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '未知');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $dataCount[0]);
$row++;

$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('代書申請保證號碼');

$col = 65;
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '版本');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '申請日期');

$row++;

for ($i = 0; $i < count($data); $i++) {
    $col     = 65;
    $version = ($data[$i]['Category'] == '') ? '未知' : $data[$i]['Category'];

    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $data[$i]['CertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $version);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['bCreateDate']);

    $row++;
}
###
$objPHPExcel->createSheet(2);
$objPHPExcel->setActiveSheetIndex(2);
$objPHPExcel->getActiveSheet()->setTitle('使用狀況');

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '申請日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '銀行');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介類型');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '版本');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '合約份數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '已使用');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '未使用');
$row++;

ksort($dataCount2);

if (is_array($dataCount2)) {
    foreach ($dataCount2 as $key => $value) {

        foreach ($value as $k => $v) {
            // $verC

            $col = 65;
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $key);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['bank']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['brand']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['app']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, getZero($v['total']));
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, getZero($v['已使用']));
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, getZero($v['未使用']));
            $row++;
        }

    }
}

function getZero($val)
{

    $val = ($val == '') ? '0' : $val;
    return $val;
}
##
$_file = iconv('UTF-8', 'BIG5', '代書合約書號碼');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;

function checkScrivenerSales($id, $sales)
{
    global $conn;

    $sql = "SELECT * FROM tScrivenerSales WHERE sScrivener = '" . $id . "' AND sSales ='" . $sales . "'";

    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true;
    } else {
        return false;
    }

}
function getScrivenerSales($id)
{
    global $conn;
    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '" . $id . "'";

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];

        $rs->MoveNext();
    }

    return @implode(',', $sales);
}

function getScrivener($id)
{
    global $conn;

    $sql = "SELECT * FROM tScrivener WHERE sId = '" . $id . "'";

    $rs = $conn->Execute($sql);

    return $rs->fields['sName'];
}

function getCategory($id)
{

    //1加盟2直營3非仲介
    if ($id == 1) {
        $cat = '加盟';
    } elseif ($id == 2) {
        $cat = '直營';
    } elseif ($id == 3) {
        $cat = '非仲介成交';
    }

    return $cat;
}

function getApplication($id)
{
    //1土地2建物3預售屋
    if ($id == 1) {
        $cat = '土地';
    } elseif ($id == 2) {
        $cat = '建物';
    } elseif ($id == 3) {
        $cat = '預售屋';
    }

    return $cat;
}
