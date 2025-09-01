<?php
ini_set('memory_limit', '256M');

require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/session_check.php';

##查詢條件
$search = trim($_POST['search_id']);
// $search = 'AA00888';

$sql_str = '';
if (!empty($search)) {
    //
    $search = (int) substr(trim($search), 2);
    $sql_str .= "AND i.iId = " . $search . "";
}

//
$all_zips      = getZipToAddr($conn);
$all_banks     = getBank($conn);
//$all_feedbacks = getAllFeedBackData($conn);

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("個案回饋資料");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('個案回饋資料');

$col = 65;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '編號(個案編號)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋方式(2:整批 3:結案)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '姓名/抬頭');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '聯絡電話');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '身份別(2:身分證編號 3:統一編號 4:護照號碼)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '證件號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '收件人稱謂');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋報表收件郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋報表收件地址');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '戶藉地址郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '戶藉地址');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '電子郵件');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '代號(總行+分行)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '總行代號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '分行代號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '總行名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '分行名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '指定帳號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '戶名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '發票總類(REC,INC)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '店東');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋比率');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '身份別');
//$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '停用');
$objPHPExcel->getActiveSheet()->setCellValue('AA' . '1', '回饋日期');
$objPHPExcel->getActiveSheet()->setCellValue('AB' . '1', '負責業務');

// //條件
// $col=65;
$row = 2;
$sql = 'SELECT
    i.iId,
    i.iFeedBack,
    i.iTitle,
    i.iMobileNum,
    i.iIdentity,
    i.iIdentityNumber,
    i.iRtitle,
    i.iZip,
    i.iAddr,
    i.iZip2,
    i.iAddr2,
    i.iEmail,
    i.iRecall,
    i.iAccountNum,
    i.iAccountNumB,
    i.iAccount,
    i.iAccountName,
    i.iInvoiceType,
	a.bManager,
	a.bStore,
	a.bFeedDateCat,
	a.bStatus,
	a.bName,
	b.bSales,
	(SELECT pName FROM tPeopleInfo WHERE pId = b.bSales) AS sales,
    (SELECT bCode FROM tBrand WHERE bId = a.bBrand) AS bCode
FROM
    tIndividualFeedBack AS i 
    LEFT JOIN tBranch AS a ON i.iId = a.bIndividual	
    LEFT JOIN tBranchSalesForPerformance AS b ON a.bId = b.bBranch
WHERE
	1
    ' . $sql_str;

$rs = $conn->Execute($sql);
$i  = 0;

while (!$rs->EOF) {
    //$rs->fields['bFeedDateCat'] = ($rs->fields['bFeedDateCat'] == 0) ? '季' : '月';
    $list[$i]['iId']             = $rs->fields['iId'];
    $list[$i]['iFeedBack']       = $rs->fields['iFeedBack'];
    $list[$i]['iTitle']          = $rs->fields['iTitle'];
    $list[$i]['iMobileNum']      = $rs->fields['iMobileNum'];
    $list[$i]['iIdentity']       = $rs->fields['iIdentity'];
    $list[$i]['iIdentityNumber']    = $rs->fields['iIdentityNumber'];
    $list[$i]['iRtitle']         = $rs->fields['iRtitle'];
    $list[$i]['iZip']            = $rs->fields['iZip'];
    $list[$i]['iAddr']           = $rs->fields['iAddr'];
    $list[$i]['iZip2']           = $rs->fields['iZip2'];
    $list[$i]['iAddr2']          = $rs->fields['iAddr2'];
    $list[$i]['iEmail']          = $rs->fields['iEmail'];
    $list[$i]['iRecall']         = $rs->fields['iRecall'];
    $list[$i]['iAccountNum']     = $rs->fields['iAccountNum'];
    $list[$i]['iAccountNumB']    = $rs->fields['iAccountNumB'];
    $list[$i]['iAccount']        = $rs->fields['iAccount'];
    $list[$i]['iAccountName']    = $rs->fields['iAccountName'];
    $list[$i]['iInvoiceType']    = $rs->fields['iInvoiceType'];

    $i++;
    $rs->MoveNext();
}

for ($i = 0; $i < count($list); $i++) {
    $col       = 65;
    $addr1     = '';
    $addr2     = '';
    $bank      = '';
    $bank2     = '';
    $bank3     = '';
    $bIdentity = '';

    $list[$i]['iStoreId'] = 'BM' . str_pad($list[$i]['iId'], 5, '0', STR_PAD_LEFT);

    $addr1 = $all_zips[$list[$i]['iZip']] . $list[$i]['iAddr']; //聯絡地址
    $addr2 = $all_zips[$list[$i]['iZip2']] . $list[$i]['iAddr2']; //戶籍地址

    if ($list[$i]['iAccountNum'] != 0) {
        $bank  = $all_banks[$list[$i]['iAccountNum']]; //總行名稱
        $bank3 = $list[$i]['iAccountNum'] . substr($list[$i]['iAccountNumB'], 0, 3); //銀行代號
    }

    if ($list[$i]['iAccountNumB'] != '') {
        $bank2 = $all_banks[$list[$i]['iAccountNum'] . $list[$i]['iAccountNumB']]; //分行名稱
    }

    if ($list[$i]['iIdentity'] == 2) {
        $iIdentity = '身份證編號';
    } else if ($list[$i]['iIdentity'] == 3) {
        $iIdentity = '統一編號';
    } else if ($list[$i]['iIdentity'] == 4) {
        $iIdentity = '護照號碼';
    } else {
        $iIdentity = '------';
    }

    $addr1 = str_replace('桃園縣桃園市', '', $addr1);
    $addr2 = str_replace('桃園縣桃園市', '', $addr2);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iStoreId']); //仲介店編號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fFeedBack']); //回饋方式
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iTitle']); //回饋金姓名/抬頭
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['iMobileNum'], PHPExcel_Cell_DataType::TYPE_STRING); //店長行動電話
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iIdentity']); //'身份別'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iIdentityNumber']); //證件號碼
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iRtitle']); //收件人稱謂
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iZip']); //聯絡地址郵遞區號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $addr1); //'聯絡地址'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iZip2']); //'戶籍地址'郵遞區號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $addr2); //'戶籍地址'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iEmail']); //'電子郵件'

    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $bank3, PHPExcel_Cell_DataType::TYPE_STRING); //'總行代號-分行代號'
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['iAccountNum'], PHPExcel_Cell_DataType::TYPE_STRING); //總行代號
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['iAccountNumB'], PHPExcel_Cell_DataType::TYPE_STRING); //分行代號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $bank); //'總行'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $bank2); //'分行'
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['iAccount'], PHPExcel_Cell_DataType::TYPE_STRING); //'指定帳號'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iAccountName']); //'戶名'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iInvoiceType']); //'發票種類
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['bManager']); //店東
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['bStore']); //仲介店名

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['iRecall']); //回饋比率
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $iIdentity); //身分別

//    if ($list[$i]['bStatus'] == 2) {
//        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '停用'); //停用
//    } elseif ($list[$i]['bStatus'] == 3) {
//        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '暫停'); //停用
//    } else {
//        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ''); //停用
//    }

    $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $list[$i]['bFeedDateCat']); //回饋日期
    $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $list[$i]['bSales']); //負責業務

//    if ($list[$i]['fStop'] == 1) {
//        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AB' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AB' . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
//    }

    $row++;
}

$_file = 'individual.xlsx';
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
##

function getZipToAddr(&$conn)
{
    $sql = "SELECT zZip, zCity, zArea FROM tZipArea WHERE 1;";
    $rs  = $conn->Execute($sql);

    $zips = [];
    while (!$rs->EOF) {
        $zips[$rs->fields['zZip']] = $rs->fields['zCity'] . $rs->fields['zArea'];
        $rs->MoveNext();
    }

    return $zips;
}

function getBank(&$conn)
{
    $sql = 'SELECT CONCAT(bBank3, bBank4) as bank, bBank4_name FROM tBank WHERE 1;';
    $rs  = $conn->Execute($sql);

    $banks = [];
    while (!$rs->EOF) {
        $banks[$rs->fields['bank']] = $rs->fields['bBank4_name'];
        $rs->MoveNext();
    }

    return $banks;
}

function getAllFeedBackData(&$conn)
{
    $sql = "SELECT * FROM tFeedBackData WHERE fType = 2 AND fStatus = 0";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[$rs->fields['fStoreId']][] = $rs->fields;

        $rs->MoveNext();
    }

    return $data;
}