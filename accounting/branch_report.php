<?php
ini_set('memory_limit', '512M');

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
    $sql_str .= "AND a.bId = " . $search . "";
}

//
$all_zips      = getZipToAddr($conn);
$all_banks     = getBank($conn);
$all_feedbacks = getAllFeedBackData($conn);

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("仲介店資料");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('仲介店資料');

$col = 65;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '編號(仲介編號)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋方式(2:整批 3:結案)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '姓名/抬頭');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '店長行動電話');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '身份別(2:身分證編號 3:統一編號 4:護照號碼)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '證件號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '收件人稱謂');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '聯絡地址郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '聯絡地址');
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
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '公司名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '回饋比率');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '身份別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . '1', '停用');
$objPHPExcel->getActiveSheet()->setCellValue('AA' . '1', '回饋日期');
$objPHPExcel->getActiveSheet()->setCellValue('AB' . '1', '負責業務');

// //條件
// $col=65;
$row = 2;
$sql = 'SELECT
	a.bId,
	a.bManager,
	a.bStore,
	a.bRecall,
	a.bFeedDateCat,
	a.bStatus,
	a.bName,
	b.bSales,
	(SELECT pName FROM tPeopleInfo WHERE pId = b.bSales) AS sales,
    (SELECT bCode FROM tBrand WHERE bId = a.bBrand) AS bCode
FROM
	tBranch AS a
JOIN
	tBranchSalesForPerformance AS b ON a.bId = b.bBranch
WHERE
	a.bId <> 0
	AND a.bId <> 1372
    ' . $sql_str;
$rs = $conn->Execute($sql);
$i  = 0;

while (!$rs->EOF) {
    $data_feedData = $all_feedbacks[$rs->fields['bId']];

    $rs->fields['bFeedDateCat'] = ($rs->fields['bFeedDateCat'] == 0) ? '季' : '月';

    if (is_array($data_feedData)) {
        foreach ($data_feedData as $key => $value) {
            $list[$i] = $value;

            $list[$i]['bId']          = $rs->fields['bId'];
            $list[$i]['bManager']     = $rs->fields['bManager'];
            $list[$i]['bStore']       = $rs->fields['bStore'];
            $list[$i]['bName']        = $rs->fields['bName'];
            $list[$i]['bRecall']      = $rs->fields['bRecall'];
            $list[$i]['bFeedDateCat'] = $rs->fields['bFeedDateCat'];
            $list[$i]['bStatus']      = $rs->fields['bStatus'];
            $list[$i]['bSales']       = $rs->fields['sales'];
            $list[$i]['bCode']        = $rs->fields['bCode'];

            $sales_arr = $sales = null;
            unset($sales_arr, $sales);

            $i++;
        }
    } else {
        $list[$i]           = $rs->fields;
        $list[$i]['bSales'] = $rs->fields['sales'];

        $i++;
    }

    $data_feedData = null;unset($data_feedData);

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

    $list[$i]['fStoreId'] = $list[$i]['bCode'] . str_pad($list[$i]['bId'], 5, '0', STR_PAD_LEFT);

    $addr1 = $all_zips[$list[$i]['fZipC']] . $list[$i]['fAddrC']; //聯絡地址
    $addr2 = $all_zips[$list[$i]['fZipR']] . $list[$i]['fAddrR']; //戶籍地址

    if ($list[$i]['fAccountNum'] != 0) {
        $bank  = $all_banks[$list[$i]['fAccountNum']]; //總行名稱
        $bank3 = $list[$i]['fAccountNum'] . substr($list[$i]['fAccountNumB'], 0, 3); //銀行代號
    }

    if ($list[$i]['fAccountNumB'] != '') {
        $bank2 = $all_banks[$list[$i]['fAccountNum'] . $list[$i]['fAccountNumB']]; //分行名稱
    }

    if ($list[$i]['fIdentity'] == 2) {
        $fIdentity = '身份證編號';
    } else if ($list[$i]['fIdentity'] == 3) {
        $fIdentity = '統一編號';
    } else if ($list[$i]['fIdentity'] == 4) {
        $fIdentity = '護照號碼';
    } else {
        $fIdentity = '------';
    }

    $addr1 = str_replace('桃園縣桃園市', '', $addr1);
    $addr2 = str_replace('桃園縣桃園市', '', $addr2);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fStoreId']); //仲介店編號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fFeedBack']); //回饋方式
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fTitle']); //回饋金姓名/抬頭
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['fMobileNum'], PHPExcel_Cell_DataType::TYPE_STRING); //店長行動電話
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fIdentity']); //'身份別'
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['fIdentityNumber'], PHPExcel_Cell_DataType::TYPE_STRING); //證件號碼
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fRtitle']); //收件人稱謂
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fZipC']); //聯絡地址郵遞區號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $addr1); //'聯絡地址'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fZipR']); //'戶籍地址'郵遞區號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $addr2); //'戶籍地址'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fEmail']); //'電子郵件'

    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $bank3, PHPExcel_Cell_DataType::TYPE_STRING); //'總行代號-分行代號'
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['fAccountNum'], PHPExcel_Cell_DataType::TYPE_STRING); //總行代號
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['fAccountNumB'], PHPExcel_Cell_DataType::TYPE_STRING); //分行代號
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $bank); //'總行'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $bank2); //'分行'
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['fAccount'], PHPExcel_Cell_DataType::TYPE_STRING); //'指定帳號'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fAccountName']); //'戶名'
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fNote']); //'發票種類
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['bManager']); //店東
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['bStore']); //仲介店名
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['bName']); //公司名稱

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['bRecall']); //回饋比率
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $fIdentity); //身分別

    if ($list[$i]['bStatus'] == 2) {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '停用'); //停用
    } elseif ($list[$i]['bStatus'] == 3) {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '暫停'); //停用
    } else {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ''); //停用
    }

    $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $list[$i]['bFeedDateCat']); //回饋日期
    $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $list[$i]['bSales']); //負責業務

    if ($list[$i]['fStop'] == 1) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AB' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AB' . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
    }

    $row++;
}

$_file = 'branch.xlsx';
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