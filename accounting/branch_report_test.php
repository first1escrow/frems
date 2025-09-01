<?php
ini_set('memory_limit', '256M');


include_once '../openadodb.php';
require_once '../bank/Classes/PHPExcel.php';
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php';
include_once '../includes/maintain/feedBackData.php';
include_once '../class/getAddress.php';
include_once '../class/getBank.php';
include_once '../session_check.php';
require_once dirname(__DIR__) . '/includes/maintain/branchData.php';

##查詢條件
$search = trim($_POST['search_id']);

if (!empty($search)) {
    //AA00888
    //(int)mb_substr(trim($list[$i]['code']), 2)
    $search = (int) mb_substr(trim($search), 2);
    $sql_str .= "AND bId = " . $search . "";

}

##
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
$sql = 'SELECT bId,bManager,bStore,bRecall,bFeedDateCat,bStatus,bName FROM  tBranch WHERE bId!=0 and bId=1632 ' . $sql_str . '  ORDER BY bId';

$rs = $conn->Execute($sql);
$i  = 0;

$branchData = new branchData;
while (!$rs->EOF) {

    $data_feedData = FeedBackData($rs->fields['bId'], 2);

    if ($rs->fields['bFeedDateCat'] == 0) { //0季
        $rs->fields['bFeedDateCat'] = '季';
    } else {
        $rs->fields['bFeedDateCat'] = '月';
    }

    if (is_array($data_feedData)) {
        foreach ($data_feedData as $key => $value) {
            $sales_arr = $branchData->getBranchSales($rs->fields['bId']);

            $sales = [];
            if (!empty($sales_arr)) {
                foreach ($sales_arr as $v) {
                    $sales[] = $v['name'];
                }
            }

            $list[$i]                 = $value;
            $list[$i]['bId']          = $rs->fields['bId'];
            $list[$i]['bManager']     = $rs->fields['bManager'];
            $list[$i]['bStore']       = $rs->fields['bStore'];
            $list[$i]['bName']        = $rs->fields['bName'];
            $list[$i]['bRecall']      = $rs->fields['bRecall'];
            $list[$i]['bFeedDateCat'] = $rs->fields['bFeedDateCat'];
            $list[$i]['bStatus']      = $rs->fields['bStatus'];
            $list[$i]['bSales']       = implode('／', $sales);

            $sales_arr = $sales = null;
            unset($sales_arr, $sales);

            $i++;
        }

    } else {
        $sales_arr = $branchData->getBranchSales($rs->fields['bId']);

        $sales = [];
        foreach ($sales_arr as $v) {
            $sales[] = $v['name'];
        }

        $list[$i]           = $rs->fields;
        $list[$i]['bSales'] = implode('／', $sales);

        $sales_arr = $sales = null;
        unset($sales_arr, $sales);

        $i++;
    }

    unset($data_feedData);

    $rs->MoveNext();

}

for ($i = 0; $i < count($list); $i++) {
    # code...
    $col       = 65;
    $addr1     = '';
    $addr2     = '';
    $bank      = '';
    $bank2     = '';
    $bank3     = '';
    $bIdentity = '';

    $list[$i]['fStoreId'] = getRealtyNo($conn, $list[$i]['bId']);

    $addr1 = addr($conn, $list[$i]['fZipC']) . $list[$i]['fAddrC']; //聯絡地址
    $addr2 = addr($conn, $list[$i]['fZipR']) . $list[$i]['fAddrR']; //戶籍地址

    if ($list[$i]['fAccountNum'] != 0) {
        # code...
        $bank = bank($conn, $list[$i]['fAccountNum']); //總行名稱
        // $bank3 = $list[$i]['fAccountNum']."-".$list[$i]['fAccountNumB'];//銀行代號
        $bank3 = $list[$i]['fAccountNum'] . substr($list[$i]['fAccountNumB'], 0, 3);
    }

    if ($list[$i]['fAccountNumB'] != '') {
        $bank2 = bank2($conn, $list[$i]['fAccountNumB'], $list[$i]['fAccountNum']); //分行名稱

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
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fIdentityNumber']); //證件號碼
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

// ###

// $_file = 'branch.xlsx' ;
$_file = 'branch.xls';
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

//    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("php://output");

exit;
##

//取得仲介店編號
function getRealtyNo($conn, $no = 0)
{

    if ($no > 0) {
        $sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="' . $no . '";';

        $rs = $conn->Execute($sql);

        return strtoupper($rs->fields['bCode']) . str_pad($rs->fields['bId'], 5, '0', STR_PAD_LEFT);
    } else {
        return false;
    }
}
function addr($conn, $no = 0)
{
    $sql = "SELECT * FROM tZipArea WHERE zZip = '" . $no . "'";

    $tmp = $conn->Execute($sql);

    $addr = $tmp->fields['zCity'] . $tmp->fields['zArea'];

    unset($tmp);

    return $addr;
}

function bank($conn, $bank)
{
    $sql = 'SELECT * FROM tBank WHERE bBank4="" AND bBank3="' . $bank . '" ORDER BY bBank3 ASC;';

    $tmp = $conn->Execute($sql);

    $name = $tmp->fields['bBank4_name'];

    return $name;
}

function bank2($conn, $bank, $bank_main)
{
    $sql = 'SELECT * FROM tBank WHERE   bBank4="' . $bank . '" AND bBank3="' . $bank_main . '" ORDER BY bBank3 ASC;';

    $tmp = $conn->Execute($sql);

    $name = $tmp->fields['bBank4_name'];

    return $name;
}
