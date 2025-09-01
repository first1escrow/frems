<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';

//上傳檔案處理
$uploaddir = __DIR__ . '/uploads/excel/';
require_once __DIR__ . '/importInvoiceuploadfile.php';

//讀取 excel 檔案
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($_file);
$currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

$i = 0;
for ($excel_line = 5; $excel_line <= $allLine; $excel_line++) {
    $check = $currentSheet->getCell("B{$excel_line}")->getValue();

    if (preg_match("/^[0-9]{3}\/[0-9]{2}\/[0-9]{2}+/", $check)) {
        $ck = 'OK';
    }

    if ($ck == 'OK') {
        $list[$i]['date'] = trim($currentSheet->getCell("B{$excel_line}")->getValue()); //日期

        $list[$i]['inv_id']   = trim($currentSheet->getCell("C{$excel_line}")->getValue()); //發票號碼
        $list[$i]['identify'] = trim($currentSheet->getCell("E{$excel_line}")->getValue()); //統編
        $list[$i]['sell_id']  = trim($currentSheet->getCell("F{$excel_line}")->getValue()); //銷貨單號
        $list[$i]['name']     = trim($currentSheet->getCell("H{$excel_line}")->getValue()); //姓名

        $list[$i]['type']        = trim($currentSheet->getCell("K{$excel_line}")->getValue()); //稅別
        $list[$i]['total_money'] = trim($currentSheet->getCell("L{$excel_line}")->getValue()); //含稅銷售額
        $list[$i]['note']        = trim($currentSheet->getCell("M{$excel_line}")->getValue()); //備註
        $list[$i]['print']       = trim($currentSheet->getCell("O{$excel_line}")->getValue()); //是否印發票

        $list[$i]['code'] = trim($currentSheet->getCell("Q{$excel_line}")->getValue()); //防偽隨機碼

        $i++;
    }

    $ck = null;unset($ck);
}

if (!is_array($list) || empty($list)) {
    die("檔案有問題");
}

##寫入資料庫
//msg:找無對應值 msg2:有寫入過 msg3:寫入失敗 msg4:資料有缺
for ($i = 0; $i < count($list); $i++) {
    $sql   = "SELECT * FROM tContractInvoiceQuery  WHERE cDefineFields ='" . $list[$i]['note'] . "' AND cObsolete = 'N'";
    $rs    = $conn->Execute($sql);
    $check = $rs->RecordCount();

    if ($check == 0) { //找無對應值
        $msg[] = $list[$i]['note'];
    } else if ($check == 1 && $rs->fields['cInvoiceNo'] != '' && $rs->fields['cInvoiceDate'] != '' && $rs->fields['cTaxType'] != '') { //有對應值&&有寫入過
        $msg2[] = $list[$i]['note'];
    } elseif ($list[$i]['inv_id'] == '' || $list[$i]['date'] == '' || $list[$i]['total_money'] == '') {
        $msg4[] = $list[$i]['note'];
    } elseif ($list[$i]['total_money'] != trim($rs->fields['cMoney'])) { //金額跟資料庫裡的不一樣
        $msg5[] = $list[$i]['note'];
    } elseif ($list[$i]['name'] != trim($rs->fields['cName']) && $list[$i]['name'] != '財團法人台灣兒童暨家庭扶助基金會') { //姓名跟資料庫裡的不一樣
        $msg6[] = $list[$i]['note'];
    } else {
        if ($list[$i]['name'] == '財團法人台灣兒童暨家庭扶助基金會') {
            $sql_up = "UPDATE
                            tContractInvoiceQuery
                        SET
                            cInvoiceNo ='" . $list[$i]['inv_id'] . "',
                            cInvoiceDate  = '" . $list[$i]['date'] . "',
                            cTaxType = '" . $list[$i]['type'] . "',
                            cMoney  = '" . $list[$i]['total_money'] . "',
                            cName ='" . $list[$i]['name'] . "',
                            cPrint = '" . $list[$i]['print'] . "',
                            cCode = '" . $list[$i]['code'] . "'
                        WHERE
                            cDefineFields ='" . $list[$i]['note'] . "';";
        } else {
            $sql_up = "UPDATE
                            tContractInvoiceQuery
                        SET
                            cInvoiceNo ='" . $list[$i]['inv_id'] . "',
                            cInvoiceDate  = '" . $list[$i]['date'] . "',
                            cTaxType = '" . $list[$i]['type'] . "',
                            cMoney  = '" . $list[$i]['total_money'] . "',
                            cPrint = '" . $list[$i]['print'] . "',
                            cCode = '" . $list[$i]['code'] . "'
                        WHERE
                            cDefineFields ='" . $list[$i]['note'] . "';";
        }

        if (!$conn->Execute($sql_up)) {
            $msg3[] = $list[$i]['note'];
        }
    }
}

##顯示有問題的
$tbl = "<div class='div-inline'><table class='tb' cellpadding='10' cellspacing='10'>";
$tbl .= "<tr><th>無對應資料(顯示備註欄位)</th></tr>";

if (is_array($msg)) {
    for ($i = 0; $i < count($msg); $i++) {
        $tbl .= "<tr><td >" . $msg[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "<tr><th>資料已重複(顯示備註欄位)</th></tr>";

if (is_array($msg2)) {
    for ($i = 0; $i < count($msg2); $i++) {
        $tbl .= "<tr><td >" . $msg2[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "<tr><th>寫入失敗備註欄位(顯示備註欄位)</th></tr>";

if (is_array($msg3)) {
    for ($i = 0; $i < count($msg3); $i++) {
        $tbl .= "<tr><td >" . $msg3[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "<tr><th>資料有缺(顯示備註欄位)</th></tr>";

if (is_array($msg4)) {
    for ($i = 0; $i < count($msg4); $i++) {
        $tbl .= "<tr><td >" . $msg4[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "<tr><th>金額不同(顯示備註欄位)</th></tr>";

if (is_array($msg5)) {
    for ($i = 0; $i < count($msg5); $i++) {
        $tbl .= "<tr><td >" . $msg5[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "<tr><th>姓名不同(顯示備註欄位)</th></tr>";

if (is_array($msg6)) {
    for ($i = 0; $i < count($msg6); $i++) {
        $tbl .= "<tr><td >" . $msg6[$i] . "</td></tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "</table><br></div>";

if (!is_array($msg4) && !is_array($msg3) && !is_array($msg2) && !is_array($msg1) && !is_array($msg5) && !is_array($msg6)) {
    $tbl = "
			<div class='div-inline2'>上傳成功</div>";
}

$msg  = empty($msg) ? '' : implode(';', $msg);
$msg2 = empty($msg2) ? '' : implode(';', $msg2);
$msg3 = empty($msg3) ? '' : implode(';', $msg3);
$msg4 = empty($msg4) ? '' : implode(';', $msg4);

write_log($msg . "," . $msg2 . "," . $msg3 . "," . $msg4 . "", 'import_income');

$smarty->assign('show', $tbl);