<?php
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/web_addr.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once __DIR__ . '/calTax.php';
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//二維陣列排序
function SortById($a, $b)
{
    if ($a['sId'] == $b['sId']) {
        return 0;
    }

    return ($a['sId'] > $b['sId']) ? 1 : -1;
}
##

//取得仲介備註
function getBranchMemo($lnk, $no)
{
    $txt = ''; // 初始化 $txt 變數
    if ($no) {
        $sql = "SELECT bStore FROM tBranch WHERE bId = '" . $no . "'";
        $_rs = $lnk->Execute($sql);

        $sql = "SELECT * FROM tBranchNote WHERE bStore = '" . $no . "' AND bDel = 0 AND bStatus = 0 ORDER BY bId DESC";
        $rs  = $lnk->Execute($sql);
        while (! $rs->EOF) {
            $txt .= $_rs->fields['bStore'] . "：\n" . $rs->fields['bNote'] . "\r\n\r\n";
            $rs->MoveNext();
        }
    }
    return $txt;
}
##

$tlog = new TraceLog();

//
$_account     = $_REQUEST["id"];
$web_addr     = preg_replace("/http:\/\//", "", $web_addr);
$buyer_mobile = ''; // 初始化買方手機變數
$owner_mobile = ''; // 初始化賣方手機變數

$objPHPExcel = new Spreadsheet();

$objPHPExcel->getDefaultStyle()->getFont()->setName('細明體');
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
$margin       = 1 / 2.54; //phpexcel 中是按英寸来计算的,所以这里换算了一下
$_left_margin = 1.5 / 2.54;
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($_left_margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($margin);
//
$objPHPExcel->getActiveSheet()->getColumnDimension('a')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('b')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('c')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('n')->setWidth(15);
//
$sql1  = 'SELECT cZip,cAddr,zCity,zArea FROM tContractProperty AS A , tZipArea AS B WHERE cCertifiedId="' . $_account . '" AND A.cZip=B.zZip;';
$rs1   = $conn->Execute($sql1);
$count = $rs1->RecordCount();

//
$objPHPExcel->getProperties()->setCreator("第一建經")
    ->setLastModifiedBy("第一建經")
    ->setTitle("案件控管表")
    ->setSubject("案件控管表")
    ->setDescription("案件控管表")
    ->setKeywords("案件控管表")
    ->setCategory("案件控管表");
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

$i        = 1;
$all_addr = ''; // 初始化變數
while (! $rs1->EOF) {
    $citys = $rs1->fields['zCity'];
    $areas = $rs1->fields['zArea'];
    $addr  = $rs1->fields["cAddr"];
    $addr  = preg_replace("/$citys/", "", $addr);
    $addr  = preg_replace("/$areas/", "", $addr);

    $all_addr .= $citys . $areas . $addr;

    if ($i != $count) {
        $all_addr .= ",";
    }
    $i++;
    $rs1->MoveNext();
}
unset($count);

$sql = '
SELECT
    a.cEscrowBankAccount,
    a.cBank,
    a.cCertifiedId,
    a.cSignDate,
    b.cName,
    b.cName1,
    b.cName2,
    b.cBranchNum,
    b.cBranchNum1,
    b.cBranchNum2,
    (SELECT bName FROM tBrand WHERE bId = b.cBrand) AS BrandName,
    (SELECT bName FROM tBrand WHERE bId = b.cBrand1) AS BrandName1,
    (SELECT bName FROM tBrand WHERE bId = b.cBrand2) AS BrandName2,
    b.cTelArea,
    b.cTelArea1,
    b.cTelArea2,
    b.cTelMain,
    b.cTelMain1,
    b.cTelMain2,
    b.cFaxArea,
    b.cFaxArea1,
    b.cFaxArea2,
    b.cFaxMain,
    b.cFaxMain1,
    b.cFaxMain2,
    a.cCaseMoney,
    c.cName as seller,
    c.cMobileNum as o_mobile,
    c.cIdentifyId as o_ID,
    c.cMoney1 as o_loan,
    c.cBankKey as o_bank,
    c.cBankBranch as o_cBankBranch,
    c.cBankAccName as o_cBankAccName,
    c.cBankAccNumber as o_cBankAccNumber,
    c.cMoney2 as cMoney2,
    d.cName Buyer,
    d.cIdentifyId as b_ID,
    d.cMobileNum as b_mobile,
    d.cBankBranch as b_cBankBranch,
    d.cBankAccName as b_cBankAccName,
    d.cBankAccNumber as b_cBankAccNumber
FROM
    tContractCase AS a
JOIN
    tContractRealestate AS b ON a.cCertifiedId=b.cCertifyId
JOIN
    tContractOwner AS c ON a.cCertifiedId=c.cCertifiedId
JOIN
    tContractBuyer AS d ON a.cCertifiedId=d.cCertifiedId
WHERE
    a.cCertifiedId="' . $_account . '"
;';
$rs = $conn->Execute($sql);
//
$_o_bank_num = isset($rs->fields["o_bank"]) ? $rs->fields["o_bank"] : '';
$_b_bank_num = isset($rs->fields["b_bank"]) ? $rs->fields["b_bank"] : '';
//
$sql_o        = "select * from tCategoryBank where cId='$_o_bank_num'";
$rs_o         = $conn->Execute($sql_o);
$_o_bank_name = ($_o_bank_num && $rs_o && ! $rs_o->EOF) ? $rs_o->fields["cBankName"] . (isset($rs->fields["o_cBankBranch"]) ? $rs->fields["o_cBankBranch"] : '') : '';

$sql_b        = "select * from tCategoryBank where cId='$_b_bank_num'";
$rs_b         = $conn->Execute($sql_b);
$_b_bank_name = ($_b_bank_num && $rs_b && ! $rs_b->EOF) ? $rs_b->fields["cBankName"] . (isset($rs->fields["b_cBankBranch"]) ? $rs->fields["b_cBankBranch"] : '') : '';
//
$sql2 = "select * from tContractIncome where cCertifiedId='$_account'";
$rs2  = $conn->Execute($sql2);
//
$sql3 = '
SELECT
    sName,
    a.cAssistant,
    b.sTelArea,
    b.sTelMain,
    b.sFaxArea,
    b.sFaxMain,
    b.sMobileNum,
    b.sRemark1
FROM
    tContractScrivener AS a,
    tScrivener AS b
WHERE
    a.cScrivener=b.sId
    AND a.cCertifiedId="' . $_account . '"
; ';
$rs3      = $conn->Execute($sql3);
$scr_memo = $rs3->fields['sRemark1'];

$sql      = "SELECT cBankName,cBranchName FROM tContractBank WHERE cBankCode = '" . $rs->fields['cBank'] . "'";
$rsBank   = $conn->Execute($sql);
$bankName = $rsBank->fields['cBankName'] . $rsBank->fields['cBranchName'];

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', ' □ 標地座落：' . $all_addr);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->mergeCells('K1:R1');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '(' . $bankName . ')專屬帳號：' . substr($rs->fields['cEscrowBankAccount'], 0, 5) . '-' . substr($rs->fields['cEscrowBankAccount'], -9));
$objPHPExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);

//
$objPHPExcel->getActiveSheet()->mergeCells('E2:H2')->setCellValue('E2', "簽約日：" . substr($rs->fields["cSignDate"], 0, 10));
$objPHPExcel->getActiveSheet()->mergeCells('I2:K2')->setCellValue('I2', "進案日：");
$objPHPExcel->getActiveSheet()->mergeCells('O2:R2')->setCellValue('O2', "結案日：");
//
$brandName        = $rs->fields['BrandName'];
$storeName        = $rs->fields["cName"];
$storeTel         = $rs->fields["cTelArea"] . "-" . $rs->fields["cTelMain"];
$storeFax         = $rs->fields["cFaxArea"] . "-" . $rs->fields["cFaxMain"];
$tmpB             = getBranch($rs->fields["cBranchNum"]);
$storeBranch      = $tmpB['bStore'];
$bServiceOrderHas = ($tmpB['bServiceOrderHas'] == '1') ? '有' : '無';
unset($tmpB);

if ($rs->fields["cBranchNum1"] > 0) {
    $brandName .= '/' . $rs->fields['BrandName1'];
    $storeName .= '/' . $rs->fields['cName1'];
    $storeTel .= '/' . $rs->fields["cTelArea1"] . "-" . $rs->fields["cTelMain1"];
    $storeFax .= '/' . $rs->fields["cFaxArea1"] . "-" . $rs->fields["cFaxMain1"];
    $tmpB = getBranch($rs->fields["cBranchNum1"]);
    $storeBranch .= '/' . $tmpB['bStore'];
    $bServiceOrderHas .= ($tmpB['bServiceOrderHas'] == '1') ? '/有' : '/無';
    unset($tmpB);

}

if ($rs->fields["cBranchNum2"] > 0) {
    $brandName .= '/' . $rs->fields['BrandName2'];
    $storeName .= '/' . $rs->fields['cName2'];
    $storeTel .= '/' . $rs->fields["cTelArea2"] . "-" . $rs->fields["cTelMain2"];
    $storeFax .= '/' . $rs->fields["cFaxArea2"] . "-" . $rs->fields["cFaxMain2"];
    $tmpB = getBranch($rs->fields["cBranchNum2"]);
    $storeBranch .= '/' . $tmpB['bStore'];
    $bServiceOrderHas .= ($tmpB['bServiceOrderHas'] == '1') ? '/有' : '/無';
    unset($tmpB);
}

function getBranch($bId)
{
    global $conn;
    $sql = "SELECT bStore,bServiceOrderHas FROM tBranch WHERE bId = '" . $bId . "'";

    $rs = $conn->Execute($sql);

    return $rs->fields;
}

$objPHPExcel->getActiveSheet()->mergeCells('A3:C3')->setCellValue('A3', "仲介公司(品牌:" . $brandName . ")");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '審核');
$objPHPExcel->getActiveSheet()->mergeCells('E3:G3')->setCellValue('E3', "案件資訊");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', '審核');
$objPHPExcel->getActiveSheet()->mergeCells('I3:K3')->setCellValue('I3', "買方");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L3', '審核');
$objPHPExcel->getActiveSheet()->mergeCells('M3:N3')->setCellValue('M3', "賣方");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O3', '審核');
$objPHPExcel->getActiveSheet()->mergeCells('P3:R3')->setCellValue('P3', "地政士");

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A4', '店名')
    ->setCellValue('E4', '前順位')
    ->setCellValue('I4', '姓名')
    ->setCellValue('P4', '姓名');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A5', '公司')
    ->setCellValue('E5', '增值稅')
    ->setCellValue('I5', '身分證號')
    ->setCellValue('P5', '經辦代書');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A6', '服務費先行撥付同意書')
    ->setCellValue('E6', '保證費')
    ->setCellValue('I6', '電話')
    ->setCellValue('P6', '經辦助理');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A7', '電話')
    ->setCellValue('E7', '預估餘額')
    ->setCellValue('I7', '')
    ->setCellValue('P7', '電話');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A8', '傳真')
    ->setCellValue('E8', '限制登記')
    ->setCellValue('I8', '')
    ->setCellValue('P8', '傳真');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A9', '仲介費(賣方)')
    ->setCellValue('E9', '私人設定')
    ->setCellValue('I9', '')
    ->setCellValue('P9', '代書手機');
//
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A10', '仲介費(買方)')
    ->setCellValue('E10', '解約條款')
    ->setCellValue('I10', '')
    ->setCellValue('P10', '助理手機');
//
// 使用更明確的邊框樣式定義
$borderStyle = [
    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    'color'       => ['rgb' => '000000'],
];

$styleArray = [
    'borders' => [
        'allBorders' => $borderStyle,
    ],
];
$_H = 23;
$objPHPExcel->getActiveSheet()->getStyle('A3:R3')->applyFromArray($styleArray);
//
$sql7 = "select * from tContractExpenditure where cCertifiedId='$_account'";
$rs7  = $conn->Execute($sql7);

//
$i = 4;

$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $storeName);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, $rs->fields["o_loan"]);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, $rs->fields["Buyer"]);
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, $rs->fields["seller"]);
$objPHPExcel->getActiveSheet()->mergeCells('Q' . $i . ':R' . $i)->setCellValue('Q' . $i, $rs3->fields["sName"]);
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
//

$i = 5;
$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $storeBranch);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, $rs2->fields["cAddedTaxMoney"]);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, $rs->fields["b_ID"]);
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, $rs->fields["o_ID"]);
$objPHPExcel->getActiveSheet()->mergeCells('Q' . $i . ':R' . $i)->setCellValue('Q' . $i, $rs3->fields["cAssistant"]);
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);

//
$i = 6;

$sql = "SELECT * FROM  tContractPhone WHERE cCertifiedId= '" . $_account . "' AND cIdentity IN(1,2)";
$tmp = $conn->Execute($sql);

while (! $tmp->EOF) {
    if ($tmp->fields['cIdentity'] == 1) {
        $buyer_mobile .= "," . $tmp->fields['cMobileNum'];
    } else if ($tmp->fields['cIdentity'] == 2) {
        $owner_mobile .= "," . $tmp->fields['cMobileNum'];
    }

    $tmp->MoveNext();
}

$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $bServiceOrderHas);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, $rs2->fields["cCertifiedMoney"]);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, "'" . $rs->fields["b_mobile"] . $buyer_mobile);
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, "'" . $rs->fields["o_mobile"] . $owner_mobile);
$objPHPExcel->getActiveSheet()->mergeCells('Q' . $i . ':R' . $i)->setCellValue('Q' . $i, "");
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);

                                                                                                                   //其他買賣方
$sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $_account . '" AND cIdentity="1" ORDER BY cId ASC;'; //買方
$rsb = $conn->Execute($sql);

$otherbuyer = []; // 初始化買方陣列
while (! $rsb->EOF) {
    $otherbuyer[] = $rsb->fields;
    $rsb->MoveNext();
}
unset($rsb);

$sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $_account . '" AND cIdentity="2" ORDER BY cId ASC;'; //賣方
$rso = $conn->Execute($sql);

$otherowner = []; // 初始化賣方陣列
while (! $rso->EOF) {
    $otherowner[] = $rso->fields;
    $rso->MoveNext();
}
unset($rso);
##

$i = 7;
$x = 0;
$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $storeTel);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, "=E21-B9-F4-F5-F6");
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, "'" . (isset($otherbuyer[$x]['cMobileNum']) ? $otherbuyer[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, "'" . (isset($otherowner[$x]['cMobileNum']) ? $otherowner[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('Q' . $i . ':R' . $i)->setCellValue('Q' . $i, $rs3->fields["sTelArea"] . "-" . $rs3->fields["sTelMain"]);
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$x++;
//
$i = 8;
$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $storeFax);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, "□有");
$objPHPExcel->getActiveSheet()->getStyle('F' . $i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, "'" . (isset($otherbuyer[$x]['cMobileNum']) ? $otherbuyer[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, "'" . (isset($otherowner[$x]['cMobileNum']) ? $otherowner[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('Q' . $i . ':R' . $i)->setCellValue('Q' . $i, $rs3->fields["sFaxArea"] . "-" . $rs3->fields["sFaxMain"]);
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$x++;
//

//助理代號
$sql = 'SELECT id FROM tTitle_SMS WHERE tTitle="助理" ORDER BY id ASC;';
$r_s = $conn->Execute($sql);
$arr = [];
while (! $r_s->EOF) {
    $arr[] = $r_s->fields['id'];
    $r_s->MoveNext();
}
$ass_id = implode('","', $arr);
unset($arr);
##

//地政士代號
$sql = 'SELECT id FROM tTitle_SMS WHERE tTitle="地政士" ORDER BY id ASC;';
$r_s = $conn->Execute($sql);
$arr = [];
while (! $r_s->EOF) {
    $arr[] = $r_s->fields['id'];
    $r_s->MoveNext();
}
$scr_id = implode('","', $arr);
unset($arr);
##

//透過 tContractScrivener 取得經辦代書與助理姓名與電話
$smsTarget = '';
$sql       = 'SELECT * FROM tContractScrivener WHERE cCertifiedId="' . $_account . '";';
$r_s       = $conn->Execute($sql);
$scrId     = $r_s->fields['cScrivener'];

$arr = [];
$arr = $r_s->fields;

//找經辦代書
$sql                     = "SELECT * FROM tScrivenerSms WHERE sId ='" . $arr['cManage'] . "'";
$_rs                     = $conn->Execute($sql);
$scrivener[0]['sName']   = $_rs->fields['sName'];
$scrivener[0]['sMobile'] = $_rs->fields['sMobile'];

//找經辦助裡
$sql                     = "SELECT * FROM tScrivenerSms WHERE sId ='" . $arr['cManage2'] . "'";
$_rs                     = $conn->Execute($sql);
$assistant[0]['sName']   = $_rs->fields['sName'];
$assistant[0]['sMobile'] = $_rs->fields['sMobile'];
unset($arr);

$objPHPExcel->getActiveSheet()->mergeCells('Q5:R5')->setCellValue('Q5', $scrivener[0]['sName']);
$objPHPExcel->getActiveSheet()->mergeCells('Q9:R9')->setCellValue('Q9', "'" . $scrivener[0]['sMobile']);

$objPHPExcel->getActiveSheet()->mergeCells('Q6:R6')->setCellValue('Q6', $assistant[0]['sName']);
$objPHPExcel->getActiveSheet()->mergeCells('Q10:R10')->setCellValue('Q10', "'" . $assistant[0]['sMobile']);
##

$i = 9;
$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, $rs7->fields["cRealestateMoney"]);
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, "□有");
$objPHPExcel->getActiveSheet()->getStyle('F' . $i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, "'" . (isset($otherbuyer[$x]['cMobileNum']) ? $otherbuyer[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, "'" . (isset($otherowner[$x]['cMobileNum']) ? $otherowner[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$x++;
//
$i = 10;
$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i)->setCellValue('B' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, "□有");
$objPHPExcel->getActiveSheet()->getStyle('F' . $i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J' . $i . ':K' . $i)->setCellValue('J' . $i, "'" . (isset($otherbuyer[$x]['cMobileNum']) ? $otherbuyer[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->mergeCells('M' . $i . ':N' . $i)->setCellValue('M' . $i, "'" . (isset($otherowner[$x]['cMobileNum']) ? $otherowner[$x]['cMobileNum'] : ''));
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':R' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$x++;
//
//
$objPHPExcel->getActiveSheet()->mergeCells('A13:C13')->setCellValue('A13', "文件");
$objPHPExcel->getActiveSheet()->mergeCells('D13:I13')->setCellValue('D13', "入帳");
$objPHPExcel->getActiveSheet()->mergeCells('J13:R13')->setCellValue('J13', "出帳");
//
$objPHPExcel->getActiveSheet()->setCellValue('A14', "名稱");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B14', '收件');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C14', '缺件');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14', '科目');
$objPHPExcel->getActiveSheet()->mergeCells('E14:F14')->setCellValue('F14', "約定價款");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G14', '日期');
$objPHPExcel->getActiveSheet()->mergeCells('H14:I14')->setCellValue('H14', "金額");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J14', '科目');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K14', '日期');
$objPHPExcel->getActiveSheet()->mergeCells('L14:M14')->setCellValue('L14', "金額");
$objPHPExcel->getActiveSheet()->mergeCells('N14:R14')->setCellValue('N14', "注意事項");
//
$sql5 = "select eTradeDate,eLender,eStatusRemark,sName from tExpense as A , tCategoryIncome as B where A.eStatusRemark=B.sId and A.eDepAccount='0060001" . $_account . "' order by sId asc";
$rs5  = $conn->Execute($sql5);

// 初始化變數
$datas      = [];
$datas_date = [];

while (! $rs5->EOF) {
    $_y         = substr($rs5->fields["eTradeDate"], 0, 3) + 1911;
    $_m         = substr($rs5->fields["eTradeDate"], 3, 2);
    $_d         = substr($rs5->fields["eTradeDate"], 5, 2);
    $_full_date = $_y . "/" . $_m . "/" . $_d;

    // 確保陣列索引存在
    if (! isset($datas[$rs5->fields["eStatusRemark"]])) {
        $datas[$rs5->fields["eStatusRemark"]] = 0;
    }

    $datas[$rs5->fields["eStatusRemark"]]      = $datas[$rs5->fields["eStatusRemark"]] + (int) substr($rs5->fields["eLender"], 0, -2);
    $datas_date[$rs5->fields["eStatusRemark"]] = $_full_date;
    $rs5->MoveNext();
}
//
$sql6 = "select * from tBankTrans WHERE tMemo='$_account'";
$rs6  = $conn->Execute($sql6);

// 初始化變數
$data6       = [];
$datas6      = [];
$datas_date6 = [];

while (! $rs6->EOF) {
    $_y         = substr($rs6->fields["tExport_time"], 0, 4);
    $_m         = substr($rs6->fields["tExport_time"], 5, 2);
    $_d         = substr($rs6->fields["tExport_time"], 8, 2);
    $_full_date = $_y . "/" . $_m . "/" . $_d;

    // 確保陣列索引存在
    if (! isset($data6[$rs6->fields["tObjKind"]])) {
        $data6[$rs6->fields["tObjKind"]] = 0;
    }

    $datas6[$rs6->fields["tObjKind"]]      = $data6[$rs6->fields["tObjKind"]] + (int) $rs6->fields["tMoney"];
    $datas_date6[$rs6->fields["tObjKind"]] = $_full_date;
    $rs6->MoveNext();
}
//

//
$i = 15;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "申請書");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '簽約款');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, $rs2->fields["cSignMoney"]);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, isset($datas_date["1"]) ? $datas_date["1"] : '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, isset($datas["1"]) ? $datas["1"] : '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '賣方頭款');

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, "□已附動撥"); //無查封、私設：照會買方：過戶前代償(同意書)
$objPHPExcel->getActiveSheet()->getStyle('N' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

//
$i = 16;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "契約書");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '用印款');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, $rs2->fields["cAffixMoney"]);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, isset($datas_date["2"]) ? $datas_date["2"] : '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, isset($datas["2"]) ? $datas["2"] : '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '仲介費');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, "□已附服務費"); //服務費申辦單(含證明單據);撥後餘額為正
$objPHPExcel->getActiveSheet()->getStyle('N' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//
$i = 17;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "身份證");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '完稅款');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, $rs2->fields["cDutyMoney"]);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, isset($datas_date["3"]) ? $datas_date["3"] : '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, isset($datas["3"]) ? $datas["3"] : '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '扣繳稅款');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$txt = '增值稅               房屋稅              □已附稅單';
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, $txt); //應繳價款無誤、尾款本票：照會買方銀行無誤

//
$i = 18;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "授權書");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$txt = '契稅                 印花稅';
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, $txt);
//
$i = 19;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "謄本");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '尾款');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, $rs2->fields["cEstimatedMoney"]);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, isset($datas_date["4"]) ? $datas_date["4"] : '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, isset($datas["4"]) ? $datas["4"] : '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$txt = '地價稅               預規';
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, $txt);
//
$i = 20;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '償還餘額');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '代償');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, " □已附代償"); //照會銀行(買、賣)無誤;私人共同代償者已匯款
$objPHPExcel->getActiveSheet()->getStyle('N' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//
$i = 21;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "");
$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, '');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, '總價款');
$objPHPExcel->getActiveSheet()->mergeCells('E' . $i . ':F' . $i)->setCellValue('E' . $i, $rs2->fields["cTotalMoney"]);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, "");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $i, '其他');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('L' . $i . ':M' . $i)->setCellValue('L' . $i, "");
$objPHPExcel->getActiveSheet()->mergeCells('N' . $i . ':R' . $i)->setCellValue('N' . $i, "");
//
$objPHPExcel->getActiveSheet(0)->getStyle('A13:R21')->applyFromArray($styleArray);
//
$i = 22;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
//
$i = 23;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':M' . $i)->setCellValue('A' . $i, "個案備註");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, " □已附點交謄本");
$objPHPExcel->getActiveSheet()->getStyle('O' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 24;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->getActiveSheet()->mergeCells('A24:M29')->setCellValue('A' . $i, "");
$objPHPExcel->getActiveSheet()->getStyle('A24:M29')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->getStyle('A24:M29')->getAlignment()->setWrapText(true);

$memos = '';
$sql   = "SELECT cRemark FROM tContractInvoice WHERE cCertifiedId='" . $_account . "'";
$tmp2  = $conn->Execute($sql);
$memos .= $tmp2->fields['cRemark'] . "\r\n";
unset($tmp2);

$memos .= getBranchMemo($conn, $rs->fields['cBranchNum']);
$memos .= getBranchMemo($conn, $rs->fields['cBranchNum1']);
$memos .= getBranchMemo($conn, $rs->fields['cBranchNum2']);
if ($scr_memo) {
    $memos .= '代書：' . $scr_memo . "\r\n";
}

if ($memos) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $memos);
}

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '仲介費(賣)');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "");
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 25;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '仲介費(買)');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "");
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 26;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "");
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 27;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '保證費');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "　　　　　　　　　利息");
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 28;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '代書費');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "");
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
//
$i = 29;
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($_H);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $i, '賣方餘額');
$objPHPExcel->getActiveSheet()->mergeCells('O' . $i . ':R' . $i)->setCellValue('O' . $i, "");
//
$objPHPExcel->getActiveSheet(0)->getStyle('A23:R29')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);

$objPHPExcel->setActiveSheetIndex(0);

// 重新應用所有的範圍邊框樣式
$allCellsRange = 'A3:R' . $i;
$objPHPExcel->getActiveSheet()->getStyle($allCellsRange)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color'       => ['rgb' => '000000'],
        ],
    ],
]);

// 移除第 11、12、22 行的框線
$noBorderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
        ],
    ],
];
$objPHPExcel->getActiveSheet()->getStyle('A11:R11')->applyFromArray($noBorderStyle);
$objPHPExcel->getActiveSheet()->getStyle('A12:R12')->applyFromArray($noBorderStyle);
$objPHPExcel->getActiveSheet()->getStyle('A22:R22')->applyFromArray($noBorderStyle);

// 強制應用所有的樣式
$objPHPExcel->getActiveSheet()->calculateColumnWidths();

// 記錄日誌
$filename = $_account . '_control_report.xlsx';
$tlog->exportWrite($_SESSION['member_id'], $filename, '管控表下載');

// 清理任何輸出緩衝區
if (ob_get_length()) {
    ob_end_clean();
}

// 設置標頭
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 創建 Excel 寫入器
$objWriter = new Xlsx($objPHPExcel);
$objWriter->setPreCalculateFormulas(true);

// 啟用嚴格模式以確保所有樣式被正確應用
$objWriter->setOffice2003Compatibility(true);
$objWriter->setIncludeCharts(true);

// 直接輸出到 PHP 輸出流
$objWriter->save('php://output');
exit;
