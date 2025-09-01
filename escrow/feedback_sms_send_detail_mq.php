<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/mq.php';

$sn   = $_POST['sn'];
$list = explode(',', $sn);

if (empty($list)) {
    exit('E');
}

$conn = new first1DB;

$total = count($list);
$cnt   = 0; //計算失敗件數

foreach ($list as $v) {
    $sql = 'SELECT `uuid`, `sBatch`, `sMobile`, `sName`, `sSMS`, `sKind` FROM `tSMSWaitSend` WHERE `uuid` = :uuid;';
    $rs  = $conn->one($sql, ['uuid' => $v]);

    if (!empty($rs['uuid']) && !empty($rs['sBatch']) && !empty($rs['sMobile']) && !empty($rs['sSMS'])) {
        $data['sms_page']    = 'sms_feedback';
        $data['member']      = $_SESSION['member_id'];
        $data['uuid']        = $rs['uuid'];
        $data['batch']       = $rs['sBatch'];
        $data['mobile_tel']  = $rs['sMobile'];
        $data['mobile_name'] = empty($rs['sName']) ? '' : $rs['sName'];
        $data['sms_txt']     = $rs['sSMS'];
        $data['target']      = empty($rs['sKind']) ? '' : $rs['sKind'];
        $data['pid']         = '';
        $data['tid']         = '';

        MQ::push('sms', $data);

        $sql = 'UPDATE `tSMSWaitSend` SET `sSent` = "Y" WHERE `uuid` = :uuid;';
        $conn->exeSql($sql, ['uuid' => $v]);
    } else {
        $cnt++;
    }
}

//推送至 Queue 的狀態
if ($cnt == 0) {
    exit('Y'); //發送成功
}

if (($cnt > 0) && ($total > $cnt)) {
    exit('P'); //部分成功
} else {
    exit('N'); //失敗
}
##
