<?php
// exit('buyerowner = ' . $buyerowner);
if (!$pdo) {
    $pdo = $pdo62;
}
//14 碼保證號碼
$tVR_Code     = $data_case['cEscrowBankAccount'];
$cCertifiedId = $data_case['cCertifiedId'];

// exit('cCertifiedId = ' . $cCertifiedId);

//蒐集出入款明細
if ($buyerowner == 2) { //賣方
    require_once __DIR__ . '/formOwnerDetail.php';
} else {
    require_once __DIR__ . '/formBuyerDetail.php';
}
##
// echo '<pre>';
// print_r($item);exit;
?>

<table class="ct_table">
    <tbody>
        <tr class="odd-row-b">
            <th colspan="3"></th>
            <th><span src="../images/loader.gif" popup_block"" style="display:none;" id="loader"></span></th>
        </tr>
        <tr class="odd-row">
            <th width="20%" height="35" class="first">日期</th>
            <th width="20%">摘要</th>
            <th width="20%">收入</th>
            <th width="20%">支出</th>
            <th width="20%" class="last">備註</th>
        </tr>
        <?php
$income_money  = 0;
$expense_money = 0;

if ($item) {
    foreach ($item as $k => $v) {
        $income_money += $v['income'];
        $expense_money += $v['expense'];

        echo '
        <tr class="intable">
            <td>' . $v['date'] . '</td>
            <td>' . $v['kind'] . '</td>
            <td style="text-align:right">NT$' . number_format($v['income']) . '</td>
            <td style="text-align:right">NT$' . number_format($v['expense']) . '</td>
            <td>' . $v['remark'] . '</td>
        </tr>
        ';
    }
}

?>
        <tr class="intable">
            <td></td>
            <td style="text-align:right">合計</td>
            <td style="text-align:right">NT$<?=number_format($income_money)?></td>
            <td style="text-align:right">NT$<?=number_format($expense_money)?></td>
            <td></td>
        </tr>
        <tr class="intable">
            <td colspan="3" style="text-align:right;">(收入-支出)</td>
            <td style="text-align:right;">NT$<?=number_format($income_money - $expense_money)?></td>
        </tr>
    </tbody>
</table>

<?php if ($data_case['cEscrowBankAccount'] == '60001090364171'): ?>
<div>※100萬不入專戶</div>
<?php endif?>