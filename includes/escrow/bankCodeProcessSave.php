<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$_POST = escapeStr($_POST);

$count      = (is_array($_POST['id'])) ? count($_POST['id']) : 0;
$year       = $_POST['year'];
$month      = $_POST['month'];
$checkCount = 0;
for ($i = 0; $i < $count; $i++) {
    $date  = ($_POST['shipdate'][$i] != '000-00-00') ? (substr($_POST['shipdate'][$i], 0, 3) + 1911) . substr($_POST['shipdate'][$i], 3) : '';
    $date2 = ($_POST['urgentdate'][$i] != '000-00-00') ? (substr($_POST['urgentdate'][$i], 0, 3) + 1911) . substr($_POST['urgentdate'][$i], 3) : '';

    $sql = "UPDATE
                tBankCodeForm2
            SET
                bProducer = '" . $_POST['producer'][$i] . "',
                bUrgentDate = '" . $date2 . "',
                bShipDate = '" . $date . "',
                bEditor = '" . $_SESSION['member_id'] . "',
                bNote = '" . $_POST['note'][$i] . "'
            WHERE
                bId = '" . $_POST['id'][$i] . "'";

    if ($conn->Execute($sql)) {
        $checkCount++;

        //20250716 更新 app 申請合約書狀態
        $sql = 'SELECT aId FROM tApplyBankCode WHERE aFormNo2 = "' . $_POST['id'][$i] . '"';
        $rs  = $conn->Execute($sql);
        if (! $rs->EOF) {
            $status = empty($date) ? 2 : 3;
            $sql    = 'UPDATE tApplyBankCode SET aProcessed = ' . $status . ' WHERE aFormNo2 = "' . $_POST['id'][$i] . '";';
            $conn->Execute($sql);
        }
    }
}

if ($checkCount == $count) {
    echo '儲存成功';
}
