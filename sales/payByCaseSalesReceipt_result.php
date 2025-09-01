<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/sales/payByCaseAccountingReceipt_result.php';

$i            = 0;
$list         = array();
$certifiedIds = array();

while (!$rs->EOF) {
    $list[$i] = $rs->fields;

    $list[$i]['cScrivener'] = 'SC' . str_pad(json_decode($list[$i]['cScrivener'])->cScrivener, 4, '0', STR_PAD_LEFT);
    $list[$i]['sOffice']    = json_decode($list[$i]['sOffice'])->scrivener;
    $list[$i]['total']      = json_decode($list[$i]['total'])->total;

    $certifiedIds[] = $rs->fields['certifiedId'];

    $i++;
    $rs->MoveNext();
}

$certifiedIdStr = implode(',', $certifiedIds);

if (!empty($certifiedIdStr)) {
    $sql = '
        SELECT
            cCertifiedId, 
            cFeedbackDate
        FROM
            `tContractCase` AS c
        WHERE
            cCertifiedId IN (' . $certifiedIdStr . ')
        ';
    $res = $conn->Execute($sql);

    $caseEndDate = array();
    while (!$res->EOF) {
        $cCertifiedId  = $res->fields['cCertifiedId'];
        $caseEndDate[$cCertifiedId] = $res->fields['cFeedbackDate'];

        $res->MoveNext();
    }

    foreach ($list as $key => $value) {
        if($caseEndDate[$value['certifiedId']] == '') $caseEndDate[$value['certifiedId']] = 'a';
        $list[$key]['endDate'] = $caseEndDate[$value['certifiedId']];
    }

    usort($list, function ($a, $b) {
        return $a['endDate'] > $b['endDate'];
    });
}

if (empty($list)) {
    echo '
        <table cellspacing="0" cellpadding="0" border="0" class="tb" width="100%">
            <tr>
                <th>收據繳回</th>
                <th>回饋日期</th>
                <th>保證號碼</th>
                <th>店編號</th>
                <th>店家名稱</th>
                <th>戶名</th>
                <th>金額</th>
                <th>業務</th>
            </tr>
            <tr>
                <td colspan=8 style="text-align:center;">無資料</td>
            </tr>
        </table>
    ';
    exit;
}
?>

<table cellspacing="0" cellpadding="0" border="0" class="tb" width="100%">
    <tr>
        <th>收據繳回</th>
        <th>回饋日期</th>
        <th>保證號碼</th>
        <th>店編號</th>
        <th>店家名稱</th>
        <th>戶名</th>
        <th>金額</th>
        <th>業務</th>
    </tr>
    <?php foreach ($list as $k => $v): ?>
        <?php if($v['total'] == 0) continue; ?>
    <tr>
        <td nowrap>
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
    </tr>
    <?php endforeach?>
</table>