<?php
// ini_set("display_errors", "On");
// error_reporting(E_ALL & ~E_NOTICE);
//設定行高
$cell_y1  = 4.5; // 內容用
$cell_y2  = 5; // 標題用
$cell_y3  = 1; // 手動跳行調行距用
$cell_y4  = 5; // 內容用
$cell_y5  = 8; // 銀行框框加大
$cell_y6  = 4; // 注意事項用
$cell_gap = 2; // 單元分隔用
$line_gap = 0.4; // 雙線條畫線用
##

//設定線條為實、虛線
class PDF1 extends PDF_Unicode
{
    public function SetDash($black = false, $white = false)
    {
        if ($black and $white) {
            $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }

        $this->_out($s);
    }
}
################
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/includes/company.json'), true);
##

// 取得買賣方資料
$sql    = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $cCertifiedId . '";';
$rs     = $conn->Execute($sql);
$detail = $rs->fields;
##

// 賣方收支明細(收入部分)##日期為空的要排最後面
$sql       = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oIncome<>"0" AND oDate!="" ORDER BY oDate,oId,oKind ASC; ;';
$rs        = $conn->Execute($sql);
$max_owner = $rs->RecordCount();
while (!$rs->EOF) {
    # code...
    $trans_owner[] = $rs->fields;
    $rs->MoveNext();
}

$sql        = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oIncome<>"0" AND oDate="" ORDER BY oDate,oId,oKind ASC;';
$rs         = $conn->Execute($sql);
$owner_max2 = $rs->RecordCount();
while (!$rs->EOF) {
    # code...
    $trans_owner[$max_owner++] = $rs->fields;
    $rs->MoveNext();
}
##

// 賣方收支明細(支出)
$sql         = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oExpense<>"0" ORDER BY oDate ASC; ;';
$rs          = $conn->Execute($sql);
$max_owner_e = $rs->RecordCount();
while (!$rs->EOF) {
    $trans_owner_e[] = $rs->fields;

    $rs->MoveNext();
}
##

//讀取買方交易明細(收入部分)##日期為空的要排最後面
$sql       = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bIncome<>"0" AND bDate!="" ORDER BY bDate,bId,bKind ASC;';
$rs        = $conn->Execute($sql);
$buyer_max = $rs->RecordCount();
while (!$rs->EOF) {
    $buyer_income[] = $rs->fields;
    $rs->MoveNext();
}

$sql        = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bIncome<>"0" AND bDate="" ORDER BY bDate,bId,bKind ASC;';
$rs         = $conn->Execute($sql);
$buyer_max2 = $rs->RecordCount();
while (!$rs->EOF) {

    $buyer_income[$buyer_max++] = $rs->fields;

    $rs->MoveNext();
}
##

//讀取買方交易明細(支出部分)
$sql         = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bExpense<>"0" ORDER BY bDate ASC;';
$rs          = $conn->Execute($sql);
$buyer_max_e = $rs->RecordCount();
while (!$rs->EOF) {

    $buyer_expense[] = $rs->fields;
    $rs->MoveNext();
}
##

// 讀取經辦人員資料
$sql = '
	SELECT
		peo.pFaxNum as FaxNum,
		peo.pId as pId,
		peo.pExt as Ext
	FROM
		tBankCode AS bkc
	JOIN
		tScrivener AS scr ON scr.sId=bkc.bSID
	JOIN
		tPeopleInfo AS peo ON scr.sUndertaker1=peo.pId
	WHERE
		bkc.bAccount LIKE "%' . $cCertifiedId . '"
';
$rs         = $conn->Execute($sql);
$undertaker = $rs->fields;
if ($undertaker['FaxNum']) {
    $temp                 = $undertaker['FaxNum'];
    $undertaker['FaxNum'] = substr($temp, 0, 7) . '-' . substr($temp, 7);
    unset($temp);
}
##

//確認簽約日期
$cSignDate = '';
$sql       = "SELECT cSignDate FROM tContractCase WHERE cCertifiedId='" . $cCertifiedId . "';";
$rs        = $conn->Execute($sql);

$cSignDate = $rs->fields['cSignDate'];
##

//賣方結清撥付款項明細-其他
$sql       = "SELECT * FROM tChecklistOther WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity = 2";
$rs        = $conn->Execute($sql);
$tax_owner = array();
while (!$rs->EOF) {
    $tax_owner[] = $rs->fields;

    $rs->MoveNext();
}
##
//買方結清撥付款項明細-其他
$sql = "SELECT * FROM tChecklistOther WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity = 1";

$rs        = $conn->Execute($sql);
$tax_buyer = array();
while (!$rs->EOF) {

    $tax_buyer[] = $rs->fields;

    $rs->MoveNext();
}
##
//結清撥付款項明細-其他-2
$sql = "SELECT * FROM tChecklistRemark WHERE cCertifiedId='" . $cCertifiedId . "' ORDER BY cId ASC";

$rs           = $conn->Execute($sql);
$remark_buy   = array();
$remark_owner = array();
while (!$rs->EOF) {

    if ($rs->fields['cIdentity'] == 1) {

        $remark_buy[] = $rs->fields;

    } elseif ($rs->fields['cIdentity'] == 2) {
        $remark_owner[] = $rs->fields;
    }

    $rs->MoveNext();
}

##
//建物

$sql          = "SELECT cAddr,(SELECT zCity FROM tZipArea WHERE zZip = cZip) AS city , (SELECT zArea FROM tZipArea WHERE zZip = cZip) AS area FROM tContractProperty WHERE cCertifiedId ='" . $cCertifiedId . "' ORDER BY cItem";
$rs           = $conn->Execute($sql);
$property_max = $rs->RecordCount();
while (!$rs->EOF) {
    $property[] = $rs->fields;

    $rs->MoveNext();
}
##
//確認賣方人數
$sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='2'";
$res = $conn->Execute($sql);
$owner = $res->recordCount();
##
//確認買方人數
$sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='1'";
$res  = $conn->Execute($sql);
$buyer = $res->recordCount();
##

//$pdf = new PDF_Unicode() ;                                                // 建立 FPDF
$pdf = new PDF1(); // 建立 FPDF

$pdf->Open(); // 開啟建立新的 PDF 檔案
$pdf->SetAuthor('First'); // 設定作者
$pdf->SetAutoPageBreak(1, 2); // 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10, 8, 10); // 設定顯示邊界 (左、上、右)
$pdf->AddPage(); // 新增一頁
$pdf->AddUniCNShwFont('Uni'); // 設定為 UTF-8 顯示輸出
$pdf->SetFont("Uni");
// $pdf->AddUniCNShwFont('uniKai','DFKaiShu-SB-Estd-BF');
// $pdf->SetFont('uniKai');
//////////////////////// 買方 ///////////////////////////

if ($iden == 'b') {
    require_once 'checklist_pdf_buyer.php';
} elseif ($iden == 'o') {
    require_once 'checklist_pdf_owner.php';
    $pdf->AddPage();
    require_once 'checklist_pdf_prevent.php';
} else {
    require_once 'checklist_pdf_buyer.php';
    //////////////////////// 賣方 ///////////////////////////
    $pdf->AddPage();
    require_once 'checklist_pdf_owner.php';
    $pdf->AddPage();
    require_once 'checklist_pdf_prevent.php';
}

/* */

/* */
// 產生輸出
//$pdf->Output($dir.$filename,'F') ;
// $pdf->Output() ;

if ($download) {
    $pdf->Output($download, 'F');
} else {
    $pdf->Output();
}

//echo $cCertifiedId."點交表已輸出" ;

#######################
//半形<=>全形
function n_to_w($strs, $types = '0')
{ // narrow to wide , or wide to narrow
    $nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " ",
    );
    $wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　",
    );

    if ($types == '0') { //半形轉全形
        // narrow to wide
        $strtmp = str_replace($nt, $wt, $strs);
    } else { //全形轉半形
        // wide to narrow
        $strtmp = str_replace($wt, $nt, $strs);
    }
    return $strtmp;
}
##

//遮蔽部分文數字
function newName($nameStr)
{
    for ($i = 0; $i < mb_strlen($nameStr, 'UTF-8'); $i++) {
        $arrName[$i] = mb_substr($nameStr, $i, 1, 'UTF-8');
        if (($i > 0) && ($i < (mb_strlen($nameStr, 'UTF-8') - 1))) {
            $arrName[$i] = 'Ｏ';
        }
    }
    return implode('', $arrName);
}
##
