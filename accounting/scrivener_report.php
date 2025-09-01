<?php
ini_set('memory_limit', '256M');

include_once '../openadodb.php';
require_once '../bank/Classes/PHPExcel.php';
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php';
include_once '../includes/maintain/feedBackData.php';
include_once '../class/getAddress.php';
include_once '../class/getBank.php';
// include_once '../session_check.php';
require_once dirname(__DIR__) . '/includes/maintain/scrivenerData.php';

##查詢條件
$search  = trim($_POST['search_id']);
$sql_str = " WHERE 1=1 ";
if (!empty($search)) {
    //SC0471
    $search = (int) mb_substr(trim($search), 2);
    $sql_str .= "AND sId = " . $search . "";
}
$_POST = $search = null;
unset($_POST, $search);
##

$row = 2;
$sql = "SELECT sId,sName,sOffice,sRecall,sSpRecall,sFeedDateCat,sStatus FROM tScrivener " . $sql_str . " ORDER BY sId";
$rs  = $conn->Execute($sql);
$i   = 0;

$scrivenerData = new scrivenerData;
while (!$rs->EOF) {
    $data_feedData = FeedBackData($rs->fields['sId'], 1);

    if ($rs->fields['sFeedDateCat'] == 0) { //0季
        $rs->fields['sFeedDateCat'] = '季';
    } else {
        $rs->fields['sFeedDateCat'] = '月';
    }

    //取得地政士業務資訊
    // $sales_arr = $scrivenerData->getScrivenerSales($rs->fields['sId']);
    $sales_arr = $scrivenerData->getScrivenerSalesForPerformance($rs->fields['sId']);

    $sales = [];
    if (!empty($sales_arr)) {
        foreach ($sales_arr as $v) {
            $sales[] = $v['name'];
        }
    }

    $sales_arr = null;
    unset($sales_arr);
    ##

    if (is_array($data_feedData)) {
        foreach ($data_feedData as $key => $value) {
            $list[$i]                 = $value;
            $list[$i]['sId']          = $rs->fields['sId'];
            $list[$i]['sName']        = $rs->fields['sName'];
            $list[$i]['sOffice']      = $rs->fields['sOffice'];
            $list[$i]['sRecall']      = $rs->fields['sRecall'];
            $list[$i]['sSpRecall']    = $rs->fields['sSpRecall'];
            $list[$i]['sFeedDateCat'] = $rs->fields['sFeedDateCat'];
            $list[$i]['sStatus']      = $rs->fields['sStatus'];
            $list[$i]['sSales']       = implode('／', $sales);
            $i++;
        }

    } else {
        $list[$i]           = $rs->fields;
        $list[$i]['sSales'] = implode('／', $sales);
        $i++;
    }

    unset($data_feedData);
    $rs->MoveNext();
}

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("地政士資料");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('地政士');

##地政士    行動電話    統一編號    身分證號碼    聯絡地址    公司地址    電子郵件    總行    分行    總行代號-分行代號    指定帳號    戶名    本票票號    開票日期    本票備註

$objPHPExcel->getActiveSheet()->setCellValue('A1', '編號(地政士編號)');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '回饋方式(2:整批 3:結案)');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '姓名/抬頭');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '店長行動電話');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '身份別(2:身分證編號 3:統一編號 4:護照號碼)');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '證件號碼');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '收件人稱謂');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '聯絡地址郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '聯絡地址');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '戶藉地址郵遞區號');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '戶藉地址');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '電子郵件');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '代號(總行+分行)');
$objPHPExcel->getActiveSheet()->setCellValue('N1', '總行代號');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '分行代號');
$objPHPExcel->getActiveSheet()->setCellValue('P1', '總行名稱');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', '分行名稱');
$objPHPExcel->getActiveSheet()->setCellValue('R1', '指定帳號');
$objPHPExcel->getActiveSheet()->setCellValue('S1', '戶名');
$objPHPExcel->getActiveSheet()->setCellValue('T1', '發票總類(REC,INC)');
$objPHPExcel->getActiveSheet()->setCellValue('U1', '地政士');
$objPHPExcel->getActiveSheet()->setCellValue('V1', '事務所名稱');
$objPHPExcel->getActiveSheet()->setCellValue('W1', '回饋比率/特殊回饋比率');
$objPHPExcel->getActiveSheet()->setCellValue('X1', '二代健保');
$objPHPExcel->getActiveSheet()->setCellValue('Y1', '身份別');
$objPHPExcel->getActiveSheet()->setCellValue('Z1', '所得類別');
$objPHPExcel->getActiveSheet()->setCellValue('AA1', '停用');
$objPHPExcel->getActiveSheet()->setCellValue('AB1', '回饋日期');
$objPHPExcel->getActiveSheet()->setCellValue('AC1', '負責業務');

for ($i = 0; $i < count($list); $i++) {
# code...
    $col       = 65;
    $addr1     = '';
    $addr2     = '';
    $bank      = '';
    $bank2     = '';
    $bank3     = '';
    $bIdentity = '';
    $re        = '';

    $list[$i]['fStoreId'] = 'SC' . str_pad($list[$i]['sId'], 4, '0', STR_PAD_LEFT);

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

    $re = $list[$i]['sRecall'] . "／" . $list[$i]['sSpRecall'];

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
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['sName']); //地政士
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['sOffice']); //事務所
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $re); //回饋比率
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ''); //二代健保
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $fIdentity); //身分別
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['fIncomeCategory']); //所得類別

    if ($list[$i]['sStatus'] == 2) {
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, '停用'); //停用
    } elseif ($list[$i]['sStatus'] == 3) {
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, '重複建檔');
    } elseif ($list[$i]['sStatus'] == 4) {
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, '未簽約');
    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, ''); //停用

    }

    $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $list[$i]['sFeedDateCat']);
    $objPHPExcel->getActiveSheet()->setCellValue('AC' . $row, $list[$i]['sSales']);

    if ($list[$i]['fStop'] == 1) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AC' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AC' . $row)->getFill()->getStartColor()->setARGB('CCCCCC');
    }

    $row++;

}

###

// $_file = 'scrivener.xlsx';
$_file = 'scrivener.xls';
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save("php://output");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("php://output");

exit;

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

function bank2($conn, $bank2, $bank)
{
    $sql = 'SELECT * FROM tBank WHERE   bBank4="' . $bank2 . '" AND bBank3 ="' . $bank . '" ORDER BY bBank3 ASC;';

    $tmp = $conn->Execute($sql);

    $name = $tmp->fields['bBank4_name'];

    return $name;
}
