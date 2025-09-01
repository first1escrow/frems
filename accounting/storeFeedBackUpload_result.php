<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

$_POST     = escapeStr($_POST);
$branch    = $_POST['branch'];
$scrivener = $_POST['scrivener'];
$date      = $_POST['sDate'];
$date2     = $_POST['sDate2'];
$str       = "sDel = 0 ";

$store = array();
if ($branch && $scrivener) {
    array_push($store, substr($branch, 2));
    array_push($store, substr($scrivener, 2));

    $str .= " AND sStoreId IN (" . @implode(',', $store) . ")";

} else if ($branch) {
    $str .= " AND sType = 2 AND sStoreId = '" . substr($branch, 2) . "'";

} elseif ($scrivener) {
    $str .= " AND sType = 1 AND sStoreId = '" . substr($scrivener, 2) . "'";
}

if ($date && $date2) {
    $tmp         = explode('-', $date);
    $search_date = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
    unset($tmp);

    $tmp          = explode('-', $date2);
    $search_date2 = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];

    $str .= " AND (sDate >= '" . $search_date . "' AND sDate <= '" . $search_date2 . "')";
}

// 排序
// 1.收款日 近到遠
// 2.季別
// 3.編號 仲介~代書

$sql = "SELECT * FROM tStoreFeedBackMoneyFrom_Record WHERE " . $str . " ORDER BY sDate DESC,sSeason DESC,sType DESC,sStoreId ASC ";
// echo $sql;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

?>
<table cellpadding="0" cellspacing="0" width="100%" class="tb2">
	<tr>
		<th><input type="checkbox" name="all" id="" checked onclick="checkALL()"></th>
		<th>編號</th>
		<th>店家</th>
		<th>季別</th>
		<th>收款日</th>
		<th>回饋金</th>
		<th>代扣二代健保</th>
		<th>代扣所得稅</th>
		<th>實收金額</th>

	</tr>
	<?php if (count($list) > 0): ?>
		<?php foreach ($list as $k => $v): ?>

			<?php
if ($v['sType'] == 1) {
    $sql           = "SELECT sName,sOffice,CONCAT('SC',LPAD(sId,4,'0')) AS code FROM tScrivener WHERE sId = '" . $v['sStoreId'] . "'";
    $rs            = $conn->Execute($sql);
    $data          = $rs->fields;
    $data['store'] = $rs->fields['sName'] . "-" . $rs->fields['sOffice'];

} else if ($v['sType'] == 2) {
    $sql           = "SELECT bStore,(Select bName From `tBrand` c Where c.bId = bBrand ) AS bBrand,CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as code FROM tBranch WHERE bId = '" . $v['sStoreId'] . "'";
    $rs            = $conn->Execute($sql);
    $data          = $rs->fields;
    $data['store'] = $rs->fields['bBrand'] . "-" . $rs->fields['bStore'];

}
$v['sDate'] = str_replace('-', '/', (substr($v['sDate'], 0, 4) - 1911) . substr($v['sDate'], 4));
?>
			<tr>
				<td>
                    <input type="checkbox" name="allForm[]" id="" value="<?=$v['sId']?>" checked onclick="showcount()">
                </td>
				<td>
                    <?=$data['code']?>
                </td>
				<td>
                    <?=$data['store']?>
                </td>
				<td>
                    <?=$v['sSeason']?>
                </td>
				<td>
                    <?=$v['sDate']?>
                </td>
				<td align="right">
                    <?=number_format($v['sFeedBackMoney'])?>
                    <input type="hidden" name="allForm_feedback_<?=$v['sId']?>" value="<?=$v['sFeedBackMoney']?>">
                </td>
				<td align="right">
                    <?=number_format($v['sNHITax'])?>
                    <input type="hidden" name="allForm_NHI_<?=$v['sId']?>" value="<?=$v['sNHITax']?>">
                </td>
				<td align="right">
                    <?=number_format($v['sTax'])?>
                    <input type="hidden" name="allForm_Tax_<?=$v['sId']?>" value="<?=$v['sTax']?>">
                </td>
				<td align="right">
                    <?=number_format($v['sAmountReceived'])?>
                    <input type="hidden" name="allForm_Amount_<?=$v['sId']?>" value="<?=$v['sAmountReceived']?>">
                </td>

			</tr>
		<?php endforeach?>
	<?php else: ?>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
	<?php endif?>

</table>
<style>
.cs_left {
    text-align: right;
    border: none;
}

.cs_right {
    text-align: right;
    border:none;

}
</style>
<center>
    <div>
        <input type="button" value="刪除" onclick="deleteUpload();" class="xxx-button">
    </div>
    <div>
        <fieldset style="width: 350px; padding: 0 5px 5px 5px;">
            <legend style="padding: 10px;">已勾選統計</legend>

            <table cellspacing="0" cellpadding="0" style="border:none;">
                <tr>
                    <td class="cs_right" style="border:none;">已勾選店家數：</td>
                    <td class="cs_left" style="border:none;"><span id="count"></span></td>
                </tr>
                <tr>
                    <td class="cs_right" style="border:none;">回饋金加總：</td>
                    <td class="cs_left" style="border:none;"><span id="feedback_total"></span></td>
                </tr>
                <tr>
                    <td class="cs_right" style="border:none;">代扣二代健保加總：</td>
                    <td class="cs_left" style="border:none;"><span id="NHI_total"></span></td>
                </tr>
                <tr>
                    <td class="cs_right" style="border:none;">代扣所得稅加總：</td>
                    <td class="cs_left" style="border:none;"><span id="tax_total"></span></td>
                </tr>
                <tr>
                    <td class="cs_right" style="border:none;">實收總金額加總：</td>
                    <td class="cs_left" style="border:none;"><span id="amount_total"></span></td>
                </tr>
            </table>

        </fieldset>

    </div>
</center>

<script>
    function showcount() {
        var _count = 0;
        var _feedback_total = 0;
        var _NHI_total = 0;
        var _tax_total = 0;
        var _amount_total = 0;

        $('[name="allForm[]"]').each(function() {
            if ($(this).prop('checked') == true) {
                _count ++;

                _feedback_total += parseInt($('[name="allForm_feedback_'+$(this).val()+'"]').val());
                _NHI_total      += parseInt($('[name="allForm_NHI_'+$(this).val()+'"]').val());
                _tax_total      += parseInt($('[name="allForm_Tax_'+$(this).val()+'"]').val());
                _amount_total   += parseInt($('[name="allForm_Amount_'+$(this).val()+'"]').val());
            }
        });

        $("#count").empty().html(_count.toLocaleString('en-US'));
        $("#feedback_total").empty().html(_feedback_total.toLocaleString('en-US'));
        $("#NHI_total").empty().html(_NHI_total.toLocaleString('en-US'));
        $("#tax_total").empty().html(_tax_total.toLocaleString('en-US'));
        $("#amount_total").empty().html(_amount_total.toLocaleString('en-US'));
    }

    showcount();
</script>