<?php
header('Content-Type: application/json');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$export     = $_POST['export'];
$export_nu  = $_POST['export_nu'];
$start_date = $_POST['start_date'];
$end_date   = $_POST['end_date'];

$limit = '';
if (empty($export_nu) && empty($start_date) && empty($end_date)) {
    $limit = 'LIMIT 1000';
}

$sql = ' IF(bKind = "地政士回饋金", c.fBankAccount = a.bAccount, TRUE ) AND a.bConfirmOk = "1"  AND a.bExport = "' . $export . '" ';

if (!empty($export_nu)) {
    $sql .= empty($export_nu) ? '' : ' AND ';
    $sql .= ' a.bExport_nu = "' . $export_nu . '" ';
}

if (!empty($start_date)) {
    $sql .= empty($start_date) ? '' : ' AND ';

    $tmp        = explode('-', $start_date);
    $start_date = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2] . ' 00:00:00';
    $sql .= ' a.bExport_time >= "' . $start_date . '" ';

    $tmp = null;unset($tmp);
}

if (!empty($end_date)) {
    $sql .= empty($end_date) ? '' : ' AND ';

    $tmp      = explode('-', $end_date);
    $end_date = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2] . ' 23:59:59';
    $sql .= ' a.bExport_time <= "' . $end_date . '" ';

    $tmp = null;unset($tmp);
}

$conn = new first1DB;

$sql = 'SELECT
            a.bUid,
            a.bCertifiedId,
            a.bDate,
            a.bKind,
            a.bBankCode,
            a.bAccount,
            a.bAccountName,
            a.bMoney,
            a.bIncomingMoney,
            a.bTxt,
            a.bExport_nu,
            a.bExport_time,
            a.bSms,
            b.fTax,
            b.fNHI,
            CONCAT("SC", LPAD(b.fTargetId, 4, "0")) as scrivener,
            (SELECT bBank3_name FROM tBank WHERE bBank3 = SUBSTRING(a.bBankCode, 1, 3) AND bBank4 = SUBSTRING(a.bBankCode, 4, 4)) as bankAlias,
            (SELECT fIdentityIdNumber FROM tFeedBackMoneyPayByCaseAccount WHERE fCertifiedId = a.bCertifiedId AND fTarget = "S" AND fPayByCaseId = b.fId) as identityIdNumber,
            a.bCreated_at
        FROM
            tBankTransRelay AS a
        JOIN
            tFeedBackMoneyPayByCase AS b ON a.bCertifiedId = b.fCertifiedId AND b.fTarget = "S"
        JOIN 
            tFeedBackMoneyPayByCaseAccount AS c ON b.fId =c.fPayByCaseId
        WHERE
            ' . $sql . '
        GROUP BY
         a.bKind, a.bCertifiedId, a.bCreated_at
        ORDER BY
            a.bOrderTime, a.bCertifiedId, a.bCreated_at ASC, a.bKind DESC
        ' . $limit . ';';
$rs = $conn->all($sql);

if (empty($rs)) {
    exit(json_encode(['status' => 'OK', 'data' => null]));
}

$html = '';
foreach ($rs as $v) {
    $export_nu = empty($v['bExport_nu']) ? '-' : $v['bExport_nu'];

    $store = 'x';
    $NHI   = $Tax   = $money   = 0;
    if ($v['bKind'] == '地政士回饋金') {
        $NHI   = $v['fNHI'];
        $Tax   = $v['fTax'];
        $store = $v['scrivener'];
        $money = $v['bMoney'] + $NHI + $Tax;

        $v['bKind'] = '回饋金';
    }

    $v['identityIdNumber'] = ($v['bKind'] == '保證費') ? 0 : $v['identityIdNumber'];

    $export_time = '-';
    if (preg_match("/^\d{4}\-\d{2}\-\d{2}/", $v['bExport_time'])) {
        $_export_time = explode('-', substr($v['bExport_time'], 0, 10));
        $export_time  = ($_export_time[0] - 1911) . '-' . $_export_time[1] . '-' . $_export_time[2];
    }

    $class = ($v['bKind'] == '回饋金') ? 'feedback' : '';

    $sms = '&nbsp;';
    if ($v['bKind'] == '回饋金') {
        $sms = empty($v['bSms']) ? '<input type="checkbox" class="js-sms-check" name="sms-uid[]" value="' . $v['bUid'] . '" onclick="smsChecked()">' : $v['bSms'];
    }

    $html .= '
        <tr class="' . $class . '">
            <td><input type="checkbox" class="js-check" name="uid[]" value="' . $v['bUid'] . '" onclick="calculateChecked()"></td>
            <td class="sms-show">' . $sms . '</td>
            <td>' . $v['bKind'] . '</td>
            <td>' . $v['bCertifiedId'] . '</td>
            <td>' . $store . '</td>
            <td style="text-align: left;padding-left: 5px;">' . $v['bAccountName'] . '</td>
            <td>' . $v['identityIdNumber'] . '</td>
            <td style="font-size: 14px;">' . number_format($money) . '</td>
            <td style="font-size: 14px;">' . number_format($Tax) . '</td>
            <td style="font-size: 14px;">' . number_format($NHI) . '</td>
            <td style="font-size: 14px;">' . number_format($v['bMoney']) . '</td>
            <td style="font-size: 14px;">' . number_format($v['bIncomingMoney']) . '</td>
            <td>' . $export_time . '</td>
            <td>' . $export_nu . '</td>
        </tr>
    ';

    $class = $export_nu = $_export_time = $export_time = $NHI = $Tax = $money = $store = $sms = null;
    unset($class, $export_nu, $_export_time, $export_time, $NHI, $Tax, $money, $store, $sms);
}

if (!empty($html)) {
    $btn = ($export == 2) ? '<button type="button" style="width:80px;" onclick="exportList()">匯出</button>' : '<button type="button" style="width:80px;" onclick="restoreList()">恢復</button>';
    $btn .= ($export == 2) ? '' : '<button type="button" style="margin-left:10px;width:80px;" onclick="sendSMS()">發送簡訊</button>';

    $html .= '
        <tr>
            <td colspan=13 style="padding: 15px;">
                ' . $btn . '
            </td>
        </tr>
    ';

    $btn = null;unset($btn);
}

exit(json_encode(['status' => 'OK', 'data' => $html]));