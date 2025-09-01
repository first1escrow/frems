<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

//檢查過濾結案日期
function checkEndDate(&$endCaseStart, &$endCaseEnd, $endDate)
{
    if (empty($endCaseStart) && empty($endCaseEnd)) {
        return true;
    }

    $tf1 = $tf2 = true;
    if (!empty($endCaseStart)) { //本案件結案日 >= 搜尋結案日(起)為真
        $tf1 = ($endDate >= $endCaseStart) ? true : false;
    }

    if (!empty($endCaseEnd)) { //本案件結案日 <= 搜尋結案日(迄)為真
        $tf2 = ($endDate <= $endCaseEnd) ? true : false;
    }

    return (empty($tf1) || empty($tf2)) ? false : true;
}

$_POST = escapeStr($_POST);

$banktranStatus = $_POST['banktranStatus'];
$exp            = $_POST['exp'];
$exportStart    = $_POST['exportStart'];
$exportEnd      = $_POST['exportEnd'];

$endCaseStart = $_POST['endCaseStart'];
if (!empty($endCaseStart) && preg_match("/^\d{3}\-\d{2}\-\d{2}$/", $endCaseStart)) {
    $tmp          = explode('-', $endCaseStart);
    $endCaseStart = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    $tmp          = null;unset($tmp);
}

$endCaseEnd = $_POST['endCaseEnd'];
if (!empty($endCaseEnd) && preg_match("/^\d{3}\-\d{2}\-\d{2}$/", $endCaseEnd)) {
    $tmp        = explode('-', $endCaseEnd);
    $endCaseEnd = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    $tmp        = null;unset($tmp);
}

require_once dirname(__DIR__) . '/includes/accounting/payByCaseAccountingReceipt_result.php';

$i            = 0;
$all          = array();
$certifiedIds = array();

while (!$rs->EOF) {
    $all[$i] = $rs->fields;

    if ($all[$i]['receipt'] == "---" and $all[$i]['fType'] == 3) {
        $certifiedId = "'".$all[$i]['certifiedId']."'";
        $all[$i]['receipt'] = '<input type="checkbox" id="receipt_' . $all[$i]['certifiedId'] . '" value="Y" onclick="caseReceipt(' . $certifiedId . ')" >';
    }

    $detail = json_decode($all[$i]['fDetail']);

    $all[$i]['cScrivener'] = 'SC' . str_pad($detail->cScrivener, 4, "0",STR_PAD_LEFT);
    $all[$i]['sOffice'] = $detail->scrivener;
    $all[$i]['total'] = $detail->total;

    $certifiedIds[] = $rs->fields['certifiedId'];

    $i++;
    $rs->MoveNext();
}

if (!empty($certifiedIds)) {
    $certifiedIdStr = implode('","', $certifiedIds);

    $sql = 'SELECT cCertifiedId as cId, cBankList as eDate FROM `tContractCase` WHERE cCertifiedId IN ("' . $certifiedIdStr . '");';
    $res = $conn->Execute($sql);

    $caseEndDate = [];
    while (!$res->EOF) {
        $caseEndDate[$res->fields['cId']] = $res->fields['eDate'];
        $res->MoveNext();
    }

    $sql = 'SELECT tMemo as cId, tBankLoansDate as eDate FROM `tBankTrans` WHERE tMemo IN ("' . $certifiedIdStr . '") AND tKind = "保證費";';
    $res = $conn->Execute($sql);

    while (!$res->EOF) {
        $caseEndDate[$res->fields['cId']] = $res->fields['eDate'];
        $res->MoveNext();
    }

    $list = [];
    foreach ($all as $key => $value) {
        $endDate = $caseEndDate[$value['certifiedId']];

        if (checkEndDate($endCaseStart, $endCaseEnd, $endDate)) {
            if($endDate == '') $endDate = 'a';
            $list[] = array_merge($value, ['endDate' => $endDate]);
        }
        $endDate = null;unset($endDate);
    }

    usort($list, function ($a, $b) {
        return ($a['endDate'] > $b['endDate']) ? 1 : -1 ;
    });

}
?>

<table cellspacing="0" cellpadding="0" border="0" class="tb" width="100%">
    <tr>
        <th><input type="checkbox" name="all" checked="" onclick="checkALL()"></th>
        <th>收據繳回</th>
        <th>履保費日期</th>
        <th>保證號碼</th>
        <th>店編號</th>
        <th>店家名稱</th>
        <th>戶名</th>
        <th>金額</th>
        <th nowrap>業務</th>
        <?php if ($banktranStatus == 2): ?>
        <th>&nbsp;</th>
        <?php endif?>
    </tr>
    <?php foreach ($list as $k => $v): ?>
    <?php if($v['total'] == 0) continue; ?>
    <tr>
        <td nowrap>
            <input type="checkbox" name="allForm[]" value="<?=$v['fId']?>" checked="" onclick="showcount()">
        </td>
        <td nowrap>
            <?php if ($v['receipt'] == '已收'): ?>
                <input type="checkbox" id="receipt_'<?=$v['certifiedId']?>'" value="N" onclick="caseReceipt(<?=$v['certifiedId']?>)" checked>
            <?php endif?>
            <?=$v['receipt']?>
        </td>
        <td nowrap><?=(substr($v['endDate'], 0, 10) == 'a') ?  '':  (substr($v['endDate'], 0, 10))?></td>
        <td nowrap><?=$v['certifiedId']?></td>
        <td nowrap><?=$v['cScrivener']?></td>
        <td nowrap><?=$v['sOffice']?></td>
        <td nowrap><?=$v['bankAccountName']?></td>
        <td align="right" nowrap>
            <?=number_format($v['total'])?> <input type="hidden" name="sFeedBackMoneyTotal_<?=$v['fId']?>"
                value="<?=$v['total']?>">
        </td>
        <td nowrap><?=$v['bSalesName']?></td>
        <?php if ($banktranStatus == 2): ?>
        <td nowrap><input type="button" value="恢復" onclick="setStatus(<?=$v['fId']?>)" class="xxx-button"></td>
        <?php endif?>
    </tr>
    <?php endforeach?>
</table>