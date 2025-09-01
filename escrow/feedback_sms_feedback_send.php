<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$data = [];

$conn = new first1DB;

if (!empty($_POST['batch'])) {
    $sql = 'SELECT * FROM `tSMSWaitSend` WHERE `sBatch` = :batch AND `sSent` = :send ORDER BY `sCreated_at` DESC;';
    $rs  = $conn->all($sql, [
        'batch' => $_POST['batch'],
        'send'  => 'N',
    ]);

    $data = empty($rs) ? [] : $rs;

    if (!empty($data)) {
        $smarty->assign('batch', $_POST['batch']);
        $smarty->assign('data', $data);
        $smarty->display('feedback_sms_feedback_send_detail.inc.tpl', '', 'escrow');

        exit;
    }
}

$sql = 'SELECT `sBatch`, `sSMS`, `sStore`, `sCreated_at`, COUNT(*) as total FROM `tSMSWaitSend` WHERE `sSent` = "N" GROUP BY `sBatch` ORDER BY `sCreated_at` DESC;';
$rs  = $conn->all($sql);

if (!empty($rs)) {
    foreach ($rs as $k => $v) {
        $target = $v['sStore'] . '等' . $v['total'] . '位';
        $sms    = mb_substr($v['sSMS'], 0, 30, "utf-8") . ' ...';
        $dt     = $v['sCreated_at'];

        $data[] = [
            'batch'    => $v['sBatch'],
            'target'   => $target,
            'sms'      => $sms,
            'datetime' => $dt,
        ];

        $target = $sms = $dt = null;
        unset($target, $sms, $dt);
    }
}

$smarty->assign('data', $data);
$smarty->display('feedback_sms_feedback_send.inc.tpl', '', 'escrow');
