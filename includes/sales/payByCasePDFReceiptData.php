<?php
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;

$company = json_decode(file_get_contents(dirname(__DIR__) . '/company.json'), true); //公司資訊

//設定行高
$cell_y1  = 4.5; // 內容用
$cell_y2  = 5;   // 標題用
$cell_y3  = 1;   // 手動跳行調行距用
$cell_y4  = 5;   // 內容用
$cell_y5  = 8;   // 銀行框框加大
$cell_y6  = 4;   // 注意事項用
$cell_gap = 2;   // 單元分隔用
$line_gap = 0.4; // 雙線條畫線用

$cell_pay1 = 6.4;
##

//取得隨案付款案件明細
$paybycase = new PayByCase();
$case      = $paybycase->getPayByCaseWithTargetId($cCertifiedId, $targetId, 'S');
$fDetail   = json_decode($case['fDetail']);
##

//取得案件合約資訊與款項資訊
$contract       = new Contract();
$contractCase   = $contract->GetContract($cCertifiedId);
$contractIncome = $contract->GetIncome($cCertifiedId);

##

//取得所屬業務資訊
$sql         = "SELECT pName, pMobile FROM tPeopleInfo WHERE pId = " . $case['fSales'];
$rs          = $conn->Execute($sql);
$salesName   = $rs->fields['pName'];
$salesMobile = $rs->fields['pMobile'];
##

//代扣部分
$tax    = $case['fTax']; //代扣稅額
$NHITax = $case['fNHI']; //代扣二代健保
##

$undertaker = getUndertaker($fDetail->cScrivener);
##

//取得案件其他資訊
$detail = [
    'bBuyer' => $pdf->Half2Full(getBuyerOwner($cCertifiedId, 'B')),
    'cOwner' => $pdf->Half2Full(getBuyerOwner($cCertifiedId, 'O')),
];
##

//計算單列高度
$max_charactor_per_row = 3 * 6; //買賣方單行文字最大字數(utf-8: 3bytes x 6 個字)

$buyer = ceil(mb_strlen($detail['bBuyer']) / $max_charactor_per_row);
$owner = ceil(mb_strlen($detail['cOwner']) / $max_charactor_per_row);

$buyer = empty($buyer) ? 1 : $buyer;
$owner = empty($owner) ? 1 : $owner;

$rows          = $buyer > $owner ? $buyer : $owner;
$cell_y5_table = $cell_y5 + (($rows - 1) * $cell_y5);

$buyer = str_split($detail['bBuyer'], $max_charactor_per_row);
$owner = str_split($detail['cOwner'], $max_charactor_per_row);

$rows = (count($buyer) > count($owner)) ? count($buyer) : count($owner);
$_h   = $cell_y5 * $rows;
##

$pdf->Open();    // 開啟建立新的 PDF 檔案
$pdf->AddPage(); // 新增一頁

$pdf->SetFontSize(14);
$pdf->Cell(190, $cell_y1, '第一建築經理(股)公司', 0, 1, 'C'); // 寫入文字
$pdf->SetFontSize(12);
$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->SetFillColor(232, 234, 237);                   //設定底色
$pdf->Rect(10, $pdf->GetY(), 190, $cell_pay1, 'DF'); //畫框並填充顏色
$pdf->Cell(190, $cell_y3, '', 0, 1);                 // 手動換行

$pdf->Cell(190, $cell_y1, '回饋金報表', 0, 1, 'C');
$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->Cell(12, $cell_y5, '名稱', 1, 0, 'C'); // 名稱
$id = 'SC' . str_pad($case['detail']['cScrivener'], 4, '0', STR_PAD_LEFT);
$pdf->Cell(88, $cell_y5, $id . '-' . $case['detail']['scrivener'], 1, 'C'); // 編號、事務所
$pdf->Cell(20, $cell_y5, '專屬顧問', 1, 0, 'C');                        // 專屬顧問
$pdf->Cell(16, $cell_y5, $salesName, 1, 0, 'C');                            // 業務員姓名
$pdf->Cell(28, $cell_y5, '專屬顧問電話', 1, 0, 'C');                  // 專屬顧問電話
$pdf->Cell(26, $cell_y5, $salesMobile, 1, 0, 'C');                          // 業務員手機
$pdf->Ln();

$pdf->Cell(12, $cell_y5, '序號', 1, 0, 'C');          // 序號
$pdf->Cell(24, $cell_y5, '結案日期', 1, 0, 'C');    // 結案日期
$pdf->Cell(24, $cell_y5, '保證號碼', 1, 0, 'C');    // 保證號碼
$pdf->Cell(30, $cell_y5, '買方', 1);                  // 買方
$pdf->Cell(30, $cell_y5, '賣方', 1);                  // 賣方
$pdf->Cell(24, $cell_y5, '買賣總價金', 1, 0, 'C'); // 買賣總價金
$pdf->Cell(24, $cell_y5, '保證費', 1, 0, 'C');       // 保證費
$pdf->Cell(22, $cell_y5, '回饋金額', 1, 0, 'C');    // 回饋金額
$pdf->Ln();

$feedBackMoneyTotal = 0; //回饋金總金額

$_Y = $pdf->GetY();
foreach ($fDetail->case as $key => $value) {
    $cCaseFeedBackMoney = $value->cCaseFeedBackMoney;
    $cCaseFeedBackMoney = preg_match("/^\d+$/i", $cCaseFeedBackMoney) ? $cCaseFeedBackMoney : 0;
    if ($cCaseFeedBackMoney == 0) {
        continue;
    }

    //序號
    $pdf->Cell(12, $cell_y5_table, ($key + 1), 0, 0, 'C');
    $pdf->Rect(10, $pdf->GetY(), 12, $_h);
    ##

    //結案日期
    $pdf->Cell(24, $cell_y5_table, $contractCase['cFeedbackDate'], 0, 0, 'C');
    $pdf->Rect(22, $pdf->GetY(), 24, $_h);
    ##

    //保證號碼
    $pdf->Cell(24, $cell_y5_table, $cCertifiedId, 0);
    $pdf->Rect(46, $pdf->GetY(), 24, $_h);
    ##

    //買方
    $_X         = $pdf->GetX();
    $_Y         = $pdf->GetY();
    $_positionX = $_X;
    $_positionY = $_Y;

    foreach ($buyer as $v) {
        $pdf->SetXY($_positionX, $_positionY);
        $pdf->Cell(30, $cell_y5, $v, 0);

        $_positionY += $cell_y5;
    }

    $pdf->Rect(70, $_Y, 30, $_h);
    ##

    //賣方
    $pdf->SetXY(($_X + 30), $_Y);
    $_X         = $pdf->GetX();
    $_Y         = $pdf->GetY();
    $_positionX = $_X;
    $_positionY = $_Y;

    foreach ($owner as $v) {
        $pdf->SetXY($_positionX, $_positionY);
        $pdf->Cell(30, $cell_y5, $v, 0);

        $_positionY += $cell_y5;
    }

    $pdf->Rect(100, $_Y, 30, $_h);
    ##

    $pdf->SetXY(130, $_Y);

    //買賣總價金
    $pdf->Cell(24, $cell_y5_table, number_format($contractIncome['cTotalMoney'], 0), 0, 0, 'R');
    $pdf->Rect(130, $pdf->GetY(), 24, $_h);
    ##

    //保證費
    $pdf->Cell(24, $cell_y5_table, number_format($contractIncome['cCertifiedMoney'], 0), 0, 0, 'R');
    $pdf->Rect(154, $pdf->GetY(), 24, $_h);
    ##

    //回饋金額
    $pdf->Cell(22, $cell_y5_table, number_format($cCaseFeedBackMoney, 0), 0, 0, 'R');
    $pdf->Rect(178, $pdf->GetY(), 22, $_h);
    ##

    $pdf->Ln();

    $feedBackMoneyTotal = $feedBackMoneyTotal + $value->cCaseFeedBackMoney;

    $_X = $pdf->GetX();
    $_Y = $pdf->GetY();

    $_positionX = $_positionY = null;
    unset($_positionX, $_positionY);
}
#正式機和本機呈現結果不同
//$pdf->SetY(($rows - 1) * $cell_y5 + $_Y);
$pdf->SetY($_Y);

//給付明細
$pdf->Rect(10, $pdf->GetY(), 190, $cell_pay1, 'DF'); //畫框並填充顏色
$pdf->Cell(190, $cell_y3, '', 0, 1);                 // 手動換行
$pdf->Cell(190, $cell_y1, '給付明細', 0, 1, 'L');
$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->Cell(30, $cell_y5, '回饋金總計', 1);                               // 回饋金總計
$pdf->Cell(160, $cell_y5, 'NT$ ' . number_format($feedBackMoneyTotal, 0), 1); //
$pdf->Ln();

$pdf->Cell(30, $cell_y5, '扣繳稅額', 1);                   // 扣繳稅額
$pdf->Cell(160, $cell_y5, 'NT$ ' . number_format($tax, 0), 1); //
$pdf->Ln();

if ($case['fType'] == 2) {                                        //本國自然人須代扣
    $pdf->Cell(30, $cell_y5, '二代健保', 1);                      // 二代健保
    $pdf->Cell(160, $cell_y5, 'NT$ ' . number_format($NHITax, 0), 1); //
    $pdf->Ln();
}

$pay = $feedBackMoneyTotal - $tax - $NHITax;
$pdf->Cell(30, $cell_y5, '給付淨額', 1); // 給付淨額
$pdf->SetFont('', 'B', 12);
$pdf->Cell(160, $cell_y5, 'NT$ ' . number_format($pay, 0), 1); //
$pdf->SetFont('', '', 12);
$pdf->Ln();

//台端約定回饋金收款帳戶
$sql         = 'SELECT a.bBank4_name, (SELECT bBank4_name FROM tBank WHERE bBank3 = a.bBank3 AND bBank4 = "") as bank_main FROM tBank AS a WHERE bBank3 = "' . $case['fBankMain'] . '" AND bBank4 = "' . $case['fBankBranch'] . '";';
$rs          = $conn->Execute($sql);
$bank_detail = $rs->fields;

$pdf->Rect(10, $pdf->GetY(), 190, $cell_pay1, 'DF'); //畫框並填充顏色
$pdf->Cell(190, $cell_y3, '', 0, 1);                 // 手動換行
$pdf->Cell(190, $cell_y1, '台端約定回饋金收款帳戶', 0, 1, 'L');
$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->Cell(30, $cell_y5, '總行', 1);                    // 總行
$pdf->Cell(65, $cell_y5, $bank_detail['bank_main'], 1);   //
$pdf->Cell(30, $cell_y5, '分行', 1);                    // 分行
$pdf->Cell(65, $cell_y5, $bank_detail['bBank4_name'], 1); //
$pdf->Ln();
$pdf->Cell(30, $cell_y5, '指定帳號', 1);         // 指定帳號
$pdf->Cell(160, $cell_y5, $case['fBankAccount'], 1); //
$pdf->Ln();
$pdf->Cell(30, $cell_y5, '戶名', 1);                   // 戶名
$pdf->Cell(160, $cell_y5, $case['fBankAccountName'], 1); //
$pdf->Ln();

$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行
$pdf->SetFont('', 'B', 12);
$pdf->Cell(190, $cell_y1, '*以上資料確認無誤後，請於下方簽名回傳。(如需變更資料請洽專屬顧問)', 0, 1, 'L');
$pdf->Ln();

//注意事項
$pdf->Cell(190, $cell_y1, '注意事項：', 0, 1, 'L');
$pdf->SetFont('', '', 12);
if ($case['fType'] != 3) {
    $pdf->Cell(190, $cell_y1, '1.本公司將依稅法規定，以收款人為所得人向稽徵機關申報執行業務所得。', 0, 1, 'L');
    $pdf->Cell(190, $cell_y1, '2.給付金額達新台幣二萬元以上，依法代扣所得稅款10%。', 0, 1, 'L');
    $pdf->Cell(190, $cell_y1, '3.給付金額達新台幣二萬元以上，依全民健康保險法扣取補充保險費2.11%。', 0, 1, 'L');
    $pdf->Cell(190, 50, '地政士簽章：', 0, 1, 'L');
} else {
    $Y = $pdf->GetY() + 20;
    $X = $pdf->GetX();
    $pdf->Cell(190, $cell_y1, '1.用印大小章需與收款戶名相同。', 0, 1, 'L');
    $pdf->Cell(190, $cell_y1, '2.本公司將依稅法規定，以收款人為所得人向稽徵機關申報執行業務所得。', 0, 1, 'L');
    $pdf->Cell(190, $cell_y1, '3.給付金額達新台幣二萬元以上，依法代扣所得稅款10%。', 0, 1, 'L');
    $pdf->Cell(60, 50, '地政士簽章：', 0, 1, 'L');

    $Y2 = $pdf->GetY();
    $pdf->SetTextColor(195, 195, 195);
    $pdf->setY($Y);
    $pdf->setX(($X + 30));
    $pdf->Cell(30, 30, '用印', 1, 0, 'C', 0);
    $pdf->setY(($Y + 12));
    $pdf->setX(($X + 70));

    $pdf->Cell(18, 18, '用印', 1, 0, 'C', 0);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->setY($Y2);
}

$pdf->SetFontSize(10);
$pdf->Text(12, 290, '中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：' . $company['tel'] . ' Ext.' . $undertaker['Ext'] . '　　傳真電話：' . $undertaker['FaxNum']);
