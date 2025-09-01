<?php


$pdf->AddPage();
$pdf->setY(0.8);


//小白單
// $y = $pdf->GetY();
// $pdf->SetY($y + 0.5);
// $pdf->writeHTMLCell(0, 0, $x, '', $y, 0, 1, 0, true, '', true);

$x = 0.7;
$y = $pdf->getY();
// $table = '
// <table width="100.5%" cellspacing="1" cellpadding="1" style="border:1px solid #CCC;line-height:8px;">
//     <tr>
//         <th width="100%" style="border:1px solid #CCC;line-height:8px;text-align:center;vertical-align:middle;">
//             <h2>代墊案件利息差額　申請單</h2>
//         </th>
//     </tr>
//     <tr>
//         <td width="100%" style="border:1px solid #CCC;line-height:8px;">
//         </td>
//     </tr>
// </table>';
$table = '';

foreach ($extra_form as $value) {

    if(is_array($_POST['account'])) {
        if(!in_array($value["tAccount"], $_POST['account'])) {
            continue;
        }
    }
    $checkbox['60001'] = $checkbox['55006'] = $checkbox['99985'] = $checkbox['99986'] = $checkbox['96988'] = '□';
    $accountPre = substr($value['tAccount'],0,5);
    $checkbox[$accountPre] = '■';

    $calculationResults = ($value['interest']) - ($value['cCertifiedMoney']) - ($value['NHITax']) - ($value['tax']);

    //20240702 不需要列出隨案回饋
    $feedBackMoney = '';
//    if(isset($value['caseFeedBackMoney']) ) {
//        $feedBackMoney = '- 隨案回饋$' . number_format($value['caseFeedBackMoney']);
//
//        $calculationResults = $calculationResults - ($value['caseFeedBackMoney']);
//    }
    $table .= '
    <br><br>
    <table width="98%" style="line-height:8px;" cellspacing="2" cellpadding="2">
        <tr>
            <th width="100%" style="text-align:left;">
                <h2 >代墊案件利息差額　申請單</h2>
            </th>
        </tr>
        <tr>
            <td width="25%" style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" >1.案件編號：</td>
            <td width="25%" style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" >' .substr($value['tAccount'], 5).'</td>
            <td width="25%" style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" >申請日：</td>
            <td width="25%" style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" >' .substr($value['tDate'], 0, 10).'</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" colspan="4">2.請選擇履保專戶銀行</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" colspan="4"> '.$checkbox['60001'].' 第一銀行： 60001 <br> '.$checkbox['55006'].' 第一銀行： 55006 <br> '.$checkbox['99985'].' 永豐銀行： 99985<br> '.$checkbox['99986'].' 永豐銀行： 99986<br> '.$checkbox['96988'].' 台新銀行： 96988</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" colspan="4">3.擇一勾選案件狀況，公司須代墊利息金額：$' .number_format($value['tMoney']).'</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" colspan="4">■ （1）利息收入>應收履保費時，適用</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:18=px;font-size: 10px;" colspan="4">
                計算：(利息收入$ '.number_format($value['interest']).'- 履保費用$ '.number_format($value['cCertifiedMoney']).'- 二代健保$'.number_format($value['NHITax']).'- 所得稅$'.number_format($value['tax']) . $feedBackMoney . ') = ' . number_format($calculationResults).';
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:11=px;font-size: 10px;" colspan="4">□ （2）重計利息後，須補發利息收入時，適用</td>
        </tr>
        <tr>
            <td style="border:1px solid #CCC;line-height:18=px;font-size: 10px;" colspan="4"> 計算：(更新後利息收入$________________ - 原利息收入$________________ )</td>
        </tr>
        <tr>
            <td colspan="2" style="line-height:11=px;font-size: 10px;">複審簽章:</td>
            <td colspan="2" style="line-height:11=px;font-size: 10px;">經辦人員簽章:</td>
        </tr>
    </table>
    <br><br><br><br><br><br><br><br><br><br>
    <hr>
    ';
}


$pdf->writeHTMLCell(0, 0, $x, '', $table, 0, 1, 0, true, '', true);
